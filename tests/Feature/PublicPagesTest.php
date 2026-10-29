<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use App\Models\SiteTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function staticPages(): array
    {
        return [
            'home' => ['/'],
            'pricing' => ['/pricing'],
            'portfolio' => ['/portfolio'],
            'about' => ['/about'],
            'contact' => ['/contact'],
            'terms' => ['/terms'],
            'privacy' => ['/privacy'],
            'cancellation policy' => ['/cancellation-policy'],
        ];
    }

    #[DataProvider('staticPages')]
    public function test_public_page_loads(string $uri): void
    {
        $this->get($uri)->assertOk();
    }

    public function test_home_page_shows_active_templates_and_published_portfolio_items(): void
    {
        SiteTemplate::factory()->create(['name' => 'Bistro']);
        SiteTemplate::factory()->inactive()->create(['name' => 'Retired Design']);
        PortfolioItem::factory()->create(['title' => 'Maple Bakery website']);
        PortfolioItem::factory()->create(['title' => 'Hidden project', 'is_published' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Bistro')
            ->assertDontSee('Retired Design')
            ->assertSee('Maple Bakery website')
            ->assertDontSee('Hidden project');
    }

    public function test_template_gallery_filters_by_industry(): void
    {
        SiteTemplate::factory()->create(['name' => 'Bistro', 'industry' => 'Restaurant']);
        SiteTemplate::factory()->create(['name' => 'Glow Studio', 'industry' => 'Salon & Spa']);

        $this->get('/templates?industry=Restaurant')
            ->assertOk()
            ->assertSee('Bistro')
            ->assertDontSee('Glow Studio');
    }

    public function test_template_page_shows_details_and_checkout_link(): void
    {
        $template = SiteTemplate::factory()->create(['slug' => 'bistro', 'features' => ['Online menu']]);

        $this->get('/templates/bistro')
            ->assertOk()
            ->assertSee($template->name)
            ->assertSee('Online menu')
            ->assertSee(route('checkout.create', $template), escape: false);
    }

    public function test_hidden_template_returns_not_found(): void
    {
        SiteTemplate::factory()->inactive()->create(['slug' => 'retired']);

        $this->get('/templates/retired')->assertNotFound();
        $this->get('/templates/retired/preview')->assertNotFound();
    }

    public function test_live_demo_page_frames_the_demo_site(): void
    {
        SiteTemplate::factory()->create(['slug' => 'bistro', 'demo_url' => 'https://demo.gulraiz.ca/bistro']);

        $this->get('/templates/bistro/preview')
            ->assertOk()
            ->assertSee('<iframe src="https://demo.gulraiz.ca/bistro"', escape: false)
            ->assertSee('Choose this design');
    }

    public function test_live_demo_page_returns_not_found_without_a_demo_url(): void
    {
        SiteTemplate::factory()->create(['slug' => 'bistro', 'demo_url' => null]);

        $this->get('/templates/bistro/preview')->assertNotFound();
    }
}
