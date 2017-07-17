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

use Avisota\Contao\Message\Core\Renderer\MessageRendererInterface;
use Contao\BackendUser;
use Contao\Doctrine\ORM\DataContainer\General\EntityModel;
use Contao\Doctrine\ORM\EntityAccessor;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetBreadcrumbEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGlobalButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGroupHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ParentViewChildRecordEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use ContaoCommunityAlliance\DcGeneral\Event\PreEditModelEvent;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
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
            GetGlobalButtonEvent::NAME => array(
                array('checkPermissionSendMessageButton')
            ),

            GetGroupHeaderEvent::NAME => array(
                array('getGroupHeader'),
            ),

            ParentViewChildRecordEvent::NAME => array(
                array('parentViewChildRecord'),
            ),

            GetBreadcrumbEvent::NAME => array(
                array('getBreadCrumb')
            ),

            PreEditModelEvent::NAME => array(
                array('addCellFromPreviousModel')
            )
        );
    }

    /**
     * Check the permission for send or preview message button.
     *
     * @param GetGlobalButtonEvent $event The event.
     *
     * @return void
     */
    public function checkPermissionSendMessageButton(GetGlobalButtonEvent $event)
    {
        $environment    = $event->getEnvironment();
        $dataDefinition = $environment->getDataDefinition();

        if ($dataDefinition->getName() !== 'orm_avisota_message_content'
            || $event->getKey() !== 'send'
        ) {
            return;
        }

        $user = BackendUser::getInstance();

        if (!$user->isAdmin
            || !$user->hasAccess('send', 'avisota_newsletter_permission')
        ) {
            $event->setHtml('');
        }
    }

    /**
     * Get the group header.
     *
     * @param GetGroupHeaderEvent $event The event.
     *
     * @return void
     */
    public function getGroupHeader(GetGroupHeaderEvent $event)
    {
        if ($event->getModel()->getProviderName() != 'orm_avisota_message_content') {
            return;
        }

        $environment = $event->getEnvironment();
        $translator  = $environment->getTranslator();

        $model = $event->getModel();

        $cell = $translator->translate('cells.' . $model->getProperty('cell'), 'orm_avisota_message_content');

        $event->setValue($cell);
    }

    /**
     * Add the recipient row.
     *
     * @param ParentViewChildRecordEvent $event The event.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
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

    /**
     * Get the bread crumb elements.
     *
     * @param GetBreadcrumbEvent $event This event.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getBreadCrumb(GetBreadcrumbEvent $event)
    {
        $environment          = $event->getEnvironment();
        $dataDefinition       = $environment->getDataDefinition();
        $propertiesDefinition = $dataDefinition->getPropertiesDefinition();
        $inputProvider        = $environment->getInputProvider();

        if ($dataDefinition->getName() !== 'orm_avisota_message_content'
            || !$inputProvider->hasParameter('act')
        ) {
            return;
        }

        if (!$inputProvider->getParameter('id')) {
            return;
        }

        $elements = $event->getElements();

        $modelId      = ModelId::fromSerialized($inputProvider->getParameter('id'));
        $dataProvider = $environment->getDataProvider($modelId->getDataProviderName());
        $repository   = $dataProvider->getEntityRepository();

        $contentEntity = $repository->findOneBy(array('id' => $modelId->getId()));
        if (null === $contentEntity) {
            $event->setElements($elements);

            return;
        }

        $messageEntity  = $contentEntity->getMessage();
        $categoryEntity = $messageEntity->getCategory();

        $entityManager = $GLOBALS['container']['doctrine.orm.entityManager'];

        $categoryMeta = $entityManager->getClassMetadata(get_class($categoryEntity));
        $messageMeta  = $entityManager->getClassMetadata(get_class($messageEntity));

        $categoryModelId    = ModelId::fromValues($categoryMeta->getTableName(), $categoryEntity->getId());
        $categoryUrlBuilder = new UrlBuilder();
        $categoryUrlBuilder
            ->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('table', $messageMeta->getTableName())
            ->setQueryParameter('pid', $categoryModelId->getSerialized())
            ->setQueryParameter('ref', TL_REFERER_ID);

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/newsletter.png',
            'text' => $categoryEntity->getTitle(),
            'url'  => $categoryUrlBuilder->getUrl()
        );

        $messageModelId    = ModelId::fromValues($messageMeta->getTableName(), $messageEntity->getId());
        $messageUrlBuilder = new UrlBuilder();
        $messageUrlBuilder
            ->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('table', $dataDefinition->getName())
            ->setQueryParameter('pid', $messageModelId->getSerialized())
            ->setQueryParameter('ref', TL_REFERER_ID);

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/newsletter.png',
            'text' => $messageEntity->getSubject(),
            'url'  => $messageUrlBuilder->getUrl()
        );

        /*if ('after' === $modelParameter) {
            $event->setElements($elements);

            return;
        }*/

        $contentUrlBuilder = new UrlBuilder();
        $contentUrlBuilder
            ->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('table', $inputProvider->getParameter('table'))
            ->setQueryParameter('act', $inputProvider->getParameter('act'))
            ->setQueryParameter('id', $inputProvider->getParameter('id'))
            ->setQueryParameter('pid', $inputProvider->getParameter('pid'))
            ->setQueryParameter('ref', TL_REFERER_ID);

        $cellProperty = $propertiesDefinition->getProperty('cell');
        $cellLanguage = $cellProperty->getExtra()['reference'];
        $typeProperty = $propertiesDefinition->getProperty('type');
        $typeLanguage = $typeProperty->getExtra()['reference'];

        $cellName = $cellLanguage[$contentEntity->getCell()];
        $typeName =
            is_string($typeLanguage[$contentEntity->getType()]) ?: $typeLanguage[$contentEntity->getType()][0];

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/newsletter.png',
            'text' =>
                $typeName . "<span style='font-weight: bold; color: #b3b3b3; padding-left: 3px;'>[$cellName]</span>",
            'url'  => $contentUrlBuilder->getUrl()
        );

        $event->setElements($elements);
    }

    /**
     * By paste after model. Add the cell information from the parent model.
     *
     * @param PreEditModelEvent $event The event.
     *
     * @return void
     */
    public function addCellFromPreviousModel(PreEditModelEvent $event)
    {
        $environment    = $event->getEnvironment();
        $dataDefinition = $environment->getDataDefinition();
        $inputProvider  = $environment->getInputProvider();

        if ($dataDefinition->getName() !== 'orm_avisota_message_content'
            || !in_array($inputProvider->getParameter('act'), array('paste', 'create'))
            || !$inputProvider->hasParameter('after')
        ) {
            return;
        }

        $entity       = $event->getModel()->getEntity();
        $dataProvider = $environment->getDataProvider();
        $repository   = $dataProvider->getEntityRepository();

        $modelId = ModelId::fromSerialized($inputProvider->getParameter('after'));

        $parentModel = $repository->find($modelId->getId());
        if (!$parentModel) {
            return;
        }

        $entity->setCell($parentModel->getCell());
    }
}
