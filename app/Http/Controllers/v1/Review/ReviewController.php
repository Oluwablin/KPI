<?php

namespace App\Http\Controllers\v1\Review;

use App\Models\Review;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * View All Performance Reviews
     */
    public function listAllReviews()
    {
        $reviews = Review::all();
        
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
     * Add a Performance Review
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review'   => 'required',
            'employee_id'    => 'required',
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

            $review = Review::create(
                [
                    "review" => $request->review,
                    "employee_id" => $request->employee_id
                ]
            );

            if ($review) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Performance Review created successfully',
                    'data' => $review,
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     * Display a Performance Review
     */
    public function show(Request $request, $id)
    {
        $review = Review::find($id);
        
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'No record found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $review
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     * Update a Performance Review
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review_id'   => 'required',
            'review'   => 'required',
            'employee_id'    => 'required',
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

            $review_update = $review->update(
                [
                    "review" => $request->review,
                    "employee_id" => $request->employee_id
                ]
            );

            if ($review_update) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Performance Review updated successfully',
                    'data' => $review,
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error, Performance Review could not be updated',
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
