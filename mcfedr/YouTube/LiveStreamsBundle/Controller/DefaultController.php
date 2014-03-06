<?php

namespace mcfedr\YouTube\LiveStreamsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/streams")
     */
    public function indexAction()
    {
        /** @var \Guzzle\Http\Message\Response $response */
        $response = $this->get('mcfedr_you_tube_live_streams.streams');
        $body = $response->getBody(true);
        $data = json_decode($body, true);

        $output = array_map(function($video) {
            return [
                'name' => $video['snippet']['title'],
                'thumb' => $video['snippet']['thumbnails']['high'],
                'videoId' => $video['id']['videoId']
            ];
        }, $data['items']);

        return new JsonResponse($output);
    }
}
