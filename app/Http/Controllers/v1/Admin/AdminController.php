<?php

namespace App\Http\Controllers\v1\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * Fetch all Admins
     */
    public function listAllAdmins()
    {
        $admins = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'Admin');
            }
        )->get();

        if (!$admins) {
            return response()->json([
                'success' => false,
                'message' => 'No record found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $admins
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * Fetch all Employees
     */
    public function listAllEmployees()
    {
        $employees = User::whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'Employee');
            }
        )->get();

        if (!$employees) {
            return response()->json([
                'success' => false,
                'message' => 'No record found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $employees
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * Add new Employee
     */
    public function store(Request $request)
    {
        $validate = $this->validateEmployee($request);
        /**
         * if validation fails
         */
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validate->errors()->all(),
                'data' => null
            ], 422);
        }
        try {
            DB::beginTransaction();

            $role = $request->role;
            $user = User::create(
                [
                    "firstname" => $request->firstname,
                    "lastname" => $request->lastname,
                    "email" => $request->email,
                    "password" =>  Hash::make($request->first_name . $request->last_name),
                    "role" => $role,
                    'is_verified' => 1,
                ]
            );

            if ($user) {
                $employee = Employee::create(
                    [
                        "firstname" => $user->firstname,
                        "lastname" => $user->lastname,
                        "user_id" => $user->id,
                    ]
                );

                $permissions = config('roles.models.permission')::all();
                if (isset($role) && $role === "admin") {
                    $userRole = User::isAdmin;
                    if ($userRole) {
                        $user->attachRole($userRole);
                        $user->syncPermissions($permissions);
                    }
                } else {
                    $userRole = User::isEmployee;
                    if ($userRole) {
                        $user->attachRole($userRole);
                    }
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Employee created Successfully!',
                    'data' => [$user, $employee]
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error, Employee could not be created',
                'data' => null,
            ], 500);
        } catch (\Throwable $th) {

            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => 'null'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * View an Employee
     */
    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User was not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => null,
                'data' => $user
            ]);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * Assign an Employee for Performance Review
     */
    public function assignEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
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

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User was not found',
                    'data' => null
                ], 404);
            }

            $employee = Employee::where('user_id', $user->id)->first();

            if ($employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee cannot review themselves, choose another employee',
                    'data' => null
                ], 404);
            }

            $review_employee = Employee::where('id', $request->employee_id)->first();

            if (!$review_employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee was not found',
                    'data' => null
                ], 404);
            }

            $review_employee->update([
                'assignee' => $user->firstname . ' ' . $user->lastname
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Employee has been assigned to participate in review',
                'data' => $user,
            ], 200);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
                'data' => null
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'firstname'   => 'required|string|max:255',
            'lastname'    => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email,' . $request->user_id,
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
            $user->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
            ]);

            $user = User::where('id', $request->user_id)->first();
            $role = $request->role;
            if ($user) {
                $employee = Employee::where('user_id', $request->user_id)->first();
                if ($employee) {
                    $employee->update(
                        [
                            "firstname" => $user->firstname,
                            "lastname" => $user->lastname,
                            "user_id" => $user->id,
                        ]
                    );
                }

                $permissions = config('roles.models.permission')::all();
                if (isset($role) && $role === "admin") {
                    $userRole = User::isAdmin;
                    if ($userRole) {
                        $user->roles()->detach();
                        $user->attachRole($userRole);
                        $user->detachPermissions($permissions);
                        $user->syncPermissions($permissions);
                    }
                } else {
                    $userRole = User::isEmployee;
                    if ($userRole) {
                        $user->roles()->detach();
                        $user->attachRole($userRole);
                    }
                }

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Employee has been updated',
                    'data' => $user,
                ], 200);
            }
            return response()->json([
                'success' => false,
                'message' => 'Error, Employee could not be updated',
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found',
                'data' => null
            ], 404);
        }
        $destroy_user = $user->destroy($user->id);

        if ($destroy_user) {
            $user->update([
                'is_active' => false
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee has been deleted',
                'data' => null,
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Error, Employee could not be deleted',
            'data' => null,
        ], 500);
    }

    /**
     * validate Employee creation
     */
    public function validateEmployee(Request $request)
    {
        $rules = [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            "role" => "required|string|in:admin,employee"
        ];

        $validateEmployee = Validator::make($request->all(), $rules);
        return $validateEmployee;
    }
}
