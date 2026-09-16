<?php

use App\Livewire\Cart;
use Livewire\Livewire;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\BaseController;
use App\Http\Controllers\Admin\LogsController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\TagsController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\NewsesController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Client\AboutController;
use App\Http\Controllers\Client\BlogsController;
use App\Http\Controllers\Admin\BannersController;
use App\Http\Controllers\Admin\CouponsController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SlidersController;
use App\Http\Controllers\Admin\BenefitsController;
use App\Http\Controllers\Admin\PartnersController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Admin\ActionLogController;
use App\Http\Controllers\Admin\DistrictsController;
use App\Http\Controllers\Admin\TextpagesController;
use App\Http\Controllers\Admin\AdminIndexController;
use App\Http\Controllers\Admin\ChangelogsController;
use App\Http\Controllers\Admin\InformationController;
use App\Http\Controllers\Admin\ConfigurationsController;
use App\Http\Controllers\Admin\PaymentGateAwayController;
use App\Http\Controllers\Admin\NewsCategoriesController;
use App\Http\Controllers\Admin\ProductCategoriesController;
use App\Http\Controllers\CheckoutPaymentController;
use App\Http\Controllers\PaymentReturnController;
use App\Http\Controllers\PaymentStatusController;
use App\Http\Controllers\PaymentWebhookController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use App\Http\Controllers\Client\ProductController as ClientProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/api', [IndexController::class, 'index']);


Route::post('/ajax-add-cart', [ProfileController::class, 'addToCart']);
Route::post('/ajax-remove-cart', [ProfileController::class, 'removeFromCart']);

Route::get('/mini-cart/html', function () {
    $cart = \Cart::getContent();
    return view('partials.mini-cart', compact('cart'))->render();
});

Route::prefix(LaravelLocalization::setLocale())->middleware('localeSessionRedirect', 'localizationRedirect', 'localeViewPath')->group(function () {

    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/livewire/update', $handle);
    });

    Route::get("/", [HomeController::class, 'index'])->name('clientHome');
    Route::get("/contact", [ContactController::class, 'index'])->name('clientContact');
    Route::get("/faq", [ContactController::class, 'faq'])->name('clientFaq');
    Route::get("/about", [AboutController::class, 'index'])->name('clientAbout');
    Route::get("/terms-and-conditions", [AboutController::class, 'terms'])->name('clientTerms');
    Route::get("/products", [ClientProductController::class, 'index'])->name('clientProducts');
    Route::get("/product/{product}", [ClientProductController::class, 'inner'])->name('clientProductsInner');
    Route::get("/blogs", [BlogsController::class, 'index'])->name('clientBlogs');
    Route::get("/blog/{blog}", [BlogsController::class, 'inner'])->name('clientBlogsInner');
    Route::get("/cart", [ProfileController::class, 'cart'])->name('clientCart');


    Route::post("/get-product-info", [ClientProductController::class, 'getProductInfo'])->name('getProductInfo');

    Route::group(['middleware' => ['guest']], function () {
        Route::get('/sign-up', [ProfileController::class, 'register'])->name('signup');
        Route::get('/sign-in', [ProfileController::class, 'login'])->name('signin');
        Route::get('/forgot-password', [ProfileController::class, 'reset'])->name('clientReset');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get("/wishlist", [ProfileController::class, 'wishlist'])->name('clientWishlist');
        Route::get("/account", [ProfileController::class, 'account'])->name('myAccount');
        Route::post("/account", [ProfileController::class, 'update'])->name('updateSettings');
        Route::get("/checkout", [ProfileController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/payment/initiate', [CheckoutPaymentController::class, 'initiate'])->name('checkout.payment.initiate');
        Route::get('/payments/status/{payment}', [PaymentStatusController::class, 'show'])->name('payments.status');

        Route::post('/ajax-add-wishlist', [ProfileController::class, 'addToWishlist']);
        Route::post('/ajax-remove-wishlist', [ProfileController::class, 'removeFromWishlist']);
    });
});

Route::get('/payments/return/{provider}', [PaymentReturnController::class, 'handleReturn'])->name('payments.return');
Route::post('/payments/webhook/{provider}', [PaymentWebhookController::class, 'handleWebhook'])->name('payments.webhook');

Route::get('/admin/login', [LoginController::class, 'index'])->middleware('AdminLogin')->name('LoginPageAdmin');
Route::post('/admin/singin', [LoginController::class, 'singin'])->middleware('AdminLogin')->name('LoginAdmin');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('LogoutAdmin');

Route::middleware(['admin', 'check_permission'])->group(function () {

    Route::prefix('admin')->group(function () {

        // ადმინისტრატორის პანელის მთავარი გვერდი
        Route::get('/', [AdminIndexController::class, 'index'])->name('AdminMainPage');

        /*
         * ყველა მოდულისათვის საერთო მეთოდები
         */

        // integer ტიპის ისეთი ველების განახლება, რომელთა შესაძლო მნიშვნელობებიცაა 0 და 1
        Route::post('status', [BaseController::class, 'status'])->name('Status');
        // წაშლა
        Route::post('/remove', [BaseController::class, 'remove'])->name('Remove');
        // სტატუსის შეცვლა რამოდენიმე ელემენტზე ერთდროულად ან მათი წაშლა
        Route::post('/multi', [BaseController::class, 'multi'])->name('Multi');
        // თანმიმდევრობის შეცვლა ჩამონათვალის გვერდზე
        Route::post('/ordering', [BaseController::class, 'ordering'])->name('Ordering');
        // მიმაგრებული ფაილის წაშლა და შესაბამისი ველის მნიშვნელობად null
        Route::post('/remove_file', [BaseController::class, 'remove_file'])->name('RemoveFile');
        // ფოტოს წაშლა გალერიიდან
        Route::post('/remove_image_from_gallery', [BaseController::class, 'remove_image_from_gallery'])->name('RemoveImageFromGallery');
        // ვიდეოს წაშლა გალერიიდან
        Route::post('/remove_video_from_gallery', [BaseController::class, 'remove_video_from_gallery'])->name('RemoveVideoFromGallery');


        Route::get("/payment-gateaway", [PaymentGateAwayController::class, 'index'])->name('payment-gateaway');
        Route::post("/payment-gateaway/update", [PaymentGateAwayController::class, 'update'])->name('payment-gateaway.update');

        // სლაიდერი
        Route::prefix('sliders')->group(function () {
            Route::controller(SlidersController::class)->group(function () {
                Route::get('/', 'index')->name('Sliders');
                Route::get('/add', 'create')->name('AddSliders');
                Route::post('create', 'store')->name('StoreSliders');
                Route::get('/edit/{id}', 'edit')->name('EditSliders');
                Route::post('update/{id}', 'update')->name('UpdateSliders');
            });
        });

        Route::prefix('benefits')->group(function () {
            Route::controller(BenefitsController::class)->group(function () {
                Route::get('/', 'index')->name('Benefits');
                Route::get('/add', 'create')->name('AddBenefits');
                Route::post('create', 'store')->name('StoreBenefits');
                Route::get('/edit/{id}', 'edit')->name('EditBenefits');
                Route::post('update/{id}', 'update')->name('UpdateBenefits');
            });
        });

        Route::prefix('banners')->group(function () {
            Route::controller(BannersController::class)->group(function () {
                Route::get('/', 'index')->name('Banners');
                Route::get('/add', 'create')->name('AddBanners');
                Route::post('create', 'store')->name('StoreBanners');
                Route::get('/edit/{id}', 'edit')->name('EditBanners');
                Route::post('update/{id}', 'update')->name('UpdateBanners');
            });
        });

        Route::prefix('actors')->group(function () {
            Route::controller(BannersController::class)->group(function () {
                Route::get('/', 'index')->name('Actors');
                Route::get('/add', 'create')->name('AddActors');
                Route::post('create', 'store')->name('StoreActors');
                Route::get('/edit/{id}', 'edit')->name('EditActors');
                Route::post('update/{id}', 'update')->name('UpdateActors');
            });
        });

        Route::prefix('partners')->group(function () {
            Route::controller(PartnersController::class)->group(function () {
                Route::get('/', 'index')->name('Partners');
                Route::get('/add', 'create')->name('AddPartners');
                Route::post('create', 'store')->name('StorePartners');
                Route::get('/edit/{id}', 'edit')->name('EditPartners');
                Route::post('update/{id}', 'update')->name('UpdatePartners');
            });
        });

        Route::prefix('products')->group(function () {

            Route::get('/', [ProductsController::class, 'index'])->name('ProductsIndex');

            // პროდუქტი
            Route::prefix('/product')->group(function () {
                Route::controller(ProductController::class)->group(function () {
                    Route::get('/', 'index')->name('Products');
                    Route::get('/add', 'create')->name('AddProducts');
                    Route::post('create', 'store')->name('StoreProducts');
                    Route::get('/edit/{id}/{page?}', 'edit')->name('EditProducts');
                    Route::post('update/{id}', 'update')->name('UpdateProducts');
                    Route::post('/remove_color_images', 'RemoveColorImages')->name('RemoveColorImageProducts');
                    Route::get('/search', 'search')->name('SearchProducts');
                    Route::post('/livesearch', 'live_search')->name('LiveSearchProducts');
                    Route::get('/import', 'import')->name('ImportProducts');
                    Route::post('/import', 'upload')->name('UploadProducts');
                });
            });

            // კატეგორიები
            Route::prefix('/categories')->group(function () {
                Route::controller(ProductCategoriesController::class)->group(function () {
                    Route::get('/', 'index')->name('ProductCategories');
                    Route::get('/add', 'create')->name('AddProductCategories');
                    Route::post('create', 'store')->name('StoreProductCategories');
                    Route::get('/edit/{id}', 'edit')->name('EditProductCategories');
                    Route::post('update/{id}', 'update')->name('UpdateProductCategories');
                });
            });

            // ბრენდები
            Route::prefix('/brands')->group(function () {
                Route::controller(BrandsController::class)->group(function () {
                    Route::get('/', 'index')->name('Brands');
                    Route::get('/add', 'create')->name('AddBrands');
                    Route::post('create', 'store')->name('StoreBrands');
                    Route::get('/edit/{id}', 'edit')->name('EditBrands');
                    Route::post('update/{id}', 'update')->name('UpdateBrands');
                });
            });
        });

        Route::prefix('newses')->group(function () {

            Route::get('/', [NewsesController::class, 'index'])->name('Newses');

            // სიხლეები
            Route::prefix('/news')->group(function () {
                Route::controller(NewsController::class)->group(function () {
                    Route::get('/', 'index')->name('News');
                    Route::get('/add', 'create')->name('AddNews');
                    Route::post('create', 'store')->name('StoreNews');
                    Route::get('/edit/{id}', 'edit')->name('EditNews');
                    Route::post('update/{id}', 'update')->name('UpdateNews');
                });
            });

            // სიხლეების კატეგორიები
            Route::prefix('/categories')->group(function () {
                Route::controller(NewsCategoriesController::class)->group(function () {
                    Route::get('/', 'index')->name('NewsCategories');
                    Route::get('/add', 'create')->name('AddNewsCategories');
                    Route::post('create', 'store')->name('StoreNewsCategories');
                    Route::get('/edit/{id}', 'edit')->name('EditNewsCategories');
                    Route::post('update/{id}', 'update')->name('UpdateNewsCategories');
                });
            });

            // ტეგები
            Route::prefix('tags')->group(function () {
                Route::controller(TagsController::class)->group(function () {
                    Route::get('/', 'index')->name('Tags');
                    Route::get('/add', 'create')->name('AddTags');
                    Route::post('create', 'store')->name('StoreTags');
                    Route::get('/edit/{id}', 'edit')->name('EditTags');
                    Route::post('update/{id}', 'update')->name('UpdateTags');
                });
            });
        });

        // ტექსტური გვერდები
        Route::prefix('textpages')->group(function () {
            Route::controller(TextpagesController::class)->group(function () {
                Route::get('/', 'index')->name('Textpages');
                Route::get('/add', 'create')->name('AddTextpages');
                Route::post('create', 'store')->name('StoreTextpages');
                Route::get('/edit/{id}', 'edit')->name('EditTextpages');
                Route::post('update/{id}', 'update')->name('UpdateTextpages');
            });
        });

        // რეგისტრირებული მომხმარებლები
        Route::prefix('users')->group(function () {
            Route::controller(UsersController::class)->group(function () {
                Route::get('/', 'index')->name('Users');
                Route::get('/filter', 'filter')->name('FilterUsers');
                Route::get('/export', 'export')->name('ExportUsers');
            });
        });

        // შეკვეთები
        Route::prefix('/orders')->group(function () {
            Route::controller(OrdersController::class)->group(function () {
                Route::get('/', 'index')->name('Orders');
                Route::get('/details/{id}', 'details')->name('OrderDetails');
                Route::get('/export', 'export')->name('ExportOrders');
                Route::post('status', 'status')->name('StatusOrders');
                Route::get('/generate_pdf/{id}', 'generate_pdf')->name('GeneratePdfOrders');
            });
        });

        Route::prefix('/faq')->group(function () {
            Route::get('/', [FaqController::class, 'index'])->name('Faq');
            Route::get('/add', [FaqController::class, 'create'])->name('AddFaq');
            Route::post('create', [FaqController::class, 'store'])->name('StoreFaq');
            Route::get('/edit/{id}', [FaqController::class, 'edit'])->name('EditFaq');
            Route::post('update/{id}', [FaqController::class, 'update'])->name('UpdateFaq');
        });

        // ამ გვერდებზე შესვლის უფლება აქვს მხოლოდ სუპერადმინს
        Route::middleware('check_if_super')->group(function () {

            // ფასდაკლებები
            Route::prefix('/coupons')->group(function () {
                Route::controller(CouponsController::class)->group(function () {
                    Route::get('/', 'index')->name('Coupons');
                    Route::post('remove', 'remove')->name('RemoveCoupons');
                    Route::get('/import', 'import')->name('ImportCoupons');
                    Route::post('/import', 'upload')->name('UploadCoupons');
                });
            });

            // უბნები
            Route::prefix('districts')->group(function () {
                Route::controller(DistrictsController::class)->group(function () {
                    Route::get('/', 'index')->name('Districts');
                    Route::get('/add', 'create')->name('AddDistricts');
                    Route::post('create', 'store')->name('StoreDistricts');
                    Route::get('/edit/{id}', 'edit')->name('EditDistricts');
                    Route::post('update/{id}', 'update')->name('UpdateDistricts');
                });
            });

            // საკონტაქტო ინფორმაციის გვერდი
            Route::prefix('informations')->group(function () {
                Route::controller(InformationController::class)->group(function () {
                    Route::get('/', 'edit')->name('EditInformations');
                    Route::post('/update/{id}', 'update')->name('UpdateInformations');
                });
            });

            // ადმინისტრატორები
            Route::prefix('admins')->group(function () {
                Route::controller(AdminsController::class)->group(function () {
                    Route::get('/', 'index')->name('Admins');
                    Route::get('/add', 'create')->name('AddAdmins');
                    Route::post('create', 'store')->name('StoreAdmins');
                    Route::get('/edit/{id}', 'edit')->name('EditAdmins');
                    Route::post('update/{id}', 'update')->name('UpdateAdmins');
                    Route::post('remove', 'remove')->name('RemoveAdmins');
                });
            });

            // ჟურნალი
            Route::prefix('logs')->group(function () {

                Route::get('/', [LogsController::class, 'index'])->name('Logs');

                Route::prefix('changelog')->group(function () {
                    Route::get('/', [ChangelogsController::class, 'index'])->name('Changelogs');
                });

                Route::prefix('operationlog')->group(function () {
                    Route::get('/', [ActionLogController::class, 'index'])->name('Operationlogs');
                });
            });

            // საიტის კონფიგურაციული პარამეტრები
            Route::prefix('configuration')->group(function () {
                Route::controller(ConfigurationsController::class)->group(function () {
                    Route::get('/', 'edit')->name('EditConfigurations');
                    Route::post('/update/{id}', 'update')->name('UpdateConfigurations');
                    Route::get('/remove_cache_key/{key}', 'remove_cache_key')->name('RemoveCacheKeyConfigurations');
                });
            });
        });
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/auth.php';
