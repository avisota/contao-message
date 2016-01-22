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
 * Class MessageOptions
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class MessageOptions implements EventSubscriberInterface
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
            'avisota.create-boilerplate-message-options' => array(
                array('createBoilerplateMessages'),
            ),

            'avisota.create-non-boilerplate-message-options' => array(
                array('createNonBoilerplateMessages'),
            ),

            'avisota.create-message-options' => array(
                array('createMessageOptions'),
            ),
        );
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createBoilerplateMessages(CreateOptionsEvent $event)
    {
        $this->getBoilerplateMessages($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getBoilerplateMessages($options = array())
    {
        $entityManager = EntityHelper::getEntityManager();
        $queryBuilder  = $entityManager->createQueryBuilder();

        /** @var Message[] $messages */
        $messages = $queryBuilder
            ->select('m')
            ->from('Avisota\Contao:Message', 'm')
            ->innerJoin('Avisota\Contao:MessageCategory', 'c', 'c.id=m.category')
            ->where('c.boilerplates=:boilerplate')
            ->orderBy('c.title', 'ASC')
            ->addOrderBy('m.subject', 'ASC')
            ->setParameter(':boilerplate', true)
            ->getQuery()
            ->getResult();

        foreach ($messages as $message) {
            $options[$message->getCategory()
                ->getTitle()][$message->getId()] = $message->getSubject();
        }

        return $options;
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createNonBoilerplateMessages(CreateOptionsEvent $event)
    {
        $this->getNonBoilerplateMessages($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getNonBoilerplateMessages($options = array())
    {
        $entityManager = EntityHelper::getEntityManager();
        $queryBuilder  = $entityManager->createQueryBuilder();

        /** @var Message[] $messages */
        $messages = $queryBuilder
            ->select('m')
            ->from('Avisota\Contao:Message', 'm')
            ->innerJoin('Avisota\Contao:MessageCategory', 'c', 'c.id=m.category')
            ->where('c.boilerplates=:boilerplate')
            ->orderBy('c.title', 'ASC')
            ->addOrderBy('m.subject', 'ASC')
            ->setParameter(':boilerplate', false)
            ->getQuery()
            ->getResult();

        foreach ($messages as $message) {
            $options[$message->getCategory()
                ->getTitle()][$message->getId()] = $message->getSubject();
        }

        return $options;
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createMessageOptions(CreateOptionsEvent $event)
    {
        if (!$event->isDefaultPrevented()) {
            $this->getMessageOptions($event->getOptions());
        }
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getMessageOptions($options = array())
    {
        $messageRepository = EntityHelper::getRepository('Avisota\Contao:Message');
        $messages          = $messageRepository->findBy(array(), array('sendOn' => 'DESC'));

        /** @var \Avisota\Contao\Entity\Message $message */
        foreach ($messages as $message) {
            $buffer = sprintf(
                '[%s] %s',
                $message->getSendOn() ? $message
                    ->getSendOn()
                    ->format($GLOBALS['TL_CONFIG']['datimFormat']) : '-',
                $message->getSubject()
            );

            $options[$message
                ->getCategory()
                ->getTitle()][$message->getId()] = $buffer;
        }

        return $options;
    }
}
