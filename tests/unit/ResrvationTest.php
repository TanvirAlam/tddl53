<?php

use App\Concert;
use App\Reservation;
use App\Order;
use App\Exceptions\NotEnoughTicketsException;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ReservationTest extends TestCase
{

    /** @test */
    public function calculatingTheTotalCost()
    {
        //$concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(3);
        //$tickets = $concert->findTickets(3);
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }

}