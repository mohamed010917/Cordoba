<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Room::class);
    }

    public function rules(): array
    {
        $user = $this->user();

        $floorRule = Rule::exists('floors', 'id');
        if ($user->hasRole('manager')) {
            // floors table uses "manger_id" in your project
            $floorRule = Rule::exists('floors', 'id')
                ->where(fn ($q) => $q->where('manger_id', $user->id));
        }

        return [
            'number' => ['required', 'string', 'max:50', Rule::unique('rooms', 'number')],
            'capacity' => ['required', 'integer', 'min:1'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'floor_id' => ['required', 'integer', $floorRule],

            // admin can assign manually; manager cannot pass this
            'manager_id' => [
                Rule::requiredIf($user->hasRole('admin')),
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($q) => $q->where('role', 'manager')),
            ],
        ];
    }
}
