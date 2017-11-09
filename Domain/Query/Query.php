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

namespace Puntmig\Search\Server\Domain\Query;

use Puntmig\Search\Query\Query as SearchQuery;
use Puntmig\Search\Server\Domain\WithAppId;

/**
 * Class Query.
 */
class Query extends WithAppId
{
    /**
     * @var SearchQuery
     *
     * Query
     */
    private $query;

    /**
     * DeleteCommand constructor.
     *
     * @param string      $appId
     * @param SearchQuery $query
     */
    public function __construct(
        string $appId,
        SearchQuery $query
    ) {
        $this->appId = $appId;
        $this->query = $query;
    }

    /**
     * Get Query.
     *
     * @return SearchQuery
     */
    public function getQuery(): SearchQuery
    {
        return $this->query;
    }
}
