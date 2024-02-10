<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Validator;
use Exception;

class CategoryController extends Controller
{
   function categoryPage(){
        return view('pages.dashboard.category-page');
    }

    public function createCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category creation failed due to validation errors.',
                    'errors' => $validator->errors(),
                ], 422);
            }
    
            $category = Category::create([
                'name' => $request->input('name'),
                'user_id' => $request->header('id'),
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully',
                'category' => $category,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category creation failed.',
            ], 422);
        }
    }

    public function categoryList(Request $request)
    {
        $userId = $request->header('id');
        $categories = Category::where('user_id', $userId)->get();

        return $categories;
    }

    public function deleteCategory(Request $request)
    {
        try {
            $categoryId = $request->input('id');
            $userId = $request->header('id');

            $deleted = Category::where('id', $categoryId)->where('user_id', $userId)->delete();

            if (!$deleted) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not found.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete category.',
            ], 500);
        }
    }

    function getCategoryById(Request $request){
        $categoryId = $request->input('id');
        $userId = $request->header('id');
        return Category::where('id', $categoryId)->where('user_id', $userId)->first();
    }

    public function updateCategory(Request $request)
    {
        try {
            $categoryId = $request->input('id');
            $userId = $request->header('id');
            
            $updated = Category::where('id', $categoryId)->where('user_id', $userId)->update([
                'name' => $request->input('name'),
            ]);

            if ($updated) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Category updated successfully.',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not found or unauthorized to update.',
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category.',
            ], 500);
        }
    }

}
