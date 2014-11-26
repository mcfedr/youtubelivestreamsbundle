<?php
/**
 * Created by mcfedr on 15/03/2014 23:56
 */

namespace Mcfedr\YouTube\LiveStreamsBundle\Command;

use Mcfedr\YouTube\LiveStreamsBundle\Streams\YouTubeStreamsLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class YouTubeStreamsCommand extends Command
{
    /**
     * @var \Mcfedr\YouTube\LiveStreamsBundle\Streams\YouTubeStreamsLoader
     */
    protected $loader;

    /**
     * @var null|string
     */
    protected $channelId;

    /**
     * @param YouTubeStreamsLoader $loader
     * @param string $channelId
     */
    public function __construct(YouTubeStreamsLoader $loader, $channelId = null)
    {
        parent::__construct();
        $this->loader = $loader;
        $this->channelId = $channelId;
    }

    protected function configure()
    {
        $this
            ->setName('mcfedr:youtube:streams')
            ->setDescription('Show the streams list')
            ->addOption('channelId', null, InputOption::VALUE_REQUIRED, 'The channel to list from', $this->channelId);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $streams = $this->loader->getStreams($input->getOption('channelId'));
        /** @var TableHelper $table */
        $table = $this->getApplication()->getHelperSet()->get('table');
        $table->setHeaders(['Name', 'Thumb', 'Video Id'])
            ->setRows(array_map('array_values', $streams));
        $table->render($output);
    }
}
