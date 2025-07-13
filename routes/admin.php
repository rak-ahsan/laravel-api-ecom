<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\WarrantyController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderFromController;
use App\Http\Controllers\Admin\OrderGuardController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RawMaterialController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\FreeDeliveryController;
use App\Http\Controllers\Admin\TimeScheduleController;
use App\Http\Controllers\Admin\UserCategoryController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\AttributeValueController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\DeliveryGatewayController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\SettingCategoryController;
use App\Http\Controllers\Admin\TermsAndConditionController;

Route::get('/clear', function () {
    Artisan::call('optimize:clear');

    return 'Success! Your are very lucky!';
});

// Auth route
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Dashboard route
    Route::get('dashboard', [DashboardController::class, 'dashboard']);

    // ================================== Start ACL management route ===========================================
    // Permission route
    Route::prefix('permissions')->group(function () {
        Route::controller(PermissionController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Role route
    Route::prefix('roles')->group(function () {
        Route::controller(RoleController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // User Category route
    Route::prefix('user-categories')->group(function () {
        Route::controller(UserCategoryController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('trash',                     'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // User route
    Route::prefix('users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/permission',               'userPermission');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });
    // ================================== End ACL management route =============================================


    // ================================== Start CMS management route ===========================================
    // Slider route
    Route::prefix('sliders')->group(function () {
        Route::controller(SliderController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Banner route
    Route::prefix('banners')->group(function () {
        Route::controller(BannerController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // About route
    Route::prefix('abouts')->group(function () {
        Route::controller(AboutController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Blog post route
    Route::prefix('blog-posts')->group(function () {
        Route::controller(BlogPostController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('trash',                     'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Contact route
    Route::prefix('contacts')->group(function () {
        Route::controller(ContactController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // FAQ route
    Route::prefix('faqs')->group(function () {
        Route::controller(FAQController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Social media route
    Route::prefix('social-medias')->group(function () {
        Route::controller(SocialMediaController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Privacy Policy route
    Route::prefix('privacy-policies')->group(function () {
        Route::controller(PrivacyPolicyController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Terms And Condition route
    Route::prefix('terms-and-conditions')->group(function () {
        Route::controller(TermsAndConditionController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });
    // ================================== End CMS management route ===========================================


    // ================================== Start product management route =====================================
    // Brand route
    Route::prefix('brands')->group(function () {
        Route::controller(BrandController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Category route
    Route::prefix('categories')->group(function () {
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Sub Category route
    Route::prefix('sub-categories')->group(function () {
        Route::controller(SubCategoryController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::get('/{id}',                     'getSubCategoryIdByCategoryId');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Attribute route
    Route::prefix('attributes')->group(function () {
        Route::controller(AttributeController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    Route::prefix('attribute-values')->group(function() {
        Route::controller(AttributeValueController::class)->group(function() {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Product route
    Route::prefix('products')->group(function () {
        Route::controller(ProductController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::get('/history/{id}',             'productHistory');
            Route::post('/update-status',           'updateStatus');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Review route
    Route::prefix('reviews')->group(function () {
        Route::controller(ReviewController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Sections route
    Route::prefix('sections')->group(function () {
        Route::controller(SectionController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Warranty route
    Route::prefix('warranties')->group(function () {
        Route::controller(WarrantyController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/permission',               'userPermission');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Tag route
    Route::prefix('tags')->group(function () {
        Route::controller(TagController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Campaign route
    Route::prefix('campaigns')->group(function () {
        Route::controller(CampaignController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });
    // ================================== End product management route =================================


    // ================================== Start purchase management route ==============================
    // Supplier route
    Route::prefix('suppliers')->group(function () {
        Route::controller(SupplierController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Purchase route
    Route::prefix('purchases')->group(function () {
        Route::controller(PurchaseController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
        });
    });
    // ================================== End purchase management route ================================


    // ================================== Start order management route =================================
    // Area route
    Route::prefix('areas')->group(function () {
        Route::controller(AreaController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // District route
    Route::prefix('districts')->group(function () {
        Route::controller(DistrictController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Zone route
    Route::prefix('zones')->group(function () {
        Route::controller(ZoneController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Status route
    Route::prefix('statuses')->group(function () {
        Route::controller(StatusController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // PaymentGateway route
    Route::prefix('payment-gateways')->group(function () {
        Route::controller(PaymentGatewayController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // DeliveryGateway route
    Route::prefix('delivery-gateways')->group(function () {
        Route::controller(DeliveryGatewayController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Coupon route
    Route::prefix('coupons')->group(function () {
        Route::controller(CouponController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Order From route
    Route::prefix('order-froms')->group(function () {
        Route::controller(OrderFromController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Order route
    Route::prefix('orders')->group(function() {
        Route::controller(OrderController::class)->group(function() {
            Route::get('/',                         'index');
            Route::get('/trash',                    'trashList');
            Route::post('',                         'store');
            Route::get('/prepared-by-list',         'preparedByList');
            Route::post('/prepared-by-restore',     'preparedByRestore');
            Route::get('/team/list',                'orderTeamList');
            Route::post('/prepared-by',             'preparedBy');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::get('/multiple/invoice',         'multipleInvoice');
            Route::get('/history/{id}',             'orderHistory');
            Route::delete('/{id}',                  'destroy');
            Route::post('/update-status',           'updateStatus');
            Route::post('/update-paid-status',      'updatePaidStatus');
            Route::post('/add-additional-cost',     'addAdditionCost');
            Route::post('/add-raw-material',        'addRawMaterial');
            Route::get("/locked-status/{id}",       'orderLockedStatus');
            Route::post("/locked/{id}",             'orderLocked');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Team member route
    Route::prefix('teams')->group(function () {
        Route::controller(TeamController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Free Delivery route
    Route::prefix('free-delivery')->group(function () {
        Route::controller(FreeDeliveryController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Raw Material route
    Route::prefix('raw-materials')->group(function () {
        Route::controller(RawMaterialController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Order Guard route
    Route::prefix('order-guards')->group(function () {
        Route::controller(OrderGuardController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
        });
    });

    // Time Schedules route
    Route::prefix('time-schedules')->group(function () {
        Route::controller(TimeScheduleController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
        });
    });

    // Order report
    Route::prefix('orders/reports')->group(function() {
        Route::controller(ReportController::class)->group(function() {
            Route::get('/',            'orderReport');
            Route::get('/monthly',     'orderReportMonthly');
            Route::get('/yearly',      'orderReportYearly');
            Route::get('/by-location', 'orderReportByLocation');
            Route::get('/by-selling',  'orderReportBySelling');
            Route::get('/by-customer', 'orderReportByCustomer');
        });
    });
    // ================================== End order management route ===================================


    // ================================== Start expense management route ===============================
    // Expense Categories route
    Route::prefix('expense-categories')->group(function () {
        Route::controller(ExpenseCategoryController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Expense route
    Route::prefix('expenses')->group(function () {
        Route::controller(ExpenseController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });
    // ================================== End expense management route =================================


    // ================================== Start setting management route ===============================
    // Setting Category route
    Route::prefix('setting-category')->group(function () {
        Route::controller(SettingCategoryController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });

    // Setting route
    Route::prefix('settings')->group(function () {
        Route::controller(SettingController::class)->group(function () {
            Route::get('/',                         'index');
            Route::post('/',                        'store');
            Route::get('/trash',                    'trashList');
            Route::get('/{id}',                     'show');
            Route::put('/{id}',                     'update');
            Route::delete('/{id}',                  'destroy');
            Route::put('/{id}/restore',             'restore');
            Route::delete('/{id}/permanent-delete', 'permanentDelete');
        });
    });
    // ================================== End setting management route =================================


    Route::post('logout', [AuthController::class, 'logout']);
});
