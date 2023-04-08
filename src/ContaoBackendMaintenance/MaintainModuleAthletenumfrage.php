<?php

declare(strict_types=1);

/*
 * This file is part of RSZ Athletenumfrage Bundle.
 *
 * (c) Marko Cupic 2023 <m.cupic@gmx.ch>
 * @license MIT
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/rsz-athletenumfrage-bundle
 */

namespace Markocupic\RszAthletenumfrageBundle\ContaoBackendMaintenance;

use Doctrine\DBAL\Connection;

class MaintainModuleAthletenumfrage
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function truncateAthletenumfrage(): void
    {
        $this->connection->executeStatement('TRUNCATE TABLE tl_athletenumfrage');
    }
}
