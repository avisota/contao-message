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
$GLOBALS['TL_LANG']['orm_avisota_message']['subject']       = array(
	'Subject',
	'Please enter the subject of this newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['alias']         = array(
	'Alias',
	'The newsletter alias is a unique reference to the newsletter which can be called instead of its ID.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['language']   = array(
	'Language',
	'Please select the newsletter language.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['description']   = array(
	'Description',
	'Please enter a short description for this newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['keywords']      = array(
	'Keywords',
	'Please enter the keywords for this newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['addFile']       = array(
	'Attach files',
	'Attach additional files to the newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['files']         = array(
	'Attachments',
	'Please chose the attachments.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['setRecipients'] = array(
	'Select recipients',
	'Select the recipients for this newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['recipients']    = array(
	'Recipients',
	'Please chose the recipients for this newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['setLayout'] = array(
	'Select layout',
	'Select the layout for this newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['layout']    = array(
	'Layout',
	'Please chose the layout for this newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['setQueue'] = array(
	'Select queue',
	'Select the queue for this newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['queue']    = array(
	'Queue',
	'Please chose the queue for this newsletter.'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['tstamp']        = array(
	'Revision date',
	'Date and time of the latest revision'
);


/**
 * Legends
 */
$GLOBALS['TL_LANG']['orm_avisota_message']['newsletter_legend'] = 'Message';
$GLOBALS['TL_LANG']['orm_avisota_message']['meta_legend']       = 'Details';
$GLOBALS['TL_LANG']['orm_avisota_message']['recipient_legend']  = 'Recipient';
$GLOBALS['TL_LANG']['orm_avisota_message']['theme_legend']      = 'Theme settings';
$GLOBALS['TL_LANG']['orm_avisota_message']['queue_legend']      = 'Queue settings';
$GLOBALS['TL_LANG']['orm_avisota_message']['attachment_legend'] = 'Attachments';


/**
 * Reference
 */
$GLOBALS['TL_LANG']['orm_avisota_message']['notSend'] = 'Not send yet';
$GLOBALS['TL_LANG']['orm_avisota_message']['sended'] = 'Send on %s';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['orm_avisota_message']['new']               = array(
	'New newsletter',
	'Add a new newsletter'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['create_from_draft'] = array(
	'New newsletter from boilerplate',
	'Add a new newsletter from boilerplate'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['show']              = array(
	'Message details',
	'Show the details of newsletter ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['copy']              = array(
	'Duplicate newsletter',
	'Duplicate newsletter ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['delete']            = array(
	'Delete newsletter',
	'Delete newsletter ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['edit']              = array(
	'Edit newsletter',
	'Edit newsletter ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['editheader']        = array(
	'Edit newsletter settings',
	'Edit newsletter settings ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message']['send']                = array(
	'View and send',
	'View and send message ID %s.'
);
