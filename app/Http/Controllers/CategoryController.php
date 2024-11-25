<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\AddRequest;
use App\Models\Category;
use App\Service\CategoryService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $service;

    //tạo constructor
    public function __construct(CategoryService $categoryService)
    {
        $this->service = $categoryService;
    }

    //create
    public function createCategory(AddRequest $addRequest)
    {
        $params = $addRequest->validated();

        $result = $this->service->createCategory($params);

        return $result;
    }

    //update
    public function updateCategory(AddRequest $addRequest, $id)
    {
        $params = $addRequest->validated();

        try {
            $category = Category::findOrFail($id);

            $result = $this->service->updateCategory($category, $params);
            return $result;
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Category not found!'
            ], 404);
        }
    }

    public function listCategory()
    {
        try {
            $result = Category::orderBy('id', 'desc')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => $result,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    //delete
    /**
     * Xóa mềm blog
     */
    public function destroyCategory($id)
    {
        try {
            $category = Category::findOrFail($id);

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category has deleted'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Category not found!'
            ], 404);
        }
    }

    //xóa thật $blog->forceDelete();
    //khôi phục $blog->restore();
    public function restore($id)
    {
        try {
            $category = Category::findOrFail($id);

            $category->restore();

            return response()->json([
                'success' => true,
                'message' => 'Category has deleted'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Category not found!'
            ], 404);
        }
    }
}
