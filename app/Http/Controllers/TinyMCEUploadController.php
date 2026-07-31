<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TinyMCEUploadController extends Controller
{
    /**
     * Handle image upload from TinyMCE
     */
    public function uploadImage(Request $request)
    {
        // Validate the upload
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid file. Only images (JPEG, PNG, JPG, GIF) up to 2MB are allowed.'
            ], 400);
        }

        try {
            $file = $request->file('file');
            
            // Generate unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Ensure upload directory exists
            $uploadPath = public_path('upload/questions');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            // Move the file
            $file->move($uploadPath, $filename);
            
            // Return the file URL
            $fileUrl = asset('upload/questions/' . $filename);
            
            return response()->json([
                'location' => $fileUrl
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
