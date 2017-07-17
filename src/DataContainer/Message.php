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

use Avisota\Contao\Entity\RecipientSource;
use Contao\Doctrine\ORM\DataContainer\General\EntityDataProvider;
use Contao\Doctrine\ORM\DataContainer\General\EntityModel;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Backend\GetThemeEvent;
use ContaoCommunityAlliance\Contao\Bindings\Events\Date\ParseDateEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\ContaoWidgetManager;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetBreadcrumbEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGroupHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ParentViewChildRecordEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use ContaoCommunityAlliance\DcGeneral\DcGeneralEvents;
use ContaoCommunityAlliance\DcGeneral\Event\ActionEvent;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Message
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Message implements EventSubscriberInterface
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
            GetGroupHeaderEvent::NAME => array(
                array('getGroupHeader'),
            ),

            ParentViewChildRecordEvent::NAME => array(
                array('parentViewChildRecord'),
            ),

            DcGeneralEvents::ACTION => array(
                array('handleActionForSelectri'),
            ),

            GetBreadcrumbEvent::NAME => array(
                array('getBreadCrumb', 1)
            )
        );
    }

    /**
     * @param $add
     * @param $dc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ShortVariable)
     * @SuppressWarnings(PHPMD.LongVariable)
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function addHeader($add, $dc)
    {
        // TODO refactore for DCG
        return;

        $newsletterCategoryRepository = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
        /** @var \Avisota\Contao\Entity\MessageCategory $newsletterCategory */
        $newsletterCategory = $newsletterCategoryRepository->find($dc->id);

        $key = $GLOBALS['TL_LANG']['orm_avisota_message_category']['recipients'][0];
        if (!$newsletterCategory->getBoilerplates() && $newsletterCategory->getRecipientsMode() != 'byMessage') {
            $fallback = $newsletterCategory->getRecipientsMode() == 'byMessageOrCategory';

            /** @var RecipientSource $recipientSource */
            $recipientSource = $newsletterCategory->getRecipients();
            if ($recipientSource) {
                $add[$key] = sprintf(
                    '<a href="contao/main.php?do=avisota_recipient_source&act=edit&id=%d">%s</a>%s',
                    $recipientSource->getId(),
                    $recipientSource->getTitle(),
                    $fallback ? ' ' . $GLOBALS['TL_LANG']['orm_avisota_message']['fallback'] : ''
                );
            } else {
                unset($add[$key]);
            }
        } else {
            unset($add[$key]);
        }

        $key = $GLOBALS['TL_LANG']['orm_avisota_message_category']['layout'][0];
        if (!$newsletterCategory->getBoilerplates() && $newsletterCategory->getLayoutMode() != 'byMessage') {
            $add[$key] = $newsletterCategory
                ->getLayout()
                ->getTitle();
            if ($newsletterCategory->getLayoutMode() == 'byMessageOrCategory') {
                $add[$key] .= ' ' . $GLOBALS['TL_LANG']['orm_avisota_message']['fallback'];
            }
        } else {
            unset($add[$key]);
        }

        $key = $GLOBALS['TL_LANG']['orm_avisota_message_category']['queue'][0];
        if (!$newsletterCategory->getBoilerplates() && $newsletterCategory->getQueueMode() != 'byMessage') {
            $add[$key] = $newsletterCategory
                ->getQueue()
                ->getTitle();
            if ($newsletterCategory->getQueueMode() == 'byMessageOrCategory') {
                $add[$key] .= ' ' . $GLOBALS['TL_LANG']['orm_avisota_message']['fallback'];
            }
        } else {
            unset($add[$key]);
        }

        return $add;
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
        if ($event->getModel()->getProviderName() != 'orm_avisota_message') {
            return;
        }

        $environment = $event->getEnvironment();
        $translator  = $environment->getTranslator();

        /** @var EntityModel $model */
        $model = $event->getModel();
        /** @var \Avisota\Contao\Entity\Message $message */
        $message = $model->getEntity();

        if ($message->getCategory()->getBoilerplates()) {
            $language = $translator->translate($message->getLanguage(), 'LNG');

            $event->setValue($language);
        } else {
            if ($model->getProperty('sendOn')) {
                $parseDateEvent = new ParseDateEvent($message->getSendOn()->getTimestamp(), 'F Y');

                /** @var EventDispatcher $eventDispatcher */
                $eventDispatcher = $GLOBALS['container']['event-dispatcher'];
                $eventDispatcher->dispatch(ContaoEvents::DATE_PARSE, $parseDateEvent);

                $event->setValue($parseDateEvent->getResult());
            } else {
                $event->setValue($translator->translate('notSend', 'orm_avisota_message'));
            }
        }
    }

    /**
     * Add the recipient row.
     *
     * @param ParentViewChildRecordEvent $event The event.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function parentViewChildRecord(ParentViewChildRecordEvent $event)
    {
        if ($event->getModel()->getProviderName() != 'orm_avisota_message') {
            return;
        }

        $environment = $event->getEnvironment();
        $translator  = $environment->getTranslator();

        /** @var EntityModel $model */
        $model = $event->getModel();
        /** @var \Avisota\Contao\Entity\Message $message */
        $message = $model->getEntity();

        if ($message->getCategory()->getBoilerplates()) {
            $language = $translator->translate($message->getLanguage(), 'LNG');

            $label = sprintf(
                '%s [%s]',
                $message->getSubject(),
                $language
            );

            $event->setHtml($label);
        } else {
            $icon = $model->getProperty('sendOn') ? 'visible' : 'invisible';

            $label = $model->getProperty('subject');

            if ($message->getSendOn()) {
                $parseDateEvent = new ParseDateEvent(
                    $message->getSendOn()->getTimestamp(),
                    $GLOBALS['TL_CONFIG']['datimFormat']
                );

                /** @var EventDispatcher $eventDispatcher */
                $eventDispatcher = $GLOBALS['container']['event-dispatcher'];
                $eventDispatcher->dispatch(ContaoEvents::DATE_PARSE, $parseDateEvent);

                $sended = sprintf(
                    $translator->translate('sended', 'orm_avisota_message'),
                    $parseDateEvent->getResult()
                );
                $label  .= ' <span style="color:#b3b3b3; padding-left:3px;">(' . $sended . ')</span>';
            }

            /** @var EventDispatcher $eventDispatcher */
            $eventDispatcher = $GLOBALS['container']['event-dispatcher'];
            $getThemeEvent   = new GetThemeEvent();
            $eventDispatcher->dispatch(ContaoEvents::BACKEND_GET_THEME, $getThemeEvent);

            $event->setHtml(
                sprintf(
                    '<div class="list_icon" style="background-image:url(\'system/themes/%s/images/%s.gif\');">%s</div>',
                    $getThemeEvent->getTheme(),
                    $icon,
                    $label
                )
            );
        }
    }

    /**
     * Handle action for selectri.
     *
     * @param ActionEvent $event The event.
     *
     * @return void
     */
    public function handleActionForSelectri(ActionEvent $event)
    {
        if (!\Input::get('striAction')
            || !\Input::get('striID')
            || (\Input::get('striID') === ''
                && \Input::get('striAction') != 'levels')
        ) {
            return;
        }

        $environment    = $event->getEnvironment();
        $dataDefinition = $environment->getDataDefinition();
        $inputProvider  = $environment->getInputProvider();

        $providerInformation =
            $dataDefinition->getDataProviderDefinition()->getInformation($dataDefinition->getName());
        $dataProviderClass   = $providerInformation->getClassName();
        /** @var EntityDataProvider $dataProvider */
        $dataProvider = new $dataProviderClass();
        $dataProvider->setBaseConfig(array('source' => $dataDefinition->getName()));
        $repository = $dataProvider->getEntityRepository();

        $messageContent = null;
        if ($inputProvider->hasParameter('id')) {
            $modelId        = ModelId::fromSerialized(\Input::get('id'));
            $messageContent = $repository->find($modelId->getId());
        }

        if (!$messageContent) {
            $contentModel = $dataProvider->getEmptyModel();

            foreach (array_keys($contentModel->getPropertiesAsArray()) as $property) {
                if (!$inputProvider->hasValue($property)) {
                    continue;
                }

                $contentModel->setProperty($property, $inputProvider->getValue($property));
            }

            $messageContent = $contentModel->getEntity();
        }

        $model = new EntityModel($messageContent);

        $selectriProperty = null;
        foreach ($dataDefinition->getPalettesDefinition()->getPalettes() as $palette) {
            foreach ($palette->getLegends() as $legend) {
                foreach ($legend->getProperties() as $legendProperty) {
                    $property = $dataDefinition->getPropertiesDefinition()->getProperty($legendProperty->getName());
                    if (!in_array($property->getWidgetType(), array('selectri', 'avisotaSelectriWithItems'))
                        || $legendProperty->getName() != \Input::get('striID')
                    ) {
                        continue;
                    }

                    $model->getEntity()->setType($palette->getName());
                    $selectriProperty = $property;
                }
            }
        }

        if (!$selectriProperty) {
            return;
        }

        $widgetManager = new ContaoWidgetManager($environment, $model);
        $widgetManager->renderWidget($selectriProperty->getName());
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

        if (false === strpos($dataDefinition->getName(), 'orm_avisota_message')) {
            return;
        }

        $modelParameter = $inputProvider->hasParameter('act') ? 'id' : 'pid';
        if ('create' === $inputProvider->getParameter('act')) {
            $inputProvider
                ->setParameter($modelParameter, ModelId::fromValues('orm_avisota_message', 0)->getSerialized());
        }
        if (false === $inputProvider->hasParameter($modelParameter)
            || !$inputProvider->getParameter($modelParameter)
        ) {
            return;
        }

        $elements = $event->getElements();

        $modelId = ModelId::fromSerialized($inputProvider->getParameter($modelParameter));
        if ('orm_avisota_message' !== $modelId->getDataProviderName()) {
            $event->setElements($elements);

            return;
        }

        $dataProvider = $environment->getDataProvider($modelId->getDataProviderName());
        $repository   = $dataProvider->getEntityRepository();

        $parentDataDefinition = $environment->getParentDataDefinition();
        if (null === $parentDataDefinition) {
            $event->setElements($elements);

            return;
        }

        $messageEntity = $repository->findOneBy(array('id' => $modelId->getId()));
        if ('create' === $inputProvider->getParameter('act')) {
            $parentDataProvider = $environment->getDataProvider($parentDataDefinition->getName());
            $parentRepository   = $parentDataProvider->getEntityRepository();

            $parentModelId = ModelId::fromSerialized($inputProvider->getParameter('pid'));
            $categoryEntity = $parentRepository->findOneBy(array('id' => $parentModelId->getId()));
        }

        if ('create' !== $inputProvider->getParameter('act')) {
            $categoryEntity = $messageEntity->getCategory();
        }

        $entityManager = $GLOBALS['container']['doctrine.orm.entityManager'];

        $categoryMeta = $entityManager->getClassMetadata(get_class($categoryEntity));

        $parentTableParameter =
            ('id' === $modelParameter) ? $dataDefinition->getName() : $parentDataDefinition->getName();
        $parentPidParameter   =
            ('id' === $modelParameter) ? $inputProvider->getParameter('pid')
                : ModelId::fromValues($categoryMeta->getTableName(), $categoryEntity->getId())->getSerialized();

        $parentUrlBuilder = new UrlBuilder();
        $parentUrlBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('table', $parentTableParameter)
            ->setQueryParameter('pid', $parentPidParameter)
            ->setQueryParameter('ref', TL_REFERER_ID);

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/newsletter.png',
            'text' => $categoryEntity->getTitle(),
            'url'  => $parentUrlBuilder->getUrl()
        );

        if (null === $messageEntity) {
            $event->setElements($elements);

            return;
        }

        $entityUrlBuilder = new UrlBuilder();
        $entityUrlBuilder
            ->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('table', $dataDefinition->getName())
            ->setQueryParameter('pid', $inputProvider->getParameter('pid'))
            ->setQueryParameter('ref', TL_REFERER_ID);

        if ('id' === $modelParameter) {
            $entityUrlBuilder
                ->setQueryParameter('act', $inputProvider->getParameter('act'))
                ->setQueryParameter('id', $inputProvider->getParameter('id'));
        }

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/newsletter.png',
            'text' => $messageEntity->getSubject(),
            'url'  => $entityUrlBuilder->getUrl()
        );

        $event->setElements($elements);
    }
}
