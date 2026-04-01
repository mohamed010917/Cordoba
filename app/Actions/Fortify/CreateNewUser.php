<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => $this->passwordRules(),
            'phone' => ['nullable', 'string', 'max:30'],
            'national_id' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'country_id' => ['required', 'exists:countries,id'],
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')->where(fn ($query) => $query->where('country_id', $input['country_id'])),
            ],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ])->validate();

        $imagePath = null;

        if (! empty($input['image'])) {
            $imagePath = $input['image']->store('users', 'public');
        }

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'phone' => $input['phone'] ?? null,
            'national_id' => $input['national_id'] ?? null,
            'gender' => $input['gender'] ?? null,
            'country_id' => $input['country_id'] ?? null,
            'city_id' => $input['city_id'] ?? null,
            'image' => $imagePath,
            'role' => 'user',
        ]);

        Role::findOrCreate('user', 'web');
        $user->assignRole('user');

        return $user;
    }
}
