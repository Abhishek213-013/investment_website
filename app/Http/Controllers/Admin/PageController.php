<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
class PageController extends Controller
{
    public function index($type)
    {
        // Type 1: Add New Page, Type 2: Update Existing Page
        if ($type == 1) {
            return Inertia::render('Admin/Pages/Create');
        } else {
            $pages = Page::with('sections')->orderBy('created_at', 'desc')->get();
            return Inertia::render('Admin/Pages/EditList', [
                'pages' => $pages
            ]);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page_name' => 'required|string|max:255',
            'page_name_bn' => 'nullable|string|max:255',
            'sections' => 'required|array|min:1',
            'sections.*.heading' => 'nullable|string|max:500',
            'sections.*.heading_bn' => 'nullable|string|max:500',
            'sections.*.description' => 'nullable|string',
            'sections.*.description_bn' => 'nullable|string',
            'sections.*.content' => 'nullable|string',
            'sections.*.content_bn' => 'nullable|string',
            'sections.*.attachments' => 'nullable|array',
            'sections.*.attachments.*' => 'file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,csv|max:10240',
            'sections.*.content_width' => 'nullable|in:25%,50%,75%,100%',
            'sections.*.attachment_width' => 'nullable|in:25%,50%,75%,100%',
            'sections.*.content_allignment' => 'nullable|in:L,R,C',
            'sections.*.attachment_allignment' => 'nullable|in:L,R',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Create page
            $page = Page::create([
                'page_name' => $request->page_name,
                'page_name_bn' => $request->page_name_bn,
                'page_slug' => Page::generateSlug($request->page_name),
            ]);

            // Create sections
            foreach ($request->sections as $index => $sectionData) {
                $attachments = [];

                // Handle file uploads
                if (isset($sectionData['attachments']) && is_array($sectionData['attachments'])) {
                    foreach ($sectionData['attachments'] as $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store('page-attachments', 'public');
                            $attachments[] = $path;
                        }
                    }
                }

                PageSection::create([
                    'page_id' => $page->page_id,
                    'heading' => $sectionData['heading'] ?? null,
                    'heading_bn' => $sectionData['heading_bn'] ?? null,
                    'description' => $sectionData['description'] ?? null,
                    'description_bn' => $sectionData['description_bn'] ?? null,
                    'content' => $sectionData['content'] ?? null,
                    'content_bn' => $sectionData['content_bn'] ?? null,
                    'attachments' => $attachments,
                    'content_width' => $sectionData['content_width'] ?? '100%',
                    'attachment_width' => $sectionData['attachment_width'] ?? '100%',
                    'content_allignment' => $sectionData['content_allignment'] ?? 'L',
                    'attachment_allignment' => $sectionData['attachment_allignment'] ?? 'R',
                    'section_order' => $index,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Page created successfully!',
                'page_id' => $page->page_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Page creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $page = Page::with(['sections' => function($query) {
            $query->orderBy('section_order', 'asc');
        }])->findOrFail($id);
        
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page
        ]);
    }

    public function update(Request $request, $id)
    {
        // Log the incoming request for debugging
        Log::info('Update page request received', [
            'page_id' => $id,
            'data' => $request->except(['sections.*.new_attachments'])
        ]);

        $validator = Validator::make($request->all(), [
            'page_name' => 'required|string|max:255',
            'page_name_bn' => 'nullable|string|max:255',
            'sections' => 'required|array|min:1',
            'sections.*.heading' => 'nullable|string|max:500',
            'sections.*.heading_bn' => 'nullable|string|max:500',
            'sections.*.description' => 'nullable|string',
            'sections.*.description_bn' => 'nullable|string',
            'sections.*.content' => 'nullable|string',
            'sections.*.content_bn' => 'nullable|string',
            'sections.*.new_attachments' => 'nullable|array',
            'sections.*.new_attachments.*' => 'file|mimes:jpg,jpeg,png,webp,pdf,doc,docx,csv|max:10240',
            'sections.*.content_width' => 'nullable|in:25%,50%,75%,100%',
            'sections.*.attachment_width' => 'nullable|in:25%,50%,75%,100%',
            'sections.*.content_allignment' => 'nullable|in:L,R,C',
            'sections.*.attachment_allignment' => 'nullable|in:L,R',
        ]);

        if ($validator->fails()) {
            Log::error('Page update validation failed', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $page = Page::findOrFail($id);
            
            // Update page
            $page->update([
                'page_name' => $request->page_name,
                'page_name_bn' => $request->page_name_bn,
            ]);

            // Get existing section IDs to track which ones to delete
            $existingSectionIds = $page->sections->pluck('page_section_id')->toArray();
            $updatedSectionIds = [];

            // Update or create sections
            foreach ($request->sections as $index => $sectionData) {
                $sectionData = (array) $sectionData;
                
                $attachments = $sectionData['existing_attachments'] ?? [];

                // Handle new file uploads
                if (isset($sectionData['new_attachments']) && is_array($sectionData['new_attachments'])) {
                    foreach ($sectionData['new_attachments'] as $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store('page-attachments', 'public');
                            $attachments[] = $path;
                        }
                    }
                }

                // If section has an ID, update it; otherwise create new
                if (isset($sectionData['page_section_id']) && $sectionData['page_section_id']) {
                    $section = PageSection::find($sectionData['page_section_id']);
                    if ($section) {
                        $section->update([
                            'heading' => $sectionData['heading'] ?? null,
                            'heading_bn' => $sectionData['heading_bn'] ?? null,
                            'description' => $sectionData['description'] ?? null,
                            'description_bn' => $sectionData['description_bn'] ?? null,
                            'content' => $sectionData['content'] ?? null,
                            'content_bn' => $sectionData['content_bn'] ?? null,
                            'attachments' => $attachments,
                            'content_width' => $sectionData['content_width'] ?? '100%',
                            'attachment_width' => $sectionData['attachment_width'] ?? '100%',
                            'content_allignment' => $sectionData['content_allignment'] ?? 'L',
                            'attachment_allignment' => $sectionData['attachment_allignment'] ?? 'R',
                            'section_order' => $index,
                        ]);
                        $updatedSectionIds[] = $sectionData['page_section_id'];
                    }
                } else {
                    $section = PageSection::create([
                        'page_id' => $page->page_id,
                        'heading' => $sectionData['heading'] ?? null,
                        'heading_bn' => $sectionData['heading_bn'] ?? null,
                        'description' => $sectionData['description'] ?? null,
                        'description_bn' => $sectionData['description_bn'] ?? null,
                        'content' => $sectionData['content'] ?? null,
                        'content_bn' => $sectionData['content_bn'] ?? null,
                        'attachments' => $attachments,
                        'content_width' => $sectionData['content_width'] ?? '100%',
                        'attachment_width' => $sectionData['attachment_width'] ?? '100%',
                        'content_allignment' => $sectionData['content_allignment'] ?? 'L',
                        'attachment_allignment' => $sectionData['attachment_allignment'] ?? 'R',
                        'section_order' => $index,
                    ]);
                    $updatedSectionIds[] = $section->page_section_id;
                }
            }

            // Delete sections that were removed
            $sectionsToDelete = array_diff($existingSectionIds, $updatedSectionIds);
            if (!empty($sectionsToDelete)) {
                $sectionsToDelete = PageSection::whereIn('page_section_id', $sectionsToDelete)->get();
                foreach ($sectionsToDelete as $section) {
                    // Delete associated files
                    foreach ($section->attachments as $attachment) {
                        Storage::disk('public')->delete($attachment);
                    }
                    $section->delete();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Page updated successfully!',
                'page_id' => $page->page_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Page update error: ' . $e->getMessage());
            Log::error('Page update stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $page = Page::findOrFail($id);
            
            // Delete associated files
            foreach ($page->sections as $section) {
                foreach ($section->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment);
                }
            }

            $page->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Page deleted successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Page deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete page: ' . $e->getMessage()
            ], 500);
        }
    }

    public function view($slug)
    {
        $page = Page::with(['sections' => function($query) {
            $query->orderBy('section_order', 'asc');
        }])->where('page_slug', $slug)->firstOrFail();
        
        return Inertia::render('PageView', [
            'page' => $page
        ]);
    }

    public function getAllPages()
    {
        $pages = Page::with('sections')->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'pages' => $pages
        ]);
    }

    public function show($id)
    {
        $page = Page::with('sections')->findOrFail($id);
        
        return response()->json([
            'page' => $page
        ]);
    }

    public function deleteAttachment($sectionId, $attachmentIndex)
    {
        try {
            $section = PageSection::findOrFail($sectionId);
            $attachments = $section->attachments;
            
            if (isset($attachments[$attachmentIndex])) {
                // Delete file from storage
                Storage::disk('public')->delete($attachments[$attachmentIndex]);
                
                // Remove from attachments array
                unset($attachments[$attachmentIndex]);
                $attachments = array_values($attachments); // Reindex array
                
                $section->update(['attachments' => $attachments]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Attachment deleted successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Attachment not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Attachment deletion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add this method to handle attachment viewing
    public function getAttachment($filename)
    {
        $path = storage_path('app/public/page-attachments/' . $filename);
        
        if (!file_exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        
        return $response;
    }
}