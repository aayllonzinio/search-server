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
use Apisearch\Server\Domain\Command\IndexItems;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;

/**
 * Class IndexItemsMiddleware.
 */
class IndexItemsMiddleware implements PluginMiddleware
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
         * @var IndexItems $command
         */
        $items = $command->getItems();
        $languages = [];
        foreach ($items as $item) {
            $language = $item->get('language');
            if (!isset($languages[$language])) {
                $languages[$language] = [];
            }

            $languages[$language][] = $items;
        }

        foreach ($languages as $currentLanguage => $languageItems) {
            $newCommand = new IndexItems(
                new RepositoryReference(
                    $command->getRepositoryReference()->getAppId(),
                    $command->getRepositoryReference()->getIndex() . '_lang_' . $currentLanguage
                ),
                $command->getToken(),
                $languageItems
            );

            $next($newCommand);
        }
    }

    /**
     * Event subscribed namespace.
     *
     * @return string
     */
    public function getSubscribedEvent(): string
    {
        return IndexItems::class;
    }
}
