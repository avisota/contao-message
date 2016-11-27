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
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\CollectStylesheetsEvent;
use Contao\Doctrine\ORM\EntityHelper;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LayoutOptions
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class LayoutOptions implements EventSubscriberInterface
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
            'avisota.create-layout-type-options' => array(
                array('createLayoutTypeOptions'),
            ),

            'avisota.create-layout-stylesheet-options' => array(
                array('crateLayoutStylesheetOptions'),
            ),

            'avisota.create-layout-options' => array(
                array('createLayoutOptions'),
            ),
        );
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createLayoutTypeOptions(CreateOptionsEvent $event)
    {
        static::getLayoutTypeOptions($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getLayoutTypeOptions($options = array())
    {
        global $container;

        $translator = $container['translator'];

        foreach ($GLOBALS['AVISOTA_MESSAGE_RENDERER'] as $rendererKey) {
            $label = $translator->translate($rendererKey, 'orm_avisota_layout');

            $options[$rendererKey] = $label;
        }

        return $options;
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function crateLayoutStylesheetOptions(CreateOptionsEvent $event)
    {
        $this->getLayoutStylesheetOptions($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getLayoutStylesheetOptions($options = array())
    {
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        if ($options instanceof \ArrayObject) {
            $stylesheets = $options;
        } else {
            $stylesheets = new \ArrayObject();
        }

        $eventDispatcher->dispatch(
            AvisotaMessageEvents::COLLECT_STYLESHEETS,
            new CollectStylesheetsEvent($stylesheets)
        );

        return $stylesheets->getArrayCopy();
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createLayoutOptions(CreateOptionsEvent $event)
    {
        $this->getLayoutOptions($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getLayoutOptions($options = array())
    {
        $layoutRepository = EntityHelper::getRepository('Avisota\Contao:Layout');
        $layouts          = $layoutRepository->findBy(array(), array('title' => 'ASC'));
        /** @var \Avisota\Contao\Entity\Layout $layout */
        foreach ($layouts as $layout) {
            $options[$layout
                ->getTheme()
                ->getTitle()][$layout->getId()] = $layout->getTitle();
        }
        return $options;
    }
}
