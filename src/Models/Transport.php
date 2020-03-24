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
     * The key is de Bol.com shipment name.
     * The values will be mapped to the correct shipment name.
     *
     * @var array
     */
    protected $transportCodes = [
        'tnt' => [
            'postnl',
        ],
        'gls' => []
    ];

    /**
     * TransportItem constructor.
     * @param string $transporterCode
     * @param string $trackingCode
     */
    public function __construct(string $transporterCode, string $trackingCode)
    {
        $this->transporterCode = $this->getCorrectTransportName($transporterCode);
        $this->trackingCode = $trackingCode;
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

        return false;
    }
}
