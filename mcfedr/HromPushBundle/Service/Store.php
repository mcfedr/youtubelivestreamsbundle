<?php
namespace mcfedr\HromPushBundle\Service;


class Store {
    private $path;

    public function __construct($path) {
        $this->path = $path;
    }

    public function getStore() {
        if(file_exists($this->path)) {
            return json_decode(file_get_contents($this->path), true);
        }
        return [
            'latestTweet' => 0
        ];
    }

    public function saveStore($store) {
        file_put_contents($this->path, json_encode($store));
    }
}
