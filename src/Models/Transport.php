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
    public $trackingCode;

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
        $this->transporterCode = $this->getCorrectTransportName($transporterCode);
        $this->trackingCode = $trackingCode;
        $this->shipmentReference = $shipmentReference;
        $this->transportCodes = config('bol-com-retailer.transport_codes');
    }

    /**
     * Returns the correct transport name.
     *
     * @param $name
     * @return bool|int|string
     */
    protected function getCorrectTransportName($name)
    {
        $name = strtolower($name);

        if(isset($this->shipmentMapping[$name])) {
            return $name;
        }

        foreach ($this->transportCodes as $key => $shipments) {
            if(in_array($name, $shipments, true)) {
                return $key;
            }
        }

        return $name;
    }
}
