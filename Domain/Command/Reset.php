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

namespace Puntmig\Search\Server\Domain\Command;

use Puntmig\Search\Server\Domain\WithAppId;

/**
 * Class Reset.
 */
class Reset extends WithAppId
{
    /**
     * @var null|string
     *
     * Language
     */
    private $language;

    /**
     * ResetCommand constructor.
     *
     * @param string      $appId
     * @param null|string $language
     */
    public function __construct(
        string $appId,
        ? string $language
    ) {
        $this->appId = $appId;
        $this->language = $language;
    }

    /**
     * Get Language.
     *
     * @return null|string
     */
    public function getLanguage(): ? string
    {
        return $this->language;
    }
}
