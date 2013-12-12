1<?php

namespace mcfedr\TwitterPushBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DeviceController extends Controller {
    /**
     * @Route("/devices")
     * @Method({"POST"})
     */
    public function registerDeviceAction(Request $request) {
        $data = $this->handleJSONRequest($request);
        if($data instanceof Response) {
            return $data;
        }

        if(!isset($data['deviceID']) || !isset($data['platform'])) {
            return new Response('Missing parameters', 400);
        }

        try {
            if($this->getPushDevices()->registerDevice($data['deviceID'], $data['platform'])) {
                return new Response('Device registered', 200);
            }
        }
        catch(PlatformNotConfiguredException $e) {
            return new Response('Unknown platform', 400);
        }

        return new Response('Unknown error', 500);
    }

    /**
     * @return Devices
     */
    private function getPushDevices() {
        return $this->get('push_devices');
    }
}
