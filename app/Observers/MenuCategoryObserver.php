<?php

namespace App\Observers;

use App\Models\MenuCategory;

class MenuCategoryObserver
{
    /**
     * Handle the MenuCategory "created" event.
     */
    public function created(MenuCategory $menuCategory): void
    {
        //
    }

    /**
     * Handle the MenuCategory "updated" event.
     */
    public function updated(MenuCategory $menuCategory): void
    {
        //
    }

    /**
     * Handle the MenuCategory "deleted" event.
     */
    public function deleted(MenuCategory $menuCategory): void
    {
        //
    }

    /**
     * Handle the MenuCategory "restored" event.
     */
    public function restored(MenuCategory $menuCategory): void
    {
        //
    }

    /**
     * Handle the MenuCategory "force deleted" event.
     */
    public function forceDeleted(MenuCategory $menuCategory): void
    {
        //
    }
}
