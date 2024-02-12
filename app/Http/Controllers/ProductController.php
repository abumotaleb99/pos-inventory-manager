<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Validator;
use File;

class ProductController extends Controller
{
    function ProductPage(){
        return view('pages.dashboard.product-page');
    }

    function ProductList(Request $request)
    {
        $userId=$request->header('id');
        return Product::where('user_id',$userId)->get();
    }

    public function createProduct(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'price' => 'required|numeric',
                'unit' => 'required|string',
                'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
                'category_id' => 'required|exists:categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user_id = $request->header('id');

            $img = $request->file('img');
            $time = time();
            $file_name = $img->getClientOriginalName();
            $img_name = "{$user_id}-{$time}-{$file_name}";
            $img_url = "uploads/{$img_name}";

            $img->move(public_path('uploads'), $img_name);

            $product = Product::create([
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit'),
                'img_url' => $img_url,
                'category_id' => $request->input('category_id'),
                'user_id' => $user_id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product.',
            ], 500);
        }
    }
    function ProductByID(Request $request)
    {
        $user_id=$request->header('id');
        $product_id=$request->input('id');
        return Product::where('id',$product_id)->where('user_id',$user_id)->first();
    }

    function updateProduct(Request $request)
    {
        try {
            $userId = $request->header('id');
            $productId = $request->input('id');
            
            $requestData = $request->only(['name', 'price', 'unit', 'category_id']);
            $hasFile = $request->hasFile('img');
            
            // Handle file upload
            if ($hasFile) {
                $img = $request->file('img');
                $imgName = $userId . '-' . time() . '-' . $img->getClientOriginalName();
                $imgUrl = "uploads/{$imgName}";
                $img->move(public_path('uploads'), $imgName);
                $requestData['img_url'] = $imgUrl;
                
                // Delete old file
                $oldFilePath = $request->input('file_path');
                File::delete($oldFilePath);
            }
            
            // Update product
            $product = Product::where('id', $productId)
                ->where('user_id', $userId)
                ->update($requestData);

            if ($product) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Product updated successfully.',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update product.',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update product.',
            ], 500);
        }
    }

    function deleteProduct(Request $request)
    {
        try {
            // Extract necessary data from the request
            $userId = $request->header('id');
            $productId = $request->input('id');
            $filePath = $request->input('file_path');
            
            // Delete the associated file
            if (!File::delete($filePath)) {
                throw new \Exception('Failed to delete file.');
            }
            
            // Delete the product from the database
            $deleted = Product::where('id', $productId)
                ->where('user_id', $userId)
                ->delete();

            if (!$deleted) {
                throw new \Exception('Failed to delete product.');
            }

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Handle any exceptions and return error response
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }




}
