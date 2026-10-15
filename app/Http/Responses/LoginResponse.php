<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        $path = match ($user->role) {
            'ADMIN' => '/admin',
            default => '/products',
        };

        return redirect()->intended($path);
    }
}