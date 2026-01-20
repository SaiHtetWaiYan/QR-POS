<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::factory()->create([
            'name' => 'Cashier',
            'email' => 'admin@pos.com',
            'password' => bcrypt('password'), // password
        ]);

        // Tables
        Table::create(['name' => 'Table 1', 'code' => 't1']);
        Table::create(['name' => 'Table 2', 'code' => 't2']);
        Table::create(['name' => 'Table 3', 'code' => 't3']);
        Table::create(['name' => 'Patio 1', 'code' => 'p1']);

        // Categories & Items
        $starters = Category::create(['name' => 'Starters', 'sort_order' => 1]);
        $mains = Category::create(['name' => 'Mains', 'sort_order' => 2]);
        $drinks = Category::create(['name' => 'Drinks', 'sort_order' => 3]);

        MenuItem::create([
            'category_id' => $starters->id,
            'name' => 'Garlic Bread',
            'description' => 'Toasted french baguette with garlic butter.',
            'price' => 5.99,
        ]);
        MenuItem::create([
            'category_id' => $starters->id,
            'name' => 'Chicken Wings',
            'description' => 'Spicy buffalo wings with blue cheese dip.',
            'price' => 9.99,
        ]);

        MenuItem::create([
            'category_id' => $mains->id,
            'name' => 'Cheeseburger',
            'description' => 'Angus beef patty, cheddar, lettuce, tomato, house sauce.',
            'price' => 14.50,
        ]);
        MenuItem::create([
            'category_id' => $mains->id,
            'name' => 'Caesar Salad',
            'description' => 'Romaine lettuce, croutons, parmesan, caesar dressing.',
            'price' => 11.00,
        ]);
         MenuItem::create([
            'category_id' => $mains->id,
            'name' => 'Steak Frites',
            'description' => 'Grilled sirloin steak with shoestring fries.',
            'price' => 24.00,
        ]);

        MenuItem::create([
            'category_id' => $drinks->id,
            'name' => 'Coke',
            'description' => '330ml can',
            'price' => 2.50,
        ]);
        MenuItem::create([
            'category_id' => $drinks->id,
            'name' => 'Craft Beer',
            'description' => 'Local IPA draft',
            'price' => 6.00,
        ]);
    }
}