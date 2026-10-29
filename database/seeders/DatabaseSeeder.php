<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use App\Models\SiteTemplate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with sample data for local development.
     * Log in as admin@gulraiz.test / password.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Mohammed Gulraiz',
            'email' => 'admin@gulraiz.test',
        ]);

        User::factory()->create([
            'name' => 'Sample Client',
            'email' => 'client@gulraiz.test',
        ]);

        $templates = [
            ['Bistro', 'Restaurant', 'A warm, photo-led design for restaurants and cafés, with menu and reservations.'],
            ['Glow Studio', 'Salon & Spa', 'A calm, elegant design for salons, spas and beauty studios, with service lists and booking links.'],
            ['Tradesman Pro', 'Trades', 'A bold design for plumbers, electricians and contractors, built to get the phone ringing.'],
            ['Open House', 'Real Estate', 'A clean design for realtors, with featured listings and a strong personal brand.'],
            ['Fit Lab', 'Fitness', 'An energetic design for gyms, trainers and studios, with class schedules and pricing.'],
            ['Counsel', 'Professional Services', 'A trustworthy design for accountants, lawyers and consultants.'],
        ];

        foreach ($templates as $index => [$name, $industry, $summary]) {
            SiteTemplate::factory()->create([
                'name' => $name,
                'slug' => str($name)->slug(),
                'industry' => $industry,
                'summary' => $summary,
                'demo_url' => 'https://example.com',
                'sort_order' => $index,
            ]);
        }

        PortfolioItem::factory()->count(3)->create();
    }
}
