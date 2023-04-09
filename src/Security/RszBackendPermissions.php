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

namespace Markocupic\RszAthletenumfrageBundle\Security;

final class RszBackendPermissions
{
    /**
     * Access is granted if the current user has access to all surveys.
     * Subject must be an operation: has_access_to_surveys.
     */
    public const USER_CAN_ACCESS_RSZ_ATHLETENUMFRAGE = 'contao_user.rsz_athletenumfragep';

}
