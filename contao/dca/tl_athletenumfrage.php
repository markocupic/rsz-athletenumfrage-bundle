<?php

/**
 * @copyright  Marko Cupic 2023 <m.cupic@gmx.ch>
 * @author     Marko Cupic
 * @package    RSZ AthletenumfrageBundle
 * @license    MIT
 * @see        https://github.com/markocupic/rsz-athletenumfrage-bundle
 *
 */

use Contao\DC_Table;
use Contao\DataContainer;
use Contao\UserModel;

$GLOBALS['TL_DCA']['tl_athletenumfrage'] = [
    'config'   => [
        'dataContainer'    => DC_Table::class,
        'ptable'           => UserModel::getTable(),
        'enableVersioning' => true,
        'sql'              => [
            'keys' => [
                'id'  => 'primary',
                'pid' => 'index',
            ],
        ],
    ],
    'list'     => [
        'sorting'           => [
            'mode'            => DataContainer::MODE_SORTED,
            'fields' => ['tstamp DESC, username'],
        ],
        'label'             => [
            'fields' => ['username'],
            'format' => '<span>#name#</span> #datum#  #trainerkommentar#',
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'    => [
                'label' => &$GLOBALS['TL_LANG']['tl_athletenumfrage']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'drucken' => [
                'href'  => 'action=drucken',
                'label' => 'drucken',
                'title' => 'drucken',
                'class' => 'icon_16',
                'icon'  => 'bundles/markocupicrszathletenumfrage/word_icon.svg',
            ],
            'show'    => [
                'label' => &$GLOBALS['TL_LANG']['tl_athletenumfrage']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],
    'palettes' => [
        'default' => '',
        'athlete' => 'username;
            {Deine Leistungen:hide},kletterleistung_indoor_rp, kletterleistung_outdoor_rp, boulderleistung_indoor, boulderleistung_outdoor, fortschritte;
            {Dein Training:hide},anz_trainings,trainingsspass,pos_training,neg_training,trainingseffizienz,selbstvertrauen_training,selbstvertrauen_wettkaempfe,training_system, trainingsplan, rumpfkrafttraining, ausdauertraining, trainingstagebuch;
            {Ziele im nächsten Trainingsjahr},ziele_indoor,ziele_outdoor,ziele_wettkampf,ziele_allgemein;
            {Allgemeines:hide},umgang_mit_druck,speed_nutzung,speed_verbesserung,gruende_mitgliedschaft, funktion_next_year, austritt, allgemeines;
            {Trainerkommentare:hide},trainerkommentar',
        'trainer' => 'tableOverview,trainerkommentar',
    ],
    'fields'   => [
        'id'                          => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid'                         => [
            'foreignKey' => 'tl_user.username',
            'sql'        => "int(10) unsigned NOT NULL default 0",
            'relation'   => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'tableOverview'               => [
            'search'  => true,
            'sorting' => true,
            'flag'    => DataContainer::SORT_INITIAL_LETTER_ASC,
            'eval'    => [],
            'sql'     => "text NULL",
        ],
        'tstamp'                      => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'readonly' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false, 'tl_class' => 'w50'],
            'sql'       => "int(10) unsigned NOT NULL default '0'",
        ],
        'username'                    => [
            'inputType' => 'text',
            'eval'      => ['submitOnChange' => true, 'readonly' => true, 'style' => 'width:30%', 'allowHtml' => false],
            'sql'       => "text NULL",
        ],
        'ziele_indoor'                => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'ziele_outdoor'               => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'ziele_wettkampf'             => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'ziele_allgemein'             => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'fortschritte'                => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'anz_trainings'               => [
            'inputType' => 'select',
            'options'   => ['1', '2', '3', '4', '5'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'trainingsspass'              => [
            'inputType' => 'select',
            'options'   => ['1', '2', '3', '4'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'pos_training'                => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'neg_training'                => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'trainingseffizienz'          => [
            'inputType' => 'select',
            'options'   => ['1', '2', '3', '4'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'selbstvertrauen_training'    => [
            'inputType' => 'select',
            'options'   => ['1', '2', '3', '4'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'selbstvertrauen_wettkaempfe' => [
            'inputType' => 'select',
            'options'   => ['1', '2', '3', '4'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'trainingsplan'               => [
            'inputType' => 'select',
            'options'   => ['ja', 'nein'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'rumpfkrafttraining'          => [
            'inputType' => 'select',
            'options'   => ['1x/Woche', '2x/Woche', '3x/Woche', '4x/Woche', '5x/Woche'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'ausdauertraining'            => [
            'inputType' => 'select',
            'options'   => ['1x/Woche', '2x/Woche', '3x/Woche', '4x/Woche', '5x/Woche'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'trainingstagebuch'           => [
            'inputType' => 'select',
            'options'   => ['ja', 'nein'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'kletterleistung_indoor_rp'   => [
            'inputType' => 'select',
            'options'   => ['6b', '6b+', '6c', '6c+', '7a', '7a+', '7b', '7b+', '7c', '7c+', '8a', '8a+', '8b', '8b+', '8c', '8c+', '9a'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'kletterleistung_outdoor_rp'  => [
            'inputType' => 'select',
            'options'   => ['6b', '6b+', '6c', '6c+', '7a', '7a+', '7b', '7b+', '7c', '7c+', '8a', '8a+', '8b', '8b+', '8c', '8c+', '9a'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'boulderleistung_indoor'      => [
            'inputType' => 'select',
            'options'   => ['B1', 'B2', 'B3', 'B4', 'B5', 'B6'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'boulderleistung_outdoor'     => [
            'inputType' => 'select',
            'options'   => ['fb6b', 'fb6b+', 'fb6c', 'fb6c+', 'fb7a', 'fb7a+', 'fb7b', 'fb7b+', 'fb7c', 'fb7c+', 'fb8a', 'fb8a+', 'fb8b', 'fb8b+', 'fb8c'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'speed_nutzung'               => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false],
            'sql'       => "text NULL",
        ],
        'speed_verbesserung'          => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false],
            'sql'       => "text NULL",
        ],
        'umgang_mit_druck'            => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false],
            'sql'       => "text NULL",
        ],
        'gruende_mitgliedschaft'      => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%', 'allowHtml' => false, 'tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'funktion_next_year'          => [
            'inputType' => 'select',
            'options'   => ['Athlet', 'Athlet und Trainer', 'Trainer', 'Austritt'],
            'eval'      => ['submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'clr'],
            'sql'       => "text NULL",
        ],
        'allgemeines'                 => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:7em; overflow:auto; width:80%;', 'allowHtml' => false, 'tl_class' => 'clr'],
            'sql'       => "text NULL",
        ],
        'trainerkommentar'            => [
            'inputType' => 'textarea',
            'eval'      => ['submitOnChange' => true, 'style' => 'height:15em; overflow:auto; width:95%;', 'allowHtml' => false],
            'sql'       => "text NULL",
        ],
    ],
];
