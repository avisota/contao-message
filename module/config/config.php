<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message
 * @license    LGPL-3.0+
 * @filesource
 */

/**
 * Entities
 */
$GLOBALS['DOCTRINE_ENTITY_CLASS']['Avisota\Contao\Entity\Layout']  = 'Avisota\Contao\Message\Core\Entity\AbstractLayout';
$GLOBALS['DOCTRINE_ENTITY_CLASS']['Avisota\Contao\Entity\Message'] = 'Avisota\Contao\Message\Core\Entity\AbstractMessage';

$GLOBALS['DOCTRINE_ENTITIES'][] = 'orm_avisota_layout';
$GLOBALS['DOCTRINE_ENTITIES'][] = 'orm_avisota_message';
$GLOBALS['DOCTRINE_ENTITIES'][] = 'orm_avisota_message_category';
$GLOBALS['DOCTRINE_ENTITIES'][] = 'orm_avisota_message_content';
$GLOBALS['DOCTRINE_ENTITIES'][] = 'orm_avisota_theme';

/**
 * Back end modules
 */
$settingsModuleIndex = 1 + array_search('avisota_settings', array_keys($GLOBALS['BE_MOD']['avisota']));

$GLOBALS['BE_MOD']['avisota'] = array_merge(
    array(
        'avisota_newsletter' => array(
            'tables'     => array(
                'orm_avisota_message_category',
                'orm_avisota_message',
                'orm_avisota_message_content',
                'orm_avisota_message_create_from_draft'
            ),
            'icon'       => 'assets/avisota/message/images/newsletter.png',
            'stylesheet' => 'assets/avisota/message/css/backend.css',
        )
    ),
    array_slice($GLOBALS['BE_MOD']['avisota'], 0, $settingsModuleIndex),
    array(
        'avisota_theme' => array
        (
            'nested' => 'avisota_config:newsletter',
            'tables' => array('orm_avisota_theme', 'orm_avisota_layout'),
            'icon'   => 'assets/avisota/message/images/theme.png',
        )
    ),
    array_slice($GLOBALS['BE_MOD']['avisota'], $settingsModuleIndex)
);

/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['avisota']['avisota_message_list']   = 'Avisota\Contao\Message\Core\Module\Listing';
$GLOBALS['FE_MOD']['avisota']['avisota_message_reader'] = 'Avisota\Contao\Message\Core\Module\Reader';

/**
 * Events
 */
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\Backend\Preview';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\DataContainer\Message';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\DataContainer\MessageContent';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\EventsSubscriber';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\Layout\ContaoStylesheets';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\DataContainer\ThemeOptions';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\DataContainer\LayoutOptions';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\DataContainer\MessageCategoryOptions';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\DataContainer\MessageOptions';
$GLOBALS['TL_EVENT_SUBSCRIBERS'][] = 'Avisota\Contao\Message\Core\DataContainer\MessageContentOptions';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['initializeDependencyContainer']['avisota-custom-menu'] = array(
    'Avisota\Contao\Message\Core\Backend\CustomMenu',
    'injectMenu'
);
$GLOBALS['TL_HOOKS']['getUserNavigation']['avisota-custom-menu']             = array(
    'Avisota\Contao\Message\Core\Backend\CustomMenu',
    'hookGetUserNavigation'
);

/**
 * Send modules
 */
$GLOBALS['AVISOTA_SEND_MODULE']['avisota_preview']          = 'Avisota\Contao\Message\Core\Send\PreviewModule';
$GLOBALS['AVISOTA_SEND_MODULE']['avisota_preview_to_user']  = 'Avisota\Contao\Message\Core\Send\SendPreviewToUserModule';
$GLOBALS['AVISOTA_SEND_MODULE']['avisota_preview_to_email'] = 'Avisota\Contao\Message\Core\Send\SendPreviewToEmailModule';
$GLOBALS['AVISOTA_SEND_MODULE']['avisota_send_immediate']   = 'Avisota\Contao\Message\Core\Send\SendImmediateModule';

/**
 * Message elements
 */
if (!isset($GLOBALS['TL_MCE'])) {
    $GLOBALS['TL_MCE'] = array();
}

/**
 * Message renderer
 */
if (!isset($GLOBALS['AVISOTA_MESSAGE_RENDERER'])) {
    $GLOBALS['AVISOTA_MESSAGE_RENDERER'] = array();
}
$GLOBALS['AVISOTA_MESSAGE_RENDERER'][] = 'default';
