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

use Contao\Doctrine\ORM\EntityInterface;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetBreadcrumbEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetOperationButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use ContaoCommunityAlliance\DcGeneral\EnvironmentInterface;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Theme
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class Theme implements EventSubscriberInterface
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
                array('getBreadCrumb')
            ),

            GetOperationButtonEvent::NAME => array(
                array('deleteInformation')
            )
        );
    }

    /**
     * Get the bread crumb elements.
     *
     * @param GetBreadcrumbEvent $event This event.
     *
     * @return void
     */
    public function getBreadCrumb(GetBreadcrumbEvent $event)
    {
        $environment    = $event->getEnvironment();
        $dataDefinition = $environment->getDataDefinition();
        $inputProvider  = $environment->getInputProvider();

        if ('avisota_theme' !== $inputProvider->getParameter('do')) {
            return;
        }

        $elements = $event->getElements();

        $rootUrlBuilder = new UrlBuilder();
        $rootUrlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('ref', TL_REFERER_ID);

        $translator = $environment->getTranslator();

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/theme.png',
            'text' => $translator->translate('avisota_theme.0', 'MOD'),
            'url'  => $rootUrlBuilder->getUrl()
        );

        $modelParameter = $inputProvider->hasParameter('act') ? 'id' : 'pid';
        if (false === $inputProvider->hasParameter($modelParameter)) {
            $event->setElements($elements);

            return;
        }

        $modelId = ModelId::fromSerialized($inputProvider->getParameter($modelParameter));
        if ('orm_avisota_theme' !== $modelId->getDataProviderName()) {
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
            'icon' => 'assets/avisota/message/images/theme.png',
            'text' => $entity->getTitle(),
            'url'  => $entityUrlBuilder->getUrl()
        );

        $event->setElements($elements);
    }

    /**
     * Check if the Layouts by this theme is in used by message.
     * If layout in used, return where and can´t delete the theme.
     *
     * @param GetOperationButtonEvent $event The event.
     *
     * @return void
     */
    public function deleteInformation(GetOperationButtonEvent $event)
    {
        $command        = $event->getCommand();
        $environment    = $event->getEnvironment();
        $dataDefinition = $environment->getDataDefinition();

        if ($dataDefinition->getName() !== 'orm_avisota_theme'
            || $command->getName() !== 'delete'
        ) {
            return;
        }

        $themeEntity      = $event->getModel()->getEntity();
        $layoutCollection = $themeEntity->getLayouts();

        $information = '';
        while ($layoutEntity = $layoutCollection->current()) {
            $currentInformation = $this->getLayoutUsedInformation($layoutEntity, $environment);
            if (!$currentInformation) {
                $layoutCollection->next();

                continue;
            }

            $information .= $currentInformation;

            $layoutCollection->next();
        }
        if (!$information) {
            return;
        }

        $translator = $environment->getTranslator();

        $information = $translator->translate('delete.information.theme', 'MCE') . $information;

        $event->setAttributes('onclick="alert(\'' . $information . '\'); Backend.getScrollOffset(); return false;"');
    }

    /**
     * Return layout used information by message.
     *
     * @param EntityInterface      $entity      The Entity.
     *
     * @param EnvironmentInterface $environment The Environment.
     *
     * @return string
     */
    protected function getLayoutUsedInformation(EntityInterface $entity, EnvironmentInterface $environment)
    {
        $dataProvider  = $environment->getDataProvider();
        $entityManager = $dataProvider->getEntityManager();
        $repository    = $entityManager->getRepository('Avisota\Contao:Message');

        $messageResult = $repository->findBy(
            array('layout' => $entity->getId()),
            array('subject' => 'ASC')
        );
        if (count($messageResult) < 1) {
            return '';
        }

        $information = '';
        foreach ($messageResult as $message) {
            $information .= "\\n";
            $information .= $message->getCategory()->getTitle();
            $information .= ' => ';
            $information .= $message->getSubject();
        }

        return $information;
    }
}
