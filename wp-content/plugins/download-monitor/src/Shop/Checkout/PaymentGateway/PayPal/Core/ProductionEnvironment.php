<?php

namespace Never5\DownloadMonitor\Shop\Checkout\PaymentGateway\PayPal\Core;

class ProductionEnvironment extends PayPalEnvironment
{
    public function __construct($clientId, $clientSecret)
    {
        parent::__construct($clientId, $clientSecret);
    }

    public function baseUrl()
    {
        return "https://api.paypal.com";
    }
}
