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

namespace Apisearch\Plugin\Redis\Domain\Middleware;

use Apisearch\Plugin\Redis\Domain\RedisWrapper;
use Apisearch\Server\Domain\Plugin\PluginMiddleware;
use Apisearch\Server\Domain\Query\CheckHealth;

/**
 * Class CheckHealthMiddleware.
 */
class CheckHealthMiddleware implements PluginMiddleware
{
    /**
     * @var RedisWrapper
     *
     * Redis wrapper
     */
    protected $redisWrapper;

    /**
     * QueryHandler constructor.
     *
     * @param RedisWrapper $redisWrapper
     */
    public function __construct(RedisWrapper $redisWrapper)
    {
        $this->redisWrapper = $redisWrapper;
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
        $data = $next($command);
        $data['status']['redis'] = $this->getRedisStatus();

        return $data;
    }

    /**
     * Get redis status.
     *
     * @return bool
     */
    private function getRedisStatus(): bool
    {
        try {
            $pong = $this
                ->redisWrapper
                ->getClient()
                ->ping();

            return '+PONG' === $pong;
        } catch (\RedisException $e) {
            // Silent pass
        }

        return false;
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
        return [
            CheckHealth::class,
        ];
    }
}
