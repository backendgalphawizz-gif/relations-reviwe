<?php

use App\Http\Controllers\API\Astrologer\AdsVideoController;
use App\Http\Controllers\API\Astrologer\AdvertisementController;
use App\Http\Controllers\API\Astrologer\AssistantChatController;
use App\Http\Controllers\API\Astrologer\AstroHostController;
use App\Http\Controllers\API\Astrologer\AstrologerAssistantController;
use App\Http\Controllers\API\Astrologer\AstrologerAvailabilityController;
use App\Http\Controllers\API\Astrologer\AstrologerCategoryController;
use App\Http\Controllers\API\Astrologer\AstrologerController;
use App\Http\Controllers\API\Astrologer\AstrologerProductController;
use App\Http\Controllers\API\Astrologer\BlogController;
use App\Http\Controllers\API\Astrologer\LiveAstroController;
use App\Http\Controllers\API\Astrologer\PermissionController;
use App\Http\Controllers\API\Astrologer\SkillController;
use App\Http\Controllers\API\Astrologer\WithDrawController;
use App\Http\Controllers\API\ArtisanApiController;
use App\Http\Controllers\API\User\AppReviewController;
use App\Http\Controllers\API\User\AstromallProductController;
use App\Http\Controllers\API\User\BannerController;
use App\Http\Controllers\API\User\CallRequestController;
use App\Http\Controllers\API\User\ChatRequestController;
use App\Http\Controllers\API\User\CommissionController;
use App\Http\Controllers\API\User\CommissionTypeController;
use App\Http\Controllers\API\User\CouponController;
use App\Http\Controllers\API\User\CustomerHomeController;
use App\Http\Controllers\API\User\DailyHoroscopeController;
use App\Http\Controllers\API\User\FollowerController;
use App\Http\Controllers\API\User\GiftController;
use App\Http\Controllers\API\User\HelpSupportController;
use App\Http\Controllers\API\User\HelpSupportQuestionController;
use App\Http\Controllers\API\User\HoroController;
use App\Http\Controllers\API\User\KundaliController;
use App\Http\Controllers\API\User\KundaliMatchingController;
use App\Http\Controllers\API\User\LanguageController;
use App\Http\Controllers\API\User\LiveUserController;
use App\Http\Controllers\API\User\MaritalStatusController;
use App\Http\Controllers\API\User\NotificationController;
use App\Http\Controllers\API\User\OrderAddressController;
use App\Http\Controllers\API\User\PaymentController;
use App\Http\Controllers\API\User\ProductCategoryController;
use App\Http\Controllers\API\User\ProductDetailController;
use App\Http\Controllers\API\User\ReportController;
use App\Http\Controllers\API\User\ReportTypeController;
use App\Http\Controllers\API\User\SystemFlagController;
use App\Http\Controllers\API\User\TicketController;
use App\Http\Controllers\API\User\TokenGeneratorController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\User\UserNotificationController;
use App\Http\Controllers\API\User\UserOrderController;
use App\Http\Controllers\API\User\UserReportController;
use App\Http\Controllers\API\User\UserReviewController;
use App\Http\Controllers\API\User\WaitListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::group(['middleware' => 'api'], function () {
    Route::post('login', [UserController::class, 'loginUser']);
    Route::post('user/add', [UserController::class, 'addUser']);
    Route::post('getProfile', [UserController::class, 'getProfile']);
    Route::post('refresh', [UserController::class, 'refreshToken']);
    Route::post('user/update/{id}', [UserController::class, 'updateUser']);
    Route::post('password/reset', [UserController::class, 'forgotPassword']);
    Route::post('userStatus/update/{id}', [UserController::class, 'activeUser']);
    Route::post('user/delete', [UserController::class, 'deleteUser']);
    Route::post('user/updateProfile', [UserController::class, 'updateUserProfile']);
    Route::post('user/send-enquiry', [UserController::class, 'sendAdvisorEnquiry']);
    Route::post('user/send-ads-enquiry', [UserController::class, 'sendAdsEnquiry']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('loginHistory', [UserController::class, 'getLoginHistory']);
    Route::post('validateSession', [AstrologerController::class, 'validateSession']);
    Route::post('validateSessionForAstrologer', [AstrologerController::class, 'validateSessionForAstrologer']);
});

Route::post('getUser', [UserController::class, 'getUsers']);
Route::get('get_pages/{slug}', [UserController::class, 'get_pages']);
Route::post('sendOtp', [UserController::class, 'sendOtp']);
Route::post('verifyOtp', [UserController::class, 'verifyOtp']);
Route::post('getUserById', [AstrologerController::class, 'getUserById']);
Route::post('getUserProfile', [AstrologerController::class, 'getUserProfile']);
//customer
Route::post('loginAppUser', [UserController::class, 'loginAppUser']);
//Device details
Route::post('deviceDetails', [UserController::class, 'addDeviceDetails']);

//------------------------------------------------ADMIN API--------------------------------------------------//
//Skill
Route::post('getSkill', [SkillController::class, 'getAppSkills']);
Route::post('activeSkill', [SkillController::class, 'activeSkills']);

//Gift
Route::post('getGift', [GiftController::class, 'getGifts']);
Route::post('activeGift', [GiftController::class, 'activeGifts']);
Route::post('sendGift', [GiftController::class, 'sendGifts']);

//Astrologer category
Route::post('getAstrologerCategory', [AstrologerCategoryController::class, 'getAstrologerCategory']);
Route::post('activeAstrologerCategory', [AstrologerCategoryController::class, 'activeAstrologerCategory']);

//Hororscope sign
Route::post('getHororscopeSign', [HoroController::class, 'getHororscopeSign']);
Route::post('activeHororscopeSign', [HoroController::class, 'activeHororscopeSigns']);

//Product category
Route::post('getproductCategory', [ProductCategoryController::class, 'getProductCategory']);
Route::post('getTopProductCategory', [ProductCategoryController::class, 'gettopthreeproductcategory']);
//Coupon code
Route::post('getCouponcode', [CouponController::class, 'getCouponcode']);

//Banner
Route::post('getBanner', [BannerController::class, 'getBanner']);
Route::post('getBannerType', [BannerController::class, 'getBannerType']);
//Notification
Route::post('notification/add', [NotificationController::class, 'addNotification']);
Route::post('notification/update/{id}', [NotificationController::class, 'updateNotification']);
Route::post('getNotification', [NotificationController::class, 'getNotification']);
Route::post('notificationStatus/update/{id}', [NotificationController::class, 'activeNotification']);
Route::post('notification/delete', [NotificationController::class, 'deleteNotification']);
Route::post('notification/send', [NotificationController::class, 'sendNotification']);
Route::post('getActiveUser', [NotificationController::class, 'getUser']);
//User notification
Route::post('userNotification/add', [UserNotificationController::class, 'addUserNotification']);
Route::post('userNotification/update/{id}', [UserNotificationController::class, 'updateUserNotification']);
Route::post('getUserNotification', [UserNotificationController::class, 'getUserNotification']);
Route::post('userNotificationStatus/update/{id}', [UserNotificationController::class, 'activeUserNotification']);
Route::post('userNotification/deleteUserNotification', [NotificationController::class, 'deleteUserNotification']);
Route::post('userNotification/deleteAllNotification', [NotificationController::class, 'deleteAllUserNotification']);
//Permission
Route::post('permission/add', [PermissionController::class, 'addPermission']);
Route::post('permission/update/{id}', [PermissionController::class, 'updatePermission']);
Route::post('getPermission', [PermissionController::class, 'getPermission']);
Route::post('permissionStatus/update/{id}', [PermissionController::class, 'activePermission']);

//Commission type pending
Route::post('getCommissionType', [CommissionTypeController::class, 'getCommissionType']);

//Commission  pending
Route::post('getCommission', [CommissionController::class, 'getCommission']);

//Help and support
Route::post('getHelpSupport', [HelpSupportController::class, 'getHelpSupport']);

//Help and support questions
Route::post('getHelpSupportQuestion', [HelpSupportQuestionController::class, 'getHelpSupportQuestion']);

Route::post('getHelpSupportSubSubCategory', [HelpSupportQuestionController::class, 'getHelpSupportSubSubCategory']);

//Astrologer
Route::post('astrologer/add', [AstrologerController::class, 'addAstrologer']);
Route::post('astrologer/update', [AstrologerController::class, 'updateAstrologer']);
Route::post('getAstrologer', [AstrologerController::class, 'getAstrologer']);
Route::post('getRandomAstrologer', [AstrologerController::class, 'getRandomAstrologer']);
Route::post('getCounsellor', [AstrologerController::class, 'getCounsellor']);
Route::post('astrologer/delete', [AstrologerController::class, 'deleteAstrologer']);
Route::post('astrologerVerify/update/{id}', [AstrologerController::class, 'verifyAstrologer']);
Route::post('getMasterAstrologer', [AstrologerController::class, 'masterAstrologer']);
Route::post('addAstrologerAvailability', [AstrologerAvailabilityController::class, 'addAstrologerAvailability']);
Route::post('updateAstrologerAvailability', [AstrologerAvailabilityController::class, 'updateAstrologerAvailability']);
Route::post('getAstrologerAvailability', [AstrologerAvailabilityController::class, 'getAstrologerAvailability']);
Route::post('checkContactNoExist', [AstrologerController::class, 'checkContactNoExist']);
Route::post('getAdminAstrologer', [AstrologerController::class, 'getAstrologerForAdmin']);
Route::post('getAstrologerById', [AstrologerController::class, 'getAstrologerById']);
Route::post('searchAstro', [AstrologerController::class, 'searchAstro']);
Route::post('getAstrologerForCustomer', [AstrologerController::class, 'getAstrologerForCustomer']);
Route::post('reportBlockAstrologer', [AstrologerController::class, 'reportblockAstrologer']);
Route::post('unBlockAstrologer', [AstrologerController::class, 'unblockAstrologer']);
Route::post('getBlockAstrologer', [AstrologerController::class, 'getBlockAstrologer']);
Route::post('send-notification', [CallRequestController::class, 'send_notification']);
Route::post('addStatus', [ChatRequestController::class, 'addChatStatus']);
Route::post('addCallStatus', [CallRequestController::class, 'addCallStatus']);
Route::post('getAstrologerByIdForAdmin', [AstrologerController::class, 'getAstrologerByIdForAdmin']);
//Blog
Route::post('getBlog', [BlogController::class, 'getBlog']);
Route::post('getAppBlog', [BlogController::class, 'getAppBlog']);
Route::post('activeBlogs', [BlogController::class, 'activeBlogs']);
Route::post('addBlogReader', [BlogController::class, 'addBlogReader']);
Route::post('getBlogById', [BlogController::class, 'blogShow']);
//Astromall product
Route::post('getAstromallProduct', [AstromallProductController::class, 'getAstromallProductForApp']);
Route::post('getAstromallProductById', [AstromallProductController::class, 'getAstromallProductById']);
Route::post('searchAstromallProductCategory', [AstromallProductController::class, 'searchInProductCategory']);
//Ads banner

//Ads video
Route::post('getAdsVideo', [AdsVideoController::class, 'getAdsVideo']);

//Astrologer advertisement
Route::post('getAdvertisement', [AdvertisementController::class, 'getAdvertisement']);

//Ticket
Route::post('ticket/add', [TicketController::class, 'addTicket']);
Route::post('ticket/update/{id}', [TicketController::class, 'updateTicket']);
Route::post('getTicket', [TicketController::class, 'getTicket']);
Route::post('ticketStatus/update/{id}', [TicketController::class, 'activeTicket']);
Route::post('ticket/deleteAll', [TicketController::class, 'deleteAllTicket']);
Route::post('ticket/addReview', [TicketController::class, 'addTicketReview']);
Route::post('ticket/getReview', [TicketController::class, 'getTicketReview']);
Route::post('ticket/updateReview', [TicketController::class, 'updateTicketReview']);
Route::post('ticket/closeTicket', [TicketController::class, 'closeTicket']);
Route::post('ticket/delete', [TicketController::class, 'deleteTicket']);
Route::post('ticket/restart', [TicketController::class, 'restartTicket']);
Route::post('ticket/pause', [TicketController::class, 'pauseTicket']);
Route::post('checkOpenTicket', [TicketController::class, 'checkOpenTicket']);
Route::post('updateTicketStatus', [TicketController::class, 'updateTicketStatus']);
//Product details
Route::post('productDetails/add', [ProductDetailController::class, 'addProductDetails']);
Route::post('productDetails/update/{id}', [ProductDetailController::class, 'updateProductDetails']);
Route::post('getProductDetails', [ProductDetailController::class, 'getProductDetails']);
Route::post('productDetailsStatus/update/{id}', [ProductDetailController::class, 'activeProductDetails']);

//------------------------------------------------CUSTOMER API--------------------------------------------------//

//Kundali
Route::post('kundali/add', [KundaliController::class, 'addKundali']);
Route::post('kundali/update/{id}', [KundaliController::class, 'updateKundali']);
Route::post('getkundali', [KundaliController::class, 'getKundalis']);
Route::post('kundali/delete', [KundaliController::class, 'deleteKundali']);
Route::post('kundali/show/{id}', [KundaliController::class, 'kundaliShow']);
Route::post('kundali/removeFromTrackPlanet', [KundaliController::class, 'removeFromTrackPlanet']);
Route::post('kundali/addForTrackPlanet', [KundaliController::class, 'addForTrackPlanet']);
Route::post('kundali/getForTrackPlanet', [KundaliController::class, 'getForTrackPlanet']);

//Order address
Route::post('orderAddress/add', [OrderAddressController::class, 'addOrderAddress']);
Route::post('orderAddress/update/{id}', [OrderAddressController::class, 'updateOrderAddress']);
Route::post('getOrderAddress', [OrderAddressController::class, 'getOrderAddress']);

//Home screen
Route::post('getCustomerHome', [CustomerHomeController::class, 'getCustomerHome']);

//Blog

//Kundali matching
Route::post('KundaliMatching/add', [KundaliMatchingController::class, 'addKundaliMatching']);

//Language
Route::post('getLanguage', [LanguageController::class, 'getLanguages']);

//review
Route::post('userReview/add', [UserReviewController::class, 'addUserReview']);
Route::post('userReview/update/{id}', [UserReviewController::class, 'updateUserReview']);
Route::post('getUserReview', [UserReviewController::class, 'getUserReview']);
Route::post('userReview/delete/{id}', [UserReviewController::class, 'deleteUserReview']);
Route::post('appReview/get', [AppReviewController::class, 'getAppReview']);
Route::post('appReview/add', [AppReviewController::class, 'addAppReview']);
Route::post('getAstrologerUserReview', [UserReviewController::class, 'getAstrologerUserReview']);
Route::post('getUserHistoryReview', [UserReviewController::class, 'getUserHistoryReview']);
Route::post('userReview/reply', [UserReviewController::class, 'replyAstrologerReview']);
Route::post('blockUserReview', [UserReviewController::class, 'blockUserReview']);
Route::post('getBlockUserReview', [UserReviewController::class, 'getBlockReview']);
Route::post('getUserReviewForAdmin', [UserReviewController::class, 'getAstrologerUserReviewForAdmin']);

//Follower
Route::post('follower/add', [FollowerController::class, 'addFollowing']);
Route::post('follower/update', [FollowerController::class, 'updateFollowing']);
Route::post('getFollower', [FollowerController::class, 'getFollowing']);
Route::post('getAstrologerFollower', [FollowerController::class, 'getFollower']);

//User report
Route::post('userReport/add', [UserReportController::class, 'addReport']);
Route::post('getUserReport', [UserReportController::class, 'getUserReportRequest']);
Route::post('userReport/update/{id}', [UserReportController::class, 'updateUserReport']);

//Marital status
Route::post('getMaritalStatus', [MaritalStatusController::class, 'getMaritalStatus']);

//Report type
Route::post('getReportType', [ReportTypeController::class, 'getReportTypes']);
//------------------------------------------------ASTROLOGER API--------------------------------------------------//

//Login
Route::post('loginAppAstrologer', [AstrologerController::class, 'loginAstrologer']);

//Astrologer assistant
Route::post('astrologerAssistant/add', [AstrologerAssistantController::class, 'addAstrologerAssistant']);
Route::post('astrologerAssistant/update', [AstrologerAssistantController::class, 'updateAstrologerAssistant']);
Route::post('getAstrologerAssistant', [AstrologerAssistantController::class, 'getAstrologerAssistant']);
Route::post('astrologerAssistant/delete', [AstrologerAssistantController::class, 'deleteAstrologerAssistant']);
Route::post('astrologerAssistant/block', [AstrologerAssistantController::class, 'blockAstrologerAssistant']);
Route::post('astrologerAssistant/unBlock', [AstrologerAssistantController::class, 'unblockAstrologerAssistant']);

// Astrologer available
Route::post('astrologerAvailable/add', [AstrologerAvailabilityController::class, 'addAstrologerAvailable']);

// Astrologer Product
Route::post('astrologerProduct/get', [AstrologerProductController::class, 'getAstrologerForProduct']);

// chatRequest
Route::post('chatRequest/add', [ChatRequestController::class, 'addChatRequest']);
Route::post('chatRequest/get', [ChatRequestController::class, 'getChatRequest']);
Route::post('chatRequest/reject', [ChatRequestController::class, 'rejectChatRequest']);
Route::post('chatRequest/removeFromWaitList', [ChatRequestController::class, 'removeFromWaitlist']);
Route::post('chatRequest/accept', [ChatRequestController::class, 'acceptChatRequest']);
Route::post('chatRequest/storeToken', [ChatRequestController::class, 'storeToken']);
Route::post('chatRequest/storeChatId', [ChatRequestController::class, 'insertChatRequest']);
Route::post('chatRequest/endChat', [ChatRequestController::class, 'endChatRequest']);
Route::post('chatRequest/acceptChatRequest', [ChatRequestController::class, 'acceptChatRequestFromCustomer']);
Route::post('chatRequest/rejectChatRequest', [ChatRequestController::class, 'rejectChatRequestFromCustomer']);
Route::post('chatRequest/endLiveChat', [ChatRequestController::class, 'endLiveChatrequest']);
Route::post('chatRequest/addIntakeForm', [ChatRequestController::class, 'intakeForm']);
Route::post('chatRequest/getIntakeForm', [ChatRequestController::class, 'getUserIntakForm']);
Route::post('checkChatSessionAvailable', [ChatRequestController::class, 'checkChatSessionTaken']);
Route::post('checkCallSessionAvailable', [ChatRequestController::class, 'checkCallSessionTaken']);
Route::post('checkFreeSessionAvailable', [ChatRequestController::class, 'checkFreeSessionAvailable']);

//callRequest
Route::post('callRequest/add', [CallRequestController::class, 'addCallRequest']);
Route::post('callRequest/get', [CallRequestController::class, 'getCallRequest']);
Route::post('callRequest/reject', [CallRequestController::class, 'rejectCallRequest']);
Route::post('callRequest/removeFromWaitList', [CallRequestController::class, 'removeFromWaitlist']);
Route::post('callRequest/accept', [CallRequestController::class, 'acceptCallRequest']);
Route::post('callRequest/storeToken', [CallRequestController::class, 'storeToken']);
Route::post('callRequest/end', [CallRequestController::class, 'endCall']);
Route::post('callRequest/acceptCallRequest', [CallRequestController::class, 'acceptCallRequestFromCustomer']);
Route::post('callRequest/rejectCallRequest', [CallRequestController::class, 'rejectCallRequestFromCustomer']);
Route::post('storeCallRecording', [CallRequestController::class, 'storeCallRecording']);
Route::post('getCallById', [CallRequestController::class, 'getCallById']);

//userOrder
Route::post('userOrder/add', [UserOrderController::class, 'addUserOrder']);
Route::post('userOrder/cancel', [UserOrderController::class, 'cancelOrder']);

//withdrawlRequest
Route::post('withdrawlrequest/add', [WithDrawController::class, 'sendWithdrawRequest']);
Route::post('withdrawlrequest/get', [WithDrawController::class, 'getWithdrawRequest']);
Route::post('withdrawlrequest/update', [WithDrawController::class, 'updateWithdrawRequest']);
Route::post('withdrawlrequest/releaseAmount', [WithDrawController::class, 'releaseAmount']);
Route::post('addpayment', [PaymentController::class, 'addPayment']);
Route::post('getRechargeAmount', [PaymentController::class, 'getRechargeAmount']);

//liveAstro
Route::post('liveAstrologer/add', [LiveAstroController::class, 'addLiveAstrologer']);
Route::post('liveAstrologer/get', [LiveAstroController::class, 'getLiveAstrologer']);
Route::post('liveAstrologer/endSession', [LiveAstroController::class, 'endLiveSession']);
Route::post('liveAstrologer/livechattoken', [LiveAstroController::class, 'addLiveChatToken']);
Route::post('liveAstrologer/getToken', [LiveAstroController::class, 'getToken']);
Route::post('liveAstrologer/getUpcomingAstrologer', [LiveAstroController::class, 'getUpcomingAstrologer']);
Route::post('searchLiveAstro', [LiveAstroController::class, 'searchLiveAstrologer']);

//astrohost
Route::post('getAstrohost', [AstroHostController::class, 'getAstrohost']);
Route::post('addAstrohost', [AstroHostController::class, 'addAstrohost']);

//waitlist
Route::post('waitlist/add', [WaitListController::class, 'addWaitList']);
Route::post('waitlist/get', [WaitListController::class, 'getWaitList']);
Route::post('waitlist/delete', [WaitListController::class, 'deleteFromWaitList']);
Route::post('waitlist/updateStatus', [WaitListController::class, 'editWaitList']);

//systemflag
Route::post('getSystemFlag', [SystemFlagController::class, 'getSystemFlag']);
Route::post('getAppLanguage', [SystemFlagController::class, 'getAppLanguage']);

//report
Route::post('getCallHistoryReport', [ReportController::class, 'getCallHistory']);
Route::post('getChatHistoryReport', [ReportController::class, 'getChatHistory']);
Route::post('partnerWiseEarning', [ReportController::class, 'partnerWiseEarning']);
Route::post('astrologerEarning', [ReportController::class, 'getAstrologerEarning']);
Route::post('orderReport', [ReportController::class, 'orderRequest']);

//liveUser
Route::post('addLiveUser', [LiveUserController::class, 'addLiveUser']);
Route::post('getLiveUser', [LiveUserController::class, 'getLiveUser']);
Route::post('deleteLiveUser', [LiveUserController::class, 'deleteLiveUser']);

//userreprot
Route::post('userreport/add', [UserReportController::class, 'addUserReportFile']);
Route::post('savetoken', [ChatRequestController::class, 'saveToken']);

//assistantChat
Route::post('getAssistantChatId', [AssistantChatController::class, 'getAssistantChat']);
Route::post('getAssistantChatHistory', [AssistantChatController::class, 'getAssistantChatHistory']);
Route::post('getCustomerPaidSession', [AssistantChatController::class, 'customerPaidSession']);
Route::post('getAssistantChatRequest', [AssistantChatController::class, 'getAssistantChatRequest']);
Route::post('deleteAssistantChat', [AssistantChatController::class, 'deleteAssistantChat']);

//dailyHoroscope
Route::post('getDailyHoroscope', [DailyHoroscopeController::class, 'getDailyHoroscope']);
Route::post('getDailyHoroscopeInsightForAdmin', [DailyHoroscopeController::class, 'getDailyHoroscopeInsightForAdmin']);
Route::post('getHoroscope', [DailyHoroscopeController::class, 'getHoroscope']);
Route::post('generateToken', [TokenGeneratorController::class, 'generateToken']);
Route::post('generateRtcToken', [TokenGeneratorController::class, 'generateRtcToken']);
Route::post('addHoroscopeFeedback', [DailyHoroscopeController::class, 'addHoroscopeFeedback']);
// Route::post('subscribe', [SubscriberController::class, 'subscribe'])->name('subscribe');
//cDemo


/**
 * New Listings API - Start
 */
Route::get('testimonials/{type?}', [UserController::class, 'getTestimonial']);
Route::get('broadcasts', [UserController::class, 'getBroadcasts']);
/**
 * New Listings API - Start
 */

// Artisan utility APIs
Route::get('optimize-clear', [ArtisanApiController::class, 'optimizeClear']);
Route::get('migrate', [ArtisanApiController::class, 'migrate']);
