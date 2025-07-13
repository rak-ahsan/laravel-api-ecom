<?php

use App\Classes\Helper;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\FAQController;
use App\Http\Controllers\Front\TagController;
use App\Http\Controllers\Front\AreaController;
use App\Http\Controllers\Front\AuthController;
use App\Http\Controllers\Front\TeamController;
use App\Http\Controllers\Front\ZoneController;
use App\Http\Controllers\Front\AboutController;
use App\Http\Controllers\Front\BrandController;
use App\Http\Controllers\Front\OrderController;
use App\Http\Controllers\Front\BannerController;
use App\Http\Controllers\Front\CouponController;
use App\Http\Controllers\Front\SliderController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\SectionController;
use App\Http\Controllers\Front\SettingController;
use App\Http\Controllers\Front\BlogPostController;
use App\Http\Controllers\Front\CampaignController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\Front\DistrictController;
use App\Http\Controllers\Front\PrivacyPolicyController;
use App\Http\Controllers\Front\PaymentGatewayController;
use App\Http\Controllers\Front\DeliveryGatewayController;

// Auth route
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

// ========================== CMS Management system ==============

// Slider route
Route::prefix('sliders')->group(function () {
    Route::controller(SliderController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// About route
Route::prefix('abouts')->group(function () {
    Route::controller(AboutController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Banner route
Route::prefix('banners')->group(function () {
    Route::controller(BannerController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Blog Post route
Route::prefix('blog-posts')->group(function () {
    Route::controller(BlogPostController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Privacy Policy
Route::prefix('privacy-policies')->group(function () {
    Route::controller(PrivacyPolicyController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Faq route
Route::prefix('faqs')->group(function () {
    Route::controller(FAQController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// ========================== Product Management system ==============

// Brand route
Route::prefix('brands')->group(function () {
    Route::controller(BrandController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Category route
Route::prefix('categories')->group(function () {
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Tag route
Route::prefix('tags')->group(function () {
    Route::controller(TagController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Product route
Route::prefix('products')->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('/',                    'index');
        Route::get('/variations',          'productVariation');
        Route::get('/{slug}',              'show');
        Route::get('/shop-sideBar',        'shopSideBar');
        Route::get('/category/{slug}',     'categoryWiseProduct');
        Route::get('/sub-category/{slug}', 'subCategoryWiseProduct');
    });
});

// Section route
Route::prefix('sections')->group(function () {
    Route::controller(SectionController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Campaign route
Route::prefix('campaigns')->group(function () {
    Route::controller(CampaignController::class)->group(function () {
        Route::get('/',         'index');
        Route::get('/products', 'campaignProductPrice');
        Route::get('/product/show/{campaignId}/{productId}', 'show');
    });
});

// ========================== Order Management system ==============

// Area route
Route::prefix('areas')->group(function () {
    Route::controller(AreaController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Zone route
Route::prefix('zones')->group(function () {
    Route::controller(ZoneController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// District route
Route::prefix('districts')->group(function () {
    Route::controller(DistrictController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Delivery gateway route
Route::prefix('delivery-gateway')->group(function () {
    Route::controller(DeliveryGatewayController::class)->group(function () {
        Route::get('/',           'index');
        Route::get('/price/{id}', 'deliveryPrice');
    });
});

// Payment gateway
Route::prefix('payment-gateway')->group(function () {
    Route::controller(PaymentGatewayController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Team route
Route::prefix('teams')->group(function () {
    Route::controller(TeamController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});

// Coupon route
Route::prefix('coupons')->group(function () {
    Route::controller(CouponController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/check', 'checkCouponCode');
    });
});

// ========================== Setting Management system ==============

// Setting route
Route::prefix('settings')->group(function () {
    Route::controller(SettingController::class)->group(function () {
        Route::get('/',     'index');
        Route::get('/{id}', 'show');
    });
});


if (Helper::getSettingValue("is_login_required")) {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('orders')->group(function () {
            Route::controller(OrderController::class)->group(function () {
                Route::get('/',     'index');
                Route::post('/',    'store');
                Route::get('/{id}', 'show');
            });
        });

        Route::post('logout', [AuthController::class, 'logout']);
    });
} else {
    // Routes without authentication
    Route::prefix('orders')->group(function () {
        Route::controller(OrderController::class)->group(function () {
            Route::get('/',     'index');
            Route::post('/',    'store');
            Route::get('/{id}', 'show');
        });
    });
}
