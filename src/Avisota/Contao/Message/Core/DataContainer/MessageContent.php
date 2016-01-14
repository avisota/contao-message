<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\DataContainer;

use Avisota\Contao\Message\Core\Renderer\MessageRendererInterface;
use Contao\Controller;
use Contao\Doctrine\ORM\DataContainer\General\EntityModel;
use Contao\Doctrine\ORM\EntityAccessor;
use Contao\Input;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGroupHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ParentViewChildRecordEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MessageContent
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class MessageContent implements EventSubscriberInterface
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
            GetGroupHeaderEvent::NAME        => array(
                array('getGroupHeader'),
            ),

            ParentViewChildRecordEvent::NAME => array(
                array('parentViewChildRecord'),
            ),
        );
    }

    /**
     * Return the send button
     *
     * @param array
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     *
     * @return string
     */
    public function sendMessageButton($href, $label, $title, $icon, $attributes)
    {
        global $container;

        /** @var Input $input */
        $input = $container['input'];

        $user = \BackendUser::getInstance();

        if (!($user->isAdmin || $user->hasAccess('send', 'avisota_newsletter_permissions'))) {
            $label = $GLOBALS['TL_LANG']['orm_avisota_message']['view_only'][0];
            $title = $GLOBALS['TL_LANG']['orm_avisota_message']['view_only'][1];
        }
        return ' &#160; :: &#160; <a href="' . Controller::addToUrl(
            $href . '&amp;id=' . $input->get('id')
        ) . '" title="' . specialchars($title) . '"' . $attributes . ' class="header_send">' . $label . '</a> ';
    }

    /**
     * @param GetGroupHeaderEvent $event
     */
    public function getGroupHeader(GetGroupHeaderEvent $event)
    {
        if ($event->getModel()->getProviderName() != 'orm_avisota_message_content') {
            return;
        }

        $model = $event->getModel();
        $cell  = $model->getProperty('cell');

        if (isset($GLOBALS['TL_LANG']['orm_avisota_message_content']['cells'][$cell])) {
            $cell = $GLOBALS['TL_LANG']['orm_avisota_message_content']['cells'][$cell];
        }

        $event->setValue($cell);
    }

    /**
     * Add the recipient row.
     *
     * @param ParentViewChildRecordEvent $event
     *
     * @internal param $array
     */
    public function parentViewChildRecord(ParentViewChildRecordEvent $event)
    {
        if ($event->getModel()->getProviderName() != 'orm_avisota_message_content') {
            return;
        }

        /** @var MessageRendererInterface $renderer */
        $renderer = $GLOBALS['container']['avisota.message.renderer'];

        /** @var EntityModel $model */
        $model = $event->getModel();
        /** @var \Avisota\Contao\Entity\MessageContent $content */
        $content = $model->getEntity();

        $key = $content->getInvisible() ? 'unpublished' : 'published';

        try {
            $element = $renderer->renderContent($content);
        } catch (\Exception $exception) {
            $element = sprintf(
                "<span style=\"color:red\">%s</span>",
                $exception->getMessage()
            );
        }

        /** @var EntityAccessor $entityAccessor */
        $entityAccessor = $GLOBALS['container']['doctrine.orm.entityAccessor'];

        $context            = $entityAccessor->getProperties($content);
        $context['key']     = $key;
        $context['element'] = $element;

        $template = new \TwigTemplate('avisota/backend/mce_element', 'html5');
        $event->setHtml($template->parse($context));
    }
}
