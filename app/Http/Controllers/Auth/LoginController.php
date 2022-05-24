<?php

namespace App\Http\Controllers\Auth;

use App\Services\BouncerService;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{

    use AuthenticatesUsers;

    /**
     * @var BouncerService
     */
    private $bouncerService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\BouncerService $bouncerService
     */
    public function __construct(BouncerService $bouncerService) {
        $this->bouncerService = $bouncerService;
    }

    /**
     * Get the authenticated User.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $data = [
            'user' => auth()->user()->load(['education', 'skills', 'shops']),
            'billing' => $this->bouncerService->canPass($request)
        ];

        return response()->json($data);
    }

     /**
     * Get a JWT via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid login credential.'], 401);
        }

        $user = $request->user()->load(['education', 'skills', 'shops']);
        $billing = $this->bouncerService->canPass($request);
        return response()->json(compact('token', 'user', 'billing'));
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = auth()->refresh();

        return response()->json(compact('token'));
    }

    /**
     * Log the user out.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $client = new Client();
        $token = auth()->user()->login_platform_token;

        switch (auth()->user()->login_platform){
            case 'facebook':
                $client->delete("https://graph.facebook.com/v10.0/me/permissions", ['form_params' => ['access_token' =>$token]]);
                break;
            case 'google':
                $client->post("https://accounts.google.com/o/oauth2/revoke", ['form_params' => ['token' =>$token]]);
                break;
            default :
                break;
        }

        auth()->user()->login_platform_token = null;
        auth()->user()->save();
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
