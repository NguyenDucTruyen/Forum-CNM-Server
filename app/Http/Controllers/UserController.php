<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\User\SignInRequest;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Requests\ForgetPassRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OTP\OtpRequest;
use App\Http\Requests\ResetPassRequest;
use App\Http\Requests\Token\RefreshRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Service\UserService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Bridge\RefreshToken;
use Laravel\Passport\Token;

class UserController extends Controller
{

    //khai báo service
    protected $service;

    //tạo constructor
    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    //register
    public function register(SignInRequest $signInRequest)
    {
        //validation
        //createRequest rex tự lấy dữ liệu trên request và validate sau đó truyền vào param
        $params = $signInRequest->validated();


        //$param chính là dữ liệu được gửi
        //tiến hành gọi request để gọi service
        $result = $this->service->create($params);

        if ($result) {
            return $result;
        }

        return response()->json(
            [
                'message' => 'Registration is unsuccessful'
            ],
            400
        );
    }

    public function sendOTP(OtpRequest $otpRequest)
    {
        $params = $otpRequest->validated();

        $result = $this->service->sendOTP($params);

        return $result;
    }


    //logIn
    public function logIn(LoginRequest $logInRequest)
    {
        $params = $logInRequest->validated();

        $result = $this->service->login($params);

        return $result;
    }

    //verifyEmail
    public function verifyEmail($id, $hash)
    {
        $result = $this->service->verifyEmail($id, $hash);

        return $result;
    }

    //resendVerificationEmail
    public function resendVerificationEmail(LoginRequest $logInRequest)
    {
        $params = $logInRequest->validated();

        $result = $this->service->resendVerificationEmail($params);

        return response()->json($result, 200);
    }

    //test auth TOKEN
    public function getAll()
    {
        try {
            $result = $this->service->getList();

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => $result,
                //show lấy thông tin user hiện tại qua TOKEN
                'user_moment' => auth()->user()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    //forgotPassword
    public function forgotPassword(ForgetPassRequest $forgetPassRequest)
    {
        $params = $forgetPassRequest->validated();

        $result = $this->service->forgotPass($params);

        return $result;
    }

    //resetPassword
    public function resetPassword(ResetPassRequest $resetPassRequest)
    {
        $params = $resetPassRequest->validated();

        $result = $this->service->resetPass($params);

        return $result;
    }

    //logOut
    public function logout(Request $request)
    {
        try {

            // Lấy user ID từ token hiện tại
            $userId = $request->user()->id;

            // Thu hồi tất cả access tokens của user
            Token::where('user_id', $userId)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Logout successful'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Logout Fail: ' . $e->getMessage()
            ], 500);
        }
    }

    //update 
    public function update(UpdateRequest $updateRequest)
    {

        $params = $updateRequest->validated();

        $user = auth()->user();

        $result = $this->service->update($user, $params);

        return $result;
    }

    //detail
    public function detail($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'message' => 'Successful',
                'data' => $user
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found!'
            ], 404);
        }
    }


    public function refreshToken(RefreshRequest $request)
    {
        $params = $request->validated();

        try {
            // Log toàn bộ token để kiểm tra
            Log::info('Received Token: ' . $params['access_token']);

            // Tìm token trong database
            $tokenModel = DB::table('oauth_access_tokens')
                ->where('id', hash('sha256', $params['access_token']))
                ->orWhere('id', $params['access_token'])
                ->first();

            // Nếu không tìm thấy, thử decode token
            if (!$tokenModel) {
                try {
                    $decoded = \Firebase\JWT\JWT::decode(
                        $params['access_token'],
                        new \Firebase\JWT\Key(
                            file_get_contents(storage_path('oauth-public.key')),
                            'RS256'
                        )
                    );

                    // Tìm token theo thông tin từ decoded token
                    $tokenModel = DB::table('oauth_access_tokens')
                        ->where('user_id', $decoded->sub)
                        ->first();
                } catch (\Exception $e) {
                    Log::error('Token decode error: ' . $e->getMessage());
                }
            }

            if (!$tokenModel) {
                return response()->json([
                    "message" => "Token không tồn tại",
                    "debug" => [
                        "token_hash" => hash('sha256', $params['access_token']),
                        "token_length" => strlen($params['access_token'])
                    ]
                ], 404);
            }

            // Lấy người dùng từ token
            $user = User::find($tokenModel->user_id);

            if (!$user) {
                return response()->json([
                    "message" => "Người dùng không tồn tại"
                ], 404);
            }

            // Tạo token mới
            $tokenName = $user->roleName == 'admin' ? 'Admin Access Token' : 'User Access Token';
            $tokenScopes = $user->roleName == 'admin' ? ['role:admin'] : ['role:user'];

            $newToken = $user->createToken($tokenName, $tokenScopes);

            $newAccessToken = $newToken->accessToken;
            $newTokenExpiry = $newToken->token->expires_at;

            // Xóa token cũ
            DB::table('oauth_access_tokens')
                ->where('id', $tokenModel->id)
                ->update(['revoked' => 1]);

            return response()->json([
                "message" => "Refresh token thành công",
                "access_token" => $newAccessToken,
                "expires_in" => $newTokenExpiry
            ], 200);
        } catch (\Exception $e) {
            Log::error('Refresh Token Error: ' . $e->getMessage());

            return response()->json([
                "message" => "Refresh token thất bại",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
