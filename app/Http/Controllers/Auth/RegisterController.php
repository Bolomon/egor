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
        $phone = preg_replace('/\D+/', '', $request->input('phone'));

        if (strlen($phone) === 11 && $phone[0] === '8') {
            $phone = '7' . substr($phone, 1);
        }

        if (strlen($phone) === 10) {
            $phone = '7' . $phone;
        }

        $request->merge(['phone' => $phone]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|size:11|unique:users,phone',
        ]);

        $token = Str::random(60);

        User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'api_token' => hash('sha256', $token),
        ]);

        return response()->json([
            'token' => $token
        ]);
    }
}
