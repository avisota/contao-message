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

namespace Avisota\Contao\Message\Core\Event;

/**
 * Class PreRenderMessageTemplatePreviewEvent
 *
 * @package Avisota\Contao\Message\Core\Event
 */
class PreRenderMessageTemplatePreviewEvent extends BasePreRenderMessageTemplateEvent
{
    const NAME = 'avisota.contao.pre-render-message-template-preview';
}
