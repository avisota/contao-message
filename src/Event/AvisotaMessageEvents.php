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
 * Class AvisotaMessageEvents
 *
 * @package Avisota\Contao\Message\Core\Event
 */
class AvisotaMessageEvents
{
    const RENDER_MESSAGE = 'avisota.contao.render-message';

    const RENDER_MESSAGE_CONTENT = 'avisota.contao.render-message-content';

    const COLLECT_STYLESHEETS = 'Avisota\Contao\Message\Core\Event\CollectStylesheets';

    const COLLECT_THEME_STYLESHEETS = 'Avisota\Contao\Message\Core\Event\CollectThemeStylesheets';

    const INITIALIZE_MESSAGE_CONTENT_RENDERER = 'Avisota\Contao\Message\Core\Event\InitializeMessageContentRenderer';

    const INITIALIZE_MESSAGE_RENDERER = 'Avisota\Contao\Message\Core\Event\InitializeMessageRenderer';

    const PRE_RENDER_MESSAGE_CONTENT = 'avisota.contao.pre-render-message-content';

    const POST_RENDER_MESSAGE_CONTENT = 'avisota.contao.post-render-message-content';

    const PRE_RENDER_MESSAGE_TEMPLATE = 'avisota.contao.pre-render-message-template';

    const POST_RENDER_MESSAGE_TEMPLATE = 'avisota.contao.post-render-message-template';

    const PRE_RENDER_MESSAGE_TEMPLATE_PREVIEW = 'avisota.contao.pre-render-message-template-preview';

    const POST_RENDER_MESSAGE_TEMPLATE_PREVIEW = 'avisota.contao.post-render-message-template-preview';

    const RENDER_MESSAGE_HEADERS = 'Avisota\Contao\Message\Core\Event\RenderMessageHeaders';

    const RESOLVE_STYLESHEET = 'Avisota\Contao\Message\Core\Event\ResolveStylesheet';
}
