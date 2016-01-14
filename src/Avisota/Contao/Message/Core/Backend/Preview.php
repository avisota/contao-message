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

namespace Avisota\Contao\Message\Core\Backend;

use Avisota\Contao\Core\Message\Renderer;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Controller\RedirectEvent;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use ContaoCommunityAlliance\DcGeneral\DcGeneralEvents;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use ContaoCommunityAlliance\DcGeneral\Event\ActionEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Preview
 *
 * @package Avisota\Contao\Message\Core\Backend
 */
class Preview implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            DcGeneralEvents::ACTION => array(
                array('handleAction'),
            ),
        );
    }

    /**
     * @param ActionEvent $event
     */
    public function handleAction(ActionEvent $event)
    {
        if (
            !$event->getResponse()
            && $event->getEnvironment()->getDataDefinition()->getName() == 'orm_avisota_message'
            && $event->getAction()->getName() == 'preview'
        ) {
            $event->setResponse($this->renderPreviewView($event->getEnvironment()));
        }
    }

    /**
     * @param EnvironmentInterface $environment
     *
     * @return string
     * @internal param DC_General $dc
     */
    public function renderPreviewView(EnvironmentInterface $environment)
    {
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        $eventDispatcher->dispatch(
            ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE,
            new LoadLanguageFileEvent('avisota_message_preview')
        );
        $eventDispatcher->dispatch(
            ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE,
            new LoadLanguageFileEvent('orm_avisota_message')
        );

        $messageRepository = EntityHelper::getRepository('Avisota\Contao:Message');

        $messageId = ModelId::fromSerialized(\Input::get('id') ? \Input::get('id') : \Input::get('pid'));
        $message   = $messageRepository->find($messageId->getId());

        if (!$message) {
            $eventDispatcher->dispatch(
                ContaoEvents::CONTROLLER_REDIRECT,
                new RedirectEvent(
                    preg_replace(
                        '#&(act=preview|id=[a-f0-9\-]+)#',
                        '',
                        \Environment::get('request')
                    )
                )
            );
        }

        $modules = new \StringBuilder();
        /** @var \Avisota\Contao\Message\Core\Send\SendModuleInterface $module */
        foreach ($GLOBALS['AVISOTA_SEND_MODULE'] as $className) {
            $class  = new \ReflectionClass($className);
            $module = $class->newInstance();
            $modules->append($module->run($message));
        }

        $context = array(
            'message' => $message,
            'modules' => $modules,
        );

        $template = new \TwigTemplate('avisota/backend/preview', 'html5');
        return $template->parse($context);
    }
}
