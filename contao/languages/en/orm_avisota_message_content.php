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
$GLOBALS['TL_LANG']['orm_avisota_message_content']['invisible']    = array(
	'Invisible',
	'Invisible',
	'Hide the element on the email.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['cell']            = array(
	'Cell',
	'Please choose the cell the content element should be showed in.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['type']         = array(
	'Element type',
	'Please choose the type of content element.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['protected']    = array(
	'Element schützen <strong style="color:red">REMOVE</strong>',
	'Das Inhaltselement nur bestimmten Gruppen anzeigen.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['groups']       = array(
	'Erlaubte Mitgliedergruppen <strong style="color:red">REMOVE</strong>',
	'Diese Gruppen können das Inhaltselement sehen.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['guests']       = array(
	'Nur Gästen anzeigen <strong style="color:red">REMOVE</strong>',
	'Das Inhaltselement verstecken sobald ein Mitglied angemeldet ist.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['cssID']        = array(
	'CSS ID/class',
	'Here you can set an ID and one or more classes.'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['space']        = array(
	'Space in front and after',
	'Here you can enter the spacing in front of and after the content element in pixel. You should try to avoid inline styles and define the spacing in a style sheet, though.'
);


/**
 * Legends
 */
$GLOBALS['TL_LANG']['orm_avisota_message_content']['type_legend']      = 'Element type';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['sortable_legend']  = 'Sorting options';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['template_legend']  = 'Template settings';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['include_legend']   = 'Include settings';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['protected_legend'] = 'Access protection';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['expert_legend']    = 'Expert settings';


/**
 * Reference
 */
$GLOBALS['TL_LANG']['orm_avisota_message_content']['cells']['header'] = 'Header';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['cells']['main']   = 'Main column';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['cells']['left']   = 'Left column';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['cells']['center'] = 'Center column';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['cells']['right']  = 'Right column';
$GLOBALS['TL_LANG']['orm_avisota_message_content']['cells']['footer'] = 'Footer';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['orm_avisota_message_content']['send']        = array(
	'View and send message',
	'View and send message'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['new']         = array(
	'New element',
	'Add a new content element'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['show']        = array(
	'Element details',
	'Show the details of content element ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['cut']         = array(
	'Move element',
	'Move content element ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['copy']        = array(
	'Duplicate element',
	'Duplicate content element ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['delete']      = array(
	'Delete element',
	'Delete content element ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['edit']        = array(
	'Edit element',
	'Edit content element ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['pasteafter']  = array(
	'Paste at the top',
	'Paste after content element ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['pastenew']    = array(
	'Add new at the top',
	'Add new after content element ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['toggle']      = array(
	'Toggle visibility',
	'Toggle the visibility of element ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['editalias']   = array(
	'Edit source element',
	'Edit the source element ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_message_content']['editarticle'] = array(
	'Edit article',
	'Edit article ID %s'
);
