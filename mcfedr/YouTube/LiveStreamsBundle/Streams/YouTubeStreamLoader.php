<?php
/**
 * Created by mcfedr on 06/03/2014 20:19
 */

namespace mcfedr\YouTube\LiveStreamsBundle\Streams;

use Guzzle\Http\Client;
use Psr\Log\LoggerInterface;

class YouTubeStreamLoader
{
    //1 Hour in seconds
    const CACHE_TIMEOUT = 3600;

    /**
     * @var \Guzzle\Http\Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $channelId;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param Client $client
     * @param string $channelId
     * @param string $cacheDir
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(Client $client, $channelId, $cacheDir, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->channelId = $channelId;
        $this->cacheDir = $cacheDir;
        $this->logger = $logger;
    }

    /**
     * Get the list of videos from YouTube
     *
     * @return array
     */
    public function getStreams()
    {
        $file = $this->getCacheFile();
        if (file_exists($file)) {
            $this->logger->debug(
                'Reading cached streams',
                [
                    'file' => $file
                ]
            );
            if (($streamsJson = file_get_contents($file)) && $streamsData = json_decode($streamsJson, true)) {
                if (time() - strtotime($streamsData['date']) < static::CACHE_TIMEOUT) {
                    return $streamsData['streams'];
                }

                $this->logger->info(
                    'Cache timed out',
                    [
                        'date' => $streamsData['date']
                    ]
                );
            } else {
                $this->logger->warning(
                    'Failed to read streams cache',
                    [
                        'file' => $file,
                        'streamsJson' => $streamsJson
                    ]
                );
            }
        }

        $response = $this->client->get(
            'search',
            [],
            [
                'query' => [
                    'part' => 'snippet',
                    'channelId' => $this->channelId,
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
                    'thumb' => $video['snippet']['thumbnails']['high'],
                    'videoId' => $video['id']['videoId']
                ];
            },
            $youTubeData['items']
        );

        $this->cacheStreams($streams);

        return $streams;
    }

    protected function cacheStreams($streams)
    {
        $this->logger->debug(
            'Caching streams',
            [
                'streams' => $streams
            ]
        );

        $dir = $this->getCacheDir();
        if (!file_exists($dir)) {
            mkdir($this->getCacheDir(), 0777, true);
        }

        $file = $this->getCacheFile();
        if (!file_put_contents(
            $file,
            json_encode(
                [
                    'streams' => $streams,
                    'date' => date('r')
                ]
            )
        )
        ) {
            $this->logger->error(
                'Failed to write streams cache',
                [
                    'file' => $file
                ]
            );
        }
    }

    protected function getCacheDir()
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . 'mcfedr_you_tube_live_streams';
    }

    protected function getCacheFile()
    {
        return $this->getCacheDir() . DIRECTORY_SEPARATOR . $this->channelId;
    }
}
