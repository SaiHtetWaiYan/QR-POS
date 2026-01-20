<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_customer_can_view_menu()
    {
        $table = Table::factory()->create(['code' => 't1']);
        
        $response = $this->get('/t/t1');

        $response->assertStatus(200);
        $response->assertSee($table->name);
    }

    public function test_customer_can_place_order()
    {
        $table = Table::factory()->create(['code' => 't1']);
        $category = Category::factory()->create();
        $item = MenuItem::factory()->create([
            'category_id' => $category->id,
            'price' => 10.00
        ]);

        // Add to cart
        $response = $this->from('/t/t1')->post('/t/t1/cart', [
            'menu_item_id' => $item->id,
            'qty' => 2,
            'note' => 'No pickles'
        ]);
        
        $response->assertRedirect('/t/t1');
        $response->assertSessionHas('cart');
        
        // Place order
        $response = $this->from('/t/t1/cart')->post('/t/t1/order', [
            'customer_note' => 'Hurry up'
        ]);

        $response->assertRedirect('/t/t1/status');
        
        $this->assertDatabaseHas('orders', [
            'table_id' => $table->id,
            'status' => 'pending',
            'customer_note' => 'Hurry up',
            'subtotal' => 20.00
        ]);

        $this->assertDatabaseHas('order_items', [
            'menu_item_id' => $item->id,
            'qty' => 2,
            'note' => 'No pickles'
        ]);
    }

    public function test_customer_can_request_bill()
    {
        $table = Table::factory()->create(['code' => 't1']);
        $order = Order::factory()->create([
            'table_id' => $table->id,
            'status' => 'served',
            'bill_requested_at' => null
        ]);

        $response = $this->from('/t/t1/status')->post("/t/t1/order/{$order->id}/bill");

        $response->assertRedirect('/t/t1/status');
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
        
        $order->refresh();
        $this->assertNotNull($order->bill_requested_at);
    }
}