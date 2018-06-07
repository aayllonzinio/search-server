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

namespace Apisearch\Server\Domain\Command;

use Apisearch\Server\Domain\AsynchronousableCommand;
use Apisearch\Server\Domain\AsynchronousRepositoryReferenceAndToken;
use Apisearch\Server\Domain\CommandWithRepositoryReferenceAndToken;
use Apisearch\Server\Domain\LoggableCommand;
use Apisearch\Server\Domain\WriteCommand;

/**
 * Class DeleteEventsIndex.
 */
class DeleteEventsIndex extends CommandWithRepositoryReferenceAndToken implements WriteCommand, LoggableCommand, AsynchronousableCommand
{
    use AsynchronousRepositoryReferenceAndToken;
}
