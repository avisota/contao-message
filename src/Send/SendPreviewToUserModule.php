<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Send;

use Avisota\Contao\Entity\Message;
use Doctrine\DBAL\Connection;

/**
 * Class SendPreviewToUserModule
 *
 * @package Avisota\Contao\Message\Core\Send
 */
class SendPreviewToUserModule implements SendModuleInterface
{
    /**
     * @param Message $message
     *
     * @return string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function run(Message $message)
    {
        global $container;

        $sendPreviewToUser = \Session::getInstance()
            ->get('AVISOTA_SEND_PREVIEW_TO_USER_EMPTY');
        $userMissing       = isset($sendPreviewToUser)
            ? $sendPreviewToUser
            : false;
        unset($sendPreviewToUser);

        /** @var Connection $connection */
        $connection = $container['doctrine.connection.default'];

        $users = $connection
            ->executeQuery('SELECT * FROM tl_user ORDER BY name')
            ->fetchAll();

        $template = new \TwigTemplate('avisota/send/send_preview_to_user', 'html5');
        return $template->parse(
            array(
                'message'     => $message,
                'users'       => $users,
                'userMissing' => $userMissing,
            )
        );
    }
}
