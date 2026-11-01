<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'address' => ['nullable', 'string', 'max:255'],
            
            'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::default()],
            'terms' => ['accepted'],
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'email' => strtolower($input['email']),
            'address' => $input['address'] ?? null,
            'password' => $input['password'],
        ]);
    }
}