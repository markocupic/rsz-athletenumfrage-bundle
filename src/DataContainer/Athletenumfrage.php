<?php

declare(strict_types=1);

namespace Markocupic\RszAthletenumfrageBundle\DataContainer;

use Contao\Backend;
use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Database;
use Contao\Date;
use Contao\Image;
use Contao\UserModel;
use Markocupic\RszAthletenumfrageBundle\Docx\DocxGenerator;
use Markocupic\RszAthletenumfrageBundle\Model\AthletenumfrageModel;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class Athletenumfrage extends Backend
{
    private int|null $pid = null;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly DocxGenerator $docxGenerator,
        private readonly Security $security,
    ) {
        parent::__construct();

        $request = $this->requestStack->getCurrentRequest();

        if ($request->query->has('id')) {
            $objAthletenumfrage = Database::getInstance()
                ->prepare('SELECT pid FROM tl_athletenumfrage WHERE id = ?')
                ->execute($request->query->get('id'))
            ;

            $objAthlet = Database::getInstance()
                ->prepare('SELECT id, username FROM tl_user WHERE id = ?')
                ->execute($objAthletenumfrage->pid)
            ;

            $this->pid = (int) $objAthlet->id;
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
        Database::getInstance()
            ->prepare('SELECT id FROM tl_athletenumfrage WHERE pid = ?')
            ->execute($user->id)
        ;

        $roles = $user->funktion ?? [];

        if (\in_array('Trainer', $roles, true) || $this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_athletenumfrage']['list']['sorting']['filter'] = [['pid = ?', $user->id]];
    }

    /**
     * Create a blanco survey from every athlete, if there is no.
     */
    #[AsCallback(table: 'tl_athletenumfrage', target: 'config.onload', priority: 100)]
    public function createProfiles(): void
    {
        $objAthlete = Database::getInstance()
            ->prepare('SELECT id, username FROM tl_user WHERE funktion LIKE ? ORDER BY dateOfBirth')
            ->execute('%Athlet%')
        ;

        while ($objAthlete->next()) {
            $objAthletenumfrage = Database::getInstance()
                ->prepare('SELECT id FROM tl_athletenumfrage WHERE pid = ?')
                ->execute($objAthlete->id)
            ;

            if (!$objAthletenumfrage->numRows) {
                $set = [
                    'pid' => $objAthlete->id,
                    'username' => $objAthlete->username,
                ];

                Database::getInstance()->prepare('INSERT INTO  tl_athletenumfrage %s')
                    ->set($set)
                    ->execute()
                ;
            }
        }
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
            $GLOBALS['TL_DCA']['tl_athletenumfrage']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_athletenumfrage']['palettes']['athlete'];
            $GLOBALS['TL_DCA']['tl_athletenumfrage']['fields']['trainerkommentar']['eval']['style'] = str_replace($GLOBALS['TL_DCA']['tl_athletenumfrage']['fields']['trainerkommentar']['eval']['style'], $GLOBALS['TL_DCA']['tl_athletenumfrage']['fields']['trainerkommentar']['eval']['style'].'" readonly="readonly', $GLOBALS['TL_DCA']['tl_athletenumfrage']['fields']['trainerkommentar']['eval']['style']);
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
        $objAthletenumfrage = Database::getInstance()
            ->prepare('SELECT * FROM tl_athletenumfrage WHERE id = ?')
            ->limit(1)
            ->execute($row['id'])
        ;

        $name = $objAthletenumfrage->username;

        if (null !== ($objUser = UserModel::findByPk($objAthletenumfrage->pid))) {
            $name = $objUser->name;
        }

        $label = str_replace('#name#', $name, $label);

        if ('' !== trim((string) $objAthletenumfrage->trainerkommentar)) {
            $label = str_replace('#trainerkommentar#', '[Kommentar vorh.]', $label);
        } else {
            $label = str_replace('#trainerkommentar#', '', $label);
        }

        if (0 === $row['tstamp']) {
            return str_replace('#datum#', '', $label);
        }

        return str_replace('#datum#', Date::parse('[D, d.m.Y, H:i]', $row['tstamp']), $label);
    }

    #[AsCallback(table: 'tl_athletenumfrage', target: 'fields.tableOverview.input_field', priority: 100)]
    public function tableOverviewInputFieldCallback(): string
    {
        $request = $this->requestStack->getCurrentRequest();

        $mySql = Database::getInstance()->prepare('SELECT * FROM tl_athletenumfrage WHERE id = ?')
            ->limit(1)
            ->execute($request->query->get('id'))
        ;

        $row = $mySql->fetchAssoc();

        $i = 0;
        $output = '
			<div class="widget">
			<br>
			<table style="width:100%">
		';

        foreach (array_keys($GLOBALS['TL_DCA']['tl_athletenumfrage']['fields']) as $key) {
            if ('tableOverview' === $key) {
                continue;
            }

            if ('id' === $key || 'pid' === $key || 'tstamp' === $key) {
                continue;
            }
            $output .= '
				<tr>
					<td style="width:50%; font-weight:bold; border:0; padding:8px 8px; '.($i % 2 ? 'background-color:#fff;' : 'background-color:#f6f6f6;').'">'.$GLOBALS['TL_LANG']['tl_athletenumfrage'][$key][0].':</td>
					<td style="width:50%; border:0; padding:8px 8px; '.($i % 2 ? 'background-color:#fff;' : 'background-color:#f6f6f6;').'">'.nl2br((string) $row[$key]).'</td>
				</tr>';
            ++$i;
        }
        $output .= '</table><br><br></div>';

        return $output;
    }
}
