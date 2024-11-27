<?php

namespace App\Service;

use App\Models\Blog;
use App\Models\Category;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class BlogService
{

    //khai bao model
    protected $model;

    //tạo constructor, khởi tạo
    public function __construct(Blog $blog)
    {
        $this->model = $blog;
    }

    public function createBlog($params)
    {
        try {
            $result = $this->model->create([
                'user_id' => auth()->user()->id,
                'category_id' => $params['category_id'],
                'title' => $params['title'],
                'content' => $params['content'],
                'blogImage' => $params['blogImage']
            ]);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(
                [
                    'message' => 'Create blog fail'
                ],
                400
            );
        }

        return response()->json([
            'message' => 'Create blog Successful',
            'data' => $result
        ], 200);
    }

    public function updateBlog1($blog, $params)
    {
        try {
            $user_blog = User::findOrFail($blog->user_id);

            $user_token = auth()->user();

            if ($user_blog->id != $user_token->id) {
                return response()->json([
                    'message' => 'You are not the owner of this blog'
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

            $result = $blog->update($params->all());

            if ($result) {
                return response()->json([
                    'message' => 'Update successful',
                    'data' => $blog->fresh()  // Lấy dữ liệu mới nhất
                ], 200);
            }

            return response()->json([  // Thêm return
                'message' => 'Update unsuccessful'
            ], 400);
        } catch (Exception $exception) {
            Log::error('Update user error: ' . $exception->getMessage());
            return response()->json([
                'message' => 'Update unsuccessful',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function updateBlog($blog, $params)
    {
        try {
            $user_blog = User::findOrFail($blog->user_id);

            $user_token = auth()->user();

            if ($user_blog->id != $user_token->id) {
                return response()->json([
                    'message' => 'You are not the owner of this blog'
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
            // Cập nhật riêng từng trường
            $blog->category_id = $params->input('category_id', $blog->category_id);
            $blog->title = $params->input('title', $blog->title);
            $blog->content = $params->input('content', $blog->content);
            $blog->blogImage = $params->input('blogImage', $blog->blogImage);

            // Kiểm tra nếu statusBlog không phải là null thì mới cập nhật
            $statusBlog = $params->input('statusBlog');
            if ($statusBlog !== null) {
                $blog->statusBlog = $statusBlog;
            }

            // Lưu lại các thay đổi
            $result = $blog->save();

            if ($result) {
                return response()->json([
                    'message' => 'Update successful',
                    'data' => $blog->fresh()  // Lấy dữ liệu mới nhất
                ], 200);
            }

            return response()->json([
                'message' => 'Update unsuccessful'
            ], 400);
        } catch (Exception $exception) {
            Log::error('Update blog error: ' . $exception->getMessage());
            return response()->json([
                'message' => 'Update unsuccessful',
                'error' => $exception->getMessage()
            ], 500);
        }
    }



    //admin
    public function updateStatusBlog($blog, $params)
    {
        try {
            // Kiểm tra nếu statusBlog không phải là null thì mới cập nhật
            $statusBlog = $params->input('statusBlog');
            if ($statusBlog !== null) {
                $blog->statusBlog = $statusBlog;

                // Lưu lại các thay đổi
                $result = $blog->save();
            }
            if ($result) {
                return response()->json([
                    'message' => 'Update Status successful',
                    'data' => $blog->fresh()  // Lấy dữ liệu mới nhất
                ], 200);
            }

            return response()->json([
                'message' => 'Update Status unsuccessful'
            ], 400);
        } catch (Exception $exception) {
            Log::error('Update blog error: ' . $exception->getMessage());
            return response()->json([
                'message' => 'Update Status unsuccessful',
                'error' => $exception->getMessage()
            ], 500);
        }
    }
}
