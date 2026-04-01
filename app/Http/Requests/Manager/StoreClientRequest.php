<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isManager();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:30'],
            'national_id' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')->where(fn ($query) => $query->where('country_id', $this->input('country_id'))),
            ],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
