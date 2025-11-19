<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Auth\InvestorAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\CommitteeController;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return Inertia::render('Auth/Login');
})->name('home');

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

// =====================
// DEBUG ROUTES
// =====================

// Check authentication status
Route::get('/check-auth', function () {
    return response()->json([
        'authenticated' => Auth::check(),
        'user' => Auth::check() ? [
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'user_type' => Auth::user()->user_type,
        ] : null
    ]);
});

// Debug user data
Route::get('/debug-user', function () {
    if (!Auth::check()) {
        return response()->json(['error' => 'No user authenticated']);
    }

    $user = Auth::user();
    
    return response()->json([
        'user_data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'is_active' => $user->is_active,
        ],
        'user_class' => get_class($user),
        'is_investor_direct' => $user->user_type === 'investor',
        'is_admin_direct' => in_array($user->user_type, ['admin', 'super_admin']),
        'is_super_admin_direct' => $user->user_type === 'super_admin',
    ]);
});

// Check specific admin user
Route::get('/debug-admin-user', function () {
    $adminUser = \App\Models\User::where('email', 'superadmin@investpro.com')->first();
    
    if (!$adminUser) {
        return response()->json([
            'error' => 'Admin user not found',
            'suggestion' => 'Run php artisan db:seed to create admin users'
        ]);
    }
    
    return response()->json([
        'user_exists' => true,
        'user_data' => [
            'id' => $adminUser->id,
            'name' => $adminUser->name,
            'email' => $adminUser->email,
            'user_type' => $adminUser->user_type,
            'is_active' => $adminUser->is_active,
            'email_verified_at' => $adminUser->email_verified_at,
        ],
        'password_match_superadmin123' => Hash::check('superadmin123', $adminUser->password),
        'password_match_admin123' => Hash::check('admin123', $adminUser->password),
    ]);
});

// Check all admin users
Route::get('/debug-all-admin-users', function () {
    $adminUsers = \App\Models\User::whereIn('user_type', ['admin', 'super_admin'])->get();
    
    return response()->json([
        'admin_users' => $adminUsers->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'is_active' => $user->is_active,
                'password_match_superadmin123' => Hash::check('superadmin123', $user->password),
                'password_match_admin123' => Hash::check('admin123', $user->password),
                'password_match_finance123' => Hash::check('finance123', $user->password),
            ];
        })
    ]);
});

// Create admin users manually (if needed)
Route::get('/create-admin-users', function () {
    // Check if users already exist
    $superAdminExists = \App\Models\User::where('email', 'superadmin@investpro.com')->exists();
    $adminExists = \App\Models\User::where('email', 'admin@investpro.com')->exists();
    
    if ($superAdminExists && $adminExists) {
        return response()->json([
            'message' => 'Admin users already exist',
            'users' => [
                'super_admin' => [
                    'email' => 'superadmin@investpro.com',
                    'password' => 'superadmin123'
                ],
                'admin' => [
                    'email' => 'admin@investpro.com', 
                    'password' => 'admin123'
                ]
            ]
        ]);
    }

    // Create super admin if doesn't exist
    if (!$superAdminExists) {
        $superAdmin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@investpro.com',
            'password' => Hash::make('superadmin123'),
            'user_type' => 'super_admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    // Create regular admin if doesn't exist
    if (!$adminExists) {
        $admin = \App\Models\User::create([
            'name' => 'System Administrator',
            'email' => 'admin@investpro.com',
            'password' => Hash::make('admin123'),
            'user_type' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    return response()->json([
        'message' => 'Admin users created successfully',
        'users' => [
            'super_admin' => [
                'email' => 'superadmin@investpro.com',
                'password' => 'superadmin123'
            ],
            'admin' => [
                'email' => 'admin@investpro.com', 
                'password' => 'admin123'
            ]
        ]
    ]);
});

// Debug session data
Route::get('/debug-session', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'user_authenticated' => auth()->check(),
        'user_id' => auth()->id(),
    ]);
});

// Debug Inertia props
Route::get('/debug-inertia-props', function () {
    $middleware = new \App\Http\Middleware\HandleInertiaRequests();
    $request = request();
    
    return response()->json([
        'shared_props' => $middleware->share($request),
        'user_authenticated' => auth()->check(),
        'user_data' => auth()->check() ? [
            'id' => auth()->user()->id,
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'user_type' => auth()->user()->user_type,
        ] : null,
    ]);
});

// Test middleware files
Route::get('/test-middleware-files', function () {
    $middlewareFiles = [
        'AdminMiddleware' => app_path('Http/Middleware/AdminMiddleware.php'),
        'InvestorMiddleware' => app_path('Http/Middleware/InvestorMiddleware.php'),
        'SuperAdminMiddleware' => app_path('Http/Middleware/SuperAdminMiddleware.php'),
    ];
    
    $results = [];
    foreach ($middlewareFiles as $name => $path) {
        $results[$name] = [
            'exists' => file_exists($path),
            'path' => $path,
        ];
    }
    
    return response()->json($results);
});

// Test direct admin login
Route::get('/test-admin-login', function () {
    $credentials = [
        'email' => 'admin@investpro.com',
        'password' => 'admin123',
        'is_active' => true
    ];

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
            'is_admin' => in_array($user->user_type, ['admin', 'super_admin']),
            'redirect_url' => '/admin/dashboard'
        ]);
    }

    return response()->json(['success' => false, 'message' => 'Login failed']);
});

// =====================
// INVESTOR ROUTES
// =====================

Route::prefix('investor')->group(function () {
    // Authentication routes
    Route::post('/login', [InvestorAuthController::class, 'login'])->name('investor.login');
    Route::post('/logout', [InvestorAuthController::class, 'logout'])->name('investor.logout');
    
    // Registration
    Route::get('/register', function () {
        return Inertia::render('Auth/InvestorRegister');
    })->name('investor.register');
    
    // Protected investor routes - TEMPORARILY WITHOUT MIDDLEWARE
    Route::get('/dashboard', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if ($user->user_type !== 'investor') {
            abort(403, 'Access denied. Investor privileges required.');
        }
        
        return Inertia::render('Investor/Dashboard', [
            'stats' => [
                'portfolioValue' => 125000,
                'activeInvestments' => 8,
                'totalReturns' => 15200,
                'pendingReturns' => 3200,
            ]
        ]);
    })->name('investor.dashboard');
    
    Route::get('/portfolio', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if ($user->user_type !== 'investor') {
            abort(403, 'Access denied. Investor privileges required.');
        }
        
        return Inertia::render('Investor/Portfolio');
    })->name('investor.portfolio');
    
    Route::get('/investments', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if ($user->user_type !== 'investor') {
            abort(403, 'Access denied. Investor privileges required.');
        }
        
        return Inertia::render('Investor/Investments');
    })->name('investor.investments');
    
    Route::get('/profile', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if ($user->user_type !== 'investor') {
            abort(403, 'Access denied. Investor privileges required.');
        }
        
        return Inertia::render('Investor/Profile');
    })->name('investor.profile');
});

// =====================
// ADMIN ROUTES
// =====================

Route::prefix('admin')->group(function () {
    // Authentication routes
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Registration
    Route::get('/register', function () {
        return Inertia::render('Auth/AdminRegister');
    })->name('admin.register');
    
    // Protected admin routes - TEMPORARILY WITHOUT MIDDLEWARE
    Route::get('/dashboard', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalInvestors' => 1247,
                'activeInvestments' => 89,
                'pendingApprovals' => 5,
                'totalVolume' => 4520000,
                'newInvestorsThisWeek' => 23,
                'completedInvestments' => 45,
                'pendingWithdrawals' => 12,
                'averageROI' => 8.5
            ]
        ]);
    })->name('admin.dashboard');
    
    // =====================
    // USER MANAGEMENT ROUTES
    // =====================
    
    // Super Admins Management
    Route::get('/users/super-admins', [UserManagementController::class, 'superAdmins'])->name('admin.users.super-admins');
    Route::post('/users/super-admins', [UserManagementController::class, 'storeSuperAdmin'])->name('admin.users.super-admins.store');
    Route::put('/users/super-admins/{id}', [UserManagementController::class, 'updateSuperAdmin'])->name('admin.users.super-admins.update');
    Route::delete('/users/super-admins/{id}', [UserManagementController::class, 'destroySuperAdmin'])->name('admin.users.super-admins.destroy');

    // Admins Management
    Route::get('/users/admins', [UserManagementController::class, 'admins'])->name('admin.users.admins');
    Route::post('/users/admins', [UserManagementController::class, 'storeAdmin'])->name('admin.users.admins.store');
    Route::put('/users/admins/{id}', [UserManagementController::class, 'updateAdmin'])->name('admin.users.admins.update');
    Route::delete('/users/admins/{id}', [UserManagementController::class, 'destroyAdmin'])->name('admin.users.admins.destroy');

    // Investors Management
    Route::get('/users/investors', [UserManagementController::class, 'investors'])->name('admin.users.investors');
    Route::post('/users/investors', [UserManagementController::class, 'storeInvestor'])->name('admin.users.investors.store');
    Route::put('/users/investors/{id}', [UserManagementController::class, 'updateInvestor'])->name('admin.users.investors.update');
    Route::get('/users/investors/{id}', [UserManagementController::class, 'showInvestor'])->name('admin.users.investors.show');
    
    // Investor Management (Legacy routes - you can keep these or remove them)
    Route::get('/investors', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Investors/Index');
    })->name('admin.investors');
    
    Route::get('/investors/{id}', function ($id) {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Investors/Show', ['id' => $id]);
    })->name('admin.investors.show');
    
    // =====================
    // DOCUMENT MANAGEMENT ROUTES
    // =====================
    
    // Document Type Management
    Route::get('/documents/type', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Documents/Type');
    })->name('admin.documents.type');
    
    // Document Site Management
    Route::get('/documents/site/{id}', function ($id) {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Documents/Site', ['siteId' => $id]);
    })->name('admin.documents.site');
    
    // Document API Routes
    Route::prefix('documents')->group(function () {
        // Document Types
        Route::get('/types', [DocumentController::class, 'getTypes'])->name('admin.documents.types.index');
        Route::post('/types', [DocumentController::class, 'storeType'])->name('admin.documents.types.store');
        Route::put('/types/{id}', [DocumentController::class, 'updateType'])->name('admin.documents.types.update');
        Route::delete('/types/{id}', [DocumentController::class, 'destroyType'])->name('admin.documents.types.destroy');
        
        // Document Fields
        Route::get('/fields/{typeId}', [DocumentController::class, 'getFields'])->name('admin.documents.fields.index');
        Route::post('/fields', [DocumentController::class, 'storeField'])->name('admin.documents.fields.store');
        Route::put('/fields/{id}', [DocumentController::class, 'updateField'])->name('admin.documents.fields.update');
        Route::delete('/fields/{id}', [DocumentController::class, 'destroyField'])->name('admin.documents.fields.destroy');
        
        // Document Content
        Route::get('/content/{typeId}', [DocumentController::class, 'getContent'])->name('admin.documents.content.index');
        Route::post('/content', [DocumentController::class, 'storeContent'])->name('admin.documents.content.store');
        Route::post('/content/{id}', [DocumentController::class, 'updateContent'])->name('admin.documents.content.update');
        Route::delete('/content/{id}', [DocumentController::class, 'destroyContent'])->name('admin.documents.content.destroy');
        
        // Attachment Management
        Route::get('/attachment/download/{filename}', [DocumentController::class, 'downloadAttachment'])->name('admin.documents.attachment.download');
        Route::get('/attachment/view/{filename}', [DocumentController::class, 'viewAttachment'])->name('admin.documents.attachment.view');
        Route::delete('/attachment/{id}', [DocumentController::class, 'deleteAttachment'])->name('admin.documents.attachment.delete');
        
        // =====================
        // FIELD CONFIGURATION ROUTES
        // =====================
        
        // Field Configuration Routes
        Route::get('/field-config/{typeId}', [DocumentController::class, 'getFieldConfigurations'])->name('admin.documents.field-config');
        Route::put('/field-config/{typeId}/{fieldId}', [DocumentController::class, 'updateFieldConfiguration'])->name('admin.documents.field-config.update');
        Route::post('/field-config/copy', [DocumentController::class, 'copyFieldConfiguration'])->name('admin.documents.field-config.copy');
        Route::get('/types-with-fields', [DocumentController::class, 'getDocumentTypesWithFields'])->name('admin.documents.types-with-fields');
    });
    
    // =====================
    // COMMITTEE MANAGEMENT ROUTES
    // =====================
    
    // Committee Pages
    Route::get('/committee/site/1', [CommitteeController::class, 'create'])->name('admin.committee.create');
    Route::get('/committee/site/2', [CommitteeController::class, 'edit'])->name('admin.committee.edit');
    Route::get('/committee/site/3', [CommitteeController::class, 'index'])->name('admin.committee.index');
    
    // Committee API Routes
    Route::prefix('committee')->group(function () {
        Route::post('', [CommitteeController::class, 'store'])->name('admin.committee.store');
        Route::get('/all', [CommitteeController::class, 'getAllCommittees'])->name('admin.committee.all');
        Route::get('/{id}', [CommitteeController::class, 'show'])->name('admin.committee.show');
        Route::put('/{id}', [CommitteeController::class, 'update'])->name('admin.committee.update');
        Route::delete('/{id}', [CommitteeController::class, 'destroy'])->name('admin.committee.destroy');
    });
    
    // Investment Management
    Route::get('/investments', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Investments/Index');
    })->name('admin.investments');
    
    Route::get('/investments/create', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Investments/Create');
    })->name('admin.investments.create');
    
    Route::get('/investments/{id}', function ($id) {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Investments/Show', ['id' => $id]);
    })->name('admin.investments.show');
    
    // Transactions
    Route::get('/transactions', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Transactions/Index');
    })->name('admin.transactions');
    
    // Settings
    Route::get('/settings', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Settings/Index');
    })->name('admin.settings');
    
    Route::get('/profile', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if (!in_array($user->user_type, ['admin', 'super_admin'])) {
            abort(403, 'Access denied. Administrator privileges required.');
        }
        
        return Inertia::render('Admin/Profile');
    })->name('admin.profile');
});

// =====================
// SUPER ADMIN ROUTES
// =====================

Route::prefix('super-admin')->group(function () {
    Route::get('/dashboard', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if ($user->user_type !== 'super_admin') {
            abort(403, 'Access denied. Super Administrator privileges required.');
        }
        
        return Inertia::render('SuperAdmin/Dashboard');
    })->name('super-admin.dashboard');
    
    Route::get('/users', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if ($user->user_type !== 'super_admin') {
            abort(403, 'Access denied. Super Administrator privileges required.');
        }
        
        return Inertia::render('SuperAdmin/Users/Index');
    })->name('super-admin.users');
    
    Route::get('/system-settings', function () {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        if ($user->user_type !== 'super_admin') {
            abort(403, 'Access denied. Super Administrator privileges required.');
        }
        
        return Inertia::render('SuperAdmin/SystemSettings');
    })->name('super-admin.system-settings');
});

// =====================
// PUBLIC ROUTES
// =====================

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/investment-opportunities', function () {
    return Inertia::render('InvestmentOpportunities');
})->name('investment-opportunities');

// =====================
// TEST MIDDLEWARE ROUTES
// =====================

Route::get('/test-investor', function () {
    return response()->json(['message' => 'Investor middleware works!']);
})->middleware(['auth', 'investor']);

Route::get('/test-admin', function () {
    return response()->json(['message' => 'Admin middleware works!']);
})->middleware(['auth', 'admin']);

Route::get('/test-super-admin', function () {
    return response()->json(['message' => 'Super Admin middleware works!']);
})->middleware(['auth', 'super_admin']);

// =====================
// FALLBACK ROUTE
// =====================

Route::fallback(function () {
    return Inertia::render('Errors/404');
});

// Add this to your web.php file temporarily for debugging
Route::get('/test-document-types', function () {
    return response()->json([
        'test' => 'API is working',
        'mainTypes' => \App\Models\DocType::whereNull('parent_id')->get(),
        'subTypes' => \App\Models\DocType::whereNotNull('parent_id')->get()
    ]);
});