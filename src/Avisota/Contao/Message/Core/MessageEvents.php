<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core;

use Avisota\Contao\Entity\MessageCategory;
use BackendTemplate;
use Contao\Doctrine\ORM\EntityHelper;

class MessageEvents
{
    /**
     * The CREATE_MESSAGE_CATEGORY_OPTIONS event occurs when an options list of message categories will be created.
     *
     * The event listener method receives a ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent instance.
     *
     * @var string
     *
     * @api
     */
    const CREATE_MESSAGE_CATEGORY_OPTIONS = 'avisota.message.create-message-category-options';

    /**
     * The CREATE_MESSAGE_OPTIONS event occurs when an options list of messages will be created.
     *
     * The event listener method receives a ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent instance.
     *
     * @var string
     *
     * @api
     */
    const CREATE_MESSAGE_OPTIONS = 'avisota.message.create-message-options';

    /**
     * The CREATE_BOILERPLATE_MESSAGE_OPTIONS event occurs when an options list of boilerplate messages will be created.
     *
     * The event listener method receives a ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent instance.
     *
     * @var string
     *
     * @api
     */
    const CREATE_BOILERPLATE_MESSAGE_OPTIONS = 'avisota.message.create-boilerplate-message-options';

    /**
     * The CREATE_NON_BOILERPLATE_MESSAGE_OPTIONS event occurs when an options list of non boilerplate messages will be created.
     *
     * The event listener method receives a ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent instance.
     *
     * @var string
     *
     * @api
     */
    const CREATE_NON_BOILERPLATE_MESSAGE_OPTIONS = 'avisota.message.create-non-boilerplate-message-options';

    /**
     * The CREATE_MESSAGE_LAYOUT_OPTIONS event occurs when an options list of message layouts will be created.
     *
     * The event listener method receives a ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent instance.
     *
     * @var string
     *
     * @api
     */
    const CREATE_MESSAGE_LAYOUT_OPTIONS = 'avisota.message.create-message-layout-options';

    /**
     * The CREATE_MESSAGE_CONTENT_CELL_OPTIONS event occurs when an options list of message content cells will be created.
     *
     * The event listener method receives a ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent instance.
     *
     * @var string
     *
     * @api
     */
    const CREATE_MESSAGE_CONTENT_CELL_OPTIONS = 'avisota.create-message-content-cell-options';

    /**
     * The CREATE_MESSAGE_CONTENT_TYPE_OPTIONS event occurs when an options list of message content types will be created.
     *
     * The event listener method receives a ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent instance.
     *
     * @var string
     *
     * @api
     */
    const CREATE_MESSAGE_CONTENT_TYPE_OPTIONS = 'avisota.create-message-content-type-options';

    /**
     * The GENERATE_VIEW_ONLINE_URL event occurs when the "view online" url is generated for a message.
     *
     * The event listener method receives a ContaoCommunityAlliance\Contao\Events\CreateOptions\GenerateViewOnlineUrlEvent instance.
     *
     * @var string
     *
     * @api
     */
    const GENERATE_VIEW_ONLINE_URL = 'avisota.generate-view-online-url';
}
