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
 * Class PreviewModule
 *
 * @package Avisota\Contao\Message\Core\Send
 */
class PreviewModule implements SendModuleInterface
{
    /**
     * @param Message $message
     *
     * @return string
     */
    public function run(Message $message)
    {
        $template = new \TwigTemplate('avisota/send/preview', 'html5');
        return $template->parse(array('message' => $message));
    }
}
