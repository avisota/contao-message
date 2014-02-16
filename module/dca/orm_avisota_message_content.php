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
		'ptable'           => 'orm_avisota_message',
		'enableVersioning' => true,
		'onload_callback'  => array
		(
			// we don�t have an permission management yet so don�t check permissions. 
			// It might throw an error if the user is not an admin.
			//array('Avisota\Contao\Core\DataContainer\MessageContent', 'checkPermission')
		)
	),
	// DataContainer
	'dca_config'      => array
	(
		'callback'       => 'DcGeneral\Callbacks\ContaoStyleCallbacks',
		'data_provider'  => array
		(
			'default' => array
			(
				'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
				'source' => 'orm_avisota_message_content'
			),
			'parent'  => array
			(
				'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
				'source' => 'orm_avisota_message'
			)
		),
		'controller'     => 'DcGeneral\Controller\DefaultController',
		'view'           => 'DcGeneral\View\DefaultView',
		'childCondition' => array(
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
	'list'            => array
	(
		'sorting'           => array
		(
			'mode'                  => 4,
			'fields'                => array('sorting'),
			'panelLayout'           => 'filter;search,limit',
			'headerFields'          => array('subject'),
			'child_record_callback' => array('Avisota\Contao\Core\DataContainer\MessageContent', 'addElement')
		),
		'global_operations' => array
		(
			'view' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['orm_avisota_message']['view'],
				'href'            => 'table=orm_avisota_message&amp;key=send',
				'class'           => 'header_send',
				'button_callback' => array('Avisota\Contao\Core\DataContainer\MessageContent', 'sendMessageButton')
			),
			'all'  => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
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
				'href'       => 'act=paste&amp;mode=copy',
				'icon'       => 'copy.gif',
				'attributes' => 'onclick="Backend.getScrollOffset();"'
			),
			'cut'    => array
			(
				'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['cut'],
				'href'       => 'act=paste&amp;mode=cut',
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
				'label'           => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['toggle'],
				'icon'            => 'visible.gif',
				'attributes'      => 'onclick="Backend.getScrollOffset(); return AjaxRequest.toggleVisibility(this, %s);"',
				'button_callback' => array('Avisota\Contao\Core\DataContainer\MessageContent', 'toggleIcon')
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
		'default'   => array
		(
			'type' => array('type', 'cell')
		),
	),
	// Subpalettes
	'metasubpalettes' => array
	(
		'protected'   => array('groups')
	),
	// Fields
	'fields'          => array
	(
		'id'              => array(
			'field' => array(
				'id'      => true,
				'type'    => 'string',
				'length'  => '36',
				'options' => array('fixed' => true),
			)
		),
		'createdAt'       => array(
			'field' => array(
				'type'          => 'datetime',
				'nullable'      => true,
				'timestampable' => array('on' => 'create')
			)
		),
		'updatedAt'       => array(
			'field' => array(
				'type'          => 'datetime',
				'nullable'      => true,
				'timestampable' => array('on' => 'update')
			)
		),
		'message'         => array(
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
		'sorting'         => array
		(
			'field' => array(
				'type' => 'integer'
			)
		),
		'type'            => array
		(
			'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['type'],
			'exclude'          => true,
			'filter'           => true,
			'inputType'        => 'select',
			'options_callback' => CreateOptionsEventCallbackFactory::createCallback('avisota.create-message-content-type-options'),
			'reference'        => &$GLOBALS['TL_LANG']['MCE'],
			'eval'             => array('includeBlankOption' => true, 'helpwizard' => true, 'submitOnChange' => true, 'tl_class' => 'w50')
		),
		'cell'            => array
		(
			'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['cell'],
			'exclude'          => true,
			'filter'           => true,
			'inputType'        => 'select',
			'options_callback' => CreateOptionsEventCallbackFactory::createCallback('avisota.create-message-content-cell-options'),
			'reference'        => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['cell'],
			'eval'             => array('mandatory' => true, 'submitOnChange' => true, 'includeBlankOption' => true, 'tl_class' => 'w50')
		),
		'personalize'     => array
		(
			'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['personalize'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'select',
			'options'   => array('anonymous', 'private'),
			'reference' => &$GLOBALS['TL_LANG']['orm_avisota_message_content'],
			'eval'      => array('tl_class' => 'long')
		),

		'protected'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['protected'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array('submitOnChange' => true)
		),
		'groups'          => array
		(
			'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['groups'],
			'exclude'    => true,
			'inputType'  => 'checkbox',
			'foreignKey' => 'tl_member_group.name',
			'eval'       => array('mandatory' => true, 'multiple' => true)
		),
		'guests'          => array
		(
			'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['guests'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox'
		),
		'cssID'           => array
		(
			'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['cssID'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('multiple' => true, 'size' => 2, 'tl_class' => 'w50')
		),
		'space'           => array
		(
			'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['space'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('multiple' => true, 'size' => 2, 'rgxp' => 'digit', 'nospace' => true)
		),
		'invisible'       => array
		(
			'label'   => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['invisible'],
			'default' => false,
			'field'   => array(
				'type' => 'boolean'
			)
		),
		'unmodifiable'    => array
		(
			'label'   => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['unmodifiable'],
			'default' => false,
			'field'   => array(
				'type' => 'boolean'
			)
		),
		'undeletable'     => array
		(
			'label'   => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['undeletable'],
			'default' => false,
			'field'   => array(
				'type' => 'boolean'
			)
		),
	),
);
