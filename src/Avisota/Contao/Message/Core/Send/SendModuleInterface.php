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
 * Interface SendModuleInterface
 *
 * @package Avisota\Contao\Message\Core\Send
 */
interface SendModuleInterface
{
    /**
     * @param Message $message
     *
     * @return mixed
     */
    public function run(Message $message);
}
