<?php

/**
 * @copyright  Marko Cupic 2023 <m.cupic@gmx.ch>
 * @author     Marko Cupic
 * @package    RSZ AthletenumfrageBundle
 * @license    MIT
 * @see        https://github.com/markocupic/rsz-athletenumfrage-bundle
 *
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
