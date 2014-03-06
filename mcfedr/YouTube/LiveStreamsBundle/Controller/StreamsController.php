<?php

namespace mcfedr\YouTube\LiveStreamsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class StreamsController extends Controller
{
    /**
     * @Route("/streams")
     */
    public function indexAction()
    {
        return new JsonResponse($this->get('mcfedr_you_tube_live_streams.loader')->getStreams());
    }
}