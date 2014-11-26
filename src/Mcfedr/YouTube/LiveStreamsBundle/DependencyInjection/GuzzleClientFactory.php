<?php
/**
 * Created by mcfedr on 26/11/14 09:23
 */

namespace Mcfedr\YouTube\LiveStreamsBundle\DependencyInjection;

use GuzzleHttp\Client;

class GuzzleClientFactory
{
    public static function get(array $options, array $subscribers)
    {
        $client = new Client($options);
        foreach ($subscribers as $subscriber) {
            $client->getEmitter()->attach($subscriber);
        }
        return $client;
    }
}
