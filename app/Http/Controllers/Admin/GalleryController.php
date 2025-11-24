<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
class GalleryController extends Controller
{
    /**
     * Show create album form
     */
    public function create()
    {
        return Inertia::render('Admin/Gallery/Create');
    }

    /**
     * Show edit albums page
     */
    public function edit()
    {
        $albums = Album::latest()->get()->map(function ($album) {
            return [
                'id' => $album->id,
                'album_title' => $album->album_title,
                'album_title_bn' => $album->album_title_bn,
                'album_description' => $album->album_description,
                'album_description_bn' => $album->album_description_bn,
                'album_cover' => $album->album_cover_url,
                'album_images' => $album->album_images_urls,
                'created_at' => $album->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $album->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return Inertia::render('Admin/Gallery/Edit', [
            'albums' => $albums
        ]);
    }

    /**
     * Store a new album
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'album_title' => 'required|string|max:255',
            'album_title_bn' => 'required|string|max:255',
            'album_description' => 'nullable|string',
            'album_description_bn' => 'nullable|string',
            'album_cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'album_images' => 'required|array|min:1',
            'album_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Handle album cover upload
            if ($request->hasFile('album_cover')) {
                $coverPath = $request->file('album_cover')->store('gallery/covers', 'public');
                $coverFileName = basename($coverPath);
            }

            // Handle album images upload
            $imageFiles = [];
            if ($request->hasFile('album_images')) {
                foreach ($request->file('album_images') as $image) {
                    $imagePath = $image->store('gallery/images', 'public');
                    $imageFiles[] = basename($imagePath);
                }
            }

            // Create album
            $album = Album::create([
                'album_title' => $request->album_title,
                'album_title_bn' => $request->album_title_bn,
                'album_description' => $request->album_description,
                'album_description_bn' => $request->album_description_bn,
                'album_cover' => $coverFileName ?? null,
                'album_images' => $imageFiles
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Album created successfully!',
                'album' => $album
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create album: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all albums
     */
    public function getAllAlbums()
    {
        $albums = Album::latest()->get()->map(function ($album) {
            return [
                'id' => $album->id,
                'album_title' => $album->album_title,
                'album_title_bn' => $album->album_title_bn,
                'album_description' => $album->album_description,
                'album_description_bn' => $album->album_description_bn,
                'album_cover' => $album->album_cover_url,
                'album_images' => $album->album_images_urls,
                'created_at' => $album->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $album->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'albums' => $albums
        ]);
    }

    /**
     * Get single album
     */
    public function show($id)
    {
        $album = Album::findOrFail($id);

        return response()->json([
            'success' => true,
            'album' => [
                'id' => $album->id,
                'album_title' => $album->album_title,
                'album_title_bn' => $album->album_title_bn,
                'album_description' => $album->album_description,
                'album_description_bn' => $album->album_description_bn,
                'album_cover' => $album->album_cover_url,
                'album_images' => $album->album_images_urls,
                'created_at' => $album->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $album->updated_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Update album
     */
    public function update(Request $request, $id)
    {
        $album = Album::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'album_title' => 'required|string|max:255',
            'album_title_bn' => 'required|string|max:255',
            'album_description' => 'nullable|string',
            'album_description_bn' => 'nullable|string',
            'album_cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'album_images' => 'nullable|array',
            'album_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'album_title' => $request->album_title,
                'album_title_bn' => $request->album_title_bn,
                'album_description' => $request->album_description,
                'album_description_bn' => $request->album_description_bn,
            ];

            // Handle album cover update
            if ($request->hasFile('album_cover')) {
                // Delete old cover
                if ($album->album_cover) {
                    Storage::disk('public')->delete('gallery/covers/' . $album->album_cover);
                }
                
                $coverPath = $request->file('album_cover')->store('gallery/covers', 'public');
                $data['album_cover'] = basename($coverPath);
            }

            // Handle album images update
            if ($request->hasFile('album_images')) {
                $newImages = [];
                foreach ($request->file('album_images') as $image) {
                    $imagePath = $image->store('gallery/images', 'public');
                    $newImages[] = basename($imagePath);
                }
                
                // Merge with existing images
                $existingImages = $album->album_images;
                $allImages = array_merge($existingImages, $newImages);
                $data['album_images'] = $allImages;
            }

            $album->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Album updated successfully!',
                'album' => $album
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update album: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete album
     */
    public function destroy($id)
    {
        try {
            $album = Album::findOrFail($id);

            // Delete cover image
            if ($album->album_cover) {
                Storage::disk('public')->delete('gallery/covers/' . $album->album_cover);
            }

            // Delete album images
            foreach ($album->album_images as $image) {
                Storage::disk('public')->delete('gallery/images/' . $image);
            }

            $album->delete();

            return response()->json([
                'success' => true,
                'message' => 'Album deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete album: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload images for album
     */
    public function uploadImages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $uploadedImages = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('gallery/images', 'public');
                $uploadedImages[] = [
                    'filename' => basename($path),
                    'url' => asset('storage/gallery/images/' . basename($path))
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully!',
                'images' => $uploadedImages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete specific image from album
     */
    public function deleteImage($albumId, $imageIndex)
    {
        try {
            $album = Album::findOrFail($albumId);
            $images = $album->album_images;

            if (isset($images[$imageIndex])) {
                // Delete file from storage
                Storage::disk('public')->delete('gallery/images/' . $images[$imageIndex]);
                
                // Remove from array
                array_splice($images, $imageIndex, 1);
                
                // Update album
                $album->update(['album_images' => $images]);

                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Image not found!'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image: ' . $e->getMessage()
            ], 500);
        }
    }

        /**
     * API endpoint for gallery albums
     */
    public function getAlbums()
    {
        try {
            $albums = Album::latest()->get()->map(function ($album) {
                return [
                    'id' => $album->id,
                    'album_title' => $album->album_title,
                    'album_title_bn' => $album->album_title_bn,
                    'album_description' => $album->album_description,
                    'album_description_bn' => $album->album_description_bn,
                    'album_cover' => $album->album_cover_url,
                    'album_images' => $album->album_images_urls,
                    'created_at' => $album->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $album->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'albums' => $albums
            ]);
        } catch (\Exception $e) {
            Log::error('Albums fetch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load albums'
            ], 500);
        }
    }
}