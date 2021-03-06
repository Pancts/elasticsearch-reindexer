<?php

/*
 * This file is part of the elasticsearch-reindexer package.
 *
 * (c) Martynas Sudintas <martynas.sudintas@ongr.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ElasticsearchReindexer\Command;

use ElasticsearchReindexer\Indexer;
use ElasticsearchReindexer\Scanner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReindexCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('reindexer')
            ->addArgument(
                'scan',
                InputArgument::REQUIRED,
                'Index name to scan'
            )
            ->addArgument(
                'index',
                InputArgument::REQUIRED,
                'Index name to index'
            )
            ->addOption(
                'host',
                'h',
                InputOption::VALUE_REQUIRED,
                'Elasticsearch index and scan client host',
                '127.0.0.1:9200'
            )
            ->addOption(
                'index-host',
                'ih',
                InputOption::VALUE_REQUIRED,
                'Elasticsearch index client host',
                null
            )
            ->addOption(
                'scan-host',
                'sh',
                InputOption::VALUE_REQUIRED,
                'Elasticsearch scan client host',
                null
            )
            ->addOption(
                'bulk',
                null,
                InputOption::VALUE_REQUIRED,
                'Indexing bulk size',
                100
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scanner = new Scanner($this->getHost($input, 'scan-host'), $input->getArgument('scan'));
        $indexer = new Indexer($this->getHost($input, 'index-host'), $input->getArgument('index'));
        $indexer->setBulkSize(intval($input->getOption('bulk')));

        $output->writeln('<info>Scanning & indexing...</info>');
        $progress = new ProgressBar($output);
        $progress->setFormat('debug_nomax');
        $progress->setRedrawFrequency(100);

        foreach ($scanner->scan() as $document) {
            $indexer->index($document);
            $progress->advance();
        }

        $progress->finish();
        $output->write("\n<info>Flushing...</info>");
        $indexer->ensure();
        $output->writeln('<comment>done.</comment>');

        return 0;
    }

    /**
     * Returns specific host, fallbacks to default if does not exist.
     *
     * @param InputInterface $input
     * @param string         $type
     *
     * @return string
     */
    private function getHost(InputInterface $input, $type)
    {
        return $input->getOption($type) !== null ? $input->getOption($type) : $input->getOption('host');
    }
}
