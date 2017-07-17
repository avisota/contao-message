<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2017 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2017
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\DataContainer;

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetBreadcrumbEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class MessageCategory
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class MessageCategory implements EventSubscriberInterface
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
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            GetBreadcrumbEvent::NAME => array(
                array('getBreadCrumb', 2)
            )
        );
    }

    /**
     * Get the bread crumb elements.
     *
     * @param GetBreadcrumbEvent $event The event.
     *
     * @return void
     */
    public function getBreadCrumb(GetBreadcrumbEvent $event)
    {
        $environment    = $event->getEnvironment();
        $dataDefinition = $environment->getDataDefinition();
        $inputProvider  = $environment->getInputProvider();
        $translator     = $environment->getTranslator();

        if (false === strpos($dataDefinition->getName(), 'orm_avisota_message')) {
            return;
        }

        $elements = $event->getElements();

        $rootUrlBuilder = new UrlBuilder();
        $rootUrlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('ref', TL_REFERER_ID);

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/newsletter.png',
            'text' => $translator->translate('avisota_newsletter.0', 'MOD'),
            'url'  => $rootUrlBuilder->getUrl()
        );

        $modelParameter = $inputProvider->hasParameter('act') ? 'id' : 'pid';
        if (false === $inputProvider->hasParameter($modelParameter)
            || !$inputProvider->getParameter($modelParameter)
        ) {
            $event->setElements($elements);

            return;
        }

        $modelId = ModelId::fromSerialized($inputProvider->getParameter($modelParameter));
        if ('orm_avisota_message_category' !== $modelId->getDataProviderName()) {
            $event->setElements($elements);

            return;
        }

        $dataProvider = $environment->getDataProvider($modelId->getDataProviderName());
        $repository   = $dataProvider->getEntityRepository();

        $entity = $repository->findOneBy(array('id' => $modelId->getId()));

        $entityUrlBuilder = new UrlBuilder();
        $entityUrlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter($modelParameter, $inputProvider->getParameter($modelParameter))
            ->setQueryParameter('ref', TL_REFERER_ID);

        if ('id' === $modelParameter) {
            $entityUrlBuilder->setQueryParameter('act', $inputProvider->getParameter('act'));
        }

        if ('pid' === $modelParameter) {
            $entityUrlBuilder->setQueryParameter('table', $dataDefinition->getName());
        }

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/newsletter.png',
            'text' => $entity->getTitle(),
            'url'  => $entityUrlBuilder->getUrl()
        );

        $event->setElements($elements);
    }
}
