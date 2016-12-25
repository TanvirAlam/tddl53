<?php

use App\Concert;
use App\Order;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function convertToAnArray()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(5);

        $order = $concert->orderTickets('jane@example.com', 5);

        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $result);
    }

    /** @test */
    public function ticketsAreReleasedWhenAnOrderIsCancelled()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);

        $order = $concert->orderTickets('jane@example.com', 5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());
        $this->assertNull(Order::find($order->id));
    }
}