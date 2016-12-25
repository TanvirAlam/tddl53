<?php

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FakePaymentGatewayTest extends TestCase
{
    //use DatabaseMigrations;

    /** @test  */
    public function changeWithAValidPaymentTokenAreSuccessful()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

    /** @test  */
    public function chargesWithAnInvalidPaymentTokenFail()
    {
        try {
            $paymentGateway = new FakePaymentGateway;
            $paymentGateway->charge(2500, 'invalid');
        } catch (PaymentFailedException $e) {
            return;
        }

        $this->fail();

    }

/*    public function userCannotViewUnpublishedConcertListing()
    {

    }*/

}
