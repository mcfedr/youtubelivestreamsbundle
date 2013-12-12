<?php
namespace mcfedr\TwitterPushBundle\Service;

use mcfedr\AWSPushBundle\Message\Message;

class TweetPusher {

    private $messages;

    public function __construct($messages) {
        $this->messages = $messages;
    }

    public function pushTweet($tweet) {
        $m = new Message($tweet['text']);
        $this->messages->broadcast($m);
    }
}
