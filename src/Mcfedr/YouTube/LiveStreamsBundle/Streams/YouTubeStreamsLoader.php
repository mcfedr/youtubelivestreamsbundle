<?php
/**
 * Created by mcfedr on 06/03/2014 20:19
 */

namespace Mcfedr\YouTube\LiveStreamsBundle\Streams;

use Doctrine\Common\Cache\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use Mcfedr\YouTube\LiveStreamsBundle\Exception\MissingChannelIdException;

class YouTubeStreamsLoader
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $channelId;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var int
     */
    protected $cacheTimeout;

    /**
     * @param Client $client
     * @param string $channelId
     * @param Cache $cache
     * @param int $cacheTimeout
     */
    public function __construct(Client $client, $channelId = null, Cache $cache = null, $cacheTimeout = 0)
    {
        $this->client = $client;
        $this->channelId = $channelId;
        $this->cache = $cache;
        $this->cacheTimeout = $cacheTimeout;
    }

    /**
     * Get the list of videos from YouTube
     *
     * @param string $channelId
     * @throws \Mcfedr\YouTube\LiveStreamsBundle\Exception\MissingChannelIdException
     * @return array
     */
    public function getStreams($channelId = null)
    {
        if (!$channelId) {
            $channelId = $this->channelId;
        }

        if (!$channelId) {
            throw new MissingChannelIdException("You must specify the channel id");
        }

        if ($this->cache) {
            $data = $this->cache->fetch($this->getCacheKey($channelId));
            if ($data !== false) {
                return $data;
            }
        }

        /** @var Response $searchResponse */
        $searchResponse = $this->client->get(
            'search',
            [
                'query' => [
                    'part' => 'id',
                    'channelId' => $channelId,
                    'eventType' => 'live',
                    'type' => 'video',
                    'maxResults' => 50
                ]
            ]
        );

        $searchData = $searchResponse->json();

        /** @var Response $videosResponse */
        $videosResponse = $this->client->get(
            'videos',
            [
                'query' => [
                    'part' => 'id,snippet,liveStreamingDetails',
                    'id' => implode(
                        ',',
                        array_map(
                            function ($video) {
                                return $video['id']['videoId'];
                            },
                            $searchData['items']
                        )
                    )
                ]
            ]
        );

        $videosData = $videosResponse->json();

        $streams = array_map(
            function ($video) {
                return [
                    'name' => $video['snippet']['title'],
                    'thumb' => $video['snippet']['thumbnails']['high']['url'],
                    'videoId' => $video['id']
                ];
            },
            array_values(
                array_filter(
                    $videosData['items'],
                    function ($video) {
                        return !isset($video['liveStreamingDetails']['actualEndTime']);
                    }
                )
            )
        );

        if ($this->cache && $this->cacheTimeout > 0) {
            $this->cache->save($this->getCacheKey($channelId), $streams, $this->cacheTimeout);
        }

        return $streams;
    }

    protected function getCacheKey($channelId)
    {
        return "mcfedr_you_tube_live_streams.$channelId";
    }
}
