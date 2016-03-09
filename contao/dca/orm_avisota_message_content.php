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

use ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory;

/**
 * Table orm_avisota_message_content
 * Entity Avisota\Contao:MessageContent
 */
$GLOBALS['TL_DCA']['orm_avisota_message_content'] = array
(
    // Entity
    'entity'          => array(
        'idGenerator' => \Doctrine\ORM\Mapping\ClassMetadataInfo::GENERATOR_TYPE_UUID
    ),
    // Config
    'config'          => array
    (
        'dataContainer'    => 'General',
        'enableVersioning' => true,
    ),
    // DataContainer
    'dca_config'      => array
    (
        'data_provider'  => array
        (
            'parent'  => array
            (
                'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
                'source' => 'orm_avisota_message'
            ),
            'default' => array
            (
                'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
                'source' => 'orm_avisota_message_content'
            ),
            array
            (
                'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
                'source' => 'orm_avisota_message_category'
            ),
        ),
        'childCondition' => array(
            array(
                'from'    => 'orm_avisota_message_category',
                'to'      => 'orm_avisota_message',
                'setOn'   => array
                (
                    array(
                        'to_field'   => 'category',
                        'from_field' => 'id',
                    ),
                ),
                'inverse' => array
                (
                    array
                    (
                        'local'     => 'category',
                        'remote'    => 'id',
                        'operation' => '=',
                    )
                )
            ),
            array(
                'from'   => 'orm_avisota_message',
                'to'     => 'orm_avisota_message_content',
                'setOn'  => array
                (
                    array(
                        'to_field'   => 'message',
                        'from_field' => 'id',
                    ),
                ),
                'filter' => array
                (
                    array
                    (
                        'local'     => 'message',
                        'remote'    => 'id',
                        'operation' => '=',
                    )
                )
            ),
        )
    ),
    // List
    'list'            => array
    (
        'sorting'           => array
        (
            'mode'         => 4,
            'fields'       => array('cell FIELD(e.cell, \'header\', \'main\', \'left\', \'center\', \'right\', \'footer\')', 'sorting'),
            'panelLayout'  => 'filter;search,limit',
            'headerFields' => array('subject'),
        ),
        'label'             => array
        (
            'fields' => array('title'),
            'format' => '%s'
        ),
        'global_operations' => array
        (
            'send' => array
            (
                'label'           => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['send'],
                'href'            => 'table=orm_avisota_message&amp;act=preview',
                'class'           => 'header_send',
                'button_callback' => array(
                    'Avisota\Contao\Message\Core\DataContainer\MessageContent',
                    'sendMessageButton'
                )
            ),
            /*
            'all'  => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            )
            */
        ),
        'operations'        => array
        (
            'edit'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ),
            'copy'   => array
            (
                'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['copy'],
                'icon'       => 'copy.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'cut'    => array
            (
                'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['cut'],
                'icon'       => 'cut.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ),
            'delete' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'toggle' => array
            (
                'label'          => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['toggle'],
                'icon'           => 'visible.gif',
                'toggleProperty' => 'invisible',
                'toggleInverse'  => true,
            ),
            'show'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            )
        ),
    ),
    // Palettes
    'palettes'        => array
    (
        '__selector__' => array('type')
    ),
    'metapalettes'    => array
    (
        'default' => array
        (
            'type'      => array('cell', 'type'),
            'published' => array('invisible'),
        ),
    ),
    // Subpalettes
    'metasubpalettes' => array
    (
        'protected' => array('groups')
    ),
    // Fields
    'fields'          => array
    (
        'id'           => array(
            'field' => array(
                'id'      => true,
                'type'    => 'string',
                'length'  => '36',
                'options' => array('fixed' => true),
            )
        ),
        'createdAt'    => array(
            'field' => array(
                'type'          => 'datetime',
                'nullable'      => true,
                'timestampable' => array('on' => 'create')
            )
        ),
        'updatedAt'    => array(
            'field' => array(
                'type'          => 'datetime',
                'nullable'      => true,
                'timestampable' => array('on' => 'update')
            )
        ),
        'message'      => array(
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['message'],
            'eval'      => array(
                'doNotShow' => true,
            ),
            'manyToOne' => array(
                'index'        => true,
                'targetEntity' => 'Avisota\Contao\Entity\Message',
                'cascade'      => array('persist', 'detach', 'merge', 'refresh'),
                'inversedBy'   => 'contents',
                'joinColumns'  => array(
                    array(
                        'name'                 => 'message',
                        'referencedColumnName' => 'id',
                    )
                )
            )
        ),
        'sorting'      => array
        (
            'default' => 0,
            'field'   => array(
                'type' => 'integer'
            )
        ),
        'cell'         => array
        (
            'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['cell'],
            'exclude'          => true,
            'filter'           => true,
            'flag'             => 1,
            'inputType'        => 'select',
            'options_callback' => CreateOptionsEventCallbackFactory::createCallback(
                \Avisota\Contao\Message\Core\MessageEvents::CREATE_MESSAGE_CONTENT_CELL_OPTIONS,
                'Avisota\Contao\Core\Event\CreateOptionsEvent'
            ),
            'reference'        => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['cells'],
            'eval'             => array(
                'mandatory'          => true,
                'submitOnChange'     => true,
                'includeBlankOption' => true,
                'tl_class'           => 'w50'
            )
        ),
        'type'         => array
        (
            'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['type'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => CreateOptionsEventCallbackFactory::createCallback(
                \Avisota\Contao\Message\Core\MessageEvents::CREATE_MESSAGE_CONTENT_TYPE_OPTIONS,
                'Avisota\Contao\Core\Event\CreateOptionsEvent'
            ),
            'reference'        => &$GLOBALS['TL_LANG']['MCE'],
            'eval'             => array(
                'includeBlankOption' => true,
                'helpwizard'         => true,
                'submitOnChange'     => true,
                'tl_class'           => 'w50'
            )
        ),
        'protected'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['protected'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => array('submitOnChange' => true)
        ),
        'groups'       => array
        (
            'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['groups'],
            'exclude'    => true,
            'inputType'  => 'checkbox',
            'foreignKey' => 'tl_member_group.name',
            'eval'       => array('mandatory' => true, 'multiple' => true)
        ),
        'guests'       => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['guests'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox'
        ),
        'cssID'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['cssID'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('multiple' => true, 'size' => 2, 'tl_class' => 'w50')
        ),
        'space'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['space'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('multiple' => true, 'size' => 2, 'rgxp' => 'digit', 'nospace' => true)
        ),
        'invisible'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['invisible'],
            'default'   => false,
            'inputType' => 'checkbox',
            'field'     => array(
                'type' => 'boolean'
            )
        ),
        'unmodifiable' => array
        (
            'label'   => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['unmodifiable'],
            'default' => false,
            'field'   => array(
                'type' => 'boolean'
            )
        ),
        'undeletable'  => array
        (
            'label'   => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['undeletable'],
            'default' => false,
            'field'   => array(
                'type' => 'boolean'
            )
        ),
    ),
);
