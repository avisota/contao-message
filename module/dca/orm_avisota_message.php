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
 * Table orm_avisota_message
 * Entity Avisota\Contao:Message
 */
$GLOBALS['TL_DCA']['orm_avisota_message'] = array
(
    // Entity
    'entity'          => array(
        'idGenerator' => \Doctrine\ORM\Mapping\ClassMetadataInfo::GENERATOR_TYPE_UUID
    ),
    // Config
    'config'          => array
    (
        'dataContainer'    => 'General',
        'ctable'           => array('orm_avisota_message_content'),
        'switchToEdit'     => true,
        'enableVersioning' => true,
    ),
    // DataContainer
    'dca_config'      => array
    (
        'data_provider'  => array
        (
            'default' => array
            (
                'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
                'source' => 'orm_avisota_message'
            ),
            'parent'  => array
            (
                'class'  => 'Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider',
                'source' => 'orm_avisota_message_category'
            )
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
    'list'            => array
    (
        'sorting'           => array
        (
            'mode'               => 4,
            'fields'             => array('sendOn'),
            'panelLayout'        => 'search,limit',
            'headerFields'       => array('title'),
            'header_callback'    => array('Avisota\Contao\Message\Core\DataContainer\Message', 'addHeader'),
            'child_record_class' => 'no_padding',
        ),
        'label'             => array
        (
            'fields' => array('title'),
            'format' => '%s'
        ),
        'global_operations' => array
        (
            /*
            'createFromDraft' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message']['create_from_draft'],
                'href'       => 'table=orm_avisota_message_create_from_draft&amp;act=edit',
                'class'      => 'header_new',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="d"'
            ),
            */
            /*
            'all'             => array
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
            'edit'       => array
            (
                'label'   => &$GLOBALS['TL_LANG']['orm_avisota_message']['edit'],
                'href'    => 'table=orm_avisota_message_content',
                'icon'    => 'edit.gif',
                'idparam' => 'pid',
            ),
            'editheader' => array
            (
                'label' => &$GLOBALS['TL_LANG']['orm_avisota_message']['editheader'],
                'href'  => 'act=edit',
                'icon'  => 'header.gif',
            ),
            'copy'       => array
            (
                'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message']['copy'],
                'icon'       => 'copy.gif',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ),
            'delete'     => array
            (
                'label'      => &$GLOBALS['TL_LANG']['orm_avisota_message']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"',
            ),
            'show'       => array
            (
                'label' => &$GLOBALS['TL_LANG']['orm_avisota_message']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            ),
            'send'       => array
            (
                'label' => &$GLOBALS['TL_LANG']['orm_avisota_message']['send'],
                'href'  => 'act=preview',
                'icon'  => 'assets/avisota/message/images/send.png',
            )
        ),
    ),
    // Palettes
    'metapalettes'    => array
    (
        'default' => array
        (
            'newsletter' => array('subject', 'alias', 'language'),
            'meta'       => array('description', 'keywords'),
            'recipient'  => array('setRecipients', 'recipients'),
            'layout'     => array('setLayout', 'layout'),
            'queue'      => array('setQueue', 'queue'),
            'attachment' => array('addFile'),
            function (\ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Palette $palette) {
                $properties = $palette->getProperties();

                $boilerplateCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyCallbackCondition(
                    function (\Contao\Doctrine\ORM\DataContainer\General\EntityModel $model = null) {
                        /** @var \Avisota\Contao\Entity\Message $message */
                        $message = $model->getEntity();

                        return $message->getCategory()->getBoilerplates();
                    }
                );

                /** @var \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\PropertyInterface $property */
                foreach ($properties as $property) {
                    switch ($property->getName()) {
                        case 'setRecipients':
                            $visibleCondition = $property->getVisibleCondition();

                            if (!$visibleCondition) {
                                $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain();
                            } else {
                                if (
                                    !$visibleCondition instanceof \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain || $visibleCondition->getConjunction() != \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain::AND_CONJUNCTION
                                ) {
                                    $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain(array($visibleCondition));
                                }
                            }

                            $visibleCondition->addCondition(new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\NotCondition($boilerplateCondition));
                            $visibleCondition->addCondition(
                                new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyCallbackCondition(
                                    function (\Contao\Doctrine\ORM\DataContainer\General\EntityModel $model = null) {
                                        /** @var \Avisota\Contao\Entity\Message $message */
                                        $message = $model->getEntity();

                                        return $message->getCategory()->getRecipientsMode() == 'byMessageOrCategory';
                                    }
                                )
                            );

                            $property->setVisibleCondition($visibleCondition);
                            break;

                        case 'recipients':
                            $visibleCondition = $property->getVisibleCondition();

                            if (!$visibleCondition) {
                                $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain();
                            } else {
                                if (
                                    !$visibleCondition instanceof \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain || $visibleCondition->getConjunction() != \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain::AND_CONJUNCTION
                                ) {
                                    $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain(array($visibleCondition));
                                }
                            }

                            $visibleCondition->addCondition(new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\NotCondition($boilerplateCondition));
                            $visibleCondition->addCondition(
                                new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyCallbackCondition(
                                    function (\Contao\Doctrine\ORM\DataContainer\General\EntityModel $model = null) {
                                        /** @var \Avisota\Contao\Entity\Message $message */
                                        $message  = $model->getEntity();
                                        $category = $message->getCategory();

                                        return $category->getRecipientsMode() == 'byMessage'
                                               || $category->getRecipientsMode() == 'byMessageOrCategory'
                                                  && $message->getSetRecipients();
                                    }
                                )
                            );

                            $property->setVisibleCondition($visibleCondition);
                            break;

                        case 'setLayout':
                            $visibleCondition = $property->getVisibleCondition();

                            if (!$visibleCondition) {
                                $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain();
                            } else {
                                if (
                                    !$visibleCondition instanceof \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain || $visibleCondition->getConjunction() != \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain::AND_CONJUNCTION
                                ) {
                                    $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain(array($visibleCondition));
                                }
                            }

                            $visibleCondition->addCondition(new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\NotCondition($boilerplateCondition));
                            $visibleCondition->addCondition(
                                new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyCallbackCondition(
                                    function (\Contao\Doctrine\ORM\DataContainer\General\EntityModel $model = null) {
                                        /** @var \Avisota\Contao\Entity\Message $message */
                                        $message = $model->getEntity();

                                        return $message->getCategory()->getLayoutMode() == 'byMessageOrCategory';
                                    }
                                )
                            );

                            $property->setVisibleCondition($visibleCondition);
                            break;

                        case 'layout':
                            $visibleCondition = $property->getVisibleCondition();

                            if (!$visibleCondition) {
                                $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain();
                            } else {
                                if (
                                    !$visibleCondition instanceof \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain || $visibleCondition->getConjunction() != \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain::AND_CONJUNCTION
                                ) {
                                    $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain(array($visibleCondition));
                                }
                            }

                            $or = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain(array(), \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain::OR_CONJUNCTION);
                            $or->addCondition($boilerplateCondition);
                            $or->addCondition(
                                new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyCallbackCondition(
                                    function (\Contao\Doctrine\ORM\DataContainer\General\EntityModel $model = null) {
                                        /** @var \Avisota\Contao\Entity\Message $message */
                                        $message  = $model->getEntity();
                                        $category = $message->getCategory();

                                        return $category->getLayoutMode() == 'byMessage'
                                               || $category->getLayoutMode() == 'byMessageOrCategory'
                                                  && $message->getSetLayout();
                                    }
                                )
                            );

                            $visibleCondition->addCondition($or);

                            $property->setVisibleCondition($visibleCondition);
                            break;

                        case 'setQueue':
                            $visibleCondition = $property->getVisibleCondition();

                            if (!$visibleCondition) {
                                $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain();
                            } else {
                                if (
                                    !$visibleCondition instanceof \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain || $visibleCondition->getConjunction() != \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain::AND_CONJUNCTION
                                ) {
                                    $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain(array($visibleCondition));
                                }
                            }

                            $visibleCondition->addCondition(new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\NotCondition($boilerplateCondition));
                            $visibleCondition->addCondition(
                                new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyCallbackCondition(
                                    function (\Contao\Doctrine\ORM\DataContainer\General\EntityModel $model = null) {
                                        /** @var \Avisota\Contao\Entity\Message $message */
                                        $message = $model->getEntity();

                                        return $message->getCategory()->getQueueMode() == 'byMessageOrCategory';
                                    }
                                )
                            );

                            $property->setVisibleCondition($visibleCondition);
                            break;

                        case 'queue':
                            $visibleCondition = $property->getVisibleCondition();

                            if (!$visibleCondition) {
                                $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain();
                            } else {
                                if (
                                    !$visibleCondition instanceof \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain || $visibleCondition->getConjunction() != \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain::AND_CONJUNCTION
                                ) {
                                    $visibleCondition = new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyConditionChain(array($visibleCondition));
                                }
                            }

                            $visibleCondition->addCondition(
                                new \ContaoCommunityAlliance\DcGeneral\DataDefinition\Palette\Condition\Property\PropertyCallbackCondition(
                                    function (\Contao\Doctrine\ORM\DataContainer\General\EntityModel $model = null) {
                                        /** @var \Avisota\Contao\Entity\Message $message */
                                        $message  = $model->getEntity();
                                        $category = $message->getCategory();

                                        return $category->getQueueMode() == 'byMessage'
                                               || $category->getQueueMode() == 'byMessageOrCategory'
                                                  && $message->getSetQueue();
                                    }
                                )
                            );

                            $property->setVisibleCondition($visibleCondition);
                            break;
                    }
                }
            }
        ),
    ),
    // Subpalettes
    'metasubpalettes' => array
    (
        'addFile' => array('files')
    ),
    // Fields
    'fields'          => array
    (
        'id'            => array(
            'field' => array(
                'id'      => true,
                'type'    => 'string',
                'length'  => '36',
                'options' => array('fixed' => true),
            )
        ),
        'createdAt'     => array(
            'field' => array(
                'type'          => 'datetime',
                'nullable'      => true,
                'timestampable' => array('on' => 'create')
            )
        ),
        'updatedAt'     => array(
            'field' => array(
                'type'          => 'datetime',
                'nullable'      => true,
                'timestampable' => array('on' => 'update')
            )
        ),
        'category'      => array(
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['category'],
            'eval'      => array(
                'doNotShow' => true,
            ),
            'manyToOne' => array(
                'index'        => true,
                'targetEntity' => 'Avisota\Contao\Entity\MessageCategory',
                'cascade'      => array('persist', 'detach', 'merge', 'refresh'),
                'inversedBy'   => 'messages',
                'joinColumns'  => array(
                    array(
                        'name'                 => 'category',
                        'referencedColumnName' => 'id',
                    )
                )
            )
        ),
        'contents'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['contents'],
            'eval'      => array(
                'doNotShow' => true,
            ),
            'oneToMany' => array(
                'targetEntity' => 'Avisota\Contao\Entity\MessageContent',
                'cascade'      => array('all'),
                'mappedBy'     => 'message',
                // 'orphanRemoval' => false,
                // 'isCascadeRemove' => false,
                'orderBy'      => array('cell' => 'ASC', 'sorting' => 'ASC')
            ),
        ),
        'subject'       => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['subject'],
            'exclude'   => true,
            'search'    => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => array(
                'mandatory'      => true,
                'maxlength'      => 255,
                'tl_class'       => 'w50',
                'decodeEntities' => true
            ),
        ),
        'alias'         => array
        (
            'label'           => &$GLOBALS['TL_LANG']['orm_avisota_message']['alias'],
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
        'language'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['language'],
            'exclude'   => true,
            'filter'    => true,
            'flag'      => 1,
            'inputType' => 'select',
            'options'   => $this->getLanguages(),
            'eval'      => array(
                'mandatory' => true,
                'tl_class'  => 'w50',
            ),
            'field'     => array(
                'type'    => 'string',
                'length'  => 5,
                'options' => array('fixed' => true),
            ),
        ),
        'description'   => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['description'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'textarea',
            'eval'      => array(
                'maxlength' => 255,
                'rows'      => 4,
            )
        ),
        'keywords'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['keywords'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => array(
                'maxlength' => 255,
                'tl_class'  => 'long'
            )
        ),
        'setRecipients' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['setRecipients'],
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'clr w50', 'submitOnChange' => true)
        ),
        'recipients'    => array
        (
            'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message']['recipients'],
            'inputType'        => 'select',
            'options_callback' => \ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory::createCallback(
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
        'setLayout'     => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['setLayout'],
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'clr m12 w50', 'submitOnChange' => true)
        ),
        'layout'        => array
        (
            'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message']['layout'],
            'inputType'        => 'select',
            'options_callback' => \ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory::createCallback(
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
        'setQueue'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['setQueue'],
            'inputType' => 'checkbox',
            'eval'      => array('tl_class' => 'clr m12 w50', 'submitOnChange' => true)
        ),
        'queue'         => array
        (
            'label'            => &$GLOBALS['TL_LANG']['orm_avisota_message']['queue'],
            'inputType'        => 'select',
            'options_callback' => \ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEventCallbackFactory::createCallback(
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
        'addFile'       => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['addFile'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => array('submitOnChange' => true)
        ),
        'files'         => array
        (
            'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message']['files'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => array(
                'fieldType' => 'checkbox',
                'files'     => true,
                'filesOnly' => true,
                'inputType' => 'checkbox',
                'multiple'  => true,
                'mandatory' => true
            ),
            'field'     => array(),
        ),
        'sendOn'        => array
        (
            'label'   => &$GLOBALS['TL_LANG']['orm_avisota_recipient']['sendOn'],
            'filter'  => true,
            'sorting' => true,
            'flag'    => 7,
            'eval'    => array(
                'doNotCopy' => true,
                'doNotShow' => true
            ),
            'field'   => array(
                'type'     => 'datetime',
                'nullable' => true,
            ),
        )
    )
);
