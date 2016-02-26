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
use Contao\Doctrine\ORM\EntityHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MessageCategoryOptions
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class MessageCategoryOptions implements EventSubscriberInterface
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
            'avisota.create-message-category-options'    => array(
                array('createMessageCategoryOptions'),
            ),
        );
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createMessageCategoryOptions(CreateOptionsEvent $event)
    {
        $this->getMessageCategoryOptions($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getMessageCategoryOptions($options = array())
    {
        $messageCategoryRepository = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
        $messageCategories         = $messageCategoryRepository->findBy(array(), array('title' => 'ASC'));
        /** @var \Avisota\Contao\Entity\MessageCategory $messageCategory */
        foreach ($messageCategories as $messageCategory) {
            $options[$messageCategory->getId()] = $messageCategory->getTitle();
        }
        return $options;
    }
}
