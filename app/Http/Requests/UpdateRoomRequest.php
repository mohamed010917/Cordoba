<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('room'));
    }

    public function rules(): array
    {
        $user = $this->user();
        $room = $this->route('room');

        $floorRule = Rule::exists('floors', 'id');
        if ($user->hasRole('manager')) {
            $floorRule = Rule::exists('floors', 'id')
                ->where(fn ($q) => $q->where('manger_id', $user->id));
        }

        return [
            'number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rooms', 'number')->ignore($room->id),
            ],
            'capacity' => ['required', 'integer', 'min:1'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'floor_id' => ['required', 'integer', $floorRule],

            'manager_id' => [
                Rule::requiredIf($user->hasRole('admin')),
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'manager')),
            ],
        ];
    }
}
