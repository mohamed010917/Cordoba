<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('floor'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
        ];
    }
}