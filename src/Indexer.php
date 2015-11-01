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

class Indexer extends AbstractClientAware
{
    /**
     * @var int
     */
    private $bulkSize = 100;

    /**
     * @var int
     */
    private $currentSize = 0;

    /**
     * @var array
     */
    private $bulk = [];

    /**
     * Indexes a document.
     *
     * @param array $document
     */
    public function index(array $document)
    {
        $head = array_diff_key($document, array_flip(['_score', '_source']));
        $head['_index'] = $this->getIndex();

        $this->addBulk(['index' => $head], $document['_source']);
    }

    /**
     * Adds document for bulk indexing.
     *
     * @param array $head
     * @param array $body
     */
    public function addBulk(array $head, array $body)
    {
        $this->bulk['body'][] = $head;
        $this->bulk['body'][] = $body;

        if (++$this->currentSize === $this->bulkSize) {
            $this->getClient()->bulk($this->bulk);
            $this->bulk = [];
            $this->currentSize = 0;
        }
    }

    /**
     * Ensures that all data has been sent and is searchable.
     */
    public function ensure()
    {
        if (!empty($this->bulk)) {
            $this->getClient()->bulk($this->bulk);
        }

        $this->getClient()->indices()->flush(['index' => $this->getIndex()]);
    }

    /**
     * @param int $bulkSize
     */
    public function setBulkSize($bulkSize)
    {
        $this->bulkSize = $bulkSize;
    }
}
