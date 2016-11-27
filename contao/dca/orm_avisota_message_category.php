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
 * Table orm_avisota_message_category
 * Entity Avisota\Contao:MessageCategory
 */
$GLOBALS['TL_DCA']['orm_avisota_message_category'] = array
(
    // Entity
    'entity'                => array(
        'idGenerator' => \Doctrine\ORM\Mapping\ClassMetadataInfo::GENERATOR_TYPE_UUID
    ),
    // Config
    'config'                => array
    (
        'dataContainer'    => 'General',
        'ctable'           => array('orm_avisota_message'),
        'switchToEdit'     => true,
        'enableVersioning' => true,
    ),
    // DataContainer
    'dca_config'            => array
    (
        'data_provider'  => array
        (
            'default'                     => array
            (
                'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
                'source' => 'orm_avisota_message_category'
            ),
            'orm_avisota_message'         => array
            (
                'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
                'source' => 'orm_avisota_message'
            ),
            'orm_avisota_message_content' => array
            (
                'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
                'source' => 'orm_avisota_message_content'
            ),
        ),
        'childCondition' => array(
            array(
                'from'   => 'orm_avisota_message_category',
                'to'     => 'orm_avisota_message',
                'setOn'  => array
                (
                    array(
                        'to_field'   => 'category',
                        'from_field' => 'id',
                    ),
                ),
                'filter' => array
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
            )
        )
    ),
    // List
    'list'                  => array
    (
        'sorting'           => array
        (
            'mode'        => 1,
            'flag'        => 1,
            'fields'      => array('title'),
            'panelLayout' => 'search,limit'
        ),
        'label'             => array
        (
            'fields' => array('title'),
            'format' => '%s'
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            )
        ),
        'operations'        => array
        (
            'edit'       => array
            (
                'label'   => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['edit'],
                'href'    => 'table=orm_avisota_message',
                'icon'    => 'edit.gif',
                'idparam' => 'pid',
            ),
            'editheader' => array
            (
                'label' => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['editheader'],
                'href'  => 'act=edit',
                'icon'  => 'header.gif',
            ),
            'copy'       => array
            (
                'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['copy'],
                'icon'       => 'copy.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ),
            // Todo add alert box description
            'delete'     => array
            (
                'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                . '\')) return false; Backend.getScrollOffset();"',
            ),
            'show'       => array
            (
                'label' => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            )
        ),
    ),
    // Palettes
    'palettes'              => array(
        '__selector__' => array('boilerplates')
    ),
    'metapalettes'          => array
    (
        'default'      => array
        (
            'category'   => array('title', 'alias'),
            'recipients' => array('recipientsMode'),
            'layout'     => array('layoutMode'),
            'queue'      => array('queueMode'),
            'online'     => array('viewOnlinePage'),
            'expert'     => array(':hide', 'boilerplates', 'showInMenu'),
        ),
        'boilerplates' => array
        (
            'category' => array('title', 'alias'),
            'expert'   => array(':hide', 'boilerplates'),
        ),
    ),
    // Subpalettes
    'metasubpalettes'       => array
    (
        'showInMenu'        => array('useCustomMenuIcon'),
        'useCustomMenuIcon' => array('menuIcon'),
    ),
    // Subselectpalettes
    'metasubselectpalettes' => array
    (
        'recipientsMode' => array
        (
            'byCategory'          => array('recipients'),
            'byMessageOrCategory' => array('recipients'),
        ),
        'layoutMode'     => array
        (
            'byCategory'          => array('layout'),
            'byMessageOrCategory' => array('layout')
        ),
        'queueMode'      => array
        (
            'byCategory'          => array('queue'),
            'byMessageOrCategory' => array('queue')
        ),
    ),
    // Fields
    'fields'                => array
    (
        'id'                => array(
            'field' => array(
                'id'      => true,
                'type'    => 'string',
                'length'  => '36',
                'options' => array('fixed' => true),
            )
        ),
        'createdAt'         => array(
            'field' => array(
                'type'          => 'datetime',
                'nullable'      => true,
                'timestampable' => array('on' => 'create')
            )
        ),
        'updatedAt'         => array(
            'field' => array(
                'type'          => 'datetime',
                'nullable'      => true,
                'timestampable' => array('on' => 'update')
            )
        ),
        'messages'          => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['messages'],
            'eval'      => array(
                'doNotShow' => true,
            ),
            'oneToMany' => array(
                'targetEntity' => 'Avisota\Contao\Entity\Message',
                'cascade'      => array('all'),
                'mappedBy'     => 'category',
                // 'orphanRemoval' => false,
                // 'isCascadeRemove' => false,
                'orderBy'      => array('sendOn' => 'ASC')
            ),
        ),
        'title'             => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['title'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => array(
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class'  => 'w50'
            )
        ),
        'alias'             => array
        (
            'label'           => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['alias'],
            'exclude'         => true,
            'search'          => true,
            'inputType'       => 'text',
            'eval'            => array(
                'rgxp'              => 'alnum',
                'unique'            => true,
                'spaceToUnderscore' => true,
                'maxlength'         => 128,
                'tl_class'          => 'w50'
            ),
            'setter_callback' => array
            (
                array('Contao\Doctrine\ORM\Helper', 'generateAlias')
            )
        ),
        'recipientsMode'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['recipientsMode'],
            'default'   => 'byCategory',
            'inputType' => 'select',
            'options'   => array('byCategory', 'byMessageOrCategory', 'byMessage'),
            'reference' => &$GLOBALS['TL_LANG']['orm_avisota_message_category'],
            'eval'      => array(
                'mandatory'      => true,
                'submitOnChange' => true,
                'tl_class'       => 'clr w50'
            )
        ),
        'recipients'        => array
        (
            'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['recipients'],
            'inputType'        => 'select',
            'options_callback' => CreateOptionsEventCallbackFactory::createCallback(
                'avisota.create-recipient-source-options',
                'Avisota\Contao\Core\Event\CreateOptionsEvent'
            ),
            'eval'             => array(
                'mandatory'          => true,
                'includeBlankOption' => true,
                'tl_class'           => 'w50'
            ),
            'manyToOne'        => array(
                'targetEntity' => 'Avisota\Contao\Entity\RecipientSource',
                'cascade'      => array('persist', 'detach', 'merge', 'refresh'),
                'joinColumns'  => array(
                    array(
                        'name'                 => 'recipientSource',
                        'referencedColumnName' => 'id',
                    ),
                ),
            ),
        ),
        'layoutMode'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['layoutMode'],
            'default'   => 'byCategory',
            'inputType' => 'select',
            'options'   => array('byCategory', 'byMessageOrCategory', 'byMessage'),
            'reference' => &$GLOBALS['TL_LANG']['orm_avisota_message_category'],
            'eval'      => array(
                'mandatory'      => true,
                'submitOnChange' => true,
                'tl_class'       => 'w50'
            )
        ),
        'layout'            => array
        (
            'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['layout'],
            'inputType'        => 'select',
            'options_callback' => CreateOptionsEventCallbackFactory::createCallback(
                'avisota.create-layout-options',
                'Avisota\Contao\Core\Event\CreateOptionsEvent'
            ),
            'eval'             => array(
                'mandatory'          => true,
                'includeBlankOption' => true,
                'tl_class'           => 'w50'
            ),
            'manyToOne'        => array(
                'targetEntity' => 'Avisota\Contao\Entity\Layout',
                'cascade'      => array('persist', 'detach', 'merge', 'refresh'),
                'joinColumns'  => array(
                    array(
                        'name'                 => 'layout',
                        'referencedColumnName' => 'id',
                    ),
                ),
            ),
        ),
        'queueMode'         => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['queueMode'],
            'default'   => 'byCategory',
            'inputType' => 'select',
            'options'   => array('byCategory', 'byMessageOrCategory', 'byMessage'),
            'reference' => &$GLOBALS['TL_LANG']['orm_avisota_message_category'],
            'eval'      => array(
                'mandatory'      => true,
                'submitOnChange' => true,
                'tl_class'       => 'w50'
            )
        ),
        'queue'             => array
        (
            'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['queue'],
            'inputType'        => 'select',
            'options_callback' => CreateOptionsEventCallbackFactory::createCallback(
                'avisota.create-queue-options',
                'Avisota\Contao\Core\Event\CreateOptionsEvent'
            ),
            'eval'             => array(
                'mandatory'          => true,
                'includeBlankOption' => true,
                'tl_class'           => 'w50'
            ),
            'manyToOne'        => array(
                'targetEntity' => 'Avisota\Contao\Entity\Queue',
                'cascade'      => array('persist', 'detach', 'merge', 'refresh'),
                'joinColumns'  => array(
                    array(
                        'name'                 => 'queue',
                        'referencedColumnName' => 'id',
                    ),
                ),
            ),
        ),
        'viewOnlinePage'    => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['viewOnlinePage'],
            'inputType' => 'pageTree',
            'field'     => array(
                'type' => 'integer',
            ),
        ),
        'boilerplates'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['boilerplates'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => array(
                'submitOnChange' => true,
                'tl_class'       => 'm12'
            )
        ),
        'showInMenu'        => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['showInMenu'],
            'inputType' => 'checkbox',
            'eval'      => array(
                'submitOnChange' => true,
                'tl_class'       => 'm12 w50'
            )
        ),
        'useCustomMenuIcon' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['useCustomMenuIcon'],
            'default'   => false,
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'm12 w50', 'submitOnChange' => true)
        ),
        'menuIcon'          => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_category']['menuIcon'],
            'inputType' => 'fileTree',
            'eval'      => array(
                'tl_class'   => 'clr',
                'files'      => true,
                'filesOnly'  => true,
                'fieldType'  => 'radio',
                'extensions' => 'png,gif,jpg,jpeg'
            ),
            'field'     => array(),
        ),
    )
);
