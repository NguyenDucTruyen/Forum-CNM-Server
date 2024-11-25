<?php

namespace App\Http\Controllers;

use App\Http\Requests\Amin\ActiveRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    //like and cancel
    public function activeUser(ActiveRequest $activeRequest)
    {
        $params = $activeRequest->validated();
        try {
            $user = User::findOrFail($params['user_id']);
            // Xử lý User hiện có
            $newStatus = $user->isActive == true ? false : true;
            $user->update(['isActive' => $newStatus]);

            $message = $newStatus == true ? 'Active user successfully' : 'Inactive user  successfully';

            return response()->json([
                'message' => $message,
                'data' => $user->refresh()
            ], 200);
        } catch (ModelNotFoundException $e) {
            Log::error(': ' . $e->getMessage());

            return response()->json([
                'message' => 'FAIL',
                'error' => $e->getMessage() 
            ], 403);
        }
    }
}
