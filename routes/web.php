<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Auth\InvestorAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\CommitteeController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\HomeInfoController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Frontend\HomeController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Home;
use App\Models\Album;
use App\Models\Page;

// =====================
// HOME ROUTE
// =====================
Route::get('/home', function () {
    // Fetch actual data from database - using your Home model
    $homeInfo = Home::first();
    
    // If no home info exists, create a default one (shouldn't happen since you have data)
    if (!$homeInfo) {
        $homeInfo = new Home();
    }

    return Inertia::render('Frontend/Home', [
        'homeInfo' => [
            'hero_title' => $homeInfo->hero_title,
            'hero_title_bn' => $homeInfo->hero_title_bn,
            'hero_subtitle' => $homeInfo->hero_subtitle,
            'hero_subtitle_bn' => $homeInfo->hero_subtitle_bn,
            'hero_image' => $homeInfo->hero_image ? Storage::url($homeInfo->hero_image) : null,
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
        ],
        'featuredServices' => [
            [
                'id' => 1,
                'title' => __('Stock Trading'),
                'description' => __('Trade stocks from global markets with advanced tools and real-time data'),
                'icon' => 'fas fa-chart-line',
                'features' => [__('Real-time Data'), __('Advanced Charts'), __('Global Markets')]
            ],
            [
                'id' => 2,
                'title' => __('Cryptocurrency'),
                'description' => __('Buy, sell and trade cryptocurrencies with secure wallet integration'),
                'icon' => 'fab fa-bitcoin',
                'features' => [__('100+ Coins'), __('Secure Wallet'), __('24/7 Trading')]
            ],
            [
                'id' => 3,
                'title' => __('Portfolio Management'),
                'description' => __('Professional portfolio management with automated rebalancing and insights'),
                'icon' => 'fas fa-briefcase',
                'features' => [__('Auto Rebalance'), __('Risk Analysis'), __('Performance Tracking')]
            ]
        ],
        'marketNews' => [
            [
                'id' => 1,
                'title' => __('Stock Markets Reach New Highs Amid Economic Recovery'),
                'excerpt' => __('Global stock markets continue their upward trend as economic indicators show strong recovery signals...'),
                'category' => __('Stocks'),
                'published_at' => now()->toISOString(),
                'read_time' => 3,
                'slug' => 'stock-markets-highs'
            ],
            [
                'id' => 2,
                'title' => __('Cryptocurrency Regulation Updates: What Investors Need to Know'),
                'excerpt' => __('New regulatory frameworks are shaping the future of cryptocurrency investments worldwide...'),
                'category' => __('Crypto'),
                'published_at' => now()->subDay()->toISOString(),
                'read_time' => 5,
                'slug' => 'crypto-regulation-updates'
            ],
            [
                'id' => 3,
                'title' => __('Federal Reserve Interest Rate Decision Impacts Markets'),
                'excerpt' => __('The latest Fed meeting results are creating waves across various investment sectors...'),
                'category' => __('Economy'),
                'published_at' => now()->subDays(2)->toISOString(),
                'read_time' => 4,
                'slug' => 'fed-rate-descision'
            ]
        ],
        'stats' => [
            'total_investors' => 50000,
            'total_volume' => 2500000000,
            'total_assets' => 150,
            'total_countries' => 80,
            'total_returns' => 15.2
        ],
        'featuredFaqs' => [
            [
                'id' => 1,
                'question' => __('How do I start investing with InvestPro?'),
                'answer' => __('Simply download our app, complete the registration process, verify your identity, and fund your account to start investing.')
            ],
            [
                'id' => 2,
                'question' => __('What is the minimum investment amount?'),
                'answer' => __('You can start investing with as little as $50. We believe in making investing accessible to everyone.')
            ],
            [
                'id' => 3,
                'question' => __('Is my investment secure?'),
                'answer' => __('Yes, we use bank-level security encryption and follow strict regulatory compliance to protect your investments and personal data.')
            ]
        ],
        'content' => [
            'home_hero_title' => __('Smart Investing Made Simple'),
            'home_hero_subtitle' => __('Join thousands of investors growing their wealth with our expert guidance and advanced trading tools.'),
            'home_hero_primary_button' => __('Start Investing'),
            'home_download_button' => __('Download App'),
            'home_services_title' => __('Our Investment Services'),
            'home_services_subtitle' => __('Comprehensive investment solutions tailored to your financial goals'),
            'home_news_title' => __('Latest Market News'),
            'home_news_subtitle' => __('Stay updated with the latest financial market trends and insights'),
            'home_cta_title' => __('Ready to Start Your Investment Journey?'),
            'home_cta_subtitle' => __('Join thousands of successful investors and take control of your financial future'),
            'home_cta_button' => __('Create Account')
        ]
    ]);
})->name('home');

Route::get('/', function () {
    return redirect()->route('home');
});

// =====================
// ABOUT US ROUTES
// =====================

// About Us Main Page
Route::get('/about', function () {
    $page = Page::with(['sections' => function($query) {
        $query->orderBy('section_order', 'asc');
    }])->where('page_slug', 'about-sharp-securities-limited')->firstOrFail();

    return Inertia::render('Frontend/About/AboutInvestPro', [
        'page' => [
            'page_id' => $page->page_id,
            'page_name' => $page->page_name,
            'page_name_bn' => $page->page_name_bn,
            'page_slug' => $page->page_slug,
            'sections' => $page->sections->map(function($section) {
                return [
                    'page_section_id' => $section->page_section_id,
                    'heading' => $section->heading,
                    'heading_bn' => $section->heading_bn,
                    'description' => $section->description,
                    'description_bn' => $section->description_bn,
                    'content' => $section->content,
                    'content_bn' => $section->content_bn,
                    'attachments' => $section->attachments,
                    'content_width' => $section->content_width,
                    'attachment_width' => $section->attachment_width,
                    'content_allignment' => $section->content_allignment,
                    'attachment_allignment' => $section->attachment_allignment,
                    'section_order' => $section->section_order,
                ];
            })->toArray()
        ]
    ]);
})->name('about');

// Vision Mission Page
Route::get('/about/vision-mission', function () {
    $page = Page::with(['sections' => function($query) {
        $query->orderBy('section_order', 'asc');
    }])->where('page_slug', 'vission-mission-values')->firstOrFail();

    return Inertia::render('Frontend/About/VisionMission', [
        'page' => [
            'page_id' => $page->page_id,
            'page_name' => $page->page_name,
            'page_name_bn' => $page->page_name_bn,
            'page_slug' => $page->page_slug,
            'sections' => $page->sections->map(function($section) {
                return [
                    'page_section_id' => $section->page_section_id,
                    'heading' => $section->heading,
                    'heading_bn' => $section->heading_bn,
                    'description' => $section->description,
                    'description_bn' => $section->description_bn,
                    'content' => $section->content,
                    'content_bn' => $section->content_bn,
                    'attachments' => $section->attachments,
                    'content_width' => $section->content_width,
                    'attachment_width' => $section->attachment_width,
                    'content_allignment' => $section->content_allignment,
                    'attachment_allignment' => $section->attachment_allignment,
                    'section_order' => $section->section_order,
                ];
            })->toArray()
        ]
    ]);
})->name('about.vision-mission');

// Message from MD Page
Route::get('/about/message-md', function () {
    $page = Page::with(['sections' => function($query) {
        $query->orderBy('section_order', 'asc');
    }])->where('page_slug', 'message-from-md')->firstOrFail();

    return Inertia::render('Frontend/About/MessageFromMD', [
        'page' => [
            'page_id' => $page->page_id,
            'page_name' => $page->page_name,
            'page_name_bn' => $page->page_name_bn,
            'page_slug' => $page->page_slug,
            'sections' => $page->sections->map(function($section) {
                return [
                    'page_section_id' => $section->page_section_id,
                    'heading' => $section->heading,
                    'heading_bn' => $section->heading_bn,
                    'description' => $section->description,
                    'description_bn' => $section->description_bn,
                    'content' => $section->content,
                    'content_bn' => $section->content_bn,
                    'attachments' => $section->attachments,
                    'content_width' => $section->content_width,
                    'attachment_width' => $section->attachment_width,
                    'content_allignment' => $section->content_allignment,
                    'attachment_allignment' => $section->attachment_allignment,
                    'section_order' => $section->section_order,
                ];
            })->toArray()
        ]
    ]);
})->name('about.message-md');

// Board of Directors Page
Route::get('/about/board', [CommitteeController::class, 'publicView'])->name('about.board');

// Gallery Page
Route::get('/about/gallery', function () {
    return Inertia::render('Frontend/About/Gallery');
})->name('about.gallery');

// =====================
// API ROUTES FOR ABOUT US
// =====================

// API Routes for Board of Directors
Route::get('/api/committees/board-of-directors', [CommitteeController::class, 'getBoardOfDirectors']);

// API Routes for Gallery
Route::get('/api/gallery/albums', [GalleryController::class, 'getAlbums']);

// =====================
// AUTH ROUTES
// =====================
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
    // HOME INFO MANAGEMENT ROUTES
    // =====================
    
    // Home Page Management
    Route::get('/home-info', [HomeInfoController::class, 'index'])->name('admin.home-info.index');
    Route::get('/home-info/data', [HomeInfoController::class, 'getHomeInfo'])->name('admin.home-info.get');
    Route::post('/home-info', [HomeInfoController::class, 'update'])->name('admin.home-info.update');
    Route::post('/home-info/upload-image', [HomeInfoController::class, 'uploadImage'])->name('admin.home-info.upload-image');
    Route::delete('/home-info/delete-image', [HomeInfoController::class, 'deleteImage'])->name('admin.home-info.delete-image');
    
    // =====================
    // PAGE MANAGEMENT ROUTES
    // =====================
    
    // Page Management Routes
    Route::get('/pages/site/{type}', [PageController::class, 'index'])->name('admin.pages.index');
    Route::post('/pages/store', [PageController::class, 'store'])->name('admin.pages.store');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('admin.pages.edit');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('admin.pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('admin.pages.destroy');
    
    // Page API Routes
    Route::prefix('pages')->group(function () {
        Route::get('/all', [PageController::class, 'getAllPages'])->name('admin.pages.all');
        Route::get('/{id}', [PageController::class, 'show'])->name('admin.pages.show');
        Route::post('/{id}/sections', [PageController::class, 'addSection'])->name('admin.pages.sections.store');
        Route::put('/sections/{sectionId}', [PageController::class, 'updateSection'])->name('admin.pages.sections.update');
        Route::delete('/sections/{sectionId}', [PageController::class, 'deleteSection'])->name('admin.pages.sections.destroy');
        Route::post('/sections/{sectionId}/attachments', [PageController::class, 'uploadAttachment'])->name('admin.pages.sections.attachments.upload');
        Route::delete('/sections/{sectionId}/attachments/{attachmentIndex}', [PageController::class, 'deleteAttachment'])->name('admin.pages.attachments.destroy');
    });
    
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

    // =====================
    // GALLERY MANAGEMENT ROUTES
    // =====================

    // Gallery Pages
    Route::get('/gallery/site/1', [GalleryController::class, 'create'])->name('admin.gallery.create');
    Route::get('/gallery/site/2', [GalleryController::class, 'edit'])->name('admin.gallery.edit');

    // Gallery API Routes
    Route::prefix('gallery')->group(function () {
        Route::post('', [GalleryController::class, 'store'])->name('admin.gallery.store');
        Route::get('/all', [GalleryController::class, 'getAllAlbums'])->name('admin.gallery.all');
        Route::get('/{id}', [GalleryController::class, 'show'])->name('admin.gallery.show');
        Route::put('/{id}', [GalleryController::class, 'update'])->name('admin.gallery.update');
        Route::delete('/{id}', [GalleryController::class, 'destroy'])->name('admin.gallery.destroy');
        Route::post('/upload-images', [GalleryController::class, 'uploadImages'])->name('admin.gallery.upload-images');
        Route::delete('/delete-image/{albumId}/{imageIndex}', [GalleryController::class, 'deleteImage'])->name('admin.gallery.delete-image');
    });
    
    // Investment Management (Legacy - you can remove these if no longer needed)
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
// FRONTEND ROUTES
// =====================

// Public page viewing route
Route::get('/page/{slug}', [PageController::class, 'view'])->name('page.view');

// Home Info API for frontend
Route::get('/api/home-info', [HomeInfoController::class, 'getFrontendHomeInfo'])->name('home-info.frontend');

// Attachment access route for page images and files
Route::get('/storage/page-attachments/{filename}', function ($filename) {
    $path = storage_path('app/public/page-attachments/' . $filename);
    
    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    
    return $response;
})->name('page.attachments.view');

// Home images access route
Route::get('/storage/home-images/{filename}', function ($filename) {
    $path = storage_path('app/public/home-images/' . $filename);
    
    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    
    return $response;
})->name('home.images.view');

// Gallery images access route
Route::get('/storage/gallery/{type}/{filename}', function ($type, $filename) {
    $path = storage_path('app/public/gallery/' . $type . '/' . $filename);
    
    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $mimeType = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $mimeType);
    
    return $response;
})->name('gallery.images.view');

// =====================
// GALLERY FRONTEND ROUTES
// =====================

// Gallery Index Page
Route::get('/gallery', function () {
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

    return Inertia::render('Gallery/Index', [
        'albums' => $albums
    ]);
})->name('gallery');

// Single Album View
Route::get('/gallery/album/{id}', function ($id) {
    $album = Album::findOrFail($id);

    $albumData = [
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

    return Inertia::render('Gallery/AlbumView', [
        'album' => $albumData
    ]);
})->name('gallery.album');

// Other Frontend Routes
Route::get('/services', function () {
    return Inertia::render('Frontend/Services');
})->name('services');

Route::get('/download', function () {
    return Inertia::render('Frontend/Download');
})->name('download');

Route::get('/news', function () {
    return Inertia::render('Frontend/News');
})->name('news');

Route::get('/faq', function () {
    return Inertia::render('Frontend/Faq');
})->name('faq');

Route::get('/contact', function () {
    return Inertia::render('Frontend/Contact');
})->name('contact');

Route::get('/investment-opportunities', function () {
    return Inertia::render('Frontend/InvestmentOpportunities');
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

// =====================
// DEBUG ROUTES
// =====================

// Add this to your web.php file temporarily for debugging
Route::get('/test-document-types', function () {
    return response()->json([
        'test' => 'API is working',
        'mainTypes' => \App\Models\DocType::whereNull('parent_id')->get(),
        'subTypes' => \App\Models\DocType::whereNotNull('parent_id')->get()
    ]);
});

// Test Pages API
Route::get('/test-pages-api', function () {
    return response()->json([
        'test' => 'Pages API is working',
        'pages' => \App\Models\Page::all(),
        'page_sections' => \App\Models\PageSection::all()
    ]);
});

// Test Home Info API
Route::get('/test-home-info', function () {
    return response()->json([
        'test' => 'Home Info API is working',
        'home_info' => Home::all(),
        'first_record' => Home::first()
    ]);
});

// Test Home Info Image Upload
Route::get('/test-home-info-image', function () {
    return response()->json([
        'storage_link_exists' => file_exists(public_path('storage')),
        'home_images_dir_exists' => file_exists(storage_path('app/public/home-images')),
        'storage_permissions' => [
            'storage_app' => is_writable(storage_path('app')),
            'storage_app_public' => is_writable(storage_path('app/public')),
        ]
    ]);
});

// Test Gallery API
Route::get('/test-gallery-api', function () {
    return response()->json([
        'test' => 'Gallery API is working',
        'albums' => Album::all(),
        'gallery_storage_exists' => file_exists(storage_path('app/public/gallery'))
    ]);
});

// Test About Us Routes
Route::get('/test-about-routes', function () {
    return response()->json([
        'about_routes' => [
            '/about' => 'About InvestPro',
            '/about/vision-mission' => 'Vision Mission Values',
            '/about/message-md' => 'Message From MD',
            '/about/board' => 'Board of Directors',
            '/about/gallery' => 'Gallery'
        ],
        'api_routes' => [
            '/api/committees/board-of-directors' => 'Board of Directors API',
            '/api/gallery/albums' => 'Gallery Albums API'
        ]
    ]);
});