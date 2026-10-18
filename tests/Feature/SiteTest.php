<?php

namespace Tests\Feature;

use App\Models\Category;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create categories for testing
        Category::create([
            'name' => 'Indoor Furniture',
            'slug' => 'indoor',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Category::create([
            'name' => 'Outdoor Dining Set',
            'slug' => 'outdoor-dining',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        Category::create([
            'name' => 'Pool Area',
            'slug' => 'pool-area',
            'sort_order' => 3,
            'is_active' => true,
        ]);
        Category::create([
            'name' => 'Outdoor Living Set',
            'slug' => 'outdoor-living',
            'sort_order' => 4,
            'is_active' => true,
        ]);
        Category::create([
            'name' => 'Rattan Poles',
            'slug' => 'rattan-poles',
            'sort_order' => 5,
            'is_active' => true,
        ]);
        Category::create([
            'name' => 'Peacock Collection',
            'slug' => 'peacock-collection',
            'sort_order' => 6,
            'is_active' => true,
        ]);
    }

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Indonesian Rattan Furniture');
    }

    public function test_category_pages_load_successfully(): void
    {
        $slugs = [
            'indoor',
            'outdoor-dining',
            'pool-area',
            'outdoor-living',
            'rattan-poles',
            'peacock-collection',
        ];

        foreach ($slugs as $slug) {
            $response = $this->get("/product-{$slug}");
            $response->assertStatus(200);
        }
    }

    public function test_about_us_page_loads_successfully(): void
    {
        $response = $this->get('/about-us');
        $response->assertStatus(200);
        $response->assertSee('About Us');
        $response->assertSee('Our Commitment');
        $response->assertSee('Our Gallery');
    }

    public function test_contact_form_submits_successfully(): void
    {
        $response = $this->postJson('/contact', [
            'type' => 'contact',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'country' => 'United States',
            'message' => 'Interested in wholesale prices.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'country' => 'United States',
        ]);
    }

    public function test_catalog_download_request_submits_successfully(): void
    {
        $response = $this->postJson('/contact', [
            'type' => 'catalog_download',
            'name' => 'Jane Smith',
            'business_type' => 'Trader',
            'email' => 'jane@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('inquiries', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'type' => 'catalog_download',
        ]);
    }

    public function test_admin_panel_accessible_by_authenticated_user(): void
    {
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_sitemap_returns_valid_xml(): void
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $response->assertSee('<urlset', false);
        $response->assertSee('/product-indoor', false);
        $response->assertSee('/product-outdoor-dining', false);
        $response->assertSee('/about-us', false);
    }
}

