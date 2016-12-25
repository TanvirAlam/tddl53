<?php

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseTicketsTests extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /** @test  */
    public function customerCanPurchaseConcertTickets()
    {
        //Create a concert
        $concert = factory(Concert::class)->create(['ticket_price' => 3250]);

        //act

        //Purchase concert tickets
        $this->json('POST', "/concerts/{$concert->id}/orders", [
           'email' => 'jhon@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        //Assert
        $this->assertResponseStatus(201);

        //make sure the customer was changed the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        //make sure that an order exists for this customer
        /*$this->assertTrue($concert->orders->contains(function ($order) {
            return $order->email == 'jhon@example.com';
        }));*/

        $order = $concert->orders()->where('email', 'jhon@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());
    }

    /** @test  */
    public function emailIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->create();

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertResponseStatus(422);

        $this->assertArrayHasKey('email', $this->decodeResponseJson());

    }

    /** @test  */
    /*public function userCannotViewUnpublishedConcertListing()
    {

    }*/

}
