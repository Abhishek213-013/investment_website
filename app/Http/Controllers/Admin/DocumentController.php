<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocType;
use App\Models\DocInfo;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Get all document types
     */
    public function getTypes(Request $request)
    {
        $types = DocType::with(['children', 'parent'])
            ->orderBy('parent_id')
            ->orderBy('type_name')
            ->get();

        return response()->json([
            'types' => $types
        ]);
    }

    /**
     * Store a new document type
     */
    public function storeType(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
            'type_name_bn' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:doc_type,id'
        ]);

        $type = DocType::create([
            'type_name' => $request->type_name,
            'type_name_bn' => $request->type_name_bn,
            'parent_id' => $request->parent_id
        ]);

        return response()->json([
            'message' => 'Document type created successfully',
            'type' => $type
        ]);
    }

    /**
     * Update a document type
     */
    public function updateType(Request $request, $id)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
            'type_name_bn' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:doc_type,id'
        ]);

        $type = DocType::findOrFail($id);
        $type->update([
            'type_name' => $request->type_name,
            'type_name_bn' => $request->type_name_bn,
            'parent_id' => $request->parent_id
        ]);

        return response()->json([
            'message' => 'Document type updated successfully',
            'type' => $type
        ]);
    }

    /**
     * Delete a document type
     */
    public function destroyType($id)
    {
        $type = DocType::findOrFail($id);
        
        // Check if type has any documents
        if ($type->documents()->exists()) {
            return response()->json([
                'error' => 'Cannot delete document type that has documents associated with it.'
            ], 422);
        }

        // Check if type has children
        if ($type->children()->exists()) {
            return response()->json([
                'error' => 'Cannot delete document type that has sub-types.'
            ], 422);
        }

        $type->delete();

        return response()->json([
            'message' => 'Document type deleted successfully'
        ]);
    }

    /**
     * Get fields for a specific document type
     */
    public function getFields($typeId)
    {
        $fields = DocInfo::where('doc_type_id', $typeId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'fields' => $fields
        ]);
    }

    /**
     * Store a new document field
     */
    public function storeField(Request $request)
    {
        $request->validate([
            'doc_type_id' => 'required|exists:doc_type,id',
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'attachments' => 'nullable|string',
            'content_text' => 'nullable|string',
            'content_bn' => 'nullable|string'
        ]);

        $field = DocInfo::create([
            'doc_type_id' => $request->doc_type_id,
            'title' => $request->title,
            'title_bn' => $request->title_bn,
            'description' => $request->description,
            'description_bn' => $request->description_bn,
            'attachments' => $request->attachments,
            'content' => $request->content_text,
            'content_bn' => $request->content_bn
        ]);

        return response()->json([
            'message' => 'Document field created successfully',
            'field' => $field
        ]);
    }

    /**
     * Update a document field
     */
    public function updateField(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'attachments' => 'nullable|string',
            'content_text' => 'nullable|string',
            'content_bn' => 'nullable|string'
        ]);

        $field = DocInfo::findOrFail($id);
        $field->update([
            'title' => $request->title,
            'title_bn' => $request->title_bn,
            'description' => $request->description,
            'description_bn' => $request->description_bn,
            'attachments' => $request->attachments,
            'content' => $request->content_text,
            'content_bn' => $request->content_bn
        ]);

        return response()->json([
            'message' => 'Document field updated successfully',
            'field' => $field
        ]);
    }

    /**
     * Delete a document field
     */
    public function destroyField($id)
    {
        $field = DocInfo::findOrFail($id);
        $field->delete();

        return response()->json([
            'message' => 'Document field deleted successfully'
        ]);
    }

    /**
     * Get content for a specific document type
     */
    public function getContent($typeId)
    {
        $content = DocInfo::where('doc_type_id', $typeId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'content' => $content
        ]);
    }

    /**
     * Store document content with file uploads
     */
    public function storeContent(Request $request)
    {
        $request->validate([
            'doc_type_id' => 'required|exists:doc_type,id',
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'content_text' => 'nullable|string',
            'content_bn' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240'
        ]);

        // Handle file uploads
        $attachmentPaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Generate unique filename with original extension
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents', $filename, 'public');
                $attachmentPaths[] = $path;
            }
        }

        // Combine with existing attachments from form
        $existingAttachments = $request->attachments ? explode(',', $request->attachments) : [];
        $allAttachments = array_merge($existingAttachments, $attachmentPaths);

        $document = DocInfo::create([
            'doc_type_id' => $request->doc_type_id,
            'title' => $request->title,
            'title_bn' => $request->title_bn,
            'description' => $request->description,
            'description_bn' => $request->description_bn,
            'content' => $request->content_text,
            'content_bn' => $request->content_bn,
            'attachments' => !empty($allAttachments) ? implode(',', $allAttachments) : null
        ]);

        // For Inertia, redirect back with success message
        return redirect()->back()->with('success', 'Document created successfully');
        
        // Alternative: Return JSON response for API calls
        // return response()->json([
        //     'message' => 'Document created successfully',
        //     'document' => $document
        // ]);
    }

    /**
     * Update document content with file uploads
     */
    public function updateContent(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'content_text' => 'nullable|string',
            'content_bn' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240'
        ]);

        $document = DocInfo::findOrFail($id);

        // Handle file uploads
        $attachmentPaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Generate unique filename with original extension
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('documents', $filename, 'public');
                $attachmentPaths[] = $path;
            }
        }

        // Combine with existing attachments from form
        $existingAttachments = $request->attachments ? explode(',', $request->attachments) : [];
        $allAttachments = array_merge($existingAttachments, $attachmentPaths);

        $document->update([
            'title' => $request->title,
            'title_bn' => $request->title_bn,
            'description' => $request->description,
            'description_bn' => $request->description_bn,
            'content' => $request->content_text,
            'content_bn' => $request->content_bn,
            'attachments' => !empty($allAttachments) ? implode(',', $allAttachments) : null
        ]);

        // For Inertia, redirect back with success message
        return redirect()->back()->with('success', 'Document updated successfully');
        
        // Alternative: Return JSON response for API calls
        // return response()->json([
        //     'message' => 'Document updated successfully',
        //     'document' => $document
        // ]);
    }

    /**
     * Delete document content and associated files
     */
    public function destroyContent($id)
    {
        $document = DocInfo::findOrFail($id);

        // Delete associated files from storage
        if ($document->attachments) {
            $attachments = explode(',', $document->attachments);
            foreach ($attachments as $attachment) {
                if (Storage::disk('public')->exists($attachment)) {
                    Storage::disk('public')->delete($attachment);
                }
            }
        }

        $document->delete();

        // For Inertia, redirect back with success message
        return redirect()->back()->with('success', 'Document deleted successfully');
        
        // Alternative: Return JSON response for API calls
        // return response()->json([
        //     'message' => 'Document deleted successfully'
        // ]);
    }

    /**
     * Download attachment file
     */
    public function downloadAttachment($filename)
    {
        $filePath = 'documents/' . $filename;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404);
        }

        return response()->download(
            Storage::disk('public')->path($filePath),
            $filename
        );
    }

    /**
     * View attachment file
     */
    public function viewAttachment($filename)
    {
        $filePath = 'documents/' . $filename;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404);
        }

        $file = Storage::disk('public')->get($filePath);
        
        // Get mime type using different approach
        $mimeType = mime_content_type(Storage::disk('public')->path($filePath));

        return response($file, 200)
            ->header('Content-Type', $mimeType);
    }

    /**
     * Delete a specific attachment from a document
     */
    public function deleteAttachment(Request $request, $id)
    {
        $request->validate([
            'attachment_path' => 'required|string'
        ]);

        $document = DocInfo::findOrFail($id);

        if ($document->attachments) {
            $attachments = explode(',', $document->attachments);
            $updatedAttachments = array_filter($attachments, function($attachment) use ($request) {
                return $attachment !== $request->attachment_path;
            });

            // Delete the file from storage
            if (Storage::disk('public')->exists($request->attachment_path)) {
                Storage::disk('public')->delete($request->attachment_path);
            }

            $document->update([
                'attachments' => !empty($updatedAttachments) ? implode(',', $updatedAttachments) : null
            ]);
        }

        return response()->json([
            'message' => 'Attachment deleted successfully'
        ]);
    }
}