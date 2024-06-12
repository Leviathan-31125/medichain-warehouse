<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserAuthController extends Controller
{
    public function register(Request $request){
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);
        $token = $user->createToken('API Token')->accessToken;

        return response([ 'user' => $user, 'token' => $token]);
    }

    public function login(Request $request){
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Incorrect Details. 
            Please try again']);
        }

        $token = auth()->user()->createToken('API Token')->accessToken;

        Session::put('accessToken', $token);
        return response(['user' => auth()->user(), 'token' => $token]);
    }

    public function details(){
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            return response()->json(['status' => 200, 'data' => $user]);
        }

        return response()->json(['status' => 400, 'message' => 'SSO Failed']);
    }

    public function getSession(){
        $session = $this->getSessionInternal();
        return response()->json(['data' => $session]);
    }

    public function logOut (Request $request) {
        $request->user()->token()->revoke();
        $request->session()->flush();
        return response()->json([ 'message' => 'Successfully logged out' ]);
    }

    private function getSessionInternal () {
        return Session::get('accessToken');
    }
}