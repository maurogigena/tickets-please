<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Models\User;
use App\Permissions\V1\Abilities;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponses; 

    /**
     * Login
     * 
     * Authenticates the user and returns the user's API Token.
     * 
     * @unauthenticated
     * @group Authentication
     * @response 200
     * {
    "data": {
        "token": "{YOUR_AUTH_KEY}"
    },
    "message": "Authenticated",
    "status": 200
    }
     */

    public function login(LoginUserRequest $request)
    {
        $this->ensureIsNotRateLimited($request);

        // $request->validate($request->all()); <- not necesary because now I'm using LoginUserRequest

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid Credentials.', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated', 
            [
                'token' => $user->createToken(
                'API token for ' . $user->email, 
                Abilities::getAbilities($user),
                now()->addMonth())->plainTextToken    
                // however, the expiration was modified in sanctum.php by default (expiration => 60*24*30)
            ]
        );

        RateLimiter::clear($this->throttleKey($request));
    }

    /**
     * Logout
     * 
     * Signs out the user and destroy's the API Token. 
     * 
     * @group Authentication
     * @response 200 {}
     */
    public function logout(Request $request) // revoke the token
    {
        // $request->user()->tokens()->delete(); <- not a good practice in this case

        $request->user()->currentAccessToken()->delete(); // deletes the token of the current active session. when the user ends the session, the token will expire 

        return $this->ok('');
    }

    // SECURITY - RATE LIMITER
    protected function ensureIsNotRateLimited(Request $request)
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            throw ValidationException::withMessages([
                'email' => ['Too many attempts. Try again in ' . RateLimiter::availableIn($this->throttleKey($request)) . ' segundos.'],
            ]);
        }

        RateLimiter::hit($this->throttleKey($request), 60);
    }

    protected function throttleKey(Request $request)
    {
        return Str::transliterate(Str::lower($request->input('email')));
    }

    // public function register() {
    //     return $this->ok('register');
    // }
}
