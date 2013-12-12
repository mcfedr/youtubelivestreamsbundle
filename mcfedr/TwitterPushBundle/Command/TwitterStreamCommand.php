<?php
namespace mcfedr\TwitterPushBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Guzzle\Http\Client;
use Guzzle\Stream\PhpStreamRequestFactory;
use mcfedr\TwitterPushBundle\Service\TweetPusher;

class TwitterStreamCommand extends Command {

    /**
     * @var Client
     */
    private $client;

    /**
     * @var TweetPusher
     */
    private $pusher;

    /**
     * @var string
     */
    private $userid;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct($client, $pusher, $userid, $logger) {
        parent::__construct();

        $this->client = $client;
        $this->pusher = $pusher;
        $this->userid = $userid;
        $this->logger = $logger;
    }

    protected function configure() {
        $this
            ->setName('mcfedr:twitter:stream')
            ->setDescription('Check for new tweets using a stream and push them')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->logger->info('Opening twitter stream');

        $request = $this->client->post('statuses/filter.json', null, array(
            'follow' => $this->userid
        ));

        $factory = new PhpStreamRequestFactory();
        $stream = $factory->fromRequest($request);

        // Read until the stream is closed
        while (!$stream->feof()) {
            // Read a line from the stream
            $line = $stream->readLine();
            if(trim($line) == '') {
                continue;
            }
            $data = json_decode($line, true);
            if(isset($data['text'])) {
                try {
                    $this->pusher->pushTweet($data);
                    $this->logger->info('Sent tweet', [
                        'TweetId' => $data['id']
                    ]);
                }
                catch(\Exception $e) {
                    $this->logger->error("Failed to push", [
                        'TweetId' => $data['id'],
                        'Exception' => $e
                    ]);
                }
            }
            else {
                $this->logger->debug('Other message', [
                    'message' => $data
                ]);
            }
        }

        $this->logger->info('Stream finished');
    }
}
