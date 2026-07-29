<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

use App\Http\Controllers\Admin\AdsVideoController;
use App\Http\Controllers\Admin\AppFeedbackController;
use App\Http\Controllers\Admin\AstrologerCategoryController;
use App\Http\Controllers\Admin\AstrologerController;
use App\Http\Controllers\Admin\AstroMallController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlockAstrologerController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CallHistoryReportController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ChatHistoryReportController;
use App\Http\Controllers\Admin\ColorSchemeController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DailyHoroScopeController;
use App\Http\Controllers\Admin\DailyHoroscopeInsightController;
use App\Http\Controllers\Admin\DarkModeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DefaultImageController;
use App\Http\Controllers\Admin\EarningController;
use App\Http\Controllers\Admin\FCMController;
use App\Http\Controllers\Admin\GiftController;
use App\Http\Controllers\Admin\HelpSupportController;
use App\Http\Controllers\Admin\HororScopeSignController;
use App\Http\Controllers\Admin\HoroscopeController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderRequestController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PartnerWiseEarningController;
use App\Http\Controllers\Admin\RechargeController;
use App\Http\Controllers\Admin\ReportBlockController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportRequestController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SkillController;
use App\Http\Controllers\Admin\SystemFlagController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TeamRoleController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\WithdrawlController;
use App\Http\Controllers\Admin\BroadcastController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;

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

Route::get('dark-mode-switcher', [DarkModeController::class, 'switch'])->name('dark-mode-switcher');
Route::get('color-scheme-switcher/{color_scheme}', [ColorSchemeController::class, 'switch']
)->name('color-scheme-switcher');

// Route to redirect to Google's OAuth page
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');

// Route to handle the callback from Google 
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::get('command', function() {
    // exit('asdfasd');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');

    return false;
});

Route::get('privacy-policy-1', function () {
    $data = DB::table('static_pages')->where('slug', 'privacy_policy')->first();

    return view('privacypolicy', compact('data'));
});

Route::get('terms-condition-1', function () {

    $data = DB::table('static_pages')->where('slug', 'terms_condition')->first();

    return view('privacypolicy', compact('data'));

});

Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'loginView')->name('login.index');
    Route::get('delete-account', 'deleteView')->name('delete-account');
    // Route::get('privacypolicy', 'privacy_policy')->name('privacypolicy');
    Route::get('termsCondition', 'termsCondition')->name('termsCondition');
    Route::post('login', 'login')->name('login');
    Route::post('delete-account', 'post_delete_account')->name('post-delete-account');
});

Route::get('get-session/{token}', [SessionController::class, 'storeSession'])->name('get-session');

//Controller

Route::get('astrologer-enquiry', [TestimonialController::class, 'getAstrologerEnquiries'])->name('astrologer.enquiry.list');

Route::get('ads-enquiry', [TestimonialController::class, 'getAds'])->name('ads');
Route::post('ads-enquiry-status', [TestimonialController::class, 'adsEnquiryStatusApi'])->name('adsEnquiryStatusApi');

Route::get('testimonials', [TestimonialController::class, 'getTestimonial'])->name('testimonials');
Route::post('add-testimonial', [TestimonialController::class, 'addTestimonialApi'])->name('addTestimonialApi');
Route::post('testimonialStatusApi', [TestimonialController::class, 'testimonialStatusApi'])->name('testimonialStatusApi');
Route::delete('testimonials', [TestimonialController::class, 'deleteTestimonial'])->name('deleteTestimonial');
Route::post('editTestimonialApi', [TestimonialController::class, 'editTestimonialApi'])->name('editTestimonialApi');

Route::get('broadcasts', [BroadcastController::class, 'getBroadcast'])->name('broadcasts');
Route::post('add-broadcast', [BroadcastController::class, 'addBroadcastApi'])->name('addBroadcastApi');
Route::post('updated-broadcast-status', [BroadcastController::class, 'broadcastStatusApi'])->name('broadcastStatusApi');
Route::delete('delete-broadcasts', [BroadcastController::class, 'deleteBroadcast'])->name('deleteBroadcast');
Route::post('update-broadcast', [BroadcastController::class, 'editBroadcastApi'])->name('editBroadcastApi');

Route::get('languages', [LanguageController::class, 'getLanguage'])->name('languages');
Route::post('add-language', [LanguageController::class, 'addLanguageApi'])->name('addLanguageApi');
Route::post('languageStatusApi', [LanguageController::class, 'languageStatusApi'])->name('languageStatusApi');
Route::delete('languages', [LanguageController::class, 'deleteLanguage'])->name('deleteLanguage');
Route::post('editLanguageApi', [LanguageController::class, 'editLanguageApi'])->name('editLanguageApi');

Route::get('skills', [SkillController::class, 'addSkill'])->name('skills');
Route::post('addSkillApi', [SkillController::class, 'addSkillApi'])->name('addSkillApi');
Route::get('skills', [SkillController::class, 'skill'])->name('skills');
Route::post('skillStatusApi', [SkillController::class, 'skillStatusApi'])->name('skillStatusApi');
Route::delete('skills', [SkillController::class, 'deleteSkill'])->name('deleteSkill');
Route::delete('gifts', [GiftController::class, 'deleteGift'])->name('deleteGift');
Route::post('skills', [SkillController::class, 'getGift'])->name('skills');

Route::get('advisorCategories', [AstrologerCategoryController::class, 'addAstrolgerCategory'])->name('astrologerCategories');
Route::post('addAdvisorCategoryApi', [AstrologerCategoryController::class, 'addAstrolgerCategoryApi'])->name('addAstrolgerCategoryApi');
Route::delete('deleteUser', [CustomerController::class, 'deleteUser'])->name('deleteUser');

//Edit

Route::post('editSkillApi', [SkillController::class, 'editSkillApi'])->name('editSkillApi');
Route::post('editGiftApi', [GiftController::class, 'editGiftApi'])->name('editGiftApi');
Route::post('editBannerApi', [BannerController::class, 'editBannerApi'])->name('editBannerApi');
Route::post('editBlogApi', [BlogController::class, 'editBlogApi'])->name('editBlogApi');
Route::post('editHororScopeSignApi', [HororScopeSignController::class, 'editHororScopeSignApi'])->name('editHororScopeSignApi');
Route::post('editAstrolgerCategoryApi', [AstrologerCategoryController::class, 'editAstrolgerCategoryApi'])->name('editAstrologerCategory');
Route::post('editCouponApi', [CouponController::class, 'editCouponApi'])->name('editCouponApi');
Route::post('editNotificationApi', [NotificationController::class, 'editNotificationApi'])->name('editNotificationApi');
Route::post('editUserApi', [CustomerController::class, 'editUserApi'])->name('editUserApi');
Route::post('editProductApi', [AstroMallController::class, 'editProductApi'])->name('editProductApi');
Route::post('editAstroMallApi', [AstroMallController::class, 'editAstroMallApi'])->name('editAstroMallApi');
Route::post('editAdsVideoApi', [AdsVideoController::class, 'editAdsVideoApi'])->name('editAdsVideoApi');
Route::post('editNewsApi', [NewsController::class, 'editNews'])->name('editNewsApi');
Route::post('verifiedAdvisorApi', [AstrologerController::class, 'verifiedAstrologerApi'])->name('verifiedAstrologerApi');
Route::post('skillStatusApi', [SkillController::class, 'skillStatusApi'])->name('skillStatusApi');
Route::post('giftStatusApi', [GiftController::class, 'giftStatusApi'])->name('giftStatusApi');
Route::post('couponStatusApi', [CouponController::class, 'couponStatusApi'])->name('couponStatusApi');
Route::post('bannerStatusApi', [BannerController::class, 'bannerStatusApi'])->name('bannerStatusApi');
Route::post('horoScopeStatusApi', [HororScopeSignController::class, 'horoScopeStatusApi'])->name('horoScopeStatusApi');
Route::post('notifcationStatusApi', [NotificationController::class, 'notifcationStatusApi'])->name('notifcationStatusApi');
Route::post('blogStatusApi', [BlogController::class, 'blogStatusApi'])->name('blogStatusApi');
Route::post('astroMallStatusApi', [AstroMallController::class, 'astroMallStatusApi'])->name('astroMallStatusApi');
Route::post('productStatusApi', [AstroMallController::class, 'productStatusApi'])->name('productStatusApi');
Route::post('videoStatusApi', [AdsVideoController::class, 'videoStatusApi'])->name('videoStatusApi');
Route::post('newsStatusApi', [NewsController::class, 'newsStatusApi'])->name('newsStatusApi');
Route::post('astrologyCategoryStatusApi', [AstrologerCategoryController::class, 'astrologyCategoryStatusApi'])->name('astrologyCategoryStatusApi');

Route::get('gifts', [GiftController::class, 'addGift'])->name('gifts');
Route::post('addGiftApi', [GiftController::class, 'addGiftApi'])->name('addGiftApi');
Route::get('report', [ReportController::class, 'addReport'])->name('report');
Route::post('reportTypes', [ReportController::class, 'getReport'])->name('reportTypes');
Route::post('reportStatus', [ReportController::class, 'reportTypeStatusApi'])->name('reportStatusApi');
Route::get('reportTypes/{page}', [ReportController::class, 'setReportpage'])->name('setReportpage');
Route::post('editReport', [ReportController::class, 'editReportApi'])->name('editReportApi');
Route::post('addReportApi', [ReportController::class, 'addReportApi'])->name('addReportApi');
Route::get('horoscopeSigns', [HororScopeSignController::class, 'addHororScopeSign'])->name('horoscopeSigns');
Route::post('addHororScopeSignApi', [HororScopeSignController::class, 'addHororScopeSignApi'])->name('addHororScopeSignApi');
Route::get('astroMall', [AstroMallController::class, 'addAstroMall'])->name('astroMall');
Route::post('addAstroMallApi', [AstroMallController::class, 'addAstroMallApi'])->name('addAstroMallApi');
Route::get('coupon-list', [CouponController::class, 'addCoupon'])->name('coupon-list');
Route::post('addCouponApi', [CouponController::class, 'addCouponApi'])->name('addCouponApi');
Route::post('addBannerApi', [BannerController::class, 'addBannerApi'])->name('addBannerApi');
Route::get('notifications', [NotificationController::class, 'addNotification'])->name('notifications');
Route::post('addNotificationApi', [NotificationController::class, 'addNotificationApi'])->name('addNotificationApi');
Route::get('blogs', [BlogController::class, 'addBlog'])->name('blogs');
Route::post('blogs', [BlogController::class, 'getBlog'])->name('blogs');
Route::post('addBlogApi', [BlogController::class, 'addBlogApi'])->name('addBlogApi');
Route::get('adsVideos', [AdsVideoController::class, 'addAdsVideo'])->name('adsVideos');
Route::post('addAdsVideoApi', [AdsVideoController::class, 'addAdsVideoApi'])->name('addAdsVideoApi');
Route::post('addNewsApi', [NewsController::class, 'addNewsApi'])->name('addNewsApi');
Route::post('addProductApi', [AstroMallController::class, 'addProductApi'])->name('addProductApi');
Route::post('addProductDetailApi', [AstroMallController::class, 'addProductDetailApi'])->name('addProductDetailApi');
Route::get('customers/add', [CustomerController::class, 'addUser'])->name('add-customer');
Route::get('dailyHoroscope/add', [DailyHoroScopeController::class, 'redirectAddDailyHoroscope'])->name('add-daily-horoscope');
Route::get('horoscope/add', [HoroscopeController::class, 'redirectAddHoroscope'])->name('add-horoscope');
Route::post('addUserApi', [CustomerController::class, 'addUserApi'])->name('addUserApi');
Route::post('dashboard', [DashboardController::class, 'getDashboard'])->name('getDashboard');
Route::get('commissions', [CommissionController::class, 'addCommission'])->name('commissions');
Route::post('addCommissionApi', [CommissionController::class, 'addCommissionApi'])->name('addCommissionApi');

// Route::group(['middleware'=>'web'],function(){
Route::group(['middleware' => ['web']], function () {
    // Route::get('dashboard', [DashboardController::class, 'getDashboard'])->name('dashboard');
    // });

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('editProfileApi', [AuthController::class, 'editProfileApi'])->name('editProfileApi');
    Route::get('editProfile', [AuthController::class, 'editProfile'])->name('editProfile');
    Route::post('changePassword', [AuthController::class, 'changePassword'])->name('changePassword');
    Route::controller(PageController::class)->group(function () {
        Route::get('dashboard', [DashboardController::class, 'getDashboard'])->name('dashboard');
        Route::get('dashboard-overview-2-page', 'dashboardOverview2')->name('dashboard-overview-2');
        Route::get('dashboard-overview-3-page', 'dashboardOverview3')->name('dashboard-overview-3');
        Route::get('dashboard-overview-4-page', 'dashboardOverview4')->name('dashboard-overview-4');
        Route::get('categories-page', 'categories')->name('categories');
        Route::get('add-product', 'addProduct')->name('add-product');
        Route::get('product-list-page', 'productList')->name('product-list');
        Route::get('product-grid-page', 'productGrid')->name('product-grid');
        Route::get('transaction-list-page', 'transactionList')->name('transaction-list');
        Route::get('transaction-detail-page', 'transactionDetail')->name('transaction-detail');
        Route::get('seller-list-page', 'sellerList')->name('seller-list');
        Route::get('seller-detail-page', 'sellerDetail')->name('seller-detail');
        Route::get('reviews-page', 'reviews')->name('reviews');
        Route::get('inbox-page', 'inbox')->name('inbox');
        Route::get('file-manager-page', 'fileManager')->name('file-manager');
        Route::get('point-of-sale-page', 'pointOfSale')->name('point-of-sale');
        Route::get('chat-page', 'chat')->name('chat');
        Route::get('post-page', 'post')->name('post');
        Route::get('calendar-page', 'calendar')->name('calendar');
        Route::get('crud-data-list-page', 'crudDataList')->name('crud-data-list');
        Route::get('crud-form-page', 'crudForm')->name('crud-form');
        Route::get('users-layout-1-page', 'usersLayout1')->name('users-layout-1');
        Route::get('users-layout-2-page', 'usersLayout2')->name('users-layout-2');
        Route::get('users-layout-3-page', 'usersLayout3')->name('users-layout-3');
        Route::get('profile-overview-1-page', 'profileOverview1')->name('profile-overview-1');
        Route::get('profile-overview-2-page', 'profileOverview2')->name('profile-overview-2');
        Route::get('profile-overview-3-page', 'profileOverview3')->name('profile-overview-3');
        Route::get('wizard-layout-1-page', 'wizardLayout1')->name('wizard-layout-1');
        Route::get('wizard-layout-2-page', 'wizardLayout2')->name('wizard-layout-2');
        Route::get('wizard-layout-3-page', 'wizardLayout3')->name('wizard-layout-3');
        Route::get('blog-layout-1-page', 'blogLayout1')->name('blog-layout-1');
        Route::get('blog-layout-2-page', 'blogLayout2')->name('blog-layout-2');
        Route::get('blog-layout-3-page', 'blogLayout3')->name('blog-layout-3');
        Route::get('pricing-layout-1-page', 'pricingLayout1')->name('pricing-layout-1');
        Route::get('pricing-layout-2-page', 'pricingLayout2')->name('pricing-layout-2');
        Route::get('invoice-layout-1-page', 'invoiceLayout1')->name('invoice-layout-1');
        Route::get('invoice-layout-2-page', 'invoiceLayout2')->name('invoice-layout-2');
        Route::get('faq-layout-1-page', 'faqLayout1')->name('faq-layout-1');
        Route::get('faq-layout-2-page', 'faqLayout2')->name('faq-layout-2');
        Route::get('faq-layout-3-page', 'faqLayout3')->name('faq-layout-3');
        Route::get('login-page', 'loginAdmin')->name('loginAdmin');
        Route::get('/', 'loginAdmin')->name('loginAdmin');
        Route::get('register-page', 'register')->name('register');
        Route::get('error-page-page', 'errorPage')->name('error-page');
        Route::get('update-profile-page', 'updateProfile')->name('update-profile');
        Route::get('change-password-page', 'changePassword')->name('change-password');
        Route::get('regular-table-page', 'regularTable')->name('regular-table');
        Route::get('tabulator-page', 'tabulator')->name('tabulator');
        Route::get('modal-page', 'modal')->name('modal');
        Route::get('slide-over-page', 'slideOver')->name('slide-over');
        Route::get('notification-page', 'notification')->name('notification');
        Route::get('tab-page', 'tab')->name('tab');
        Route::get('accordion-page', 'accordion')->name('accordion');
        Route::get('button-page', 'button')->name('button');
        Route::get('alert-page', 'alert')->name('alert');
        Route::get('progress-bar-page', 'progressBar')->name('progress-bar');
        Route::get('tooltip-page', 'tooltip')->name('tooltip');
        Route::get('dropdown-page', 'dropdown')->name('dropdown');
        Route::get('typography-page', 'typography')->name('typography');
        Route::get('icon-page', 'icon')->name('icon');
        Route::get('loading-icon-page', 'loadingIcon')->name('loading-icon');
        Route::get('regular-form-page', 'regularForm')->name('regular-form');
        Route::get('datepicker-page', 'datepicker')->name('datepicker');
        Route::get('tom-select-page', 'tomSelect')->name('tom-select');
        Route::get('file-upload-page', 'fileUpload')->name('file-upload');
        Route::get('wysiwyg-editor-classic', 'wysiwygEditorClassic')->name('wysiwyg-editor-classic');
        Route::get('wysiwyg-editor-inline', 'wysiwygEditorInline')->name('wysiwyg-editor-inline');
        Route::get('wysiwyg-editor-balloon', 'wysiwygEditorBalloon')->name('wysiwyg-editor-balloon');
        Route::get('wysiwyg-editor-balloon-block', 'wysiwygEditorBalloonBlock')->name('wysiwyg-editor-balloon-block');
        Route::get('wysiwyg-editor-document', 'wysiwygEditorDocument')->name('wysiwyg-editor-document');
        Route::get('validation-page', 'validation')->name('validation');
        Route::get('chart-page', 'chart')->name('chart');
        Route::get('slider-page', 'slider')->name('slider');
        Route::get('image-zoom-page', 'imageZoom')->name('image-zoom');
        Route::get('add-customer', 'addCustomer')->name('add-customer');
        Route::get('customer-detail', 'customerDetail')->name('customer-detail');
        Route::get('advisors', 'astrologerList')->name('astrologers');
        Route::get('advisors', 'astrologerDetail')->name('astrologer-detail');
        Route::get('add-advisor', 'addAstrologer')->name('add-astrologer');
        Route::get('skills', 'skillList')->name('skills');
        Route::get('add-skill', 'addSkill')->name('add-skill');
        Route::get('gifts', 'giftList')->name('gifts');
        Route::get('commissions', 'commissionList')->name('commissions');
        Route::get('advisorCategories', 'advisorCategoryList')->name('astrologerCategories');
        Route::get('horoscopeSigns', 'hororScopeSignList')->name('horoscopeSigns');
        Route::get('coupon-list', 'couponList')->name('coupon-list');
        Route::get('banners', 'bannerList')->name('banners');
        Route::post('updatePages', 'updatePages')->name('updatePages');
        Route::get('about_us', 'about_us')->name('about_us');
        Route::get('privacy_policy', 'privacy_policy')->name('privacy_policy');
        Route::get('terms_condition', 'terms_condition')->name('terms_condition');
        Route::get('report', 'report')->name('report');
        Route::get('notifications', 'notificationList')->name('notifications');
        Route::get('user-notification-list', 'userNotificationList')->name('user-notification-list');
        Route::get('permission', 'permission')->name('permission');
        Route::get('commission-type', 'commissionType')->name('commission-type');
        Route::get('help-support', 'helpSupport')->name('help-support');
        Route::get('help-support-queston-answer', 'helpSupportQuestonAnswer')->name('help-support-queston-answer');
        Route::get('setting', 'systemFlag')->name('setting');
        Route::get('blogs', 'blogList')->name('blogs');
        Route::get('helpSupport', 'helpSupport')->name('helpSupport');
        Route::get('add-blog', 'addBlog')->name('add-blog');
        Route::get('blog-detail', 'blogDetail')->name('blog-detail');
        Route::get('astroMall', 'astromall')->name('astroMall');
        Route::get('adsVideos', 'adsVideo')->name('adsVideos');
        Route::get('tickets', 'ticket')->name('tickets');
        Route::get('edit-blog', 'editBlog')->name('edit-blog');
        Route::get('products', 'product')->name('products');
        Route::get('edit-customer', 'editCustomer')->name('edit-customer');
        Route::get('edit-product', 'editProduct')->name('edit-product');
        Route::get('callHistory', 'callHistory')->name('callHistory');
        Route::get('chat', 'chat')->name('chat');
        Route::get('reportBlock', 'reportBlock')->name('reportBlock');
        Route::get('product-detail', 'productDetail')->name('product-detail');
        Route::get('dailyHoroscope', 'dailyHoroscope')->name('dailyHoroscope');
        Route::get('withdrawalRequests', 'withdrawlRequest')->name('withdrawalRequests');
        //Get data Controller
        Route::get('skills', [SkillController::class, 'getSkill'])->name('skills');
        Route::get('advisorReview', [ReportBlockController::class, 'getReportBlock'])->name('astrologerReview');
        Route::get('blockAdvisor', [BlockAstrologerController::class, 'getBlockAstrologer'])->name('blockAstrologer');
        Route::post('blockAdvisor', [BlockAstrologerController::class, 'getBlockAstrologer'])->name('blockAstrologer');
        Route::get('gifts', [GiftController::class, 'getGift'])->name('gifts');
        Route::post('gifts', [GiftController::class, 'getGift'])->name('gifts');
        Route::get('tickets', [TicketController::class, 'getTicket'])->name('tickets');

        Route::get('horoscopeSigns', [HororScopeSignController::class, 'getHororScopeSign'])->name('horoscopeSigns');
        Route::get('productCategories', [AstroMallController::class, 'getastroMall'])->name('productCategories');
        Route::post('productCategories', [AstroMallController::class, 'getastroMall'])->name('productCategories');
        Route::get('coupon-list', [CouponController::class, 'getCoupon'])->name('coupon-list');
        Route::get('products', [AstroMallController::class, 'getProduct'])->name('products');
        Route::post('products', [AstroMallController::class, 'getProduct'])->name('products');
        Route::get('banners', [BannerController::class, 'getBanner'])->name('banners');
        Route::get('notifications', [NotificationController::class, 'getNotification'])->name('notifications');
        Route::get('blogs', [BlogController::class, 'getBlog'])->name('blogs');
        Route::get('reportTypes', [ReportController::class, 'getReport'])->name('reportTypes');
        Route::get('adsVideos', [AdsVideoController::class, 'getAdsVideo'])->name('adsVideos');
        Route::get('relationReviveNews', [NewsController::class, 'getNews'])->name('astroguruNews');
        Route::get('advisors', [AstrologerController::class, 'getAstrologer'])->name('astrologers');
        Route::get('printadvisorlist', [AstrologerController::class, 'printAstrologer'])->name('printastrologerlist');
        Route::get('exportAdvisorCSV', [AstrologerController::class, 'exportAstrologer'])->name('exportAstrologerCSV');
        Route::post('advisors', [AstrologerController::class, 'getAstrologer'])->name('astrologers');
        Route::get('products/add', [AstroMallController::class, 'getastroMallCategory'])->name('add-product');
        Route::get('customers', [CustomerController::class, 'getUser'])->name('customers');
        Route::post('customers', [CustomerController::class, 'getUser'])->name('customers');
        Route::get('printcustomerlist', [CustomerController::class, 'printCustomerList'])->name('printcustomerlist');
        Route::get('exportcustomerCSV', [CustomerController::class, 'exportCustomerCSV'])->name('exportcustomerCSV');
        Route::get('commissions', [CommissionController::class, 'getCommission'])->name('commissions');
        Route::get('coupon-list/{page}', [CouponController::class, 'setCouponPage'])->name('setCouponPage');
        Route::get('customers/{id}', [CustomerController::class, 'getUserDetailApi'])->name('customer-detail');
        Route::get('advisors/{id}', [AstrologerController::class, 'astrologerDetailApi'])->name('astrologer-detail');
        Route::get('advisors-list/advisors-detail/{id}', [AstrologerController::class, 'astrologerDetailApi'])->name('astrologer-detail');
        Route::get('customers/edit/{id}', [CustomerController::class, 'editUser'])->name('edit-customer');
        Route::get('products/edit/{id}', [AstroMallController::class, 'editProduct'])->name('edit-product');
        Route::get('blogs/{id}', [BlogController::class, 'getBlogById'])->name('getBlogById');
        Route::get('products/{id}', [AstroMallController::class, 'getCategoryById'])->name('getCategoryById');
        Route::get('editBlog/{id}', [BlogController::class, 'editBlog'])->name('editBlog');
        Route::get('advisorCategories', [AstrologerCategoryController::class, 'getAstrolgerCategory'])->name('astrologerCategories');
        Route::get('withdrawalRequests', [WithdrawlController::class, 'getWithDrawlRequest'])->name('withdrawalRequests');
        Route::post('withdrawalRequests', [WithdrawlController::class, 'getWithDrawlRequest'])->name('withdrawalRequests');
        Route::post('releaseAmount', [WithdrawlController::class, 'releaseAmount'])->name('releaseAmount');
        Route::get('commissions/{page}', [CommissionController::class, 'setCommissionPage'])->name('setCommissionPage');
        Route::post('editCommissionApi', [CommissionController::class, 'editCommissionApi'])->name('editCommissionApi');
        Route::get('setting', [SystemFlagController::class, 'getSystemFlag'])->name('setting');
        Route::post('editSystemFlag', [SystemFlagController::class, 'editSystemFlag'])->name('editSystemFlag');
        Route::post('update-system-flag-publish-status', [SystemFlagController::class, 'updateSystemFlagPublished'])->name('updateSystemFlagPublished');
        Route::post('sendNotification', [NotificationController::class, 'sendNotification'])->name('sendNotification');
        Route::get('editNotification/{id}', [NotificationController::class, 'redirectEditNotification'])->name('redirectEditNotification');
        Route::get('callHistory', [CallHistoryReportController::class, 'getCallHistory'])->name('callHistory');
        Route::post('callHistory', [CallHistoryReportController::class, 'getCallHistory'])->name('callHistory');
        Route::get('reportrequest', [ReportRequestController::class, 'getReportRequest'])->name('reportrequest');
        Route::post('reportrequest', [ReportRequestController::class, 'getReportRequest'])->name('reportrequest');
        Route::get('printPdf', [CallHistoryReportController::class, 'printPdf'])->name('printPdf');
        Route::get('export-csv', [CallHistoryReportController::class, 'exportCSV'])->name('exportCSV');
        Route::get('export-report-csv', [ReportRequestController::class, 'exportCSV'])->name('exportReportCSV');
        Route::get('export-chat-csv', [ChatHistoryReportController::class, 'exportChatCSV'])->name('exportChatCSV');
        Route::get('export-earning-csv', [PartnerWiseEarningController::class, 'exportPartnerWiseCSV'])->name('exportPartnerWiseCSV');
        Route::get('export-advisor-earning-csv', [EarningController::class, 'exportAstrologerEarningCSV'])->name('exportAstrologerEarningCSV');
        Route::get('export-orderrequest', [OrderRequestController::class, 'exportOrderRequestCSV'])->name('exportOrderRequestCSV');
        Route::get('chatHistory', [ChatHistoryReportController::class, 'getChatHistory'])->name('chatHistory');
        Route::post('chatHistory', [ChatHistoryReportController::class, 'getChatHistory'])->name('chatHistory');
        Route::get('printChatPdf', [ChatHistoryReportController::class, 'printPdf'])->name('printChatPdf');
        Route::get('partnerWiseEarning', [PartnerWiseEarningController::class, 'getPartnerWiseEarning'])->name('partnerWiseEarning');
        Route::get('printPartnerWisePdf', [PartnerWiseEarningController::class, 'printPdf'])->name('printPartnerWisePdf');
        Route::get('earning-report', [EarningController::class, 'getEarning'])->name('earning-report');
        Route::get('printAdvisorEarning', [EarningController::class, 'printPdf'])->name('printAstrologerEarning');
        Route::get('orderrequest', [OrderRequestController::class, 'getOrderRequest'])->name('orderrequest');
        Route::post('orderrequest', [OrderRequestController::class, 'getOrderRequest'])->name('orderrequest');
        Route::get('orders', [OrderController::class, 'getOrders'])->name('orders');
        Route::post('orders', [OrderController::class, 'getOrders'])->name('orders');
        Route::post('changeOrder', [OrderController::class, 'changeOrderStatus'])->name('changeOrder');
        Route::get('setOrderRequest/{page}', [OrderRequestController::class, 'setOrderRequestPage'])->name('setOrderRequestPage');
        Route::get('printOrderRequest', [OrderRequestController::class, 'printPdf'])->name('printOrder');
        Route::get('printReportPdf', [ReportRequestController::class, 'printPdf'])->name('printReport');
        Route::post('/save-token', [FCMController::class, 'index'])->name('save-token');
        Route::post('/createChat', [ChatController::class, 'createChat'])->name('createChat');
        Route::get('tickets/chats/{id?}', [ChatController::class, 'getFireStoredata'])->name('chats');
        Route::post('closeTicket', [TicketController::class, 'closeTicket'])->name('closeTicket');
        Route::post('pauseTicket', [TicketController::class, 'pauseTicket'])->name('pauseTicket');
        Route::post('helpSupport/add', [HelpSupportController::class, 'addHelpSupport'])->name('addHelpSupport');
        Route::post('helpSupport/edit', [HelpSupportController::class, 'editHelpSupport'])->name('editHelpSupport');
        Route::get('helpSupport', [HelpSupportController::class, 'getHelpSupport'])->name('helpSupport');
        Route::post('helpSupportSubCategory/add', [HelpSupportController::class, 'addHelpSupportSubCategory'])->name('addHelpSupportSubCategory');
        Route::get('helpSupportsubCategory/{helpSupportId?}', [HelpSupportController::class, 'getHelpSupportSubCategory'])->name('helpSupportsubCategory');
        Route::post('helpsupportsubsubcategory/add', [HelpSupportController::class, 'addHelpSupportSubSubCategory'])->name('addHelpSupportSubSubCategory');
        Route::get('helpSupportsubsubCategory/{helpSupportSubCategoryId?}', [HelpSupportController::class, 'getHelpSupportSubSubCategory'])->name('helpSupportsubsubCategory');
        Route::post('editHelpSupportSubCategory', [HelpSupportController::class, 'editHelpSupportSubCategory'])->name('editHelpSupportSubCategory');
        Route::delete('deleteHelpSupport', [HelpSupportController::class, 'deleteHelpSupport'])->name('deleteHelpSupport');
        Route::delete('deleteSubSupport', [HelpSupportController::class, 'deleteSubSupport'])->name('deleteSubSupport');
        Route::post('editHelpSupportSubSubCategory', [HelpSupportController::class, 'editHelpSupportSubSubCategory'])->name('editHelpSupportSubSubCategory');
        Route::delete('deleteHelpSupportSubSubCategory', [HelpSupportController::class, 'deleteHelpSupportSubSubCategory'])->name('deleteHelpSupportSubSubCategory');
        Route::get('dailyHoroscope', [DailyHoroScopeController::class, 'getDailyHoroscope'])->name('dailyHoroscope');
        Route::post('addDailyHoroscope', [DailyHoroScopeController::class, 'addDailyHoroscope'])->name('addDailyHoroscope');
        Route::get('dailyHoroscope/edit', [DailyHoroScopeController::class, 'redirectEditDailyHoroscope'])->name('redirectEditDailyHoroscope');
        Route::get('horoscope/edit', [HoroscopeController::class, 'redirectEditHoroscope'])->name('redirectEditHoroscope');
        Route::post('editDailyHoroscope', [DailyHoroScopeController::class, 'editDailyHoroscope'])->name('editDailyHoroscope');
        Route::get('dailyHoroscopeInsight', [DailyHoroScopeInsightController::class, 'getDailyHoroscopeInsight'])->name('dailyHoroscopeInsight');
        Route::get('getDailyHoroScopeInsight/{id}', [DailyHoroScopeInsightController::class, 'filterDailyHoroscopeInsight'])->name('getDailyHoroScopeInsight');
        Route::post('addDailyHoroscopeInsight', [DailyHoroScopeInsightController::class, 'addDailyHoroscopeInsight'])->name('addDailyHoroscopeInsight');
        Route::post('editDailyHoroscopeInsight', [DailyHoroScopeInsightController::class, 'editDailyHoroscopeInsight'])->name('editDailyHoroscopeInsight');
        Route::delete('deleteHoroscopeInsight', [DailyHoroScopeInsightController::class, 'deleteHoroscopeInsight'])->name('deleteHoroscopeInsight');
        Route::delete('deleteHoroscope', [DailyHoroScopeController::class, 'deleteHoroscope'])->name('deleteHoroscope');
        Route::get('horoscope', [HoroscopeController::class, 'getHoroscope'])->name('horoscope');
        Route::post('horoscope', [HoroscopeController::class, 'getHoroscope'])->name('horoscope');
        Route::post('dailyHoroscope', [DailyHoroScopeController::class, 'getDailyHoroscope'])->name('getDailyHoroscope');
        Route::post('dailyHoroscopeInsight', [DailyHoroScopeInsightController::class, 'getDailyHoroscopeInsight'])->name('dailyHoroscopeInsight');
        Route::post('addHoroscope', [HoroscopeController::class, 'addHoroscope'])->name('addHoroscope');
        Route::post('loginApi', [LoginController::class, 'loginApi'])->name('loginApi');
        Route::post('editHoroscope', [HoroscopeController::class, 'editHoroscope'])->name('editHoroscope');
        Route::delete('deleteHoro', [HoroscopeController::class, 'deleteHoroscope'])->name('deleteHoro');
        Route::delete('deleteVideo', [AdsVideoController::class, 'deleteVideo'])->name('deleteVideo');
        Route::delete('deleteNews', [NewsController::class, 'deleteNews'])->name('deleteNews');
        Route::post('verifiedAdvisor', [DashboardController::class, 'verifiedAstrologer'])->name('verifiedAstrologer');
        Route::get('pagination/fetch_data', [CustomerController::class, 'fetch_data'])->name('fetch_data');
        Route::get('feedback', [AppFeedbackController::class, 'getAppFeedback'])->name('feedback');
        Route::get('customerProfile', [DefaultImageController::class, 'getDefaultImage'])->name('customerProfile');
        Route::post('customerProfile', [DefaultImageController::class, 'getDefaultImage'])->name('customerProfile');
        Route::post('addDefaultProfile', [DefaultImageController::class, 'addDefaultImage'])->name('addDefaultProfile');
        Route::post('editCustomerProfile', [DefaultImageController::class, 'updateDefaultImage'])->name('editCustomerProfile');
        Route::post('customerProfileApi', [DefaultImageController::class, 'activeInactiveDefaultProfile'])->name('customerProfileApi');
        Route::get('advisors/edit/{id}', [AstrologerController::class, 'editAstrologer'])->name('edit-astrologer');
        Route::post('editAdvisorApi', [AstrologerController::class, 'editAstrologerApi'])->name('editAstrologerApi');
        Route::delete('deleteReview', [ReportBlockController::class, 'deleteReview'])->name('deleteReview');
        Route::delete('deleteBlog', [BlogController::class, 'deleteBlog'])->name('deleteBlog');
        Route::delete('deleteRechargeAmount', [RechargeController::class, 'deleteRechargeAmount'])->name('deleteRechargeAmount');
        Route::get('rechargeAmount', [RechargeController::class, 'getRechargeAmount'])->name('rechargeAmount');
        Route::post('rechargeAmount', [RechargeController::class, 'getRechargeAmount'])->name('rechargeAmount');
        Route::post('addRechargeAmount', [RechargeController::class, 'addRechargeAmount'])->name('addRechargeAmount');
        Route::get('/404', function () {
            if (\Auth::guard('advisor')->check()) {
                $homeUrl = url('/advisor/dashboard');
            } elseif (\Auth::guard('web')->check() || \Auth::check()) {
                $homeUrl = url('/admin/dashboard');
            } else {
                $homeUrl = session('error_home_url', url('/admin/login'));
            }

            $data = ['homeUrl' => $homeUrl];

            // side-menu layout requires admin auth; otherwise use main (same error-page look)
            if (!\Auth::guard('web')->check() && !\Auth::check()) {
                $data['layout'] = 'main';
            }

            return view('pages/404', $data);
        });
        Route::get('horoscopeFeedback', [DailyHoroScopeController::class, 'getHoroscopeFeedback'])->name('horoscopeFeedback');
        Route::post('horoscopeFeedback', [DailyHoroScopeController::class, 'getHoroscopeFeedback'])->name('horoscopeFeedback');
        Route::post('teamRole', [TeamRoleController::class, 'getTeamRole'])->name('teamRole');
        Route::get('teamRole', [TeamRoleController::class, 'getTeamRole'])->name('teamRole');
        Route::delete('deleteRole', [TeamRoleController::class, 'deleteTeamRole'])->name('deleteRole');
        Route::post('addTeamRoleApi', [TeamRoleController::class, 'addTeamRoleApi'])->name('addTeamRoleApi');
        Route::post('editTeamRoleApi', [TeamRoleController::class, 'editTeamRoleApi'])->name('editTeamRoleApi');
        Route::get('teamRole/add', [TeamRoleController::class, 'redirectAddTeamRole'])->name('teamRole/add');
        Route::get('teamRole/edit/{id}', [TeamRoleController::class, 'redirectEditTeamRole'])->name('teamRole/edit/{id}');
        Route::get('team-list', [TeamRoleController::class, 'getTeamMember'])->name('team-list');
        Route::post('team-list', [TeamRoleController::class, 'getTeamMember'])->name('team-list');
        Route::post('addTeamApi', [TeamRoleController::class, 'addTeamApi'])->name('addTeamApi');
        Route::delete('deleteMember', [TeamRoleController::class, 'deleteTeamMember'])->name('deleteMember');
        Route::post('editTeamMemberApi', [TeamRoleController::class, 'editTeamMemberApi'])->name('editTeamMemberApi');

    });
});