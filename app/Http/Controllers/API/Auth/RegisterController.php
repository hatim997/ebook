<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function register_attempt(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'terms' => 'required|string|max:255',
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

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->email_verified_at = Carbon::now();
            $user->password = Hash::make($request->password);

            $username = $this->generateUsername($request->name);
            while (User::where('username', $username)->exists()) {
                $username = $this->generateUsername($request->name);
            }
            $user->username = $username;
            $user->save();

            $user->syncRoles('user');

            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->first_name = $request->name;
            $profile->phone_number = $request->phone;
            $profile->save();

            $token = $user->createToken($user->name, ['auth_token'])->plainTextToken;

            Auth::attempt(['email' => $request->email, 'password' => $request->password]);

            // if (Auth::check()) {
            //     VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            //         return (new MailMessage)
            //             ->subject('Verify Email Address')
            //             ->line('Click the button below to verify your email address.')
            //             ->action('Verify Email Address', $url);
            //     });
            // }

            app('notificationService')->notifyUsers([$user], 'Welcome to ' . Helper::getCompanyName(), 'Start reading today and enjoy your journey');
            // $user->sendEmailVerificationNotification();

            DB::commit();

            return response()->json([
                'message' => 'Your account has been created successfully.',
                'user' => $user->only(['id', 'name', 'email', 'username']),
                'token' => $token,
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('User registration failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong! Please try again later',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function generateUsername($name)
    {
        $name = strtolower(str_replace(' ', '', $name));
        return $name . rand(1000, 9999);
    }
}
