<?php
declare(strict_types=1);

class IndexController extends ControllerBase
{

    public function indexAction()
    {

       /* $api = new \Binance\API('UOtNGCZKb6ybDSwQZFfibbivHqBUQKt5ve00NS2SNErJGB4K3QfKhzkuJKQPwQQz', 'AkN97ecgZO3v8a9cIXjlMEQbZRGtXuecXvrIqocCTro7RedjWsMVda0aZ5OLEywZ');
        //$api = new \Binance\RateLimiter($api);

        //var_dump($api->buy('BNBBTC', 1, 0.0005));
        date_default_timezone_set('UTC');

        $this->view->disable();
        try {
            $this->view->setVar('price', $api->depth('BNBBTC'));
            $this->view->count = $api->getRequestCount();

            $ticker = $api->price('BNBBTC'); // Make sure you have an updated ticker object for this to work
            $balances = $api->balances('BTC');
            print_r($balances);
        } catch (Exception $e) {
            $this->view->setVar('price', 'error');
        }*/
    }

}

