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

namespace Markocupic\RszAthletenumfrageBundle\DataContainer;

use Contao\Backend;
use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Date;
use Contao\Image;
use Contao\UserModel;
use Doctrine\DBAL\Connection;
use Markocupic\RszAthletenumfrageBundle\Docx\DocxGenerator;
use Markocupic\RszAthletenumfrageBundle\Model\AthletenumfrageModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Twig\Environment as Twig;

class Athletenumfrage extends Backend
{
    private int|null $pid = null;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Connection $connection,
        private readonly DocxGenerator $docxGenerator,
        private readonly Twig $twig,
        private readonly Security $security,
    ) {
        parent::__construct();

        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->has('id')) {
            $this->pid = $this->connection->fetchOne('SELECT pid FROM tl_athletenumfrage WHERE id = ?', [
                $request->query->get('id'),
            ]);
        }
    }

    /**
     * Create a blanco survey from every athlete, if there is no.
     */
    #[AsCallback(table: 'tl_athletenumfrage', target: 'config.onload', priority: 255)]
    public function createProfiles(): void
    {
        $result = $this->connection->executeQuery('SELECT id, username FROM tl_user WHERE funktion LIKE ? ORDER BY dateOfBirth', ['%Athlet%']);

        $rows = $result->fetchAllAssociative();

        foreach ($rows as $row) {
            if (false === $this->connection->fetchOne('SELECT id FROM tl_athletenumfrage WHERE pid = ?', [$row['id']])) {
                $set = [
                    'pid' => $row['id'],
                    'username' => $row['username'],
                ];

                $this->connection->insert('tl_athletenumfrage', $set);
            }
        }
    }

    #[AsCallback(table: 'tl_athletenumfrage', target: 'config.onload', priority: 100)]
    public function route(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ('drucken' === $request->query->get('action')) {
            $objAthletenumfrage = AthletenumfrageModel::findByPk($request->query->get('id'));

            if (null !== $objAthletenumfrage) {
                Controller::loadDataContainer('tl_athletenumfrage');
                Controller::loadLanguageFile('tl_athletenumfrage');

                $arrDca = $GLOBALS['TL_DCA']['tl_athletenumfrage'];
                $arrLang = $GLOBALS['TL_LANG']['tl_athletenumfrage'];

                $this->docxGenerator->print($objAthletenumfrage, $arrDca, $arrLang);
            }
        }
    }

    /**
     * Filter list for athletes.
     */
    #[AsCallback(table: 'tl_athletenumfrage', target: 'config.onload', priority: 100)]
    public function filterList(): void
    {
        $user = $this->security->getUser();

        // Nur Admins und Trainer haben Zugriff auf fremde Umfragen
        $roles = $user->funktion ?? [];

        if (\in_array('Trainer', $roles, true) || $this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_athletenumfrage']['list']['sorting']['filter'] = [['pid = ?', $user->id]];
    }

    #[AsCallback(table: 'tl_athletenumfrage', target: 'config.onload', priority: 100)]
    public function checkPermission(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ('drucken' === $request->query->get('action') || 'edit' === $request->query->get('act') || 'show' === $request->query->get('act') || 'delete' === $request->query->get('act')) {
            $user = $this->security->getUser();

            if ($this->security->isGranted('ROLE_ADMIN')) {
                return;
            }

            if ($this->pid === (int) $user->id) {
                return;
            }

            if (\in_array('Trainer', $user->funktion, true)) {
                return;
            }

            $this->redirect('contao?do=tl_athletenumfrage');
        }
    }

    #[AsCallback(table: 'tl_athletenumfrage', target: 'config.onload', priority: 100)]
    public function setPalette(): void
    {
        $user = $this->security->getUser();

        // Trainer
        if (\in_array('Trainer', $user->funktion, true) || $this->security->isGranted('ROLE_ADMIN')) {
            $GLOBALS['TL_DCA']['tl_athletenumfrage']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_athletenumfrage']['palettes']['trainer'];
        }

        // Athlete
        if ($this->pid === (int) $user->id) {
            $GLOBALS['TL_DCA']['tl_athletenumfrage']['fields']['trainerkommentar']['eval']['readonly'] = 'true';
        }
    }

    #[AsCallback(table: 'tl_athletenumfrage', target: 'list.operations.drucken.button', priority: 100)]
    public function printerIcon(array $row, string $href, string $label, string $title, string $icon, string $attributes): string
    {
        return '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="drucken"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }

    #[AsCallback(table: 'tl_athletenumfrage', target: 'list.label.label', priority: 100)]
    public function labelCallback(array $row, string $label): string
    {
        $arrSurvey = $row;

        $name = $arrSurvey['username'];

        if (null !== ($objUser = UserModel::findByPk($arrSurvey['pid']))) {
            $name = $objUser->name;
        }

        $label = str_replace('#name#', $name, $label);

        if ('' !== trim((string) $arrSurvey['trainerkommentar'])) {
            $label = str_replace('#trainerkommentar#', '[Kommentar vorh.]', $label);
        } else {
            $label = str_replace('#trainerkommentar#', '', $label);
        }

        if (0 === $arrSurvey['tstamp']) {
            return str_replace('#datum#', '', $label);
        }

        return str_replace('#datum#', Date::parse('[D, d.m.Y, H:i]', $arrSurvey['tstamp']), $label);
    }

    #[AsCallback(table: 'tl_athletenumfrage', target: 'fields.summary.input_field', priority: 100)]
    public function getSummaryTable(DataContainer $dc, string $label): string
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM tl_athletenumfrage WHERE id = ?', [$dc->id]);

        unset($row['id'], $row['pid'], $row['tstamp'], $row['summary']);

        Controller::loadLanguageFile('tl_athletenumfrage');

        return $this->twig->render(
            '@MarkocupicRszAthletenumfrage/survey.html.twig',
            [
                'keys' => array_keys($row),
                'row' => $row,
                'lang' => $GLOBALS['TL_LANG']['tl_athletenumfrage'],
            ]
        );
    }
}
