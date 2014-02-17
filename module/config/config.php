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
 * Message elements
 */
if (!isset($GLOBALS['TL_MCE'])) {
	$GLOBALS['TL_MCE'] = array();
}
