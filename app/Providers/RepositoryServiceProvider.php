<?php

namespace App\Providers;

use App\Models\Admin;
use App\Repositories\AbstractRepository;
use App\Repositories\AdminRepository;
use App\Repositories\AdsRepository;
use App\Repositories\AnnouncementRepository;
use App\Repositories\BookingRepository;
use App\Repositories\EmploymentDetailRepository;
use App\Repositories\EventBookingRepository;
use App\Repositories\EventRepository;
use App\Repositories\FoodMenuRepository;
use App\Repositories\Interfaces\AbstractRepositoryInterface;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Repositories\Interfaces\AdsRepositoryInterface;
use App\Repositories\Interfaces\AnnouncementRepositoryInterface;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Repositories\Interfaces\EmploymentDetailRepositoryInterface;
use App\Repositories\Interfaces\EventBookingRepositoryInterface;
use App\Repositories\Interfaces\EventRepositoryInterface;
use App\Repositories\Interfaces\FoodMenuRepositoryInterface;
use App\Repositories\Interfaces\InviteRepositoryInterface;
use App\Repositories\Interfaces\JetskiEventRepositoryInterface;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use App\Repositories\Interfaces\MembershipInformationRepositoryInterface;
use App\Repositories\Interfaces\MenuCategoryRepositoryInterface;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use App\Repositories\Interfaces\NotificationRepositoryInterface;
use App\Repositories\Interfaces\OrderCartItemRepositoryInterface;
use App\Repositories\Interfaces\OrderCartRepositoryInterface;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\UserMembershipRepositoryInterface;
use App\Repositories\Interfaces\UserRelativeRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\UserWatercraftRepositoryInterface;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Repositories\InviteRepository;
use App\Repositories\JetskiEventRepository;
use App\Repositories\MemberRepository;
use App\Repositories\MembershipInformationRepository;
use App\Repositories\MenuCategoryRepository;
use App\Repositories\MenuRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderCartItemRepository;
use App\Repositories\OrderCartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserMembershipRepository;
use App\Repositories\UserRelativeRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserWatercraftRepository;
use App\Repositories\WalletRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AbstractRepositoryInterface::class, AbstractRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, function ($app) {
            return new AdminRepository(new Admin);
        });
        $this->app->bind(MemberRepositoryInterface::class, MemberRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserRelativeRepositoryInterface::class, UserRelativeRepository::class);
        $this->app->bind(UserMembershipRepositoryInterface::class, UserMembershipRepository::class);
        $this->app->bind(MenuRepositoryInterface::class, MenuRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(InviteRepositoryInterface::class, InviteRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        $this->app->bind(AnnouncementRepositoryInterface::class, AnnouncementRepository::class);
        $this->app->bind(WalletRepositoryInterface::class, WalletRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(AdsRepositoryInterface::class, AdsRepository::class);
        $this->app->bind(MembershipInformationRepositoryInterface::class, MembershipInformationRepository::class);
        $this->app->bind(EmploymentDetailRepositoryInterface::class, EmploymentDetailRepository::class);
        $this->app->bind(UserWatercraftRepositoryInterface::class, UserWatercraftRepository::class);
        $this->app->bind(MenuCategoryRepositoryInterface::class, MenuCategoryRepository::class);
        $this->app->bind(FoodMenuRepositoryInterface::class, FoodMenuRepository::class);
        $this->app->bind(OrderCartItemRepositoryInterface::class, OrderCartItemRepository::class); 
        $this->app->bind(OrderCartRepositoryInterface::class, OrderCartRepository::class);
        $this->app->bind(JetskiEventRepositoryInterface::class, JetskiEventRepository::class);
        $this->app->bind(EventBookingRepositoryInterface::class, EventBookingRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
