# YouTube Live Streams Bundle

A bundle for sending tweets as push notifications

[![Latest Stable Version](https://poser.pugx.org/mcfedr/youtubelivestreamsbundle/v/stable.png)](https://packagist.org/packages/mcfedr/youtubelivestreamsbundle)
[![License](https://poser.pugx.org/mcfedr/youtubelivestreamsbundle/license.png)](https://packagist.org/packages/mcfedr/youtubelivestreamsbundle)
[![Build Status](https://travis-ci.org/mcfedr/youtubelivestreamsbundle.svg?branch=master)](https://travis-ci.org/mcfedr/youtubelivestreamsbundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/22b72cc8-cd47-4784-8e6a-76c7cddbbe0d/mini.png)](https://insight.sensiolabs.com/projects/22b72cc8-cd47-4784-8e6a-76c7cddbbe0d)

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
            new Mcfedr\YouTube\LiveStreamsBundle\McfedrYouTubeLiveStreamsBundle(),

### Routing

Setup the controllers in your routing.yml

    mcfedr_you_tube_live_streams:
        resource: "@McfedrYouTubeLiveStreamsBundle/Controller/"
        type:     annotation
        prefix:   /


## Config

This is sample configuration, to add to your config.yml

    mcfedr_you_tube_live_streams:
        api_key: youtube api key
        channel_id: id of the channel
        cache_timeout: 3600 #cache for an hour

You might want to put something like this in your config_dev.yml

    mcfedr_you_tube_live_streams:
        cache_timeout: 0

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
