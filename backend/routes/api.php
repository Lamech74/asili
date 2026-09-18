<?php

use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AdminProductController;
use App\Http\Controllers\Api\V1\AdminCategoryController;
use App\Http\Controllers\Api\V1\AdminServiceController;
use App\Http\Controllers\Api\V1\AdminBlogPostController;
use App\Http\Controllers\Api\V1\AdminTestimonialController;
use App\Http\Controllers\Api\V1\AdminFaqController;
use App\Http\Controllers\Api\V1\AdminSiteSettingController;
use App\Http\Controllers\Api\V1\AdminMediaAssetController;
use App\Http\Controllers\Api\V1\AdminUploadController;
use App\Http\Controllers\Api\V1\PublicCategoryController;
use App\Http\Controllers\Api\V1\PublicProductController;
use App\Http\Controllers\Api\V1\PublicServiceController;
use App\Http\Controllers\Api\V1\PublicBlogPostController;
use App\Http\Controllers\Api\V1\PublicTestimonialController;
use App\Http\Controllers\Api\V1\PublicFaqController;
use App\Http\Controllers\Api\V1\PublicSiteSettingController;
use App\Http\Controllers\Api\V1\PublicMediaAssetController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/categories', [PublicCategoryController::class, 'index']);
    Route::get('/products', [PublicProductController::class, 'index']);
    Route::get('/services', [PublicServiceController::class, 'index']);
    Route::get('/blog', [PublicBlogPostController::class, 'index']);
    Route::get('/testimonials', [PublicTestimonialController::class, 'index']);
    Route::get('/faqs', [PublicFaqController::class, 'index']);
    Route::get('/settings', [PublicSiteSettingController::class, 'index']);
    Route::get('/media', [PublicMediaAssetController::class, 'index']);
    Route::post('/contact', [ContactController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::middleware('role:admin,editor')->group(function () {
            Route::get('/admin/products', [AdminProductController::class, 'index']);
            Route::post('/admin/products', [AdminProductController::class, 'store']);
            Route::put('/admin/products/{product}', [AdminProductController::class, 'update']);
            Route::delete('/admin/products/{product}', [AdminProductController::class, 'destroy']);
            Route::get('/admin/categories', [AdminCategoryController::class, 'index']);
            Route::post('/admin/categories', [AdminCategoryController::class, 'store']);
            Route::put('/admin/categories/{category}', [AdminCategoryController::class, 'update']);
            Route::delete('/admin/categories/{category}', [AdminCategoryController::class, 'destroy']);
            Route::get('/admin/services', [AdminServiceController::class, 'index']);
            Route::post('/admin/services', [AdminServiceController::class, 'store']);
            Route::put('/admin/services/{service}', [AdminServiceController::class, 'update']);
            Route::delete('/admin/services/{service}', [AdminServiceController::class, 'destroy']);
            Route::get('/admin/blog', [AdminBlogPostController::class, 'index']);
            Route::post('/admin/blog', [AdminBlogPostController::class, 'store']);
            Route::put('/admin/blog/{blogPost}', [AdminBlogPostController::class, 'update']);
            Route::delete('/admin/blog/{blogPost}', [AdminBlogPostController::class, 'destroy']);
            Route::get('/admin/testimonials', [AdminTestimonialController::class, 'index']);
            Route::post('/admin/testimonials', [AdminTestimonialController::class, 'store']);
            Route::put('/admin/testimonials/{testimonial}', [AdminTestimonialController::class, 'update']);
            Route::delete('/admin/testimonials/{testimonial}', [AdminTestimonialController::class, 'destroy']);
            Route::get('/admin/faqs', [AdminFaqController::class, 'index']);
            Route::post('/admin/faqs', [AdminFaqController::class, 'store']);
            Route::put('/admin/faqs/{faq}', [AdminFaqController::class, 'update']);
            Route::delete('/admin/faqs/{faq}', [AdminFaqController::class, 'destroy']);
            Route::get('/admin/settings', [AdminSiteSettingController::class, 'index']);
            Route::post('/admin/settings', [AdminSiteSettingController::class, 'store']);
            Route::put('/admin/settings/{siteSetting}', [AdminSiteSettingController::class, 'update']);
            Route::delete('/admin/settings/{siteSetting}', [AdminSiteSettingController::class, 'destroy']);
            Route::get('/admin/media', [AdminMediaAssetController::class, 'index']);
            Route::post('/admin/media', [AdminMediaAssetController::class, 'store']);
            Route::put('/admin/media/{mediaAsset}', [AdminMediaAssetController::class, 'update']);
            Route::delete('/admin/media/{mediaAsset}', [AdminMediaAssetController::class, 'destroy']);
            Route::post('/admin/uploads/image', [AdminUploadController::class, 'store']);
        });
    });
});
