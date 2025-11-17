<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends Controller
{
    // Super Admins Management
    public function superAdmins()
    {
        $superAdmins = User::where('user_type', 'super_admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $superAdmins,
            'userType' => 'super_admin',
            'stats' => [
                'activeToday' => User::where('user_type', 'super_admin')
                    ->whereDate('last_login_at', today())
                    ->count(),
                'last7Days' => User::where('user_type', 'super_admin')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
            ]
        ]);
    }

    // Admins Management
    public function admins()
    {
        $admins = User::where('user_type', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $admins,
            'userType' => 'admin',
            'stats' => [
                'activeToday' => User::where('user_type', 'admin')
                    ->whereDate('last_login_at', today())
                    ->count(),
                'last7Days' => User::where('user_type', 'admin')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
            ]
        ]);
    }

    // Investors Management
    public function investors()
    {
        $investors = User::where('user_type', 'investor')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                // Add mock investment data for now
                $user->total_investment = rand(1000, 50000);
                $user->active_investments = rand(1, 10);
                return $user;
            });

        return Inertia::render('Admin/Users/Index', [
            'users' => $investors,
            'userType' => 'investor',
            'stats' => [
                'activeToday' => User::where('user_type', 'investor')
                    ->whereDate('last_login_at', today())
                    ->count(),
                'last7Days' => User::where('user_type', 'investor')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->count(),
                'totalInvestment' => $investors->sum('total_investment'),
            ]
        ]);
    }

    // Store Super Admin
    public function storeSuperAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'dob' => 'required|date',
            'education_qualification' => 'required|string',
            'institute' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'dob' => $request->dob,
            'education_qualification' => $request->education_qualification,
            'institute' => $request->institute,
            'password' => Hash::make($request->password),
            'user_type' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Super Admin created successfully.');
    }

    // Store Admin
    public function storeAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'dob' => 'required|date',
            'education_qualification' => 'required|string',
            'institute' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'dob' => $request->dob,
            'education_qualification' => $request->education_qualification,
            'institute' => $request->institute,
            'password' => Hash::make($request->password),
            'user_type' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Admin created successfully.');
    }

    // Update Super Admin
    public function updateSuperAdmin(Request $request, $id)
    {
        $user = User::where('user_type', 'super_admin')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'dob' => 'required|date',
            'education_qualification' => 'required|string',
            'institute' => 'required|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'dob' => $request->dob,
            'education_qualification' => $request->education_qualification,
            'institute' => $request->institute,
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Super Admin updated successfully.');
    }

    // Update Admin
    public function updateAdmin(Request $request, $id)
    {
        $user = User::where('user_type', 'admin')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'dob' => 'required|date',
            'education_qualification' => 'required|string',
            'institute' => 'required|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'dob' => $request->dob,
            'education_qualification' => $request->education_qualification,
            'institute' => $request->institute,
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Admin updated successfully.');
    }

    // Destroy Super Admin
    public function destroySuperAdmin($id)
    {
        $user = User::where('user_type', 'super_admin')->findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Super Admin deleted successfully.');
    }

    // Destroy Admin
    public function destroyAdmin($id)
    {
        $user = User::where('user_type', 'admin')->findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Admin deleted successfully.');
    }

    // Show Investor Details
    public function showInvestor($id)
    {
        $investor = User::where('user_type', 'investor')->findOrFail($id);

        return Inertia::render('Admin/Users/InvestorShow', [
            'investor' => $investor,
        ]);
    }

    public function storeInvestor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'dob' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'dob' => $request->dob,
            'user_type' => 'investor',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Investor created successfully.');
    }

    // Update Investor
    public function updateInvestor(Request $request, $id)
    {
        $user = User::where('user_type', 'investor')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'dob' => 'nullable|date',
            'password' => 'nullable|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Investor updated successfully.');
    }
}