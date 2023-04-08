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

use Markocupic\RszAthletenumfrageBundle\ContaoBackendMaintenance\MaintainModuleAthletenumfrage;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['rsz_tools']['tl_athletenumfrage'] = [
    'tables' => ['tl_athletenumfrage'],
];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_athletenumfrage'] = \Markocupic\RszAthletenumfrageBundle\Model\AthletenumfrageModel::class;

/**
 * Maintenance
 */
$GLOBALS['TL_PURGE']['custom']['rsz_athletenumfrage'] = [
    'callback' => [MaintainModuleAthletenumfrage::class, 'truncateAthletenumfrage'],
];
