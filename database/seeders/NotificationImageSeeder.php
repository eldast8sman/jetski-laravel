<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        collect([
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749266/jetski/drink_c_rbk8d1.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749265/jetski/Calendar_perspective_matte_s_1_x10kjd.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749265/jetski/drink_a_r3oage.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749264/jetski/drink_d_bnjiwi.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749265/jetski/access_d_t0rw4e.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749262/jetski/default_pawqtk.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749261/jetski/drink_b_mrs8ps.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749261/jetski/access_c_kudzh3.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749239/jetski/meal_c_ha1hre.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749231/jetski/meal_b_swnrsf.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749231/jetski/meal_d_rncabd.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749225/jetski/user_j7abig.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749226/jetski/access_a_eukxqg.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749226/jetski/access_b_t32vgu.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749224/jetski/pay_b_zgglhn.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749224/jetski/pay_a_wx08sk.png',
            'https://res.cloudinary.com/dxiusbd9h/image/upload/v1656749222/jetski/meal_a_ffoimf.png'
        ])->map(function($image) use ($faker){
            DB::table('notification_images')->insert([
                'uuid' => $faker->uuid(),
                'photo' => $image
            ]);
        });
    }
}
