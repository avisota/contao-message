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
$GLOBALS['TL_LANG']['orm_avisota_layout']['type']             = array(
	'Type',
	'Please choose the layout type.'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['title']             = array(
	'Title',
	'Please enter the layout title.'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['alias']              = array(
	'Alias',
	'The layout alias is a unique reference to the layout which can be used instead of its ID.'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['preview']           = array(
	'Preview image',
	'Please chose a preview image.'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['stylesheets']       = array(
	'Stylesheets',
	'Please chose the stylesheets to used in this layout.'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['allowedCellContents']             = array(
	'Allowed contents',
	'Choose the allowed contents for each cell (empty to reset to defaults).'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['clearStyles']             = array(
	'Remove global styles',
	'Remove the global styles from the template.'
);


/**
 * Legends
 */
$GLOBALS['TL_LANG']['orm_avisota_layout']['layout_legend']     = 'Layout';
$GLOBALS['TL_LANG']['orm_avisota_layout']['structure_legend'] = 'Structure and content';
$GLOBALS['TL_LANG']['orm_avisota_layout']['template_legend']  = 'Template settings';
$GLOBALS['TL_LANG']['orm_avisota_layout']['expert_legend']    = 'Experts settings';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['orm_avisota_layout']['new']    = array(
	'New layout',
	'Create a new layout'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['show']   = array(
	'Layout details',
	'Show the details of layout ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['edit']   = array(
	'Edit layout',
	'Edit layout ID %s'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['copy']   = array(
	'Duplicate layout',
	'Duplicate layout %s'
);
$GLOBALS['TL_LANG']['orm_avisota_layout']['delete'] = array(
	'Delete layout',
	'Delete layout ID %s'
);


/**
 * Reference
 */
$GLOBALS['TL_LANG']['orm_avisota_layout']['default'] = 'Default generic rendering';
