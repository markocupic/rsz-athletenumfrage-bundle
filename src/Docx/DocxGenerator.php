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

namespace Markocupic\RszAthletenumfrageBundle\Docx;

use Contao\Date;
use Contao\UserModel;
use Markocupic\PhpOffice\PhpWord\MsWordTemplateProcessor;
use Markocupic\RszAthletenumfrageBundle\Model\AthletenumfrageModel;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;

class DocxGenerator
{
    private const TEMPLATE_SRC = 'vendor/markocupic/rsz-athletenumfrage-bundle/contao/templates/docx/athletenumfrage.docx';
    private const TARGET_FILENAME = '%s/athletenumfrage_%s_%s.docx';

    /**
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function print(AthletenumfrageModel $objAthletenumfrage, array $arrDca, array $arrLang): void
    {
        /** @var UserModel $objUser */
        $objUser = $objAthletenumfrage->getRelated('pid');

        // Save file in the system temp directory.
        $targetFilename = sprintf(
            self::TARGET_FILENAME,
            sys_get_temp_dir(),
            strtolower($objUser->username),
            Date::parse('Y-m-d', time()),
        );

        // Create template processor object
        $objPhpWord = new MsWordTemplateProcessor(self::TEMPLATE_SRC, $targetFilename);

        $objPhpWord->replace('athlete_name', $objUser->name);
        $objPhpWord->replace('athlete_dateOfBirth', Date::parse('d.m.Y', $objUser->dateOfBirth));
        $objPhpWord->replace('date', Date::parse('d.m.Y', $objAthletenumfrage->tstamp));
        $objPhpWord->replace('trainerkommentar', $objUser->trainerkommentar);

        $arrSkip = [
            'id',
            'pid',
            'summary',
            'tstamp',
            'username',
            'trainerkommentar',
        ];

        foreach (array_keys($arrDca['fields']) as $fieldName) {
            if (\in_array($fieldName, $arrSkip, true)) {
                continue;
            }

            // Clone row
            $objPhpWord->createClone('label');

            $label = (string) $arrLang[$fieldName][0];
            $response = (string) $objAthletenumfrage->{$fieldName};

            // Push data to clone
            $objPhpWord->addToClone('label', 'label', $label, ['multiline' => false]);
            $objPhpWord->addToClone('label', 'response', $response, ['multiline' => true]);
        }

        // Generate Docx file from template;
        $objPhpWord->generateUncached(true)
            ->sendToBrowser(true)
            ->generate()
        ;
    }
}
