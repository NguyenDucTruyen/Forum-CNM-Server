<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Kiểm tra xem đã có admin user chưa
        $adminExists = User::where('roleName', 'admin')->exists();

        if (!$adminExists) {
            // Tạo admin user nếu chưa có
            $admin = User::create([
                'lastName' => 'admin',
                'email' => 'nmdadmin@gmail.com',
                'password' => '123456789', // Nên thay đổi mật khẩu này
                'roleName' => 'admin',
                'email_verified_at' => Carbon::now(),
            ]);
        } 
    }
}
