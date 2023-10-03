<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\TeacherRegisterRequest;
use App\Models\Teacher;
use App\Services\TeacherService\TeacherLoginService\TeacherLoginService;
use App\Services\TeacherService\TeacherRegisterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class TeacherAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:teacher', ['except' => ['login', 'register', 'verify']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {

        return (new TeacherLoginService())->login($request);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(TeacherRegisterRequest $request)
    {

        return (new TeacherRegisterService())->register(($request));
    }

    public function verify($token)
    {
        $teacher = Teacher::whereRememberToken($token)->first();
        if (!$teacher) {
            return response()->json(['message' => 'The Token is Invalid']);
        }
        $teacher->remember_token = null;
        $teacher->email_verified_at = now();
        $teacher->save();
        return response()->json(['message' => 'Your Account has been verified']);
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('teacher')->logout();
        return response()->json(['message' => 'Teacher successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth('teacher')->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth('teacher')->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth('teacher')->user()
        ]);
    }
}