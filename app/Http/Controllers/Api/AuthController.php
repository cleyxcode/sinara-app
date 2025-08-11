<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
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

    // Fungsi untuk mengirim email reset password
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = UserApp::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan dalam sistem'
            ], 404);
        }

        try {
            // Generate token reset password
            $token = Str::random(64);

            // Simpan token ke database (Anda perlu membuat tabel password_resets)
            DB::table('password_resets')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now()
                ]
            );

            // Kirim email reset password
            Mail::send('emails.password-reset', [
                'token' => $token,
                'user' => $user,
                'email' => $request->email
            ], function ($message) use ($request, $user) {
                $message->to($request->email, $user->name);
                $message->subject('Reset Password - Aplikasi Kesehatan');
            });

            return response()->json([
                'success' => true,
                'message' => 'Link reset password telah dikirim ke email Anda'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email reset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Fungsi untuk reset password dengan token
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cek apakah token valid
            $passwordReset = DB::table('password_resets')
                ->where('email', $request->email)
                ->first();

            if (!$passwordReset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token reset password tidak valid'
                ], 400);
            }

            // Cek apakah token cocok
            if (!Hash::check($request->token, $passwordReset->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token reset password tidak valid'
                ], 400);
            }

            // Cek apakah token sudah expired (24 jam)
            if (Carbon::parse($passwordReset->created_at)->addHours(24)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token reset password sudah expired'
                ], 400);
            }

            // Cari user berdasarkan email
            $user = UserApp::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak ditemukan dalam sistem'
                ], 404);
            }

            // Update password user
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Hapus token reset password dari database
            DB::table('password_resets')->where('email', $request->email)->delete();

            // Revoke semua token yang ada (opsional, untuk keamanan)
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Fungsi untuk verifikasi token reset password (opsional)
    public function verifyResetToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan validasi',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $passwordReset = DB::table('password_resets')
                ->where('email', $request->email)
                ->first();

            if (!$passwordReset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid'
                ], 400);
            }

            if (!Hash::check($request->token, $passwordReset->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid'
                ], 400);
            }

            if (Carbon::parse($passwordReset->created_at)->addHours(24)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token sudah expired'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token valid'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}