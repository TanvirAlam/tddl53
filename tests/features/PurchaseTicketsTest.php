<?php

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseTicketsTests extends TestCase
{
    /*use DatabaseMigrations;*/

    /** @test  */
    public function customerCanPurchaseConcertTickets()
    {
        //Arrange
        //Create a concert
        $concert = factory(Concert::class)->create(['tickets_price' => 3250]);

        //act

        //Purchase concert tickets
        $this->json('POST', "/concerts/{$concert->id}/orders", [
           'email' => 'jhon@example.com',
            'tickets_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);


        //Assert
        //make sure the customer was changed the correct amount
        $this->assertEquals(9750, $paymentGateway->totalCharges());

        //make sure that an order exists for this customer
        /*$this->assertTrue($concert->orders->contains(function ($order) {
            return $order->email == 'jhon@example.com';
        }));*/


        $order = $concert->order()-where('email', 'jhon@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets->count());
    }

    /** @test  */
    /*public function userCannotViewUnpublishedConcertListing()
    {

    }*/

}
