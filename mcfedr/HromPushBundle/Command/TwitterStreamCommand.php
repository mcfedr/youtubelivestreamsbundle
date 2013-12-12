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
use Guzzle\Stream\PhpStreamRequestFactory;

class TwitterStreamCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('hrom:twitter:stream')
            ->setDescription('Check for new tweets from hrom using a stream')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $request = $this->getTwitterStreamClient()->post('statuses/filter.json', null, array(
            'follow' => $this->getContainer()->getParameter('mcfedr_hrom_push.userid') . ',40682763'
        ));

        $factory = new PhpStreamRequestFactory();
        $stream = $factory->fromRequest($request);

        // Read until the stream is closed
        while (!$stream->feof()) {
            // Read a line from the stream
            $line = $stream->readLine();
            if($line == '') {
                continue;
            }
            $data = json_decode($line, true);
            if($data['text']) {
                $this->pushTweet($data);
            }
        }
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
    private function getTwitterStreamClient() {
        return $this->getContainer()->get('twitter_stream_client');
    }

    /**
     * @return Messages
     */
    private function getPushMessages() {
        return $this->getContainer()->get('pushMessages');
    }
}
