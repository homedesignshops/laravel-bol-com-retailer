<?php


namespace HomeDesignShops\LaravelBolComRetailer\Models;


class Transport
{
    /**
     * @var string
     */
    public $transporterCode;

    /**
     * @var string
     */
    public $trackAndTraceCode;

    /**
     * @var string
     */
    public $shipmentReference;

    /**
     * The key is de Bol.com shipment name.
     * The values will be mapped to the correct shipment name.
     *
     * @var array
     */
    protected $transportCodes = [];

    /**
     * TransportItem constructor.
     * @param string $transporterCode
     * @param string $trackingCode
     * @param string $shipmentReference
     */
    public function __construct(string $transporterCode, string $trackingCode, string $shipmentReference)
    {
        $this->transporterCode = $transporterCode;
        $this->trackAndTraceCode = $trackingCode;
        $this->shipmentReference = $shipmentReference;
    }
}
