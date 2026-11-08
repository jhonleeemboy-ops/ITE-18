<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'mobile_number' => 'required|string',
            'password'      => 'required|string|min:8',
            'purok'         => 'required|integer|between:1,8',
        ]);

        // normalize 0917… / +63917… / 917… → 9171234567
        $mobile = substr(preg_replace('/\D/', '', $data['mobile_number']), -10);

        if (!preg_match('/^9\d{9}$/', $mobile)) {
            throw ValidationException::withMessages([
                'mobile_number' => 'Invalid mobile number.',
            ]);
        }

        if (User::where('mobile_number', $mobile)->exists()) {
            throw ValidationException::withMessages([
                'mobile_number' => 'This mobile number is already registered.',
            ]);
        }

        $household = Household::create([
            'head_name' => $data['name'],
            'purok'     => $data['purok'],
        ]);

        $user = User::create([
            'household_id'  => $household->id,
            'name'          => $data['name'],
            'mobile_number' => $mobile,
            'password'      => $data['password'], // hashed by the cast
            'role'          => 'resident',
        ]);

        return response()->json([
            'user'  => $user->load('household'),
            'token' => $user->createToken('web')->plainTextToken,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'password'      => 'required|string',
            'mobile_number' => 'required_without:email|nullable|string',
            'email'         => 'required_without:mobile_number|nullable|email',
        ]);

        // residents sign in with mobile, staff with email
        if ($request->filled('email')) {
            $user = User::where('email', $request->email)->first();
        } else {
            $mobile = substr(preg_replace('/\D/', '', $request->mobile_number), -10);
            $user = User::where('mobile_number', $mobile)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'The mobile number/email or password is incorrect.',
            ]);
        }

        return response()->json([
            'user'  => $user->load('household'),
            'token' => $user->createToken('web')->plainTextToken,
        ]);
    }
}