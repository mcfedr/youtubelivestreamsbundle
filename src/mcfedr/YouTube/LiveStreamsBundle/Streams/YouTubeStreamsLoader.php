<?php
/**
 * Created by mcfedr on 06/03/2014 20:19
 */

namespace mcfedr\YouTube\LiveStreamsBundle\Streams;

use Doctrine\Common\Cache\Cache;
use Guzzle\Http\Client;
use mcfedr\YouTube\LiveStreamsBundle\Exception\MissingChannelIdException;
use Psr\Log\LoggerInterface;

class YouTubeStreamsLoader
{
    /**
     * @var \Guzzle\Http\Client
     */
    protected $client;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
     * @param \Psr\Log\LoggerInterface $logger
     * @param string $channelId
     * @param Cache $cache
     * @param int $cacheTimeout
     */
    public function __construct(Client $client, LoggerInterface $logger, $channelId = null, Cache $cache = null, $cacheTimeout = 0)
    {
        $this->client = $client;
        $this->channelId = $channelId;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->cacheTimeout = $cacheTimeout;
    }

    /**
     * Get the list of videos from YouTube
     *
     * @param string $channelId
     * @throws \mcfedr\YouTube\LiveStreamsBundle\Exception\MissingChannelIdException
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

        $response = $this->client->get(
            'search',
            [],
            [
                'query' => [
                    'part' => 'snippet',
                    'channelId' => $channelId,
                    'eventType' => 'live',
                    'type' => 'video'
                ]
            ]
        )->send();

        $youTubeData = json_decode($response->getBody(true), true);

        $streams = array_map(
            function ($video) {
                return [
                    'name' => $video['snippet']['title'],
                    'thumb' => $video['snippet']['thumbnails']['high']['url'],
                    'videoId' => $video['id']['videoId']
                ];
            },
            $youTubeData['items']
        );

        if ($this->cache && $this->cacheTimeout > 0) {
            $this->cache->save($this->getCacheKey($channelId), $streams, $this->cacheTimeout);
        }

        return $streams;
    }

    protected function getCacheKey($channelId)
    {
        return "'mcfedr_you_tube_live_streams.$channelId";
    }
}
