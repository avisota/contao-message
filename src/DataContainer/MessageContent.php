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
use Contao\BackendUser;
use Contao\Controller;
use Contao\Doctrine\ORM\DataContainer\General\EntityModel;
use Contao\Doctrine\ORM\EntityAccessor;
use Contao\Input;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetBreadcrumbEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGlobalButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGroupHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ParentViewChildRecordEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use ContaoCommunityAlliance\DcGeneral\DC_General;
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
     * @param GetGroupHeaderEvent $event
     * @SuppressWarnings(PHPMD.Superglobals)
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
     * @param ParentViewChildRecordEvent $event
     *
     * @internal param $array
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

        $context = $entityAccessor->getProperties($content);
        $context['key'] = $key;
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
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function getBreadCrumb(GetBreadcrumbEvent $event)
    {
        $environment   = $event->getEnvironment();
        $dataDefinition = $environment->getDataDefinition();
        $inputProvider = $environment->getInputProvider();
        $translator    = $environment->getTranslator();

        $messageContentParameter = $inputProvider->hasParameter('act') ? 'id' : 'pid';

        if ($dataDefinition->getName() !== 'orm_avisota_message_content'
            || !$inputProvider->hasParameter($messageContentParameter)
        ) {
            return;
        }


        $elements = $event->getElements();

        $messageContentModelId = ModelId::fromSerialized($inputProvider->getParameter($messageContentParameter));
        $dataProvider = $environment->getDataProvider($messageContentModelId->getDataProviderName());
        $model = $dataProvider->fetch($dataProvider->getEmptyConfig()->setId($messageContentModelId->getId()));

        $messageContent = $message = null;
        if ($model->getDataDefinitionName() === 'orm_avisota_message_content') {
            $messageContent = $model->getEntity();
            $message = $messageContent->getMessage();
        }

        if ($model->getDataDefinitionName() === 'orm_avisota_message') {
            $message = $model->getEntity();
        }

        $messageCategory = $message->getCategory();

        $urlNewsletterBuilder = new UrlBuilder();
        $urlNewsletterBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('ref', TL_REFERER_ID);

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/newsletter.png',
            'text' => $translator->translate('avisota_newsletter.0', 'MOD'),
            'url'  => $urlNewsletterBuilder->getUrl()
        );

        global $container;

        $entityManager = $container['doctrine.orm.entityManager'];

        $messageMeta = $entityManager->getClassMetadata(get_class($message));
        $messageCategoryMeta = $entityManager->getClassMetadata(get_class($messageCategory));

        $urlMessageCategoryBuilder = new UrlBuilder();
        $urlMessageCategoryBuilder->setPath('contao/main.php')
            ->setQueryParameter(
                'do',
                $inputProvider->getParameter('do')
            )
            ->setQueryParameter(
                'table',
                $messageMeta->getTableName()
            )
            ->setQueryParameter(
                'pid',
                ModelId::fromValues(
                    $messageCategoryMeta->getTableName(),
                    $messageCategory->getId()
                )->getSerialized()
            )
            ->setQueryParameter('ref', TL_REFERER_ID);

        $elements[] = array(
            'text' => $messageCategory->getTitle(),
            'url' => $urlMessageCategoryBuilder->getUrl()
        );

        if (!$messageContent) {
            $event->setElements($elements);

            return;
        }

        $messageContentMeta = $entityManager->getClassMetadata(get_class($messageContent));

        $urlMessageBuilder = new UrlBuilder();
        $urlMessageBuilder->setPath('contao/main.php')
            ->setQueryParameter(
                'do',
                $inputProvider->getParameter('do')
            )
            ->setQueryParameter(
                'table',
                $messageContentMeta->getTableName()
            )
            ->setQueryParameter(
                'pid',
                ModelId::fromValues(
                    $messageMeta->getTableName(),
                    $message->getId()
                )->getSerialized()
            )
            ->setQueryParameter('ref', TL_REFERER_ID);

        $elements[] = array(
            'text' => $message->getSubject(),
            'url' => $urlMessageBuilder->getUrl()
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
