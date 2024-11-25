<?php

namespace App\Service;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

class CategoryService
{
    //khai bao model
    protected $model;

    //tạo constructor, khởi tạo
    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    //create
    public function createCategory($params)
    {
        try {
            $result = $this->model->create($params);
        } catch (Exception $exception) {
            Log::error($exception);
            return response()->json(
                [
                    'message' => 'Create category fail'
                ],
                400
            );
        }

        return response()->json([
            'message' => 'Create category Successful',
            'data' => $result
        ], 200);
    }

    //update
    public function updateCategory($category, $params)
    {
        try {
            Log::info('Params:', $params);

            $result = $category->update($params);

            if ($result) {
                return response()->json([
                    'message' => 'Update successful',
                    'data' => $category->fresh()  // Lấy dữ liệu mới nhất
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
}
