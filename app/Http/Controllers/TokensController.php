<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TokensController extends Controller
{
    /**
     * Common validation rules
     *
     * @var array
     */
    protected array $rules = [
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
        'password' => 'required|min:4',
        'device_name' => 'required',
    ];

    /**
     * Validate user credentials from database
     *
     * @param string $email
     * @param string $password
     * @return User
     */
    private function validateUserCredentials($email, $password)
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        return $user;
    }

    /**
     * Validate user and return his token
     *
     * @param LoginRequest $request
     * @return Response
     */
    public function getLoginToken(LoginRequest $request)
    {
        try {
            $request->validate($this->rules);

            $user = $this->validateUserCredentials($request->email, $request->password);

            $token = $user->createToken($request->device_name);

            return response()->json([
                'token_id' => $token->accessToken->id,
                'token' => $token
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Register user and return his token
     *
     * @param LoginRequest $request
     * @return Response
     */
    public function register(LoginRequest $request)
    {
        try {
            /**
             * Add unique email check to validations
             */
            $rules = $this->rules;
            $rules['email'] = array_merge($rules['email'], [('unique:'.User::class)]);

            // Add name validation to common rules
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                ...$rules,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken($request->device_name)->plainTextToken;

            return response()->json([
                'token_id' => $token->accessToken->id,
                'token' => $token
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Undocumented function
     *
     * @param LoginRequest $request
     * @return Response
     */
    public function revokeToken(LoginRequest $request)
    {
        try {
            $request->validate([
                ...$this->rules,
                'token_id' => 'required|string'
            ]);

            $user = $this->validateUserCredentials($request->email, $request->password);

            $user->tokens()->where('id', $request->token_id)->delete();

            return response()->noContent(200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Revoke all user tokens
     *
     * @param LoginRequest $request
     * @return Response
     */
    public function revokeAllTokens(LoginRequest $request)
    {
        try {
            $request->validate($this->rules);

            $user = $this->validateUserCredentials($request->email, $request->password);

            $user->tokens()->delete();

            return response()->noContent(200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }
}
