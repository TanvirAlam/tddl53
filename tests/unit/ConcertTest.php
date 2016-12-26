<?php

use App\Concert;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_get_formatted_date()
    {
        //create a concert with a known date
        $concert = factory(Concert::class)->make([
           'date' => Carbon::parse('2016-12-01 8:00pm'),

        ]);

        //verify the date is formatted as expected
        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /** @test */
    public function can_get_formatted_time()
    {
        //create a concert with a known date
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),

        ]);

        //verify the date is formatted as expected
        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    public function can_get_ticket_price_in_dollars()
    {
        //create a concert with a known date
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750,

        ]);

        //verify the date is formatted as expected
        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    public function concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA = factory(Concert::class)->create([
            'published_at' => Carbon::parse('-1 week'),
        ]);

        $publishedConcertB = factory(Concert::class)->create([
            'published_at' => Carbon::parse('-1 week'),
        ]);

        $unpublishedConcert = factory(Concert::class)->create([
            'published_at' => null,
        ]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test  */
    public function canOrderConcertTickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $order = $concert->orderTickets('jane@example.com', 3);
        $this->assertEquals('jane@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());

    }

    /** @test */
    public function canAddTickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(50);
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    public function ticketsRemainingDesNotIncludeTicketsAssociatedWithAnOrder()
    {
        $concert = factory(Concert::class)->create()->addTickets(50);
        $concert->orderTickets('jane@example.com', 30);
        $this->assertEquals(20, $concert->ticketsRemaining());

    }

    /** @test */
    public function tryingToPurchaseMoreTicketsThanremainThrowsAnException()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);

        try {
            $concert->orderTickets('jane@example.com', 11);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('john@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order succeeded even though there were not enough tickets remaining.');
    }

    /** @test */
    public function cannotOrderTicketsThatHaveAlreadyBeenPurchase()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);
        $concert->orderTickets('jane@example.com', 8);

        try {
            $concert->orderTickets('jane@example.com', 3);
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('john@example.com'));
            $this->assertEquals(2, $concert->ticketsRemaining());
            return;
        }

        $this->fail('Order succeeded even though there were not enough tickets remaining.');
    }

    /** @test */
    public function canReserveAvailableTickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reserveTickets = $concert->reserveTickets(2);

        $this->assertCount(2, $reserveTickets);
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    public function cannotReserveTicketsThatHaveAlreadyBeenPurchased()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $concert->orderTickets('jane@example.com', 2);

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Reserve tickets succeeded even though the tickets were already sold.");
    }

    /** @test */
    public function cannotReserveTicketsThatHaveAlreadyBeenReserved()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);
        $concert->reserveTickets(2);

        try {
            $concert->reserveTickets(2);
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Reserve tickets succeeded even though the tickets were already reserved.");
    }
}
