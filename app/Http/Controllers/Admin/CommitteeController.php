<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommitteeInfo;
use App\Models\CommitteeMemberInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class CommitteeController extends Controller
{
    /**
     * Display the create committee page
     */
    public function create()
    {
        return Inertia::render('Admin/Committee/CreateCommittee');
    }

    /**
     * Store a newly created committee
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Board Of Directors,Executive Committee,Other',
            'starting_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:starting_date',
            'members' => 'required|array|min:1',
            'members.*.name' => 'required|string|max:255',
            'members.*.designation' => 'required|string|max:255',
            'members.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'members.*.dob' => 'nullable|date',
            'members.*.joining_date' => 'required|date'
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Create committee
                $committee = CommitteeInfo::create([
                    'Committee_Name' => $request->name,
                    'Committee_Type' => $request->type,
                    'Committee_Starting_Date' => $request->starting_date,
                    'Committee_Expiry_Date' => $request->expiry_date,
                ]);

                // Create committee members
                foreach ($request->members as $memberData) {
                    $imagePath = null;
                    
                    if (isset($memberData['image']) && $memberData['image']) {
                        $imagePath = $memberData['image']->store('committee-members', 'public');
                    }

                    CommitteeMemberInfo::create([
                        'Committee_ID' => $committee->Committee_ID,
                        'Member_Name' => $memberData['name'],
                        'Member_Designation' => $memberData['designation'],
                        'Member_Image' => $imagePath,
                        'Member_DOB' => $memberData['dob'] ?? null,
                        'Member_Joining_Date' => $memberData['joining_date'],
                    ]);
                }
            });

            return response()->json([
                'message' => 'Committee created successfully'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Committee creation failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to create committee',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the update committee page
     */
    public function edit()
    {
        // This will be used for the update page (/admin/committee/site/2)
        // Get all committees for selection
        $committees = CommitteeInfo::with('members')
            ->orderBy('Created_At', 'desc')
            ->get();

        return Inertia::render('Admin/Committee/UpdateCommittee', [
            'committees' => $committees
        ]);
    }

    /**
     * Get specific committee for editing
     */
    public function show($id)
    {
        try {
            $committee = CommitteeInfo::with('members')->findOrFail($id);

            return response()->json([
                'committee' => $committee
            ]);
        } catch (\Exception $e) {
            Log::error('Committee show failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Committee not found',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 404);
        }
    }

    /**
     * Update a committee
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Board Of Directors,Executive Committee,Other',
            'starting_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:starting_date',
            'members' => 'required|array|min:1',
            'members.*.name' => 'required|string|max:255',
            'members.*.designation' => 'required|string|max:255',
            'members.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'members.*.dob' => 'nullable|date',
            'members.*.joining_date' => 'required|date'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                // Find and update committee
                $committee = CommitteeInfo::findOrFail($id);
                $committee->update([
                    'Committee_Name' => $request->name,
                    'Committee_Type' => $request->type,
                    'Committee_Starting_Date' => $request->starting_date,
                    'Committee_Expiry_Date' => $request->expiry_date,
                ]);

                // Delete existing members
                CommitteeMemberInfo::where('Committee_ID', $id)->delete();

                // Create new committee members
                foreach ($request->members as $memberData) {
                    $imagePath = null;
                    
                    if (isset($memberData['image']) && $memberData['image']) {
                        $imagePath = $memberData['image']->store('committee-members', 'public');
                    }

                    CommitteeMemberInfo::create([
                        'Committee_ID' => $committee->Committee_ID,
                        'Member_Name' => $memberData['name'],
                        'Member_Designation' => $memberData['designation'],
                        'Member_Image' => $imagePath,
                        'Member_DOB' => $memberData['dob'] ?? null,
                        'Member_Joining_Date' => $memberData['joining_date'],
                    ]);
                }
            });

            return response()->json([
                'message' => 'Committee updated successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Committee update failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to update committee',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display committee list
     */
    public function index()
    {
        // This will be used for the committee list page (/admin/committee/site/3)
        $committees = CommitteeInfo::with('members')
            ->orderBy('Created_At', 'desc')
            ->get();

        return Inertia::render('Admin/Committee/CommitteeList', [
            'committees' => $committees
        ]);
    }

    /**
     * Delete a committee
     */
    public function destroy($id)
    {
        try {
            $committee = CommitteeInfo::findOrFail($id);
            $committee->delete();

            return response()->json([
                'message' => 'Committee deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Committee deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to delete committee',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get all committees for API (used in UpdateCommittee component)
     */
    public function getAllCommittees()
    {
        try {
            $committees = CommitteeInfo::with('members')
                ->orderBy('Created_At', 'desc')
                ->get();

            return response()->json([
                'committees' => $committees
            ]);
        } catch (\Exception $e) {
            Log::error('Get all committees failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to load committees',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

        /**
     * Public view for board of directors
     */
    public function publicView()
    {
        $committees = CommitteeInfo::with('members')
            ->where('Committee_Type', 'Board Of Directors')
            ->where('Committee_Expiry_Date', '>=', now())
            ->orWhereNull('Committee_Expiry_Date')
            ->orderBy('Committee_Starting_Date', 'desc')
            ->get();

        return Inertia::render('Frontend/About/BoardOfDirectors', [
            'committees' => $committees
        ]);
    }

    /**
     * API endpoint for board of directors
     */
    public function getBoardOfDirectors()
    {
        try {
            $committees = CommitteeInfo::with('members')
                ->where('Committee_Type', 'Board Of Directors')
                ->where(function($query) {
                    $query->where('Committee_Expiry_Date', '>=', now())
                        ->orWhereNull('Committee_Expiry_Date');
                })
                ->orderBy('Committee_Starting_Date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'committees' => $committees
            ]);
        } catch (\Exception $e) {
            Log::error('Board of directors fetch failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load board information'
            ], 500);
        }
    }

}