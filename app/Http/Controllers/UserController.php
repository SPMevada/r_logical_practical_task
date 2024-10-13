<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if(Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        return  view('users.login');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('user.index');
    }
    
    public function register()
    {
        return  view('users.index');
    }

    public function store(UserRequest $request)
    {
        try{
            User::create([
                'name' => !empty($request->name) ? $request->name : '',
                'email' => !empty($request->email) ? $request->email : '',
                'password' => !empty($request->password) ? Hash::make($request->password) : ''
            ]);
        } catch (\Exception $e) {
            return response()->json($this->ajaxResponse(false,[],$e->getMessage()),200);
        }
        return response()->json($this->ajaxResponse(true,[],'User registed.'),200);
    }

    public function ajaxResponse($status,$data,$message) {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
    }

    public function login(LoginRequest $request)
    {
        $credintials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if(Auth::attempt($credintials)) {
            return response()->json($this->ajaxResponse(true,[],'loged in succeessfully.'),200);   
        }
        return response()->json($this->ajaxResponse(true,[],'Something went wrong.'),200);
    }

    public function dashboard()
    {
        return view('dashboard');
    }
}
