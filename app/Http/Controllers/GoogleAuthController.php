<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    // Phương thức đăng nhập thông qua Google access_token
    public function loginWithGoogleToken(Request $request)
    {
        try {
            // Kiểm tra access_token đã được gửi lên chưa
            $accessToken = $request->input('access_token');
            if (!$accessToken) {
                return response()->json(['error' => 'Access token is required'], 400);
            }

            // Lấy thông tin người dùng từ Google bằng cách sử dụng access_token
            $googleUser = Socialite::driver('google')->userFromToken($accessToken);

            $user = User::where('email', $googleUser->getEmail())->first();
            if ($user) {
                return response()->json(['error' => 'Email has already been registered. Please try using a different email address.'], 404);
            }

            // Kiểm tra xem người dùng đã tồn tại trong database hay chưa
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if (!$user) {
                // Nếu người dùng chưa có trong DB, tạo mới
                $user = User::create([
                    'lastName' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'profileImage' => $googleUser->getAvatar(),
                ]);
            }
            
            if($user->isActive == false) {
                return response()->json([
                    'message' => 'Your account has been deactivated. Please contact support for further assistance.'
                ], 403);
            }

            // Tạo token cho người dùng khi đăng nhập thành công
            $tokenResult = $user->createToken('Personal Access Token', ['role:user']);
            $accessToken = $tokenResult->accessToken;
            $tokenExpiry = $tokenResult->token->expires_at;

            // Trả về thông tin người dùng và token
            return response()->json([
                'access_token' => $accessToken,
                'expires_in' => $tokenExpiry,
                'token_type' => 'Bearer',
            ]);
        } catch (\Throwable $e) {
            Log::error('Google Authentication Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Authentication failed.'], 500);
        }
    }
}
