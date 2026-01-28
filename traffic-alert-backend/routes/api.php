<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RssNewsController;
use App\Http\Controllers\Api\EmailVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication API routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/deactivate', [AuthController::class, 'deactivate']);
    });
});

// Email Verification routes
Route::prefix('email')->group(function () {
    Route::post('/send-code', [EmailVerificationController::class, 'sendVerificationCode']);
    Route::post('/verify-code', [EmailVerificationController::class, 'verifyCode']);
});


// Location API routes
Route::prefix('locations')->group(function () {
    Route::get('/wards', [LocationController::class, 'getWards']);
    Route::get('/streets', [LocationController::class, 'getStreets']);
    Route::get('/streets/{id}', [LocationController::class, 'getStreetDetail']);
    Route::get('/streets/ward/{wardId}', [LocationController::class, 'getStreetsByWard']);
    Route::get('/severity-levels', [LocationController::class, 'getSeverityLevels']);
    Route::get('/data', [LocationController::class, 'getLocationData']);
});

// Alert API routes
Route::prefix('alerts')->group(function () {
    // Public routes (no authentication required)
    Route::get('/public', [AlertController::class, 'publicIndex']); // For /routes page
    Route::get('/map', [AlertController::class, 'getApprovedAlertsForMap']);
    Route::get('/{id}', [AlertController::class, 'show']);

    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [AlertController::class, 'index']); // User's own alerts
        Route::post('/', [AlertController::class, 'store']);
        Route::put('/{id}', [AlertController::class, 'update']);
        Route::put('/{id}/approve', [AlertController::class, 'approve']);
        Route::put('/{id}/reject', [AlertController::class, 'reject']);
        Route::delete('/{id}', [AlertController::class, 'destroy']);
        Route::post('/street/{id}/coordinates', [AlertController::class, 'updateStreetCoordinates']);
    });
});

// Detection API routes (for testing)
Route::prefix('detection')->group(function () {
    Route::get('/health', [App\Http\Controllers\Api\DetectionController::class, 'health']);
    Route::post('/detect', [App\Http\Controllers\Api\DetectionController::class, 'detect']);
});

// Telegram Test Route
Route::get('/telegram/test', [App\Http\Controllers\Api\TelegramTestController::class, 'testSimpleMessage']);

// Admin API routes
Route::prefix('admin')->middleware(['auth:sanctum', App\Http\Middleware\CheckAdminRole::class])->group(function () {
    // Dashboard
    Route::get('/dashboard/statistics', [App\Http\Controllers\Api\Admin\AdminDashboardController::class, 'getStatistics']);
    Route::get('/dashboard/activities', [App\Http\Controllers\Api\Admin\AdminDashboardController::class, 'getRecentActivities']);
    
    // Alert Management
    Route::prefix('alerts')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\AdminAlertController::class, 'index']);
        Route::put('/{id}/approve', [App\Http\Controllers\Api\Admin\AdminAlertController::class, 'approve']);
        Route::put('/{id}/reject', [App\Http\Controllers\Api\Admin\AdminAlertController::class, 'reject']);
        Route::delete('/{id}', [App\Http\Controllers\Api\Admin\AdminAlertController::class, 'destroy']);
    });

    // User Management
    Route::prefix('users')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\AdminUserController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Api\Admin\AdminUserController::class, 'show']);
        Route::post('/', [App\Http\Controllers\Api\Admin\AdminUserController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Api\Admin\AdminUserController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Api\Admin\AdminUserController::class, 'destroy']);
        Route::put('/{id}/toggle-status', [App\Http\Controllers\Api\Admin\AdminUserController::class, 'toggleStatus']);
    });

    // Event Management
    Route::prefix('events')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\AdminEventController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Api\Admin\AdminEventController::class, 'show']);
        Route::post('/', [App\Http\Controllers\Api\Admin\AdminEventController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Api\Admin\AdminEventController::class, 'update']);
        Route::delete('/{id}', [App\Http\Controllers\Api\Admin\AdminEventController::class, 'destroy']);
        Route::put('/{id}/toggle-status', [App\Http\Controllers\Api\Admin\AdminEventController::class, 'toggleStatus']);
    });

    // Notification Management
    Route::prefix('notifications')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\AdminNotificationController::class, 'index']);
        Route::get('/events', [App\Http\Controllers\Api\Admin\AdminNotificationController::class, 'getEvents']);
        Route::get('/{id}', [App\Http\Controllers\Api\Admin\AdminNotificationController::class, 'show']);
        Route::post('/', [App\Http\Controllers\Api\Admin\AdminNotificationController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Api\Admin\AdminNotificationController::class, 'update']);
        Route::post('/{id}/send', [App\Http\Controllers\Api\Admin\AdminNotificationController::class, 'send']);
        Route::delete('/{id}', [App\Http\Controllers\Api\Admin\AdminNotificationController::class, 'destroy']);
    });

    // AI Results Management
    Route::prefix('ai-results')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\AdminAIResultController::class, 'index']);
        Route::get('/statistics', [App\Http\Controllers\Api\Admin\AdminAIResultController::class, 'statistics']);
        Route::get('/{id}', [App\Http\Controllers\Api\Admin\AdminAIResultController::class, 'show']);
        Route::post('/{id}/verify', [App\Http\Controllers\Api\Admin\AdminAIResultController::class, 'verify']);
        Route::post('/{id}/unverify', [App\Http\Controllers\Api\Admin\AdminAIResultController::class, 'unverify']);
        Route::delete('/{id}', [App\Http\Controllers\Api\Admin\AdminAIResultController::class, 'destroy']);
    });

    // Statistics
    Route::prefix('statistics')->group(function () {
        Route::get('/overview', [App\Http\Controllers\Api\Admin\AdminStatisticsController::class, 'overview']);
        Route::get('/events-by-type', [App\Http\Controllers\Api\Admin\AdminStatisticsController::class, 'eventsByType']);
        Route::get('/events-trend', [App\Http\Controllers\Api\Admin\AdminStatisticsController::class, 'eventsTrend']);
        Route::get('/ai-by-label', [App\Http\Controllers\Api\Admin\AdminStatisticsController::class, 'aiByLabel']);
        Route::get('/top-users', [App\Http\Controllers\Api\Admin\AdminStatisticsController::class, 'topUsers']);
        Route::get('/events-by-area', [App\Http\Controllers\Api\Admin\AdminStatisticsController::class, 'eventsByArea']);
        Route::get('/export', [App\Http\Controllers\Api\Admin\AdminStatisticsController::class, 'export']);
    });

    // Camera Management
    Route::prefix('cameras')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\AdminCameraController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Api\Admin\AdminCameraController::class, 'show']);
        Route::post('/', [App\Http\Controllers\Api\Admin\AdminCameraController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Api\Admin\AdminCameraController::class, 'update']);
        Route::post('/{id}/toggle-status', [App\Http\Controllers\Api\Admin\AdminCameraController::class, 'toggleStatus']);
        Route::delete('/{id}', [App\Http\Controllers\Api\Admin\AdminCameraController::class, 'destroy']);
    });
});

// RSS News API routes
Route::get('/news/traffic', [RssNewsController::class, 'getTrafficNews']);

// AI Detection API (from Python service)
Route::post('/ai/detect', [App\Http\Controllers\Api\AIDetectionController::class, 'detect']);
