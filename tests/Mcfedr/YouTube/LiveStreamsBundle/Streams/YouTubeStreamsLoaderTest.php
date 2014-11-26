<?php
/**
 * Created by mcfedr on 26/11/14 11:33
 */

namespace Mcfedr\YouTube\LiveStreamsBundle\Streams;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;

class YouTubeStreamsLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetStreams()
    {
        $client = new Client();

        $mock = new Mock($this->getResponses());

        $client->getEmitter()->attach($mock);

        $loader = new YouTubeStreamsLoader($client, 'UC2oGvjIJwxn1KeZR3JtE-uQ');
        $json = $loader->getStreams();
        $this->assertInternalType('array', $json);
    }

    /**
     * @expectedException \Mcfedr\YouTube\LiveStreamsBundle\Exception\MissingChannelIdException
     */
    public function testGetStreamsNoChannel()
    {
        $loader = new YouTubeStreamsLoader(new Client());
        $loader->getStreams();
    }

    private function getResponses() {
        return [
            new Response(200, ['content-type' => 'application/json; charset=UTF-8'], Stream::factory(<<<RESPONSE
{
    "kind": "youtube#searchListResponse",
    "etag": "\"SXtVtkiDWqZpl4dTpZv5nFU7t-8/lYDDt-qOMHuMscxPrY7JL4FLBZo\"",
    "pageInfo": {
        "totalResults": 1,
        "resultsPerPage": 50
    },
    "items": [
        {
            "kind": "youtube#searchResult",
            "etag": "\"SXtVtkiDWqZpl4dTpZv5nFU7t-8/RHmF4hOYje9IeGWhjR8mPagRSzo\"",
            "id": {
                "kind": "youtube#video",
                "videoId": "RqbyYOCAFJU"
            }
        }
    ]
}

RESPONSE
            )),
            new Response(200, ['content-type' => 'application/json; charset=UTF-8'], Stream::factory(<<<RESPONSE
{
    "kind": "youtube#videoListResponse",
    "etag": "\"SXtVtkiDWqZpl4dTpZv5nFU7t-8/G3khJbCuWU63t6JknnbRxuDItNg\"",
    "pageInfo": {
        "totalResults": 1,
        "resultsPerPage": 1
    },
    "items": [
        {
            "kind": "youtube#video",
            "etag": "\"SXtVtkiDWqZpl4dTpZv5nFU7t-8/j1FFP_ApkEIp6EApvE8wewAVtAU\"",
            "id": "RqbyYOCAFJU",
            "snippet": {
                "publishedAt": "2014-11-21T08:16:02.000Z",
                "channelId": "UC2oGvjIJwxn1KeZR3JtE-uQ",
                "title": "Hromadske",
                "description": "Descr",
                "thumbnails": {
                    "default": {
                        "url": "https://i.ytimg.com/vi/RqbyYOCAFJU/default_live.jpg",
                        "width": 120,
                        "height": 90
                    },
                    "medium": {
                        "url": "https://i.ytimg.com/vi/RqbyYOCAFJU/mqdefault_live.jpg",
                        "width": 320,
                        "height": 180
                    },
                    "high": {
                        "url": "https://i.ytimg.com/vi/RqbyYOCAFJU/hqdefault_live.jpg",
                        "width": 480,
                        "height": 360
                    },
                    "standard": {
                        "url": "https://i.ytimg.com/vi/RqbyYOCAFJU/sddefault_live.jpg",
                        "width": 640,
                        "height": 480
                    },
                    "maxres": {
                        "url": "https://i.ytimg.com/vi/RqbyYOCAFJU/maxresdefault_live.jpg",
                        "width": 1280,
                        "height": 720
                    }
                },
                "channelTitle": "Title",
                "categoryId": "25",
                "liveBroadcastContent": "live"
            },
            "liveStreamingDetails": {
                "actualStartTime": "2014-11-21T08:29:01.409Z",
                "scheduledStartTime": "2014-11-21T09:00:00.000Z",
                "concurrentViewers": "2864"
            }
        }
    ]
}
RESPONSE
            )),
        ];
    }
}
