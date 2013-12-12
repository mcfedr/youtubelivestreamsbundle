<?php
namespace mcfedr\HromPushBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Http\Client;
use ekreative\AWSPushBundle\Message\Message;
use ekreative\AWSPushBundle\Service\Messages;

class TwitterFetchCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('hrom:twitter:fetch')
            ->setDescription('Check for new tweets from hrom')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $request = $this->getTwitterClient()->get('statuses/user_timeline.json');
        $request->getQuery()->set('screen_name', $this->getContainer()->getParameter('mcfedr_hrom_push.username'));
        $request->getQuery()->set('exclude_replies', 'true');

        $response = $request->send()->json();
        $store = $this->getStore();

        foreach(array_reverse($response) as $tweet) {
            if($tweet['id'] > $store['latestTweet']) {
                $store['latestTweet'] = $tweet['id'];
                $this->pushTweet($tweet);
                $output->writeln('pushed ' . $tweet['id']);
            }
        }

        $this->saveStore($store);
    }

    private function pushTweet($tweet) {
        $m = new Message($tweet['text']);
        $m->setCustom([
            'text' => $tweet['text']
        ]);
        $this->getPushMessages()->broadcast($m);
    }

    /**
     * @return Client
     */
    private function getTwitterClient() {
        return $this->getContainer()->get('twitter_client');
    }

    /**
     * @return Messages
     */
    private function getPushMessages() {
        return $this->getContainer()->get('pushMessages');
    }

    private function getStore() {
        $storeFile = $this->getContainer()->getParameter('mcfedr_hrom_push.store');
        if(file_exists($storeFile)) {
            return json_decode(file_get_contents($storeFile), true);
        }
        return [
            'latestTweet' => 0
        ];
    }

    private function saveStore($store) {
        $storeFile = $this->getContainer()->getParameter('mcfedr_hrom_push.store');
        file_put_contents($storeFile, json_encode($store));
    }
}
