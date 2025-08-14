<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
        ]);

        $token = Str::random(60);

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'api_token' => hash('sha256', $token),
        ]);

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
