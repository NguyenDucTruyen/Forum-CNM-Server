<?php

namespace App\Http\Controllers;

use App\Http\Requests\Blog\AddRequest;
use App\Http\Requests\StatusRequest;
use App\Models\Blog;
use App\Models\Category;
use App\Models\User;
use App\Service\BlogService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    protected $service;

    //tạo constructor
    public function __construct(BlogService $blogService)
    {
        $this->service = $blogService;
    }

    //add
    public function createBlog(AddRequest $addRequest)
    {
        $params = $addRequest->validated();

        $result = $this->service->createBlog($params);

        return $result;
    }

    //update
    //update
    public function updateBlog(Request $request, $id)
    {
        $params = $request;

        try {
            $blog = Blog::findOrFail($id);

            $result = $this->service->updateBlog($blog, $params);
            return $result;
        } catch (ModelNotFoundException $e) {
            Log::error($e);
            return response()->json([
                'message' => 'Blog not found!'
            ], 404);
        }
    }




    //delete
    /**
     * Xóa mềm blog
     */
    public function destroyBlog($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            $user_blog = User::findOrFail($blog->user_id);

            $user_token = auth()->user();

            if ($user_token->role != 'admin' && $user_blog->id != $user_token->id) {
                return response()->json([
                    'message' => 'You are not permission of this blog'
                ], 403);
            }
        } catch (ModelNotFoundException $e) {
            Log::error(': ' . $e->getMessage());

            return response()->json([
                'message' => 'You are not the owner of this blog',
                'error' => $e->getMessage()
            ], 403);
        } catch (\Exception $e) {
            // Xử lý lỗi khác
            Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }

        try {
            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog has deleted'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Blog not found!'
            ], 404);
        }
    }


    public function listBlog()
    {
        try {
            $result = Blog::orderBy('id', 'desc')->get();

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

    //get by id category
    public function listBlogCategory($id)
    {
        try {
            $category = Category::findOrFail($id);

            $blogs = $category->blogs;

            return response()->json([
                'message' => 'success',
                'data' => $blogs
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Category not found!'
            ], 404);
        }
    }

    //get by id User
    public function listBlogUser($id)
    {
        try {
            $user = User::findOrFail($id);

            $blogs = $user->blogs;

            return response()->json([
                'message' => 'success',
                'date' => $blogs
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found!'
            ], 404);
        }
    }


    //listBlogUserDeleted
    public function listBlogUserDeleted()
    {
        try {
            $user = auth()->user();

            // Query base để lấy các blog đã xóa
            $query = Blog::onlyTrashed();

            // Nếu không phải admin, chỉ lấy blog của user đó
            if ($user->role != 'admin') {
                $query->where('user_id', $user->id);
            }

            // Thêm sắp xếp
            $query->orderBy('deleted_at', 'desc');

            // Thực thi query để lấy kết quả
            $deletedBlogs = $query->get(); // Thêm dòng này để thực thi query

            // Log để debug
            Log::info('Deleted blogs query:', [
                'user_id' => $user->id,
                'role' => $user->role,
                'count' => $deletedBlogs->count(),
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Retrieved deleted blogs successfully',
                'data' => $deletedBlogs
            ], 200);
        } catch (Exception $e) {
            Log::error('Error retrieving deleted blogs: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve deleted blogs',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }
}
