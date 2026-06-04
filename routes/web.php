<?php

use Illuminate\Support\Facades\Route;

// ── User-facing controllers ──────────────────────────────────────────────────
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;

// ── Admin controllers (aliased to avoid name collisions) ─────────────────────
use App\Http\Controllers\Admin\AuthController     as AdminAuthController;
use App\Http\Controllers\Admin\UserController     as AdminUserController;
use App\Http\Controllers\Admin\OrderController    as AdminOrderController;
use App\Http\Controllers\Admin\ProductController  as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes - ZYRA E-Commerce Platform
|--------------------------------------------------------------------------
*/

// ============================================================================
// PUBLIC ROUTES
// ============================================================================

// Home & Static Pages
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');



Route::get('/privacy-policy', function () {
    return view('pages.privacy-policy');
})->name('privacy-policy');

Route::get('/size-guide', function () {
    return view('pages.size-guide');
})->name('size-guide');

// ============================================================================
// ORDER TRACKING ROUTES
// ============================================================================

Route::get('/track-order', [App\Http\Controllers\OrderTrackingController::class, 'index'])->name('track-order.index');
Route::post('/track-order', [App\Http\Controllers\OrderTrackingController::class, 'track'])->name('track-order.track');

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================

Route::middleware('guest')->group(function () {
    // User Authentication
    Route::get('/login',    [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);

    // Password Reset
    Route::get('/forgot-password',          [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password',         [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}',   [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password',          [AuthController::class, 'resetPassword'])->name('password.update');

    // Admin Login
    Route::get('/admin/login',  [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login']);
});

// Logout (requires authentication)
Route::post('/logout',       [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('auth:admin');

// ============================================================================
// OTP VERIFICATION ROUTES
// ============================================================================

Route::middleware(['guest'])->group(function () {
    Route::get('/otp/verify', [AuthController::class, 'showOtpVerificationForm'])->name('otp.verify.form');
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');
    Route::post('/otp/cancel', [AuthController::class, 'cancelOtpVerification'])->name('otp.cancel');
});

// Email Verification
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/email/resend', [AuthController::class, 'resendVerification'])->name('verification.resend')->middleware('auth');

// ============================================================================
// PRODUCT ROUTES
// ============================================================================

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/',      [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}',[ProductController::class, 'show'])->name('show');
});

Route::get('/shop', [ProductController::class, 'index'])->name('shop');

// ============================================================================
// CATEGORY ROUTES
// ============================================================================

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/',      [CategoryController::class, 'index'])->name('index');
    Route::get('/{slug}',[CategoryController::class, 'show'])->name('show');
});

// ============================================================================
// CART ROUTES
// ============================================================================

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',           [CartController::class, 'index'])->name('index');
    Route::post('/add',       [CartController::class, 'add'])->name('add');
    Route::put('/{cartKey}',  [CartController::class, 'update'])->name('update');
    Route::delete('/{cartKey}',[CartController::class, 'remove'])->name('remove');
    Route::delete('/',        [CartController::class, 'clear'])->name('clear');
    Route::get('/count',      [CartController::class, 'count'])->name('count');

    // Coupon
    Route::post('/coupon/apply',    [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('/coupon/remove', [CartController::class, 'removeCoupon'])->name('coupon.remove');



    // Save for Later
    Route::post('/save-for-later', [CartController::class, 'saveForLater'])->name('save-for-later');
    Route::post('/restore-saved',  [CartController::class, 'restoreSaved'])->name('restore-saved');
});

// ============================================================================
// WISHLIST ROUTES
// ============================================================================
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [\App\Http\Controllers\WishlistController::class, 'index'])->name('index');
    Route::post('/toggle', [\App\Http\Controllers\WishlistController::class, 'toggle'])->name('toggle');
    Route::get('/count', [\App\Http\Controllers\WishlistController::class, 'count'])->name('count');
    Route::post('/{productId}/move-to-cart', [\App\Http\Controllers\WishlistController::class, 'moveToCart'])->name('move-to-cart');
});

// ============================================================================
// AUTHENTICATED USER ROUTES
// ============================================================================

Route::middleware(['auth'])->group(function () {

    // ================= USER PROFILE =================
    Route::prefix('profile')->name('users.')->group(function () {
        Route::get('/',        [UserController::class, 'profile'])->name('profile');
        Route::get('/edit',    [UserController::class, 'edit'])->name('edit');
        Route::post('/update', [UserController::class, 'update'])->name('update');

        // Orders (users/orders.blade.php)
        Route::get('/orders',  [UserController::class, 'orders'])->name('orders');


    });

    // ================= USER ORDERS =================
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',          [OrderController::class, 'index'])->name('index');
        Route::get('/checkout',  [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/',         [OrderController::class, 'store'])->name('store');
        Route::get('/{order}',   [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });

    // ================= USER RETURNS =================
    Route::prefix('returns')->name('returns.')->group(function () {
        Route::get('/',                    [ReturnController::class, 'index'])->name('index');
        Route::get('/create/{orderItem}',  [ReturnController::class, 'create'])->name('create');
        Route::post('/',                   [ReturnController::class, 'store'])->name('store');
        Route::get('/{return}',            [ReturnController::class, 'show'])->name('show');
    });
});

// ============================================================================
// ADMIN ROUTES
// ============================================================================

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin'])
    ->group(function () {

    // ================= MASS DESTROY =================
    Route::post('/mass-destroy/{model}', [\App\Http\Controllers\Admin\MassDestroyController::class, 'destroy'])->name('mass_destroy');

    // ================= DASHBOARD =================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ================= ANALYTICS =================
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // ================= PRODUCTS =================
    Route::get('/products',                   [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create',            [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products',                  [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit',    [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}',         [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}',      [AdminProductController::class, 'destroy'])->name('products.destroy');

    // ================= ORDERS =================
    Route::get('/orders',          [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',  [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update.status');
    Route::delete('/orders/{order}',  [AdminOrderController::class, 'destroy'])->name('orders.destroy');

    // ================= RETURNS =================
    Route::get('/returns',           [ReturnController::class, 'adminIndex'])->name('returns.index');
    Route::get('/returns/{return}',  [ReturnController::class, 'adminShow'])->name('returns.show');
    Route::post('/returns/{return}/approve', [ReturnController::class, 'approve'])->name('returns.approve');
    Route::post('/returns/{return}/reject',  [ReturnController::class, 'reject'])->name('returns.reject');
    Route::post('/returns/{return}/complete',[ReturnController::class, 'complete'])->name('returns.complete');

    // ================= CATEGORIES =================
    Route::get('/categories',[AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create',[AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories',[AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit',[AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}',[AdminCategoryController::class, 'update'])->name('categories.update');

    // ================= USERS =================
    Route::get('users',           [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/{user}',    [AdminUserController::class, 'show'])->name('users.show');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    // ================= SETTINGS =================
    Route::get('/settings',   [SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');
});

// ============================================================================
// PAYMENT ROUTES
// ============================================================================

Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/failure', [PaymentController::class, 'failure'])->name('failure');
    Route::post('/create', [PaymentController::class, 'create'])->name('create');
    Route::post('/store', [PaymentController::class, 'store'])->name('store');
    Route::post('/webhook', [PaymentController::class, 'webhook'])->name('webhook');
});

// ============================================================================
// API ROUTES (Public)
// ============================================================================

Route::prefix('api')->name('api.')->group(function () {
    // Authentication Status
    Route::get('/auth/check', [AuthController::class, 'checkAuth'])->name('auth.check');

    // Categories
    Route::get('/categories',                    [CategoryController::class, 'apiIndex'])->name('categories.index');
    Route::get('/categories/{slug}/products',    [CategoryController::class, 'apiProducts'])->name('categories.products');

    // Products

});
