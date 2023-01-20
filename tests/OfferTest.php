<?php

namespace HomeDesignShops\LaravelBolComRetailer\Tests;

use HomeDesignShops\LaravelBolComRetailer\BolComRetailerServiceProvider;
use HomeDesignShops\LaravelBolComRetailer\Facades\BolComRetailer;
use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use Illuminate\Support\Collection;
use Picqer\BolRetailerV8\Model\Order;
use Picqer\BolRetailerV8\Model\RetailerOffer;

class OfferTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [BolComRetailerServiceProvider::class];
    }

    /** @test */
    public function it_should_get_an_offer()
    {
        // Given we have an offer id
        $offer = BolComRetailer::getOffer('13722de8-8182-d161-5422-4a0a1caab5c8');

        $this->assertInstanceOf(RetailerOffer::class, $offer);

        // Should return null if order not found
        $offer = BolComRetailer::getOffer('123');
        $this->assertNull($offer);
    }

    /**
     * @test
     */
    public function it_should_update_an_offer_state()
    {
        // Given we have an offer
        $offer = BolComRetailer::getOffer('13722de8-8182-d161-5422-4a0a1caab5c8');

        // When we update the offer to inactive
        $offer->onHoldByRetailer = false;
        $processStatus = BolComRetailer::updateOffer($offer);

        // Then we expect a process status object back,
        // and it's pending
        $this->assertSame($processStatus->status, 'PENDING');
        $this->assertSame($processStatus->eventType, 'UPDATE_OFFER');
        $this->assertSame($processStatus->entityId, $offer->offerId);
    }
}
