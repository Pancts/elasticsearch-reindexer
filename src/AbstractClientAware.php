<?php

/*
 * This file is part of the elasticsearch-reindexer package.
 *
 * (c) Martynas Sudintas <martynas.sudintas@ongr.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ElasticsearchReindexer;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

abstract class AbstractClientAware
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $index;

    /**
     * Initializes elasticsearch client.
     *
     * @param string $host
     * @param string $index
     */
    public function __construct($host, $index)
    {
        $this->index = $index;
        $this->client = ClientBuilder::create()
            ->setHosts([$host])
            ->build();
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    protected function getIndex()
    {
        return $this->index;
    }
}
