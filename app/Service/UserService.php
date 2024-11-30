<?php

namespace App\Service;

//thực hiện các CRUD , gọi MODEL

use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\PasswordResetOtp;
use App\Models\SendOTP;

class UserService
{
    //khai bao model
    protected $model;
    protected $modalPass;

    //tạo constructor, khởi tạo
    public function __construct(User $user, PasswordReset $passwordReset)
    {
        $this->model = $user;
        $this->modalPass = $passwordReset;
    }

    //register
    public function create($params)
    {
        try {
            // Kiểm tra OTP
            $otpRecord = SendOTP::where('email', $params['email'])->first();

            if (!$otpRecord || !Hash::check($params['otp'], $otpRecord->otp)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP không hợp lệ hoặc đã hết hạn'
                ], 400);
            }

            if ($otpRecord->created_at->addMinutes(15)->isPast()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP đã hết hạn'
                ], 400);
            }

            //insert vô db
            $user = $this->model->create($params);

            // Xóa OTP sau khi reset
            $otpRecord->delete();
        } catch (Exception $exception) {
            Log::error($exception);
            return false;
        }

        return response()->json([
            'message' => 'Register Successful. Please login to use.',
            'data'=>$user
        ], 200);
    }

    //sendOPT register
    public function sendOTP($params)
    {
        try {
            // Tạo OTP 6 số
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Lưu OTP vào database
            SendOTP::updateOrCreate(
                ['email' => $params['email']],
                [
                    'email' => $params['email'],
                    'otp' => Hash::make($otp),
                    'created_at' => now()
                ]
            );

            Mail::raw("Mã OTP của bạn là: {$otp}. Mã này có hiệu lực trong 15 phút.", function ($message) use ($params) {
                $message->to($params['email'])->subject('Mã OTP đăng ký tài khoảnkhoản');
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to send OTP link',
                'error' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'OTP link sent to your email'
        ], 200);
    }



    //ham update
    public function update($user, $params)
    {
        try {
            Log::info('Params:', $params);

            $result = $user->update($params);

            if ($result) {
                return response()->json([
                    'message' => 'Update successful',
                    'data' => $user->fresh()  // Lấy dữ liệu mới nhất
                ], 200);
            }

            return response()->json([  // Thêm return
                'message' => 'Update unsuccessful'
            ], 400);
        } catch (Exception $exception) {
            Log::error('Update user error: ' . $exception->getMessage());
            return response()->json([
                'message' => 'Update failed',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    //function login
    public function login($params)
    {
        //check email first
        $user = $this->model->where('email', $params['email'])->first();

        // Thêm log để kiểm tra password trong DB
        Log::info('Password in DB: ' . $user->password);
        Log::info('Password input: ' . $params['password']);
        //check hash password
        $checkPass = Hash::check($params['password'], $user->password);

        Log::info('User attempt login: ' . $params['email']);
        Log::info('User found: ' . ($user ? 'Yes' : 'No'));
        Log::info('Password check: ' . ($checkPass ? 'Pass' : 'Fail'));

        if (!$checkPass) {
            return response()->json([
                'message' => 'Email or Password is incorrect',
                'code' => '404'
            ], 404);
        }
        
        if($user->isActive == false) {
            return response()->json([
                'message' => 'Your account has been deactivated. Please contact support for further assistance.'
            ], 403);
        }


        //create TOKEN by sanctum
        //$token = $user->createToken('user')->plainTextToken;

        //create TOKEN by passport

        // Create token based on user role
        $tokenName = $user->roleName == 'admin' ? 'Admin Access Token' : 'User Access Token';

        $tokenScopes = $user->roleName == 'admin' ? ['role:admin'] : ['role:user'];

        $tokenResult = $user->createToken($tokenName, $tokenScopes);

        $accessToken = $tokenResult->accessToken;

        $tokenExpiry = $tokenResult->token->expires_at;



        return response()->json([
            'message' => 'Login successful',
            //can create TOKEN if you want
            'access_token' => $accessToken,
            'expires_in' => $tokenExpiry
        ], 200);
    }

    //verifyEmail
    public function verifyEmail($id, $hash)
    {
        //check email first
        $user = $this->model->findOrFail($id);

        // Kiểm tra hash verification URL hợp lệ
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'message' => 'URL xác thực không hợp lệ'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email has verified already'
            ], 400);
        }

        // Thực hiện xác thực email
        if ($user->markEmailAsVerified()) {
            return response()->json([
                'message' => 'Verify email successful'
            ], 200);
        }

        return response()->json([
            'message' => 'Verify email unsuccess'
        ], 400);
    }

    //resendVerificationEmail
    public function resendVerificationEmail($params)
    {
        //check email first
        $user = $this->model->where('email', $params['email'])->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email đã được xác thực trước đó'
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Email xác thực đã được gửi lại'
        ], 200);
    }

    //test auth TOKEN
    public function getList()
    {
        return $this->model->orderBy('id', 'desc')->get();
    }



    public function forgotPass($params)
    {
        try {
            //check email first
            $user = $this->model->where('email', $params['email'])->first();

            // Tạo OTP 6 số
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Lưu OTP vào database
            PasswordResetOtp::updateOrCreate(
                ['email' => $params['email']],
                [
                    'email' => $params['email'],
                    'otp' => Hash::make($otp),
                    'created_at' => now()
                ]
            );

            Mail::raw("Mã OTP của bạn là: {$otp}. Mã này có hiệu lực trong 15 phút.", function ($message) use ($params) {
                $message->to($params['email'])->subject('Mã OTP đặt lại mật khẩu');
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to send reset link',
                'error' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Reset password link sent to your email'
        ], 200);
    }


    public function resetPass($params)
    {
        try {
            // Kiểm tra OTP
            $otpRecord = PasswordResetOtp::where('email', $params['email'])->first();

            if (!$otpRecord || !Hash::check($params['otp'], $otpRecord->otp)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP không hợp lệ hoặc đã hết hạn'
                ], 400);
            }

            // Kiểm tra OTP còn hiệu lực (ví dụ: trong vòng 15 phút)
            if ($otpRecord->created_at->addMinutes(15)->isPast()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP đã hết hạn'
                ], 400);
            }

            // Cập nhật mật khẩu
            $user = $this->model->where('email', $params['email'])->first();

            $user->password = $params['password'];

            $user->save();

            // Xóa OTP sau khi reset
            $otpRecord->delete();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to reset password',
                'error' => $e->getMessage()
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully'
        ], 200);
    }
}
