<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user_apps,email',
            'password' => 'required|string|min:6',
            'alamat' => 'required|string|max:255',
            'umur' => 'required|integer|min:1|max:120',
            'phone' => 'required|string|max:20',
            'fasilitas_kesehatan' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = UserApp::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'alamat' => $request->alamat,
                'umur' => $request->umur,
                'phone' => $request->phone,
                'fasilitas_kesehatan' => $request->fasilitas_kesehatan,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil didaftarkan',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'alamat' => $user->alamat,
                        'umur' => $user->umur,
                        'phone' => $user->phone,
                        'fasilitas_kesehatan' => $user->fasilitas_kesehatan,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = UserApp::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'alamat' => $user->alamat,
                    'umur' => $user->umur,
                    'phone' => $user->phone,
                    'fasilitas_kesehatan' => $user->fasilitas_kesehatan,
                ],
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diambil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'alamat' => $user->alamat,
                    'umur' => $user->umur,
                    'phone' => $user->phone,
                    'fasilitas_kesehatan' => $user->fasilitas_kesehatan,
                ]
            ]
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('user_apps', 'email')->ignore($user->id)
            ],
            'alamat' => 'sometimes|string|max:255',
            'umur' => 'sometimes|integer|min:1|max:120',
            'phone' => 'sometimes|string|max:20',
            'fasilitas_kesehatan' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = array_filter($request->only(['name', 'email', 'alamat', 'umur', 'phone', 'fasilitas_kesehatan']));
            
            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'alamat' => $user->alamat,
                        'umur' => $user->umur,
                        'phone' => $user->phone,
                        'fasilitas_kesehatan' => $user->fasilitas_kesehatan,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pembaruan profil gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password saat ini salah'
            ], 400);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password baru harus berbeda dari password saat ini'
            ], 400);
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diperbarui'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pembaruan password gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}