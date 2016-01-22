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

/**
 * Class SendPreviewToEmailModule
 *
 * @package Avisota\Contao\Message\Core\Send
 */
class SendPreviewToEmailModule implements SendModuleInterface
{
    /**
     * @param Message $message
     *
     * @return string
     */
    public function run(Message $message)
    {
        $emailMissing = isset($_SESSION['AVISOTA_SEND_PREVIEW_TO_EMAIL_EMPTY'])
            ? $_SESSION['AVISOTA_SEND_PREVIEW_TO_EMAIL_EMPTY']
            : false;
        unset($_SESSION['AVISOTA_SEND_PREVIEW_TO_EMAIL_EMPTY']);

        $template = new \TwigTemplate('avisota/send/send_preview_to_email', 'html5');
        return $template->parse(
            array(
                'message'      => $message,
                'emailMissing' => $emailMissing,
            )
        );
    }
}
