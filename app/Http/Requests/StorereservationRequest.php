<?php

namespace App\Http\Requests;

use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;

class StorereservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'accompany_number' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    $room = Room::find($this->room_id);
                    if ($room && $value > $room->capacity) {
                        $fail("The number of accompanies cannot exceed the room's capacity ({$room->capacity}).");
                    }
                },
            ],
            'stripe_token' => 'required|string',
        ];
    }
}
