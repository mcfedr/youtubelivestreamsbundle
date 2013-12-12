<?php
namespace mcfedr\HromPushBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Http\Client;
use mcfedr\HromPushBundle\Service\Store;
use mcfedr\HromPushBundle\Service\TweetPusher;

class TwitterFetchCommand extends Command {

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var TweetPusher
     */
    private $pusher;

    /**
     * @var string
     */
    private $username;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($client, $store, $pusher, $username, $logger) {
        parent::__construct();

        $this->client = $client;
        $this->store = $store;
        $this->pusher = $pusher;
        $this->username = $username;
        $this->logger = $logger;
    }

    protected function configure() {
        $this
            ->setName('hrom:twitter:fetch')
            ->setDescription('Check for new tweets from hrom')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $request = $this->client->get('statuses/user_timeline.json');
        $request->getQuery()->set('screen_name', $this->username);
        $request->getQuery()->set('exclude_replies', 'true');

        $response = $request->send()->json();
        $store = $this->store->getStore();

        foreach(array_reverse($response) as $tweet) {
            if($tweet['id'] > $store['latestTweet']) {
                $store['latestTweet'] = $tweet['id'];
                try {
                    $this->pusher->pushTweet($tweet);
                    $this->logger->info('Sent tweet', [
                        'TweetId' => $tweet['id']
                    ]);
                }
                catch(\Exception $e) {
                    $this->logger->error("Failed to push", [
                        'TweetId' => $tweet['id'],
                        'Exception' => $e
                    ]);
                }
            }
        }

        $this->store->saveStore($store);
    }
}
