<?php

namespace Database\Seeders;

use App\Http\Controllers\Admin\MembershipTypeController;
use App\Models\MembershipType;
use App\Models\Product;
use App\Repositories\MembershipTypeRepository;
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
        $repo = new MembershipTypeRepository(new MembershipType());
        $types = file_get_contents(base_path('/data/membership_types.json'));
        $types = json_decode($types, true);

        foreach($types as $type){
          $found = $repo->findFirstBy([
              'name' => $type['name']
          ]);

          if(empty($found)){
              $repo->store($type);
          } else {
              $repo->update($found->id, $type);
          }
      }
    }
}
