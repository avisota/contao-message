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

use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetBreadcrumbEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetOperationButtonEvent;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;
use ContaoCommunityAlliance\DcGeneral\DataDefinition\Definition\View\Command;
use ContaoCommunityAlliance\DcGeneral\DC_General;
use ContaoCommunityAlliance\UrlBuilder\UrlBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Layout
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class Layout implements EventSubscriberInterface
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
        $environment   = $event->getEnvironment();
        $inputProvider = $environment->getInputProvider();

        if (!$inputProvider->hasParameter('id')) {
            return;
        }

        $layoutModelId = ModelId::fromSerialized($inputProvider->getParameter('id'));
        if ($layoutModelId->getDataProviderName() !== 'orm_avisota_layout') {
            return;
        }

        $elements = $event->getElements();

        $urlThemeBuilder = new UrlBuilder();
        $urlThemeBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('ref', TL_REFERER_ID);

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/theme.png',
            'text' => $GLOBALS['TL_LANG']['MOD']['avisota_theme'][0],
            'url'  => $urlThemeBuilder->getUrl()
        );

        $urlLayoutBuilder = new UrlBuilder();
        $urlLayoutBuilder->setPath('contao/main.php')
            ->setQueryParameter('do', $inputProvider->getParameter('do'))
            ->setQueryParameter('table', $inputProvider->getParameter('table'))
            ->setQueryParameter('pid', $inputProvider->getParameter('pid'))
            ->setQueryParameter('ref', TL_REFERER_ID);

        $themeModelId = ModelId::fromSerialized($inputProvider->getParameter('pid'));
        $dataProvider = $environment->getDataProvider($themeModelId->getDataProviderName());
        $model        = $dataProvider->fetch($dataProvider->getEmptyConfig()->setId($themeModelId->getId()));
        $entity       = $model->getEntity();

        $elements[] = array(
            'icon' => 'assets/avisota/message/images/layout.png',
            'text' => $entity->getTitle(),
            'url'  => $urlLayoutBuilder->getUrl()
        );

        $event->setElements($elements);
    }

    /**
     * Check if the Layout is in used by message.
     * If this in used, return where and can´t delete it.
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

        if ($dataDefinition->getName() !== 'orm_avisota_layout'
            || $command->getName() !== 'delete'
        ) {
            return;
        }

        $entity        = $event->getModel()->getEntity();
        $dataProvider  = $environment->getDataProvider();
        $entityManager = $dataProvider->getEntityManager();
        $repository    = $entityManager->getRepository('Avisota\Contao:Message');

        $messageResult = $repository->findBy(
            array('layout' => $entity->getId()),
            array('subject' => 'ASC')
        );
        if (count($messageResult) < 1) {
            return;
        }

        $translator = $environment->getTranslator();

        $information = $translator->translate('delete.information.layout', 'MCE');
        foreach ($messageResult as $message) {
            $information .= "\\n";
            $information .= $message->getCategory()->getTitle();
            $information .= ' => ';
            $information .= $message->getSubject();
        }

        $event->setAttributes('onclick="alert(\'' . $information . '\'); Backend.getScrollOffset(); return false;"');
    }

    /**
     * Add the type of content element
     *
     * @param array
     *
     * @return string
     */
    public static function addElement($contentData)
    {
        return sprintf(
            '<div>%s</div>' . "\n",
            $contentData['title']
        );
    }

    /**
     * @param DC_General|\Avisota\Contao\Entity\Layout $layout
     *
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getDefaultSelectedCellContentElements($layout)
    {
        $value = array();

        list($group, $mailChimpTemplate) = explode(':', $layout->getMailchimpTemplate());
        if (isset($GLOBALS['AVISOTA_MAILCHIMP_TEMPLATE'][$group][$mailChimpTemplate])) {
            $config = $GLOBALS['AVISOTA_MAILCHIMP_TEMPLATE'][$group][$mailChimpTemplate];

            if (isset($config['cells'])) {
                foreach ($config['cells'] as $cellName => $cellConfig) {
                    if (isset($cellConfig['preferredElements'])) {
                        foreach ($cellConfig['preferredElements'] as $elementName) {
                            $value[] = $cellName . ':' . $elementName;
                        }
                    } else {
                        foreach ($GLOBALS['TL_MCE'] as $elements) {
                            foreach ($elements as $elementType) {
                                $value[] = $cellName . ':' . $elementType;
                            }
                        }
                    }
                }
            }
        }

        return $value;
    }

    /**
     * @param                               $value
     * @param \Avisota\Contao\Entity\Layout $layout
     *
     * @return array
     */
    public static function getterCallbackAllowedCellContents($value, \Avisota\Contao\Entity\Layout $layout)
    {
        if ($value === null) {
            return static::getDefaultSelectedCellContentElements($layout);
        }

        return $value;
    }

    /**
     * @param                               $value
     * @param \Avisota\Contao\Entity\Layout $layout
     *
     * @return null
     */
    public static function setterCallbackAllowedCellContents($value, \Avisota\Contao\Entity\Layout $layout)
    {
        if (!is_array($value)) {
            $value = null;
        } else {
            if ($value !== null) {
                $defaultValue = static::getDefaultSelectedCellContentElements($layout);

                $diffLeft  = array_diff($value, $defaultValue);
                $diffRight = array_diff($defaultValue, $value);

                if (!(count($diffLeft) + count($diffRight))) {
                    $value = null;
                }
            }
        }

        return $value;
    }
}
