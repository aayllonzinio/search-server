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

use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\DeleteItems;
use Apisearch\Server\Domain\Command\IndexItems;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;

/**
 * Class DeleteItemsMiddleware.
 */
class DeleteItemsMiddleware implements PluginMiddleware
{
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
         * @var DeleteItems $command
         */
        $newCommand = new DeleteItems(
            new RepositoryReference(
                $command->getRepositoryReference()->getAppId(),
                $command->getRepositoryReference()->getIndex() . '_lang_*'
            ),
            $command->getToken(),
            $command->getItemsUUID()
        );

        $next($newCommand);
    }

    /**
     * Event subscribed namespace.
     *
     * @return string
     */
    public function getSubscribedEvent(): string
    {
        return DeleteItems::class;
    }
}
