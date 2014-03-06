# YouTube Live Streams Bundle

A bundle for sending tweets as push notifications

[![Latest Stable Version](https://poser.pugx.org/mcfedr/youtubelivestreamsbundle/v/stable.png)](https://packagist.org/packages/mcfedr/youtubelivestreamsbundle)
[![License](https://poser.pugx.org/mcfedr/youtubelivestreamsbundle/license.png)](https://packagist.org/packages/mcfedr/youtubelivestreamsbundle)

## Install

### Composer

    php composer.phar require mcfedr/youtubelivestreamsbundle

### AppKernel

Include the bundle in your AppKernel
You need to also load the AWSPushBundle

    public function registerBundles()
    {
        $bundles = array(
            ...
            new new mcfedr\YouTube\LiveStreamsBundle\mcfedrYouTubeLiveStreamsBundle(),

### Routing

Setup the controllers in your routing.yml

    mcfedr_you_tube_live_streams:
        resource: "@mcfedrYouTubeLiveStreamsBundle/Controller/"
        type:     annotation
        prefix:   /


## Config

This is sample configuration, to add to your config.yml

    mcfedr_you_tube_live_streams:
        api_key: youtube api key
        channel_id: id of the channel
        cache_timeout: 3600 #cache for an hour

## Usage

You get get your list of streams

    GET /streams
    {
        "streams": [
            {
                "name": "Громадське ONLINE. 6 березня",
                "thumb": "https://i.ytimg.com/vi/Ou7hfc_LAeY/hqdefault.jpg",
                "videoId": "Ou7hfc_LAeY"
            },
            {
                "name": "Hromadske ONLINE RU",
                "thumb": "https://i.ytimg.com/vi/O5j1mifrhK4/hqdefault.jpg",
                "videoId": "O5j1mifrhK4"
            }
        ]
    }