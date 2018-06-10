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

namespace Apisearch\Plugin\LanguageSplit\Domain\Middleware;

use Apisearch\Plugin\MetadataFields\Domain\Repository\MetadataRepository;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Result\Result;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;
use Apisearch\Server\Domain\Query\Query;

/**
 * Class QueryMiddleware.
 */
class QueryMiddleware implements PluginMiddleware
{
    /**
     * @var MetadataRepository
     *
     * Metadata repository
     */
    private $metadataRepository;

    /**
     * OnItemsWereIndexed constructor.
     *
     * @param MetadataRepository $metadataRepository
     */
    public function __construct(MetadataRepository $metadataRepository)
    {
        $this->metadataRepository = $metadataRepository;
    }

    /**
     * Execute middleware.
     *
     * @param CommandWithRepositoryReferenceAndToken $command
     * @param callable                               $next
     *
     * @return mixed
     */
    public function execute(
        CommandWithRepositoryReferenceAndToken $command,
        $next
    ) {
        /**
         * @var Query $command
         */
        $newCommand = new Query(
            new RepositoryReference(
                $command->getRepositoryReference()->getAppId(),
                $command->getRepositoryReference()->getIndex() . '_lang_*'
            ),
            $command->getToken(),
            $command->getQuery()
        );

        /**
         * @var Result
         */
        return $next($newCommand);
    }

    /**
     * Event subscribed namespace.
     *
     * @return string
     */
    public function getSubscribedEvent(): string
    {
        return Query::class;
    }
}
