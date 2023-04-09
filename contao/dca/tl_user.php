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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\System;
use Contao\DataContainer;

// Extend the default palette with permissions
PaletteManipulator::create()
    ->addLegend('rsz_permission_legend', 'amg_legend', PaletteManipulator::POSITION_BEFORE)
    ->addField(['rsz_athletenumfragep'], 'rsz_permission_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user');

// Add fields to tl_user
$GLOBALS['TL_DCA']['tl_user']['fields']['rsz_athletenumfragep'] = [
    'exclude'   => true,
    'inputType' => 'checkbox',
    'options'   => [
        'has_access_to_surveys',
        'can_fill_in_survey',
    ],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval'      => ['multiple' => true],
    'sql'       => 'blob NULL',
];
