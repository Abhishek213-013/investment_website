<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class HomeInfoController extends Controller
{
    /**
     * Display the home page management interface
     */
    public function index()
    {
        $homeInfo = Home::getHomeInfo();

        return Inertia::render('Admin/HomeInfo/Index', [
            'homeInfo' => $homeInfo,
            'heroImageUrl' => $homeInfo->hero_image ? $this->getImageUrl($homeInfo->hero_image) : null,
        ]);
    }

    /**
     * Get image URL - handles both storage paths and full URLs
     */
    private function getImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return null;
        }

        // If it's already a full URL, return as is
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // If it's a storage path, generate the URL
        if (strpos($imagePath, 'home-images/') === 0) {
            return Storage::url($imagePath);
        }

        // Default case
        return Storage::url($imagePath);
    }

    /**
     * Get home info data for API
     */
    public function getHomeInfo()
    {
        try {
            $homeInfo = Home::getHomeInfo();

            return response()->json([
                'success' => true,
                'data' => [
                    ...$homeInfo->toArray(),
                    'hero_image_url' => $homeInfo->hero_image ? $this->getImageUrl($homeInfo->hero_image) : null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch home info',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update home info
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hero_title' => 'nullable|string|max:255',
            'hero_title_bn' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_subtitle_bn' => 'nullable|string',
            'hero_button1' => 'nullable|string|max:100',
            'hero_button1_bn' => 'nullable|string|max:100',
            'hero_button2' => 'nullable|string|max:100',
            'hero_button2_bn' => 'nullable|string|max:100',
            'service_title' => 'nullable|string|max:255',
            'service_title_bn' => 'nullable|string|max:255',
            'news_title' => 'nullable|string|max:255',
            'news_title_bn' => 'nullable|string|max:255',
            'faq_title' => 'nullable|string|max:255',
            'faq_title_bn' => 'nullable|string|max:255',
            'cta_button1' => 'nullable|string|max:100',
            'cta_button1_bn' => 'nullable|string|max:100',
            'cta_button2' => 'nullable|string|max:100',
            'cta_button2_bn' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            $homeInfo = Home::saveHomeInfo($data);

            return response()->json([
                'success' => true,
                'message' => 'Home page content updated successfully',
                'data' => [
                    ...$homeInfo->toArray(),
                    'hero_image_url' => $homeInfo->hero_image ? $this->getImageUrl($homeInfo->hero_image) : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update home info: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload hero image via file input
     */
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hero_image_file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'hero_title' => 'nullable|string|max:255',
            'hero_title_bn' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string',
            'hero_subtitle_bn' => 'nullable|string',
            'hero_button1' => 'nullable|string|max:100',
            'hero_button1_bn' => 'nullable|string|max:100',
            'hero_button2' => 'nullable|string|max:100',
            'hero_button2_bn' => 'nullable|string|max:100',
            'service_title' => 'nullable|string|max:255',
            'service_title_bn' => 'nullable|string|max:255',
            'news_title' => 'nullable|string|max:255',
            'news_title_bn' => 'nullable|string|max:255',
            'faq_title' => 'nullable|string|max:255',
            'faq_title_bn' => 'nullable|string|max:255',
            'cta_button1' => 'nullable|string|max:100',
            'cta_button1_bn' => 'nullable|string|max:100',
            'cta_button2' => 'nullable|string|max:100',
            'cta_button2_bn' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid image file or data',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Delete old image if exists
            $oldHomeInfo = Home::first();
            if ($oldHomeInfo && $oldHomeInfo->hero_image) {
                Storage::delete($oldHomeInfo->hero_image);
            }

            // Store new image
            $path = $request->file('hero_image_file')->store('home-images', 'public');
            
            // Prepare data for update
            $data = $request->except(['hero_image_file']);
            $data['hero_image'] = $path;

            // Update home info with new image path and other data
            $homeInfo = Home::saveHomeInfo($data);

            return response()->json([
                'success' => true,
                'message' => 'Home page content and image updated successfully',
                'data' => [
                    ...$homeInfo->toArray(),
                    'hero_image_url' => $this->getImageUrl($homeInfo->hero_image),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete hero image
     */
    public function deleteImage()
    {
        try {
            $homeInfo = Home::first();

            if ($homeInfo && $homeInfo->hero_image) {
                Storage::delete($homeInfo->hero_image);
                $homeInfo->update(['hero_image' => null]);

                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image found to delete'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get home info for frontend
     */
    public function getFrontendHomeInfo()
    {
        try {
            $homeInfo = Home::getHomeInfo();

            return response()->json([
                'success' => true,
                'data' => [
                    'hero_title' => $homeInfo->hero_title,
                    'hero_title_bn' => $homeInfo->hero_title_bn,
                    'hero_subtitle' => $homeInfo->hero_subtitle,
                    'hero_subtitle_bn' => $homeInfo->hero_subtitle_bn,
                    'hero_image' => $homeInfo->hero_image ? $this->getImageUrl($homeInfo->hero_image) : null,
                    'hero_button1' => $homeInfo->hero_button1,
                    'hero_button1_bn' => $homeInfo->hero_button1_bn,
                    'hero_button2' => $homeInfo->hero_button2,
                    'hero_button2_bn' => $homeInfo->hero_button2_bn,
                    'service_title' => $homeInfo->service_title,
                    'service_title_bn' => $homeInfo->service_title_bn,
                    'news_title' => $homeInfo->news_title,
                    'news_title_bn' => $homeInfo->news_title_bn,
                    'faq_title' => $homeInfo->faq_title,
                    'faq_title_bn' => $homeInfo->faq_title_bn,
                    'cta_button1' => $homeInfo->cta_button1,
                    'cta_button1_bn' => $homeInfo->cta_button1_bn,
                    'cta_button2' => $homeInfo->cta_button2,
                    'cta_button2_bn' => $homeInfo->cta_button2_bn,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch home info',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}