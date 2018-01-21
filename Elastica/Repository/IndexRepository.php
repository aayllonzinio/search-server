<?php

/*
 * This file is part of the Apisearch Server
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author PuntMig Technologies
 */

declare(strict_types=1);

namespace Apisearch\Server\Elastica\Repository;

use Apisearch\Model\Coordinate;
use Apisearch\Model\Item;
use Apisearch\Server\Domain\Repository\Repository\IndexRepository as IndexRepositoryInterface;
use Apisearch\Server\Elastica\ElasticaWrapperWithRepositoryReference;
use Elastica\Document;
use Elastica\Document as ElasticaDocument;
use Elastica\Index\Stats;

/**
 * Class IndexRepository.
 */
class IndexRepository extends ElasticaWrapperWithRepositoryReference implements IndexRepositoryInterface
{
    /**
     * Create the index.
     */
    public function createIndex()
    {
        @mkdir($this->getConfigPath(), 0755, true);
        $this
            ->elasticaWrapper
            ->createIndex(
                $this->getRepositoryReference(),
                $this->repositoryConfig['shards'],
                $this->repositoryConfig['replicas']
            );

        $this
            ->elasticaWrapper
            ->createIndexMapping($this->getRepositoryReference());

        $this->refresh();
    }

    /**
     * Delete the index.
     */
    public function deleteIndex()
    {
        $this
            ->elasticaWrapper
            ->deleteIndex($this->getRepositoryReference());

        $this->deleteConfigFolder();
        @rmdir($this->getConfigPath());
    }

    /**
     * Reset the index.
     */
    public function resetIndex()
    {
        $this
            ->elasticaWrapper
            ->resetIndex($this->getRepositoryReference());

        $this->refresh();
    }

    /**
     * Get the index stats.
     *
     * @return Stats
     */
    public function getIndexStats(): Stats
    {
        return $this
            ->elasticaWrapper
            ->getIndexStats($this->getRepositoryReference());
    }

    /**
     * Generate items documents.
     *
     * @param Item[] $items
     */
    public function addItems(array $items)
    {
        $documents = [];
        foreach ($items as $item) {
            $documents[] = $this->createItemDocument($item);
        }

        if (empty($documents)) {
            return;
        }

        $this
            ->elasticaWrapper
            ->addDocuments(
                $this->getRepositoryReference(),
                $documents
            );

        $this->refresh();
    }

    /**
     * Create item document.
     *
     * @param Item $item
     *
     * @return Document
     */
    private function createItemDocument(Item $item): Document
    {
        $uuid = $item->getUUID();
        $itemDocument = [
            'uuid' => [
                'id' => $uuid->getId(),
                'type' => $uuid->getType(),
            ],
            'coordinate' => $item->getCoordinate() instanceof Coordinate
                ? $item
                    ->getCoordinate()
                    ->toArray()
                : null,
            'metadata' => array_filter($item->getMetadata()),
            'indexed_metadata' => array_filter($item->getIndexedMetadata()),
            'searchable_metadata' => array_filter(
                $item->getSearchableMetadata(),
                [$this, 'filterElement']
            ),
            'exact_matching_metadata' => array_values(
                array_filter(
                    $item->getExactMatchingMetadata(),
                    [$this, 'filterElement']
                )
            ),
            'suggest' => array_filter($item->getSuggest()),
        ];

        return new ElasticaDocument($uuid->composeUUID(), $itemDocument);
    }

    /**
     * Specific array filter.
     *
     * @param mixed $element
     *
     * @return mixed $element
     */
    private function filterElement($element)
    {
        return !(
            is_null($element) ||
            (is_bool($element) && !$element) ||
            (is_array($element) && empty($element))
        );
    }

    /**
     * Delete all config folder.
     */
    private function deleteConfigFolder()
    {
        $files = glob($this->getConfigPath().'/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
