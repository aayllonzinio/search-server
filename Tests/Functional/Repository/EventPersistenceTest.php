<?php

/*
 * This file is part of the Search Server Bundle.
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

namespace Puntmig\Search\Server\Tests\Functional\Repository;

use Puntmig\Search\Model\ItemUUID;
use Puntmig\Search\Query\Query;
use Puntmig\Search\Server\Tests\Functional\PuntmigSearchServerBundleFunctionalTest;

/**
 * Class EventPersistenceTest.
 */
class EventPersistenceTest extends PuntmigSearchServerBundleFunctionalTest
{
    /**
     * Test something.
     */
    public function testEventPersistence()
    {
        $eventRepository = self::get('search_server.event_repository');
        $eventRepository->setAppId(self::$appId);
        $eventRepository->createRepository(true);

        $this->reset();
        $this->assertCount(
            1,
            $eventRepository->all()
        );
        $this->deleteItems([new ItemUUID('1', 'product')]);
        $this->assertCount(
            2,
            $eventRepository->all()
        );
        $this->deleteItems([new ItemUUID('2', 'product')]);
        $this->assertCount(
            3,
            $eventRepository->all()
        );
        $this->query(Query::createMatchAll());
        $this->assertCount(
            4,
            $eventRepository->all()
        );
        $this->reset();
        $this->assertCount(
            5,
            $eventRepository->all()
        );
        $this->reset();
        $this->assertCount(
            6,
            $eventRepository->all()
        );

        $eventRepository->setAppId(self::$anotherAppId);
        $eventRepository->createRepository(true);
        $this->assertCount(
            0,
            $eventRepository->all()
        );
    }
}
