<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdsController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FoodMenuController;
use App\Http\Controllers\Admin\JetskiEventController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\MembershipTypeController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderCartController as AdminOrderCartController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\AdsController as ControllersAdsController;
use App\Http\Controllers\AuthController as ControllersAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController as ControllersEventController;
use App\Http\Controllers\FoodMenuController as ControllersFoodMenuController;
use App\Http\Controllers\JetskiEventController as ControllersJetskiEventController;
use App\Http\Controllers\MembershipController as ControllersMembershipController;
use App\Http\Controllers\MenuController as ControllersMenuController;
use App\Http\Controllers\NotificationImageController;
use App\Http\Controllers\OrderCartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RelativeController;
use App\Http\Controllers\SparkleController;
use App\Http\Controllers\WalletController;
use App\Services\G5PosService;
use App\Services\SparkleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::prefix('admin')->group(function(){
    Route::controller(AuthController::class)->group(function(){
        Route::post('/first-admin', 'store')->name('admin.firstStore');
        Route::get('/find-by-verification-token/{token}', 'fetch_by_verification_token')->name('admin.findByVerificationToken');
        Route::post('/activate-account', 'activate_account')->name('admin.activate_account');
        Route::post('/forgot-password', 'forgot_password')->name('admin.forgot_password');
        Route::post('/reset-password', 'reset_password')->name('admin.resetPassword');
        Route::post('/login', 'login')->name('admin.login');
        Route::get('/refresh-token', 'refresh_token')->name('admin.refreshToken');
    });

    Route::get('/notification-images', [NotificationImageController::class, 'index'])->name('notification-images');

    Route::middleware('auth:admin-api')->group(function(){
        Route::controller(AdminDashboardController::class)->group(function(){
            Route::get('/dashboard', 'index');
        });

        Route::controller(AuthController::class)->group(function(){
            Route::get('/me', 'me')->name('admin.me');
            Route::post('/account-details', 'update_account_details')->name('admin.updateAccountDetails');
            Route::post('/me', 'update')->name('admin.profileUpdate');
            Route::post('/change-password', 'change_password')->name('admin.passwordChange');
        });

        Route::controller(AdminController::class)->group(function(){
            Route::get('/admins', 'index')->name('admin.admins.index');
            Route::post('/admins', 'store')->name('admin.admins.store');
            Route::get('/admins/{uuid}', 'show')->name('admin.admins.show');
            Route::post('/admins/{uuid}', 'update')->name('admin.admin.update');
            Route::delete('/admins/{uuid}', 'destroy')->name('admin.admin.delete');
        });

        Route::controller(MembershipController::class)->prefix('members')->group(function(){
            Route::get('/', 'index')->name('admin.members.index');
            Route::post('/', 'store')->name('admin.members.store');
            Route::post('/bulk', 'store_bulk')->name('admin.members.store.bulk');
            Route::get('/{uuid}/verification-resend', 'resend_activation_link')->name('admin.members.verificationLinkResend');
            Route::get('/{uuid}', 'show')->name('admin.members.show');
            Route::post('/{uuid}/activation', 'user_activation')->name('admin.members.activation');
            Route::get('/{uuid}/wallet', 'wallet')->name('admin.members.wallet');
            Route::get('/{uuid}/wallet-transactions', 'wallet_transactions')->name('admin.members.walletTreansactions');
            Route::post('/{uuid}/profile', 'update')->name('admin.members.update');
            Route::post('/{uuid}/membership', 'update_membership_information')->name('admin.members.membership.update');
            Route::post('/{uuid}/watercraft', 'update_watercraft_information')->name('admin.members.watercraft.update');
            Route::post('/{uuid}/employment', 'update_employment_information')->name('admin.members.employment.update');
        });

        Route::controller(MembershipTypeController::class)->prefix('membership-types')->group(function(){
            Route::post('/default/add', 'store_default')->name('admin.membershipType.storeDefault');
            Route::post('/', 'store')->name('admin.membershipType.store');
            Route::get('/', 'index')->name('admin.membershipType.index');
            Route::post('/{uuid}', 'update')->name('admin.membershipType.update');
            Route::delete('/{uuid}', 'destroy')->name('admin.membershipType.delete');
        });

        Route::get('/transactions', [MembershipController::class, 'all_transactions'])->name('admin.members.store.bulk');

        Route::controller(MenuCategoryController::class)->prefix('menu-categories')->group(function(){
            Route::get('/', 'index')->name('admin.menuCategory.index');
            Route::post('/', 'store')->name('admin.menuCategory.store');
            Route::get('/{uuid}', 'show')->name('admin.menuCategory.show');
            Route::put('/{uuid}', 'update')->name('admin.menuCategory.update');
            Route::delete('/{uuid}', 'destroy')->name('admin.menuCategory.delete');
        });

        Route::controller(FoodMenuController::class)->prefix('food-menu')->group(function(){
            Route::post('/', 'refresh_menu')->name('foodMenu.refresh');
            Route::get('/screen/{screen_uuid}', 'index')->name('foodMenu.index');
            Route::get('/menu/new/{screen_uuid}', 'new_menu')->name('foodMenu.newMenu');
            Route::get('/menu/add-ons', 'add_ons')->name('foodMenu.addOns.index');
            Route::get('/menu/delivery-fees', 'delivery_fees')->name('foodMenu.delivery_fees.index');
            Route::get('/{uuid}', 'show')->name('foodMenu.show');
            Route::post('/{uuid}', 'update')->name('foodMenu.update')->name('foodMenu.update');
            Route::get('/{uuid}/availability', 'availability')->name('foodMenu.availability');
            Route::delete('delete-photo/{uuid}', 'delete_photo');
        });

        Route::controller(AdminOrderCartController::class)->prefix('order-carts')->group(function(){
            Route::get('/', 'index')->name('admin.orderCart.index');
            Route::post('/', 'place_order')->name('admin.orderCart.place');
            Route::get('/orders/completed', 'completed_orders')->name('admin.orderCart.completed');
            Route::get('/{uuid}', 'show')->name('admin.orderCart.show');
            Route::post('/{uuid}/confirm', 'confirm_order')->name('admin.orderCart.confirm');
            Route::post('/{uuid}', 'modify_order')->name('admin.orderCart.modify');
            Route::put('/{uuid}/status', 'change_status')->name('admin.orderCart.changeStatus');
        });

        Route::prefix('orders')->group(function(){
            Route::controller(MenuController::class)->prefix('menu')->group(function(){
                Route::get('/', 'index')->name('admin.menu.index');
                Route::get('/{id}', 'show')->name('admin.menu.show');
                Route::get('/search', 'search')->name('admin.menu.search');
                Route::get('/{id}/modifiers', 'modifiers')->name('admin.menu.modifiers');
            });
            Route::controller(AdminOrderController::class)->group(function(){
                Route::get('/', 'index')->name('admin.orders.index');
                Route::get('/past', 'past_orders')->name('admin.orders.past');
                Route::get('/search', 'search')->name('admin.orders.search');
                Route::post('/', 'store')->name('admin.orders.store');
            });
        });

        Route::prefix('bookings')->controller(AdminBookingController::class)->group(function(){
            Route::get('/', 'index')->name('admin.booking.index');
            Route::get('/past', 'pastBookings')->name('admin.booking.past');
        });

        // Route::prefix('events')->controller(EventController::class)->group(function(){
        //     Route::post('/', 'store')->name('admin.event.store');
        //     Route::get('/', 'index')->name('admin.event.index');
        //     Route::get('/past', 'past_events')->name('/admin.events.past');
        //     Route::post('/{uuid}', 'update')->name('admin.event.update');
        //     Route::delete('/{uuid}', 'destroy')->name('admin.event.delete');
        // });

        Route::prefix('announcements')->controller(AnnouncementController::class)->group(function(){
            Route::post('/', 'store')->name('admin.anouncement.store');
            Route::get('/', 'index')->name('admin.announcement.index');
            Route::get('/{uuid}', 'show')->name('admin.announcement.show');
        });

        Route::prefix('/ads')->controller(AdsController::class)->group(function(){
            Route::post('/', 'store')->name('admin.ads.store');
            Route::get('/', 'index')->name('admin.ads.index');
            Route::get('/{uuid}', 'show')->name('admin.ads.update');
            Route::post('/{uuid}', 'update')->name('admin.ads.update');
            Route::post('/{uuid}/change-status', 'change_status')->name('admin.ads.changeStatus');
            Route::delete('/{uuid}', 'destroy')->name('admin.ads.delete');
        });

        Route::prefix('/popup-ads')->controller(AdsController::class)->group(function(){
            Route::post('/', 'store_popup')->name('admin.popupAds.store');
            Route::get('/', 'popup_index')->name('admin.popupAds.index');
            Route::get('/{uuid}', 'show')->name('admin.popupAds.update');
            Route::post('/{uuid}', 'update')->name('admin.popupAds.update');
            Route::delete('/{uuid}', 'destroy')->name('admin.popupAds.delete');
        });

        Route::prefix('events')->controller(JetskiEventController::class)->group(function(){
            Route::get('/g5-tickets/refresh', 'refresh_ticketing')->name('admin.events.tickets.refresh');
            Route::get('/g5-tickets/search', 'g5_tickets')->name('admin.events.tickets.search');
            Route::post('/', 'store')->name('admin.events.store');
            Route::get('/', 'index')->name('admin.events.index');
            Route::get('/upcoming/fetch', 'upcoming_events')->name('admin.events.upcoming');
            Route::get('/{uuid}', 'show')->name('admin.events.show');
            Route::post('/{uuid}', 'update')->name('admin.events.update');
            Route::get('/{uuid}/display', 'event_activate')->name('admin.events.activate');
            Route::delete('/{uuid}', 'destroy')->name('admin.events.delete');
        });
    });
});

Route::prefix('user')->group(function(){
    Route::controller(ControllersAuthController::class)->group(function(){
        Route::post('/forgot-password', 'forgot_password')->name('user.forgotPassword');
        Route::post('/reset-password', 'reset_password')->name('user.resetPassword');

        Route::get('/fetch-by-token', 'fetch_token')->name('user.fetchByToken');
        Route::post('/verify-email', 'activate_account')->name('user.verifyEmail');

        Route::post('/login', 'login')->name('user.login');

        Route::get('/refresh-token', 'refresh_token')->name('user.refreshToken');

        Route::post('resend-otp', 'resend_otp')->name('user.OPTResend');
    });

    Route::middleware('auth:user-api')->group(function(){
        Route::prefix('account')->group(function(){
            Route::controller(ControllersAuthController::class)->group(function(){
                Route::get('/', 'me')->name('user.me');
                Route::post('/profile-photo', 'change_profile_photo')->name('user.profilePhoto.change');
                Route::put('/', 'update')->name('user.updateProfile');
                Route::put('/update-password', 'change_password')->name('user.changePassword');
            });

            Route::controller(RelativeController::class)->prefix('relatives')->group(function(){
                Route::post('/', 'store')->name('user.relative.store');
                Route::get('/', 'index')->name('user.relative.index');
                Route::get('/{id}', 'show')->name('user.relative.show');
                Route::post('/{id}', 'update')->name('user.relative.update');
                Route::put('/{uuid}/activation', 'activation')->name('user.relative.activation');
                Route::delete('/{id}', 'destroy')->name('user.relative.delete');
            });

            Route::controller(ControllersMembershipController::class)->prefix('membership')->group(function(){
                Route::get('/types', 'types')->name('user.membershipTypes');
                Route::get('/', 'index')->name('user.membershipInformation');
                Route::put('/', 'update')->name('user.membershipInformation.update');
            });
        });

        Route::controller(ControllersFoodMenuController::class)->prefix('food-menu')->group(function(){
            Route::get('/screen/{slug}', 'index')->name('foodMenu.index');
            Route::get('/{slug}', 'show')->name('foodMenu.show');
        });

        Route::prefix('orders')->group(function(){
            Route::controller(ControllersMenuController::class)->prefix('menu')->group(function(){
                Route::get('/{id}', 'index')->name('user.menu.index');
                Route::get('/{id}/modifiers', 'modifiers')->name('user.menu.modifier');
            });
            Route::controller(OrderController::class)->group(function(){
                Route::get('/', 'index')->name('user.order.index');
                Route::get('/past', 'past_orders')->name('user.order.past');
                Route::post('/', 'store')->name('user.order.store');
            });
        });

        Route::prefix('carts')->controller(OrderCartController::class)->group(function(){
            Route::get('/' , 'index')->name('user.cart.index');
            Route::get('/{uuid}', 'show')->name('user.cart.show');
            Route::get('/orders/completed', 'completed_orders')->name('user.cart.completed');
            // Route::post('/', 'add_item_to_cart')->name('user.cart.add');
            Route::post('/', 'place_order')->name('user.cart.placeOrder');
            Route::post('/{uuid}', 'modify_order')->name('user.cart.modify');
        });

        // Route::prefix('bookings')->controller(BookingController::class)->group(function(){
        //     Route::get('/', 'index')->name('user.booking.index');
        //     Route::get('/past', 'pastBookings')->name('user.booking.past');
        //     Route::get('/{uuid}', 'show')->name('user.booking.show');
        //     Route::post('/', 'store')->name('user.booking.store');
        //     Route::post('/{uuid}', 'update')->name('user.booking.update');
        //     Route::delete('/{uuid}', 'destroy')->name('user.booking,delete');
        // });

        // Route::prefix('events')->controller(ControllersEventController::class)->group(function(){
        //     Route::get('/', 'index')->name('user.event.index');
        //     Route::get('/past', 'pastEvents')->name('user.event.past');
        // });

        Route::prefix('events')->controller(ControllersJetskiEventController::class)->group(function(){
            Route::get('/', 'index')->name('user.event.index');
            Route::get('/upcoming/fetch', 'upcoming_events')->name('user.event.upcoming');
            Route::get('/{uuid}', 'show')->name('user.event.show');
            Route::post('/', 'book_tickets')->name('user.event.book');
            Route::get('/bookings/all', 'booking_index')->name('user.event.bookings');
        });

        Route::prefix('payments')->controller(PaymentController::class)->group(function(){
            Route::get('/', 'index')->name('user.payments.index');
            Route::get('/{uuid}', 'show')->name('user.payment.show');
        });

        Route::controller(ControllersAdsController::class)->prefix('ads')->group(function(){
            Route::get('/', 'index')->name('user.ads.index');
            Route::get('/{uuid}/click-increment', 'click_increment')->name('user.ads.click');
        });

        Route::controller(WalletController::class)->prefix('wallet')->group(function(){
            Route::get('/', 'wallet_details')->name('user.wallet.details');
            Route::get('/transactions', 'wallet_transactions')->name('user.wallet.transactions');
        });

        Route::get('/', [DashboardController::class, 'index'])->name('userDashboard');
    });
});

Route::get('/sparkle/webhook', [SparkleController::class, 'webhook']);
Route::get('/sparkle/transactions', [SparkleController::class, 'fetch_transactions']);
Route::get('/sparkle/customers', [SparkleController::class, 'customers']);
Route::get('/g5-login', [G5PosService::class, 'login']);
Route::get('/g5-members', [MembershipController::class, 'store_g5_members']);
Route::get('/g5-menu', [MenuController::class, 'store_g5_menu']);
Route::get('/sparkle/login', [SparkleService::class, 'login']);
Route::get('/user-g5-orders/{user_id}', [ControllersAuthController::class, 'get_user_g5_orders']);
Route::get('/store-test-user', [MembershipController::class, 'add_test_user']);