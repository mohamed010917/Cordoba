<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreReceptionistRequest extends FormRequest{
    public function authorize(): bool
    {
        return Auth::check() && auth()->user()->hasRole('manager');
    }

    public function rules(): array 
    {
        return [

            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'national_id' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
    
}