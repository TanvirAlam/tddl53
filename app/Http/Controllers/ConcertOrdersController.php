<?php

namespace App\Http\Controllers;

use App\Concert;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }
    public function store($concertId)
    {
        $concert = Concert::find($concertId);

        // Charging the customer
        $this->paymentGateway->charge(request('ticket_quantity') * $concert->ticket_price, request('payment_token'));

        //Creating the order
        //$order = $concert->orderTickets($email, $ticketQuantity);

        $order = $concert->orders()->create(['email' => request('email')]);

        foreach (range(1, request('ticket_quantity')) as $item) {
            $order->tickets()->create([]);
        }

        return response()->json([], 201);
    }
}
