<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FurnitureSet;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $imageService = app(ImageUploadService::class);
        $downloadedDir = 'C:/Users/ridlo/.gemini/antigravity/brain/181b3f98-d22b-4bc8-982b-d85aaa97db8e/scratch/downloaded_assets';

        // 0. Admin user
        User::updateOrCreate(
            ['email' => 'admin@nusarattan.com'],
            [
                'name' => 'Nusarattan Admin',
                'password' => Hash::make('password'),
            ]
        );

        // Helper to upload or copy local asset
        $uploadAsset = function ($hashWithExt, $folder = 'products') use ($imageService, $downloadedDir) {
            $source = $downloadedDir.'/'.$hashWithExt;
            if (File::exists($source)) {
                return $imageService->store($source, $folder);
            }

            return '/storage/'.$folder.'/'.$hashWithExt;
        };

        // 1. Settings
        $settings = [
            'whatsapp_number' => '+6282228764381',
            'catalog_url' => '/catalog.pdf',
            'video_url' => 'https://youtu.be/8AHPXm9Y6mI',
            'youtube_id' => '8AHPXm9Y6mI',
            'address' => 'Jl. Makadam, Warung Pojok Kebon, Cirebon, West Java, Indonesia',
            'map_lat' => '-6.715',
            'map_lng' => '108.55',
            'footer_tagline' => 'Well-crafted rattan furniture from Nusantara (Indonesia archipelago).',
            'copyright_text' => '© '.date('Y').' PT Rekayasa Karya Utama',
            'contact_email' => 'nusarattanid@gmail.com',
            'hero_heading' => 'Indonesian Rattan Furniture Exporter, Wholesale and Custom Manufacturer',
            'hero_subheading' => 'We deliver premium and sustainable rattan products worldwide.',
        ];

        foreach ($settings as $k => $v) {
            Setting::updateOrCreate(['key' => $k], ['value' => $v]);
        }

        // 2. Categories
        $categoriesData = [
            [
                'name' => 'Indoor Furniture',
                'slug' => 'indoor',
                'sort_order' => 1,
                'meta_title' => 'Indoor Furniture | Rattan Furniture by Nusarattan',
                'meta_description' => 'Browse Indoor Furniture from Nusarattan, Indonesian rattan furniture manufacturer. Custom designs, wholesale export, handcrafted by local artisans.',
                'image_hash' => 'le4pgX1rZlDrscUSRlbI7X9VNw.png',
            ],
            [
                'name' => 'Outdoor Dining Set',
                'slug' => 'outdoor-dining',
                'sort_order' => 2,
                'meta_title' => 'Outdoor Dining Set | Rattan Furniture by Nusarattan',
                'meta_description' => 'Browse Outdoor Dining Set from Nusarattan, Indonesian rattan furniture manufacturer. Custom designs, wholesale export, handcrafted by local artisans.',
                'image_hash' => 'f7gSQZxJBevsTq7iYuDVd1EdgnQ.png',
            ],
            [
                'name' => 'Pool Area',
                'slug' => 'pool-area',
                'sort_order' => 3,
                'meta_title' => 'Pool Area | Rattan Furniture by Nusarattan',
                'meta_description' => 'Browse Pool Area from Nusarattan, Indonesian rattan furniture manufacturer. Custom designs, wholesale export, handcrafted by local artisans.',
                'image_hash' => 'ncm7cNk5s0NKxBjSaTiB1K1oSg.png',
            ],
            [
                'name' => 'Outdoor Living Set',
                'slug' => 'outdoor-living',
                'sort_order' => 4,
                'meta_title' => 'Outdoor Living Set | Rattan Furniture by Nusarattan',
                'meta_description' => 'Browse Outdoor Living Set from Nusarattan, Indonesian rattan furniture manufacturer. Custom designs, wholesale export, handcrafted by local artisans.',
                'image_hash' => 'wHvnf8O31Q9q6zXhv24ByshWJ8.png',
            ],
            [
                'name' => 'Rattan Poles',
                'slug' => 'rattan-poles',
                'sort_order' => 5,
                'meta_title' => 'Rattan Poles | Rattan Furniture by Nusarattan',
                'meta_description' => 'Browse Rattan Poles from Nusarattan, Indonesian rattan furniture manufacturer. Custom designs, wholesale export, handcrafted by local artisans.',
                'image_hash' => '1dNV9VyoVFpphs4sJw6nWII9vE.png',
            ],
            [
                'name' => 'Peacock Collection',
                'slug' => 'peacock-collection',
                'sort_order' => 6,
                'meta_title' => 'Peacock Collection | Rattan Furniture by Nusarattan',
                'meta_description' => 'Browse Peacock Collection from Nusarattan, Indonesian rattan furniture manufacturer. Custom designs, wholesale export, handcrafted by local artisans.',
                'image_hash' => 'p0E6m2QnhAn9fp7dmD2PK92fly8.png',
            ],
        ];

        $catMap = [];
        foreach ($categoriesData as $catItem) {
            $cat = Category::updateOrCreate(
                ['slug' => $catItem['slug']],
                [
                    'name' => $catItem['name'],
                    'sort_order' => $catItem['sort_order'],
                    'is_active' => true,
                    'meta_title' => $catItem['meta_title'],
                    'meta_description' => $catItem['meta_description'],
                ]
            );
            $catMap[$catItem['slug']] = $cat;

            // Also save category thumbnail URL in settings for easy retrieval in snippets
            $catThumbUrl = $uploadAsset($catItem['image_hash'], 'categories');
            Setting::set('category_thumb_'.$catItem['slug'], $catThumbUrl);
        }

        // 3. Featured Products on Home
        $featuredProducts = [
            [
                'code' => 'FRP-RC-01',
                'name' => 'FRP-RC-01',
                'material' => 'Natural rattan frame & handwoven rattan webbing',
                'price_from' => 48.00,
                'image_hash' => 'UbQWn62A8L7WbKUP0QtCYRiA.png',
            ],
            [
                'code' => 'FRP-RC-02',
                'name' => 'FRP-RC-02',
                'material' => 'Natural rattan frame & handwoven rattan webbing',
                'price_from' => 51.00,
                'image_hash' => '85fY5LnFJbviSp9zB45nBi2m20.png',
            ],
            [
                'code' => 'FRP-RC-03',
                'name' => 'FRP-RC-03',
                'material' => 'Natural rattan frame & handwoven rattan webbing',
                'price_from' => 46.00,
                'image_hash' => 'exbDKa7Tw48PNEpiMo2ODT8HAM.png',
            ],
            [
                'code' => 'FRP-RC-04',
                'name' => 'FRP-RC-04',
                'material' => 'Natural rattan frame & handwoven rattan webbing',
                'price_from' => 67.00,
                'image_hash' => 'xsSMwKkb33GEUrX9ZLqWFbqMPSA.png',
            ],
            [
                'code' => 'FRP-RC-05',
                'name' => 'FRP-RC-05',
                'material' => 'Rattan frame and synthetic webbing.',
                'price_from' => 53.00,
                'image_hash' => 'FA1aeIvc320zaqushhYtmVBU.png',
            ],
            [
                'code' => 'FRP-RC-07',
                'name' => 'FRP-RC-07',
                'material' => 'Solid wooden frame & wicker rattan webbing.',
                'price_from' => 43.00,
                'image_hash' => '2aG1n5hBjSd4w2JBypyyvD29Fw.png',
            ],
            [
                'code' => 'FRP-RC-08',
                'name' => 'FRP-RC-08',
                'material' => 'Rattan frame',
                'price_from' => 52.00,
                'image_hash' => 'FYdbO5q5ECGOwenF5wCp3Yz5Nr0.png',
            ],
            [
                'code' => 'FRP-RC-12',
                'name' => 'FRP-RC-12',
                'material' => 'Rattan frame and wicker webbing',
                'price_from' => 109.00,
                'image_hash' => 'UGv2TejQFUlDkbX2IUQCoqHqUXs.png',
            ],
        ];

        foreach ($featuredProducts as $idx => $fp) {
            $slug = Str::slug($fp['name']);
            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $catMap['indoor']->id,
                    'code' => $fp['code'],
                    'name' => $fp['name'],
                    'material' => $fp['material'],
                    'price_from' => $fp['price_from'],
                    'is_featured' => true,
                    'is_active' => true,
                    'sort_order' => $idx + 1,
                ]
            );

            $imgUrl = $uploadAsset($fp['image_hash']);
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => 1],
                [
                    'url' => $imgUrl,
                    'alt' => $product->name,
                    'is_primary' => true,
                ]
            );
        }

        // 4. Products by Category
        // Indoor
        $indoorProducts = [
            ['name' => 'Bahamas Water Hyacinth', 'material' => 'Water Hyacinth Material', 'img' => 'Y1muE80WTQEGpLzoGT8umWvJq8.png'],
            ['name' => 'Bahamas Wicker', 'material' => 'Wicker Material', 'img' => 'yNn6CshFiwvmK01wWZeKodncI.png'],
            ['name' => 'Bosanova', 'material' => 'BL Twisted Astor Lamination Material', 'img' => 'Y3s8XaTs2dyWWabbVPTrT0TsqHM.png'],
            ['name' => 'Louisiana', 'material' => 'Water Hyacinth Banana Mix Material', 'img' => 'aYDRWE0h2UnkUaZfESIWWDy1f3A.png'], // Ejaan diperbaiki dari Lousiana
            ['name' => 'Ostrava Set', 'material' => 'Wicker Material', 'img' => 'CBexoSnS71i1Q69AjPj33mYIx0.png'],
            ['name' => 'St. Barbara Wicker', 'material' => 'Wicker Material', 'img' => 'gf99N6au3sPXrfRlaLryNQcBc.png'],
        ];
        foreach ($indoorProducts as $i => $item) {
            $prod = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $catMap['indoor']->id,
                    'name' => $item['name'],
                    'material' => $item['material'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
            $imgUrl = $uploadAsset($item['img']);
            ProductImage::updateOrCreate(
                ['product_id' => $prod->id, 'sort_order' => 1],
                ['url' => $imgUrl, 'alt' => $item['name'], 'is_primary' => true]
            );
        }

        // Outdoor Dining
        $outdoorDiningProducts = [
            ['name' => 'Brastagi Dining Set', 'material' => 'Polypeel 5-6mm', 'img' => '72rhj1OfF9RQDrITftj5EC6Oqs.png'],
            ['name' => 'Hamburg Dining Set', 'material' => 'Half Rod 5-6 mm Material', 'img' => 'Nk79CMoJM1AZ3jNisWmYTsBukA.png'],
            ['name' => 'Livorno Dining Set', 'material' => 'Polypeel 5-6mm Brown Material', 'img' => 'cnTCzQPs6z4jUgP0jQ8YWDjH434.png'],
            ['name' => 'Savanah Dining Set', 'material' => 'Polypeel 5-6mm Brown Material', 'img' => 'RExGnbvX1XchaF2QO3Rqd2wePc.png'],
        ];
        foreach ($outdoorDiningProducts as $i => $item) {
            $prod = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $catMap['outdoor-dining']->id,
                    'name' => $item['name'],
                    'material' => $item['material'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
            $imgUrl = $uploadAsset($item['img']);
            ProductImage::updateOrCreate(
                ['product_id' => $prod->id, 'sort_order' => 1],
                ['url' => $imgUrl, 'alt' => $item['name'], 'is_primary' => true]
            );
        }

        // Pool Area
        $poolAreaProducts = [
            ['name' => 'Anabel Lounger', 'material' => 'Polypeel 5-6mm Brown Material', 'img' => '4fMN45jV7l7BZVllWL7MyozuLOw.png'],
            ['name' => 'Calamera', 'material' => 'Polypeel 5-6mm Material', 'img' => 'KwK9zqy7BGGB4Yqj9pPBY94VhQ.png'],
            ['name' => 'Hamburg Day Bed', 'material' => 'Polypeel 5-6mm Brown Material', 'img' => 'j1gwR6EnmUrkbU5JA1POO8k5f8s.png'],
            ['name' => 'Hamburg Lounger', 'material' => 'Polyrod 5-6mm Kubu', 'img' => 'Q1iVa98faw3bJ16ARopnXYlGN0.png'],
            ['name' => 'San Diego Lounger', 'material' => 'Polypeel 5-6mm Brown Material', 'img' => 'whvWUKujjVfjs42ADKIe6yixkxU.png'],
            ['name' => 'Savanah Lounger', 'material' => 'Polypeel 5-6mm Brown Material', 'img' => 'dbBCgc4XHC3FdfARJElWzu7Fp0.png'],
            ['name' => 'Shonia Sofa', 'material' => 'Polypeel 5-6mm', 'img' => 'dcJGVz4ZvrADbn9HZyvxbLjJISs.png'],
            ['name' => 'Shower With Teak Floor', 'material' => 'Polypeel 5-6mm', 'img' => '7j9V1OnQjWS6nnvg0aMkJoPCY.png'],
        ];
        foreach ($poolAreaProducts as $i => $item) {
            $prod = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $catMap['pool-area']->id,
                    'name' => $item['name'],
                    'material' => $item['material'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
            $imgUrl = $uploadAsset($item['img']);
            ProductImage::updateOrCreate(
                ['product_id' => $prod->id, 'sort_order' => 1],
                ['url' => $imgUrl, 'alt' => $item['name'], 'is_primary' => true]
            );
        }

        // Outdoor Living
        $outdoorLivingProducts = [
            ['name' => 'Annabelle Living Set', 'material' => 'Half Rod 5-6 mm', 'img' => 'qg6okrcndjM2sWncOoUl612trL8.png'],
            ['name' => 'Hamburg', 'material' => 'Full Rod 5-6 mm Material', 'img' => 'BTYYTi7dX8Vu1KKE1Llv1MTIFA.png'],
            ['name' => 'San Diego Living Set', 'material' => 'Half Rod 5-6 mm Material', 'img' => 'C6K9CDL3a1sHoQKgt1vBVvx10.png'],
            ['name' => 'Savanah Living Set', 'material' => 'Polypeel 5-6mm Brown Material', 'img' => 'LAKc47vmZeiMWshYbQG8XDtUCA4.png'],
        ];
        foreach ($outdoorLivingProducts as $i => $item) {
            $prod = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $catMap['outdoor-living']->id,
                    'name' => $item['name'],
                    'material' => $item['material'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
            $imgUrl = $uploadAsset($item['img']);
            ProductImage::updateOrCreate(
                ['product_id' => $prod->id, 'sort_order' => 1],
                ['url' => $imgUrl, 'alt' => $item['name'], 'is_primary' => true]
            );
        }

        // Rattan Poles
        $rattanPolesProducts = [
            ['name' => '01/14 Living Set', 'material' => 'Natural Rattan Pole', 'img' => 'XpsJiDdEMqno4u0UraHd6Fpf1QA.png'],
            ['name' => '01/15 Living Set', 'material' => 'Natural Rattan Pole', 'img' => 'wCaZNtkCOg5xgMi5P5m1gGVLEI.png'],
            ['name' => 'Breakfast Set', 'material' => 'Natural Rattan Pole', 'img' => 'jRFchfSYangnpCFOsQrTChf4o7E.png'],
            ['name' => 'Melangi Living Set', 'material' => 'Natural Rattan Pole', 'img' => 'FM6qNLs523mDRvN07biUnQI7I.png'],
            ['name' => 'Ocabama Living Set', 'material' => 'Natural Rattan Pole', 'img' => 'Hi6JgnV2fauumZ41qK2nIifGjE.png'],
            ['name' => 'Rainbow', 'material' => 'Natural Rattan Pole', 'img' => 'rofN5tpGEeBEGwGW1TbbzxtEvkk.png'],
            ['name' => 'Rainbow Living Set', 'material' => 'Natural Rattan Pole', 'img' => 'EY5ofYBFoFWY1jhY8NUrRjz1jFo.png'],
            ['name' => 'Warsawa Living Set', 'material' => 'Natural Rattan Pole', 'img' => 'uTjUi6ozucm5QTeVtMMQkDwgwE.png'],
        ];
        foreach ($rattanPolesProducts as $i => $item) {
            $prod = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $catMap['rattan-poles']->id,
                    'name' => $item['name'],
                    'material' => $item['material'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
            $imgUrl = $uploadAsset($item['img']);
            ProductImage::updateOrCreate(
                ['product_id' => $prod->id, 'sort_order' => 1],
                ['url' => $imgUrl, 'alt' => $item['name'], 'is_primary' => true]
            );
        }

        // Peacock Collection
        $peacockProducts = [
            ['name' => 'All Core', 'code' => 'ALLC-AC-01', 'material' => 'Natural Rattan Core', 'img' => 'q7ZdvsU4ZjFNYatCgQnED20m5gw.png'],
            ['name' => 'Double Seat', 'code' => 'DOSE-DS-01', 'material' => 'Natural Rattan Core', 'img' => 'Zqqwgv0UW9EhmvQpBwn3z90.png'],
            ['name' => 'Emanuelle', 'code' => 'EMAN-EMN-01', 'material' => 'Natural Rattan Core', 'img' => 'xBO2TJb0XswoLNr7abW2iI0xrU.png'],
            ['name' => 'Imanuele Polos', 'code' => 'IP001 IP-IP1-01', 'material' => 'Natural Rattan Core', 'img' => '6m96Z2hxRTZhJbAYDrud0LYWhY.png'],
            ['name' => 'Kembang Biasa', 'code' => 'KEBI-KB-01', 'material' => 'Natural Rattan Core', 'img' => 'AZYJ6Yl8IB87LEFAf7T1GdboskQ.png'],
            ['name' => 'Kembang Es', 'code' => 'KEME-KE-01', 'material' => 'Natural Rattan Core', 'img' => 'a6xZ0wv9QWzx2NuwDNj17ko05YU.png'],
            ['name' => 'Mawar', 'code' => 'MW001 MW-MW1-01', 'material' => 'Natural Rattan Core', 'img' => 'sxaN6kqr71HmQUGysNni23AvUic.png'],
            ['name' => 'Peacock Kembang', 'code' => 'PK001 PK-PK1-01', 'material' => 'Natural Rattan Core', 'img' => 'lxJTBEP080jwS5RKbeo4BU41p8.png'],
        ];
        foreach ($peacockProducts as $i => $item) {
            $prod = Product::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'category_id' => $catMap['peacock-collection']->id,
                    'code' => $item['code'],
                    'name' => $item['name'],
                    'material' => $item['material'],
                    'is_active' => true,
                    'sort_order' => $i + 1,
                ]
            );
            $imgUrl = $uploadAsset($item['img']);
            ProductImage::updateOrCreate(
                ['product_id' => $prod->id, 'sort_order' => 1],
                ['url' => $imgUrl, 'alt' => $item['name'], 'is_primary' => true]
            );
        }

        // 5. Furniture Set (01/14 Furniture Set)
        $fullSetImageUrl = $uploadAsset('XhnRolyYqTtb1ora7r8UTjAyLE.png', 'furniture-sets');
        $furnitureSet = FurnitureSet::updateOrCreate(
            ['name' => '01/14 Furniture Set'],
            [
                'price' => 400.00,
                'sale_price' => 368.00,
                'image_url' => $fullSetImageUrl,
                'whatsapp_message' => 'Hi there, I would like to know the 01/14 Furniture set. Please give me the detail, Thanks!',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        // Sub items in the set
        $setItems = [
            [
                'group' => 'arm-chair',
                'code' => '01/14-AC-01',
                'name' => 'Arm Chair 01/14-AC-01',
                'width_cm' => 72,
                'depth_cm' => 74,
                'height_cm' => 75,
                'img' => '3X1gCA0f9uJQRAETfrc6iOTndk.png',
                'sort_order' => 1,
            ],
            [
                'group' => 'sofa',
                'code' => '01/14-SF3S-02',
                'name' => 'Sofa 3 Seater 01/14-SF3S-02',
                'width_cm' => 190,
                'depth_cm' => 74,
                'height_cm' => 76,
                'img' => 'FQ1QdO2rZ7c0m81mz2ReOZ3kU4.png',
                'sort_order' => 2,
            ],
            [
                'group' => 'table',
                'code' => 'MELS-CT-03',
                'name' => 'Coffee Table MELS-CT-03', // Ejaan diperbaiki dari Coffee Tabel
                'width_cm' => 109,
                'depth_cm' => 57,
                'height_cm' => 42,
                'img' => 'B4Lp9kvBbeS7fKsg9uigjPp89g.png',
                'sort_order' => 3,
            ],
        ];

        foreach ($setItems as $item) {
            $slug = Str::slug($item['name']);
            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'code' => $item['code'],
                    'name' => $item['name'],
                    'width_cm' => $item['width_cm'],
                    'depth_cm' => $item['depth_cm'],
                    'height_cm' => $item['height_cm'],
                    'is_active' => true,
                    'sort_order' => $item['sort_order'],
                ]
            );

            $imgUrl = $uploadAsset($item['img']);
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'sort_order' => 1],
                ['url' => $imgUrl, 'alt' => $item['name'], 'is_primary' => true]
            );

            // Attach to set pivot
            $furnitureSet->products()->syncWithoutDetaching([
                $product->id => [
                    'group' => $item['group'],
                    'sort_order' => $item['sort_order'],
                ],
            ]);
        }

        // 6. Global Site Assets (Hero image, Contact BG, About Us photos)
        $heroUrl = $uploadAsset('NDA9WQBPmx3H3V4hlKJLuG3wyo.png', 'site');
        Setting::set('hero_image_url', $heroUrl);

        $contactBgUrl = $uploadAsset('5wRxPWPSIln2LuBclV1cNDfyarI.png', 'site');
        Setting::set('contact_bg_url', $contactBgUrl);

        $logoIconUrl = $uploadAsset('UPROaBi4W9KCVokCF8lnVYonS0.png', 'site');
        Setting::set('logo_icon_url', $logoIconUrl);

        // About Us images
        Setting::set('about_hero_image', $uploadAsset('ORwOHGcr13XnwWSgliAr9Ib7TU8.png', 'about'));
        Setting::set('about_commit_1', $uploadAsset('7w4kD5IYYISEDC89sBIsY2hpc.jpg', 'about'));
        Setting::set('about_commit_2', $uploadAsset('VtXT53GI7yCey34d2t8MWQVmDSk.png', 'about'));

        // About Gallery
        $gallery = [
            ['url' => $uploadAsset('dGbkz1MDgDEd4vPMUyznxcRU.png', 'gallery'), 'caption' => 'Crafting rattan frame'],
            ['url' => $uploadAsset('Iav2hc0kRnBkpDFCAXJH8Pk.png', 'gallery'), 'caption' => 'Our shipping to Japan', 'badge' => 'Our shipping to Japan'],
            ['url' => $uploadAsset('m91K7LvVBkLxNrY6WN7gCyR9pc.jpeg', 'gallery'), 'caption' => 'Assembly process'],
            ['url' => $uploadAsset('6TvozthQ7YAUWpmtDyINBnvVCA.jpeg', 'gallery'), 'caption' => 'Finished chairs'],
            ['url' => $uploadAsset('cXBcZUNfeOSl6wq7TfzVPBQZ4zk.png', 'gallery'), 'caption' => 'Pendant lamp showroom'],
            ['url' => $uploadAsset('S1atSY3HGwp5grBHNM9QWNrDGwI.png', 'gallery'), 'caption' => 'Warehouse storage'],
            ['url' => $uploadAsset('iElI0MPU6evFm9QZZ9flY3o1sA.jpeg', 'gallery'), 'caption' => 'Dining table setting'],
            ['url' => $uploadAsset('Kb7q0SjiU5Jm0VMnLgFFJcD6Z60.jpeg', 'gallery'), 'caption' => 'Outdoor dining area'],
        ];
        Setting::set('about_gallery_json', json_encode($gallery));
    }
}
