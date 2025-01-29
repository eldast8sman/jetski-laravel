<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class FoodMenu extends Model
{
    use HasSlug;

    protected $fillable = [
        'uuid',
        'slug',
        'menu_category_id',
        'name',
        'description',
        'amount',
        'availability',
        'availability_time',
        'g5_id',
        'shelf_life_from',
        'shelf_life_to',
        'ingredients',
        'details',
        'total_orders',
        'parent_id',
        'modifier_id',
        'is_stand_alone',
        'is_add_on',
        'add_ons',
        'is_new'
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function add_ons(){
        if(empty($this->add_ons)){
            return null;
        };
        $add_on_ids = json_decode($this->add_ons, true);
        $first = array_shift($add_on_ids);

        $add_ons = $this->where('is_add_on', 1)->where('id', $first);
        if(!empty($add_on_ids)){
            foreach($add_on_ids as $add_on_id){
                $add_ons = $add_ons->orWhere('id', $add_on_id);
            }
        }
        $add_ons = $add_ons->orderBy('name', 'asc');
        return $add_ons;
    }

    public function photos(){
        return $this->hasMany(FoodMenuPhoto::class);
    }

    public function category(){
        return $this->belongsTo(MenuCategory::class, 'menu_category_id', 'id');
    }

    public function scopeIsValid($query){
        $today = Carbon::today('Africa/Lagos');
    }
}
