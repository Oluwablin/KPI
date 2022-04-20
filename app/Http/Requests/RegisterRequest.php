<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules =  [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
            'confirmPassword' => 'same:password',
            "role" => "required|string|in:admin,employee"
        ];

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    // public function messages()
    // {
    //     return [
    //         'firstname.required' => 'Firstname is required',
    //         'lastname.required' => 'Lastname is required',
    //         'email.required' => 'Email is required',
    //         'password.required' => 'Password is required',
    //         'role.required' => 'Role is required',
    //         'email.unique' => 'Email already exists',
    //         'password.min' => 'Password length must not be less than 6 characters',
    //     ];
    // }
}
