<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPassOTPMail;

class ForgetPasswordController extends Controller
{

    public function forgetPassEmail(Request $request)
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255'],
        ];

        if (config('captcha.version') !== 'no_captcha') {
            $rules['g-recaptcha-response'] = 'required|captcha';
        } else {
            $rules['g-recaptcha-response'] = 'nullable';
        }

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'errors' => $validate->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'No user found with this email address.',
                ], Response::HTTP_NOT_FOUND);
            }

            // ✅ Generate a unique 6-digit OTP that doesn't exist for any user
            do {
                $otp = rand(100000, 999999);
            } while (User::where('forget_pass_otp', $otp)->exists());

            // Save OTP to user record
            $user->forget_pass_otp = $otp;
            $user->save();

            // ✅ Send OTP email
            Mail::to($user->email)->send(new ForgetPassOTPMail($user, $otp));

            DB::commit();

            return response()->json([
                'message' => 'An OTP has been sent to your email. Please use it to reset your password.',
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('User Forget Password failed', ['error' => $th->getMessage()]);

            return response()->json([
                'message' => 'Something went wrong! Please try again later',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function OtpVerification(Request $request)
    {
        $rules = [
            'otp' => ['required', 'string', 'max:255'],
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'errors' => $validate->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            // Find user by OTP
            $user = User::where('forget_pass_otp', $request->otp)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid or expired OTP.',
                ], Response::HTTP_NOT_FOUND);
            }

            // ✅ Clear OTP after verification for security
            $user->forget_pass_otp = null;
            $user->save();

            // ✅ Authenticate user and create token
            $token = $user->createToken('PasswordResetToken')->plainTextToken;
            $user->remember_token = $token;
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully. You can now reset your password.',
                'token' => $token,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('User OTP Verification failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong! Please try again later',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function resetPassword(Request $request)
    {
        $rules = [
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
            'confirm_password' => ['required', 'same:password'],
        ];

        $validate = Validator::make($request->all(), $rules);
        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation Error!',
                'errors' => $validate->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            // Find user by token
            $user = User::where('remember_token', $request->token)->first();

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid or expired token.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Update user's password
            $user->password = Hash::make($request->password);
            $user->save();

            // ✅ Invalidate token after password reset
            $user->tokens()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. You can now login with your new password.',
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('User Password Reset failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong! Please try again later',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
