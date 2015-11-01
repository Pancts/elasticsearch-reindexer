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

class Scanner extends AbstractClientAware
{
    /**
     * Scans elasticsearch index.
     *
     * @return \Generator
     */
    public function scan()
    {
        $response = $this->getClient()->search($this->getParams());
        $id = $response['_scroll_id'];

        while (true) {
            $response = $this
                ->getClient()
                ->scroll(
                    [
                        'scroll_id' => $id,
                        'scroll' => '1m'
                    ]
                );
            if (count($response['hits']['hits']) > 0) {
                $id = $response['_scroll_id'];
                foreach ($response['hits']['hits'] as $hit) {
                    yield $hit;
                }
            } else {
                break;
            }
        }
    }

    /**
     * Parameters for search request.
     *
     * @return array
     */
    protected function getParams()
    {
        return [
            'search_type' => 'scan',
            'scroll' => '1m',
            'size' => 25,
            'index' => $this->getIndex(),
            'body' => [
                'query' => [
                    'match_all' => []
                ]
            ]
        ];
    }
}
