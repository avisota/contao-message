<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
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
$GLOBALS['BE_MOD']['avisota']['avisota_outbox']     = array(
	'callback'   => 'Avisota\Contao\Core\Backend\Outbox',
	'icon'       => 'system/modules/avisota/html/outbox.png',
	'stylesheet' => 'assets/avisota-core/css/stylesheet.css'
);
$GLOBALS['BE_MOD']['avisota']['avisota_newsletter'] = array(
	'tables'     => array(
		'orm_avisota_message_category',
		'orm_avisota_message',
		'orm_avisota_message_content',
		'orm_avisota_message_create_from_draft'
	),
	'send'       => array('Avisota\Contao\Core\Backend\Preview', 'sendMessage'),
	'icon'       => 'system/modules/avisota/html/newsletter.png',
	'stylesheet' => 'assets/avisota-core/css/stylesheet.css'
);
$GLOBALS['BE_MOD']['avisota']['avisota_theme']            = array
(
	'nested'     => 'avisota_config:newsletter',
	'tables'     => array('orm_avisota_theme', 'orm_avisota_layout'),
	'icon'       => 'assets/avisota-core/images/theme.png',
	'stylesheet' => 'assets/avisota-core/css/stylesheet.css'
);

/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['avisota']['avisota_list']         = 'Avisota\Contao\Core\Message\List';
$GLOBALS['FE_MOD']['avisota']['avisota_reader']       = 'Avisota\Contao\Core\Message\Reader';

/**
 * Events
 */
$GLOBALS['TL_EVENTS']['avisota/message.collect-stylesheets'][] = array(
	'Avisota\Contao\Message\Core\Layout\ContaoStylesheets',
	'collectStylesheets'
);
$GLOBALS['TL_EVENTS']['avisota/message.resolve-stylesheet'][]  = array(
	'Avisota\Contao\Message\Core\Layout\ContaoStylesheets',
	'resolveStylesheet'
);

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['initializeSystem']['avisota-custom-menu'] = array(
	'Avisota\Contao\Message\Core\Backend\CustomMenu',
	'injectMenu'
);
$GLOBALS['TL_HOOKS']['getUserNavigation']['avisota-custom-menu']     = array(
	'Avisota\Contao\Message\Core\Backend\CustomMenu',
	'hookGetUserNavigation'
);
$GLOBALS['TL_HOOKS']['loadLanguageFile']['avisota-custom-menu']      = array(
	'Avisota\Contao\Message\Core\Backend\CustomMenu',
	'hookLoadLanguageFile'
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
