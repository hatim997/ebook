<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Biodata;
use App\Models\Course;
use App\Models\Review;
use App\Models\Subcourse;
use App\Models\UserFavourite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        try {
            $popularNovels = Course::with('reviews')
                ->select('courses.*')
                ->addSelect([
                    'review_count' => function ($query) {
                        $query->selectRaw('COUNT(*)')
                            ->from('reviews')
                            ->whereColumn('reviews.course_id', 'courses.id');
                    },
                    'adjusted_avg_rating' => function ($query) {
                        $query->selectRaw('AVG(COALESCE(rating, 0))')
                            ->from('reviews')
                            ->whereColumn('reviews.course_id', 'courses.id');
                    }
                ])
                ->when($request->search, function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                })
                ->orderByDesc('review_count')
                ->orderByDesc('adjusted_avg_rating')
                ->paginate(8);

            $newNovels = Course::latest()
                ->when($request->search, function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                })
                ->take(8)
                ->get();

            return response()->json([
                'popular_novels' => $popularNovels,
                'new_novels' => $newNovels
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('API Home failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


public function popularNovels(Request $request)
    {
        try {
            $popularNovels = Course::with('reviews')
                ->select('courses.*')
                ->addSelect([
                    'review_count' => function ($query) {
                        $query->selectRaw('COUNT(*)')
                            ->from('reviews')
                            ->whereColumn('reviews.course_id', 'courses.id');
                    },
                    'adjusted_avg_rating' => function ($query) {
                        $query->selectRaw('AVG(COALESCE(rating, 0))')
                            ->from('reviews')
                            ->whereColumn('reviews.course_id', 'courses.id');
                    }
                ])
                ->when($request->search, function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                })
                ->orderByDesc('review_count')
                ->orderByDesc('adjusted_avg_rating')
                ->paginate(8);

            

            return response()->json([
                'popular_novels' => $popularNovels,
            
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('API Home failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


 public function newNovels(Request $request)
    {
        try {
           

            $newNovels = Course::with('subcourses')->latest()
                ->when($request->search, function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                })
                ->take(8)
                ->get();

            return response()->json([
               
                'new_novels' => $newNovels
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('API Home failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }





    public function novels(Request $request)
    {
        try {
            $courses = Course::with('reviews','subcourses')
                ->select('courses.*')
                ->addSelect([
                    'review_count' => function ($query) {
                        $query->selectRaw('COUNT(*)')
                            ->from('reviews')
                            ->whereColumn('reviews.course_id', 'courses.id');
                    },
                    'adjusted_avg_rating' => function ($query) {
                        $query->selectRaw('AVG(COALESCE(rating, 0))')
                            ->from('reviews')
                            ->whereColumn('reviews.course_id', 'courses.id');
                    }
                ])
                ->when($request->search, function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%');
                })
                ->orderByDesc('review_count')
                ->orderByDesc('adjusted_avg_rating')
                ->paginate(12);

            return response()->json(['courses' => $courses], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::error('API Novels failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function myFavourites()
    {
        try {
            $userFavourites = UserFavourite::with('course.reviews')
                ->where('user_id', auth()->id())
                ->paginate(12);

            return response()->json(['favourites' => $userFavourites], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('API Favourites failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function newEpisodes()
    {
        try {
            $biodatas = Biodata::with('course','subcourse')
                ->where('is_new', 1)
                ->orderByDesc('id')
                ->limit(32)
                ->get();

            return response()->json(['new_episodes' => $biodatas], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('API Episodes failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function novelDetails($id)
    {
        try {
            $course = Course::with('reviews.user.profile', 'subcourses')->findOrFail($id);

            $relatedCourses = Course::where('id', '!=', $course->id)
                ->inRandomOrder()
                ->limit(5)
                ->get();

            return response()->json([
                'course' => $course,
                'related' => $relatedCourses
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('API Novel Details failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function subcourseDetails($id)
    {
        try {
            $subcourse = Subcourse::with('course', 'biodatas')->findOrFail($id);
            $biodatas = Biodata::where('sub_course_id', $id)
                ->orderByRaw('CAST(position AS UNSIGNED) ASC')
                ->get();

            $relatedCourses = Course::where('id', '!=', $subcourse->course->id)
                ->inRandomOrder()
                ->limit(5)
                ->get();

            return response()->json([
                'subcourse' => $subcourse,
                'biodatas' => $biodatas,
                'related_courses' => $relatedCourses
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('API Subcourse Details failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function readNovel($id)
    {
        try {
            $file = Biodata::with('subcourse')->findOrFail($id);
            return response()->json(['file' => $file], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('API Read Novel failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeReview(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|between:1,5',
            'review' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    
        try {
            $review = new Review();
            $review->user_id = auth()->id();
            $review->course_id = $id;
            $review->rating = $request->rating;
            $review->review = $request->review;
            $review->save();
    
            return response()->json([
                'message' => 'Review submitted successfully',
                'review' => $review
            ], Response::HTTP_CREATED);
    
        } catch (\Throwable $th) {
            Log::error('Review submission failed', ['error' => $th->getMessage()]);
            return response()->json([
                'message' => 'Something went wrong, please try again later.',
                'error' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function addFavourite($id)
    {
        try {
            $course = Course::findOrFail($id);
            $userFavourite = UserFavourite::where('course_id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if ($userFavourite) {
                $userFavourite->delete();
                return response()->json(['message' => 'Removed from favourites'], Response::HTTP_OK);
            } else {
                $new = new UserFavourite();
                $new->user_id = auth()->id();
                $new->course_id = $id;
                $new->save();
                return response()->json(['message' => 'Added to favourites'], Response::HTTP_OK);
            }

        } catch (\Throwable $th) {
            Log::error('API Add to Favourite failed', ['error' => $th->getMessage()]);
            return response()->json(['message' => 'Something went wrong!'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
