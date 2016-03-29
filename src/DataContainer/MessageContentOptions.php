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

namespace Avisota\Contao\Message\Core\DataContainer;

use Avisota\Contao\Core\Event\CreateOptionsEvent;
use Avisota\Contao\Message\Core\MessageEvents;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MessageContentOptions
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class MessageContentOptions implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            MessageEvents::CREATE_MESSAGE_CONTENT_TYPE_OPTIONS => array(
                array('createMessageContentTypeOptions'),
            ),

            MessageEvents::CREATE_MESSAGE_CONTENT_CELL_OPTIONS => array(
                array('createMessageContentCellOptions'),
            ),

            'avisota.create-article-options' => array(
                array('createArticleAliasOptions'),
            ),

            GetPropertyOptionsEvent::NAME => array(
                array('createContentTypeOptions', 10),
            ),
        );
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createMessageContentTypeOptions(CreateOptionsEvent $event)
    {
        if (!$event->isDefaultPrevented()) {
            $this->getMessageContentTypeOptions($event->getDataContainer(), $event->getOptions());
        }
    }

    /**
     * Return all newsletter elements as array
     *
     * @param       $dc
     * @param array $options
     *
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function getMessageContentTypeOptions($dc, $options = array())
    {
        if (!count($options)) {
            foreach ($GLOBALS['TL_MCE'] as $elementGroup => $elements) {
                if (isset($GLOBALS['TL_LANG']['MCE'][$elementGroup])) {
                    $elementGroup = $GLOBALS['TL_LANG']['MCE'][$elementGroup];
                }

                if (!isset($options[$elementGroup])) {
                    $options[$elementGroup] = array();
                }

                foreach ($elements as $elementType) {
                    $label = isset($GLOBALS['TL_LANG']['MCE'][$elementType])
                        ? $GLOBALS['TL_LANG']['MCE'][$elementType]
                        : $elementType;

                    if (is_array($label)) {
                        $label = $label[0];
                    }

                    $options[$elementGroup][$elementType] = $label;
                }
            }
        }

        return $options;
    }

    /**
     * Get a list of areas from the parent category.
     *
     * @param CreateOptionsEvent $event
     *
     * @internal param DC_General $dc
     */
    public function createMessageContentCellOptions(CreateOptionsEvent $event)
    {
        $this->getMessageContentCellOptions($event->getOptions());
    }

    /**
     * Get a list of areas from the parent category.
     *
     * @param array $options
     *
     * @return array
     * @internal param DC_General $dc
     */
    public function getMessageContentCellOptions($options = array())
    {
        if (!count($options)) {
            $options[] = 'center';
        }

        return $options;
    }

    /**
     * Get all articles and return them as array (article alias)
     *
     * @param CreateOptionsEvent $event
     *
     * @return array
     * @internal param $object
     *
     */
    public function createArticleAliasOptions(CreateOptionsEvent $event)
    {
        $this->getArticleAliasOptions($event->getOptions());
    }

    /**
     * Get all articles and return them as array (article alias)
     *
     * @param array $options
     *
     * @return array
     * @internal param $object
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getArticleAliasOptions($options = array())
    {
        $pids = array();

        $user = \BackendUser::getInstance();

        if (!$user->isAdmin) {
            foreach ($user->pagemounts as $id) {
                $pids[] = $id;
                $pids   = array_merge($pids, $this->getChildRecords($id, 'tl_page', true));
            }

            if (empty($pids)) {
                return $options;
            }

            $alias = \Database::getInstance()->execute(
                "SELECT a.id, a.title, a.i" .
                "nColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid WHERE a.pid IN(" .
                implode(
                    ',',
                    array_map('intval', array_unique($pids))
                ) . ") ORDER BY parent, a.sorting"
            );
        } else {
            $alias = \Database::getInstance()->execute(
                "SELECT a.id, a.title, a.inColumn, p.title AS parent " .
                "FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid ORDER BY parent, a.sorting"
            );
        }

        if ($alias->numRows) {
            /** @var EventDispatcher $eventDispatcher */
            $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

            $eventDispatcher->dispatch(
                ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE,
                new LoadLanguageFileEvent('tl_article')
            );

            while ($alias->next()) {
                $buffer = (strlen($GLOBALS['TL_LANG']['tl_article'][$alias->inColumn])
                        ? $GLOBALS['TL_LANG']['tl_article'][$alias->inColumn]
                        : $alias->inColumn) . ', ID ' . $alias->id;

                $options[$alias->parent][$alias->id] = $alias->title . ' (' . $buffer . ')';
            }
        }

        return $options;
    }

    /**
     * @param CreateOptionsEvent|GetPropertyOptionsEvent $event
     * @param                                            $name
     * @param EventDispatcher                            $eventDispatcher
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function createContentTypeOptions(GetPropertyOptionsEvent $event, $name, EventDispatcher $eventDispatcher)
    {
        if ($event->getPropertyName() !== 'allowedCellContents') {
            return;
        }

        $options = $event->getOptions();
        if (!is_array($options)) {
            $options = (array) $options;
        }

        foreach ($GLOBALS['TL_MCE'] as $elementGroup => $elements) {
            if (isset($GLOBALS['TL_LANG']['MCE'][$elementGroup])) {
                $elementGroupLabel = $GLOBALS['TL_LANG']['MCE'][$elementGroup];
            } else {
                $elementGroupLabel = $elementGroup;
            }
            foreach ($elements as $elementType) {
                if (isset($GLOBALS['TL_LANG']['MCE'][$elementType])) {
                    $elementLabel = $GLOBALS['TL_LANG']['MCE'][$elementType][0];
                } else {
                    $elementLabel = $elementType;
                }

                $options[$elementGroupLabel][$elementType] = sprintf(
                    '%s',
                    $elementLabel
                );
            }
        }

        $event->setOptions($options);
    }
}
