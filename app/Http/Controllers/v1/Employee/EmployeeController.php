<?php

namespace App\Http\Controllers\v1\Employee;

use App\Models\Employee;
use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * View All Performance Reviews for Feedback
     */
    public function listReviewsForFeedback()
    {
        $reviews = Review::where('is_submitted', 0)->get();
        
        if (!$reviews) {
            return response()->json([
                'success' => false,
                'message' => 'No record found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $reviews
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * Submit feedback for a Performance Review
     */
    public function reviewFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feedback'   => 'required',
            'review_id'    => 'required',
        ]);

        if ($validator->fails()) {
            $response = [];
            foreach ($validator->messages()->toArray() as $key => $value) {
                $obj = new \stdClass();
                $obj->name = $key;
                $obj->message = $value[0];
                array_push($response, $obj);
            }

            return response()->json([
                'success' => false,
                'message' => $response,
                'data'    => 'null',
            ], 422);
        }

        try {
            DB::beginTransaction();

            $review = Review::find($request->review_id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Performance Review not found',
                    'data' => null
                ], 404);
            }

            $review_feedback = $review->update(
                [
                    "feedback" => $request->feedback,
                    "is_submitted" => 1,
                ]
            );

            if ($review_feedback) {

                $employee = Employee::where('id', $review->employee_id)->first();

                $employee_update = $employee->update(
                    [
                        "feedback" => $request->feedback,
                        "reviewed_by" => auth()->user()->firstname . ' ' . auth()->user()->lastname,
                        "is_reviewed" => 1,
                    ]
                );

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Feedback submitted successfully',
                    'data' => $review_feedback,
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error, Performance Review could not be created',
                'data' => null,
            ], 500);
        } catch (\Throwable $th) {

            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ], 500);
        }
    }

}
