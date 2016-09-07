<?php
/**
 * Created by mcfedr on 07/09/2016 22:27
 */

namespace Mcfedr\YouTube\LiveStreamsBundle\DependencyInjection;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class GuzzleClientFactory
{
    public static function get(array $options, $key)
    {
        $stack = HandlerStack::create();
        $stack->unshift(Middleware::mapRequest(function (RequestInterface $request) use ($key) {
            return $request->withUri(Uri::withQueryValue($request->getUri(), 'key', $key));
        }));

        $options['handler'] = $stack;

        return new Client($options);
    }
}
