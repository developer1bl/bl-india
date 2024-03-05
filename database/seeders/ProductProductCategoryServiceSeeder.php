<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ProductProductCategoryServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complaince1 = ['aa1', 'aa2', 'aa3', 'aa4'];
        
        $service1 = Service::create([
                        'service_name' => 'a1',
                        'service_slug' => 'a1-slug',
                        'service_image_id' => 'a1-image-id',
                        'service_img_alt' => 'a1-img-alt',
                        'service_compliance' => json_encode($complaince1),
                        'service_description' =>  'this is just a dummy service description',
                        'faqs' => '',
                        'seo_title' => '',
                        'seo_description' => '',
                        'seo_keywords' => '',
                        'service_featured' => '',
                        'service_product_show' => '',
                        'service_order' => '',
                        'service_status' => 1,
                    ]);

        $complaince2 = ['ab1', 'ab2', 'ab3', 'ab4'];
        
        $service2 = Service::create([
                        'service_name' => 'a1',
                        'service_slug' => 'a1-slug',
                        'service_image_id' => 'a1-image-id',
                        'service_img_alt' => 'a1-img-alt',
                        'service_compliance' => json_encode($complaince2),
                        'service_description' =>  'this is just a dummy service description',
                        'faqs' => '',
                        'seo_title' => '',
                        'seo_description' => '',
                        'seo_keywords' => '',
                        'service_featured' => '',
                        'service_product_show' => '',
                        'service_order' => '',
                        'service_status' => 1,
                    ]);

    }
}
