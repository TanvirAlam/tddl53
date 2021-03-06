<?php

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    /** @test  */
    public function userCanViewAPublishedConcertListingTest()
    {
        //Arrange
        //Create a concert
        $concert = factory(Concert::class)->states('published')->create([

            'title' => 'The Red Chord',
            'subtitle' => 'With love',
            'date' => Carbon::parse('December 1, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Helsingor Kommune',
            'venue_address' => 'Nordvej 19, 1tv',
            'city' => 'Helsingor',
            'state' => 'H',
            'zip' => '3000',
            'addition_information' => 'Call-92165545',

        ]);



        //Act
        $this->visit('/concerts/'.$concert->id);

        //Assert
        $this->see('The Red Chord');
        $this->see('With love');
        $this->see('December 1, 2016');
        $this->see('8:00pm');
        $this->see('32.50');
        $this->see('The Helsingor Kommune');
        $this->see('Nordvej 19, 1tv');
        $this->see('Helsingor, H 3000');
        $this->see('Call-92165545');
    }

    /** @test  */
    public function userCannotViewUnpublishedConcertListing()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $this->get('/concerts/'.$concert->id);

        $this->assertResponseStatus(404);
    }

}
