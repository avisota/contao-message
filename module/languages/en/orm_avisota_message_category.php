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
 * Fields
 */
$GLOBALS['TL_LANG']['orm_avisota_message_category']['title']             = array(
	'Title',
	'Please enter the newsletter category title.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['alias']             = array(
	'Alias',
	'The category alias is a unique reference to the category which can be called instead of its ID.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['recipientsMode']    = array(
	'Recipients selection mode',
	'Please chose the recipients selection mode.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['recipients']        = array(
	'Recipients',
	'Please chose the preselected recipients.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['layoutMode']        = array(
	'Layout selection mode',
	'Please chose the layout selection mode.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['layout']            = array(
	'Layout',
	'Please chose the preselected layout.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['queueMode']         = array(
	'Queue selection mode',
	'Please chose the queue selection mode.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['queue']             = array(
	'Queue',
	'Please chose the preselected queue.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['viewOnlinePage']    = array(
	'View online page',
	'Please chose the page to display the message online.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['boilerplates']      = array(
	'Contains boilerplates',
	'This category contain boilerplate mailings.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['showInMenu']        = array(
	'Show in menu',
	'Please chose to show this category in the backend menu.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['useCustomMenuIcon'] = array(
	'Use custom icon',
	'Please chose if you want to use a custom icon in the menu.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['menuIcon']          = array(
	'Custom icon',
	'Please chose a custom backend menu item.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['tstamp']            = array(
	'Revision date',
	'Date and time of the latest revision'
);


/**
 * Legends
 */
$GLOBALS['TL_LANG']['orm_avisota_message_category']['category_legend']   = 'Category';
$GLOBALS['TL_LANG']['orm_avisota_message_category']['recipients_legend'] = 'Recipients settings';
$GLOBALS['TL_LANG']['orm_avisota_message_category']['layout_legend']     = 'Layout settings';
$GLOBALS['TL_LANG']['orm_avisota_message_category']['queue_legend']      = 'Queue settings';
$GLOBALS['TL_LANG']['orm_avisota_message_category']['online_legend']     = 'Online settings';
$GLOBALS['TL_LANG']['orm_avisota_message_category']['expert_legend']     = 'Experts settings';


/**
 * Reference
 */
$GLOBALS['TL_LANG']['orm_avisota_message_category']['byCategory']          = 'only in category';
$GLOBALS['TL_LANG']['orm_avisota_message_category']['byMessageOrCategory'] = 'preselected in category, overwriteable in newsletter';
$GLOBALS['TL_LANG']['orm_avisota_message_category']['byMessage']           = 'only in newsletter';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['orm_avisota_message_category']['new']        = array(
	'New category',
	'Create a new category'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['show']       = array(
	'Category details',
	'Show the details of category ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['edit']       = array(
	'Edit category',
	'Edit category ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['editheader'] = array(
	'Edit category settings',
	'Edit category settings ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['copy']       = array(
	'Duplicate category',
	'Duplicate category ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_category']['delete']     = array(
	'Delete category',
	'Delete category ID %s'
);
