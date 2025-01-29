<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $faker->addProvider(new FakerPicsumImagesProvider($faker));

        Product::create([
            'uuid' => $faker->uuid(),
            'name' => "JetSki",
            'description' => "Jetski across the ocean",
            'amount' => "1000000",
            'available' => true,
            'category' => 'Infrastructure',
            'photo' => $faker->imageUrl()
          ]);
      
          Product::create([
            'uuid' => $faker->uuid(),
            'name' => "Boat",
            'description' => "Sail across the ocean in a boat of your own",
            'amount' => "1000000",
            'available' => true,
            'category' => 'Infrastructure',
            'photo' => $faker->imageUrl()
          ]);
      
          Product::create([
            'uuid' => $faker->uuid(),
            'name' => "Scuba ",
            'description' => "Curiousity got the best of you, why not go for a scuba dive",
            'amount' => "1000000",
            'available' => true,
            'category' => 'Infrastructure',
            'photo' => $faker->imageUrl()
          ]);
      
          Product::create([
            'uuid' => $faker->uuid(),
            'name' => "Social",
            'description' => "Enjoy the scenary of the ocean, and not the ocean itself, sit back, relax, and have a good time",
            'amount' => "1000000",
            'available' => true,
            'category' => 'Infrastructure',
            'photo' => $faker->imageUrl()
          ]);
    }
}
