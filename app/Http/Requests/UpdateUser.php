<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('create users') ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->route('user')->id],
            'password' => ['required', 'string', 'min:8'],
            'image' => ['nullable', 'image', 'max:2048' , 'mimes:jpg,jpeg,png,gif'],
            'role' => ['required', 'in:admin,manager,user,receptionist'],
            'is_active' => ['required', 'boolean'],
            'phone' => ['nullable', 'string', 'max:20'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'in:male,female,other'],   
             "country_id" => ['required' , 'exists:countries,id'],

        ];
    }
}
