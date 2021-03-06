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
 */

declare(strict_types=1);

namespace Apisearch\Plugin\Multilanguage\Domain\Middleware;

use Apisearch\Model\Item;
use Apisearch\Repository\RepositoryReference;
use Apisearch\Server\Domain\Command\IndexItems;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;
use League\Tactician\CommandBus;

/**
 * Class IndexItemsMiddleware.
 */
class IndexItemsMiddleware implements PluginMiddleware
{
    /**
     * @var CommandBus
     *
     * Command bus
     */
    private $commandBus;

    /**
     * IndexItemsMiddleware constructor.
     *
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * Execute middleware.
     *
     * @param mixed    $command
     * @param callable $next
     *
     * @return mixed
     */
    public function execute(
        $command,
        $next
    ) {
        /*
         * We should check if this is a language specific command
         */
        if (1 === preg_match('~\w*_plugin_language_\w{2}~', $command->getIndex())) {
            return $next($command);
        }

        /**
         * @var IndexItems
         */
        $itemsSplittedByLanguage = [];
        $itemsWithoutLanguage = [];
        foreach ($command->getItems() as $item) {
            $language = $item->get('language');
            if (is_null($language)) {
                $itemsWithoutLanguage[] = $item;
                continue;
            }

            if (!isset($itemsSplittedByLanguage[$language])) {
                $itemsSplittedByLanguage[$language] = [];
            }

            $itemsSplittedByLanguage[$language][] = $item;
        }

        /*
         * If we have not found any item with language, just follow the normal
         * workflow
         */
        if (empty($itemsSplittedByLanguage)) {
            return $next($command);
        }

        foreach ($itemsSplittedByLanguage as $language => $items) {
            $this->enqueueLanguageSpecificIndexItems(
                $command,
                $items,
                $language
            );
        }

        if (!empty($itemsWithoutLanguage)) {
            $this->enqueueWithoutLanguageIndexItems(
                $command,
                $itemsWithoutLanguage
            );
        }
    }

    /**
     * Enqueue new command.
     *
     * @param IndexItems $command
     * @param Item[]     $items
     * @param string     $language
     */
    private function enqueueLanguageSpecificIndexItems(
        IndexItems $command,
        array $items,
        string $language
    ) {
        $this
            ->commandBus
            ->handle(
                new IndexItems(
                    RepositoryReference::create(
                        $command->getAppId(),
                        $command->getIndex().'_plugin_language_'.$language
                    ),
                    $command->getToken(),
                    $items
                )
            );
    }

    /**
     * Enqueue new command.
     *
     * @param IndexItems $command
     * @param Item[]     $items
     */
    private function enqueueWithoutLanguageIndexItems(
        IndexItems $command,
        array $items
    ) {
        $this
            ->commandBus
            ->handle(
                new IndexItems(
                    RepositoryReference::create(
                        $command->getAppId(),
                        $command->getIndex()
                    ),
                    $command->getToken(),
                    $items
                )
            );
    }

    /**
     * Events subscribed namespace. Can refer to specific class namespace, any
     * parent class or any interface.
     *
     * By returning an empty array, means coupled to all.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [IndexItems::class];
    }
}
