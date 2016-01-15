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

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['metapalettes']['avisota_message_list']   = array
(
    'title'                => array(
        'name',
        'headline',
        'type',
    ),
    'avisota_message_list' => array(
        'avisota_message_categories',
    ),
    'template'             => array(
        'jumpTo',
    ),
    'protected'            => array(
        ':hide',
        'protected',
    ),
    'expert'               => array(
        ':hide',
        'guests',
        'cssID',
        'space',
    )
);
$GLOBALS['TL_DCA']['tl_module']['metapalettes']['avisota_message_reader'] = array
(
    'title'                  => array(
        'name',
        'headline',
        'type',
    ),
    'avisota_message_reader' => array(
        'avisota_message_categories',
    ),
    'template'               => array(
        'avisota_message_layout',
        'avisota_message_cell',
    ),
    'protected'              => array(
        ':hide',
        'protected',
    ),
    'expert'                 => array(
        ':hide',
        'guests',
        'cssID',
        'space',
    )
);

/**
 * General module fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['avisota_message_categories'] = array
(
    'exclude'          => true,
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['avisota_message_categories'],
    'inputType'        => 'checkbox',
    'options_callback' => \ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory::createCallback(
        \Avisota\Contao\Message\Core\MessageEvents::CREATE_MESSAGE_CATEGORY_OPTIONS,
        'Avisota\Contao\Core\Event\CreateOptionsEvent'
    ),
    'eval'             => array(
        'mandatory' => true,
        'multiple'  => true,
    ),
);
$GLOBALS['TL_DCA']['tl_module']['fields']['avisota_message_layout']     = array
(
    'exclude'          => true,
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['avisota_message_layout'],
    'inputType'        => 'select',
    'options_callback' => \ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory::createCallback(
        \Avisota\Contao\Message\Core\MessageEvents::CREATE_MESSAGE_LAYOUT_OPTIONS,
        'Avisota\Contao\Core\Event\CreateOptionsEvent'
    ),
    'eval'             => array(
        'mandatory' => true,
        'tl_class'  => 'w50',
    ),
);
$GLOBALS['TL_DCA']['tl_module']['fields']['avisota_message_cell']       = array
(
    'exclude'          => true,
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['avisota_message_cell'],
    'inputType'        => 'checkboxWizard',
    'options_callback' => \ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory::createCallback(
        \Avisota\Contao\Message\Core\MessageEvents::CREATE_MESSAGE_CONTENT_CELL_OPTIONS,
        'Avisota\Contao\Core\Event\CreateOptionsEvent'
    ),
    'eval'             => array(
        'mandatory' => true,
        'multiple'  => true,
        'tl_class'  => 'clr',
    ),
);
