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

namespace Avisota\Contao\Message\Core;

use Avisota\Contao\Entity\Layout;
use Avisota\Contao\Entity\Message;
use Avisota\Contao\Entity\MessageCategory;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageEvent;
use Avisota\Contao\Message\Core\Template\MutablePreRenderedMessageTemplate;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\Contao\Events\CreateOptions\CreateOptionsEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetSelectModeButtonsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EventsSubscriber
 *
 * @package Avisota\Contao\Message\Core
 */
class EventsSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            MessageEvents::CREATE_MESSAGE_CATEGORY_OPTIONS => array(
                array('createMessageCategoryOptions'),
            ),

            MessageEvents::CREATE_MESSAGE_OPTIONS => array(
                array('createMessageOptions'),
            ),

            MessageEvents::CREATE_BOILERPLATE_MESSAGE_OPTIONS => array(
                array('createBoilerplateMessageOptions'),
            ),

            MessageEvents::CREATE_NON_BOILERPLATE_MESSAGE_OPTIONS => array(
                array('createNonBoilerplateMessageOptions'),
            ),

            MessageEvents::CREATE_MESSAGE_LAYOUT_OPTIONS => array(
                array('creatMessageLayoutOptions'),
            ),

            AvisotaMessageEvents::RENDER_MESSAGE => array(
                array('renderMessage'),
            ),
            

            GetSelectModeButtonsEvent::NAME => array(
                array('deactivateButtonsForEditAll'),
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
     * @return array|\ArrayAccess
     */
    public function getMessageCategoryOptions($options = array())
    {
        if (!is_array($options) && !$options instanceof \ArrayAccess) {
            $options = array();
        }

        $repository   = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
        $queryBuilder = $repository->createQueryBuilder('mc');
        $queryBuilder
            ->select('mc')
            ->orderBy('mc.title');
        $query = $queryBuilder->getQuery();
        /** @var MessageCategory[] $messageCategories */
        $messageCategories = $query->getResult();

        foreach ($messageCategories as $messageCategory) {
            $options[$messageCategory->getId()] = $messageCategory->getTitle();
        }

        return $options;
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createMessageOptions(CreateOptionsEvent $event)
    {
        $this->getMessageOptions($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getMessageOptions($options = array())
    {
        if (!is_array($options) && !$options instanceof \ArrayAccess) {
            $options = array();
        }

        $repository   = EntityHelper::getRepository('Avisota\Contao:Message');
        $queryBuilder = $repository->createQueryBuilder('m');
        $queryBuilder
            ->select('m')
            ->innerJoin('m.category', 'c')
            ->orderBy('c.title')
            ->addOrderBy('m.subject');
        $query    = $queryBuilder->getQuery();
        $messages = $query->getResult();

        $this->fillOptions($options, $messages);

        return $options;
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createBoilerplateMessageOptions(CreateOptionsEvent $event)
    {
        $this->getBoilerplateMessageOptions($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getBoilerplateMessageOptions($options = array())
    {
        if (!is_array($options) && !$options instanceof \ArrayAccess) {
            $options = array();
        }

        $repository   = EntityHelper::getRepository('Avisota\Contao:Message');
        $queryBuilder = $repository->createQueryBuilder('m');
        $expr         = $queryBuilder->expr();
        $queryBuilder
            ->select('m')
            ->innerJoin('m.category', 'c')
            ->where($expr->eq('c.boilerplates', ':boilerplates'))
            ->setParameter('boilerplates', true)
            ->orderBy('c.title')
            ->addOrderBy('m.subject');
        $query    = $queryBuilder->getQuery();
        $messages = $query->getResult();

        $this->fillOptions($options, $messages);

        return $options;
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function createNonBoilerplateMessageOptions(CreateOptionsEvent $event)
    {
        $this->getNonBoilerplateMessageOptions($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getNonBoilerplateMessageOptions($options = array())
    {
        if (!is_array($options) && !$options instanceof \ArrayAccess) {
            $options = array();
        }

        $repository   = EntityHelper::getRepository('Avisota\Contao:Message');
        $queryBuilder = $repository->createQueryBuilder('m');
        $expr         = $queryBuilder->expr();
        $queryBuilder
            ->select('m')
            ->innerJoin('m.category', 'c')
            ->where($expr->eq('c.boilerplates', ':boilerplates'))
            ->setParameter('boilerplates', false)
            ->orderBy('c.title')
            ->addOrderBy('m.subject');
        $query    = $queryBuilder->getQuery();
        $messages = $query->getResult();

        $this->fillOptions($options, $messages);

        return $options;
    }

    /**
     * Fill the options array with the messages.
     *
     * @param array|\ArrayAccess $options
     * @param array|Message[]    $messages
     */
    protected function fillOptions($options, array $messages)
    {
        foreach ($messages as $message) {
            $category = $message->getCategory();

            $options[$category->getTitle()][$message->getId()] = $message->getSubject();
        }
    }

    /**
     * @param CreateOptionsEvent $event
     */
    public function creatMessageLayoutOptions(CreateOptionsEvent $event)
    {
        $this->getMessageLayoutOptions($event->getOptions());
    }

    /**
     * @param array $options
     *
     * @return array|\ArrayAccess
     */
    public function getMessageLayoutOptions($options = array())
    {
        if (!is_array($options) && !$options instanceof \ArrayAccess) {
            $options = array();
        }

        $repository   = EntityHelper::getRepository('Avisota\Contao:Layout');
        $queryBuilder = $repository->createQueryBuilder('l');
        $queryBuilder
            ->select('l')
            ->orderBy('l.title');
        $query = $queryBuilder->getQuery();
        /** @var Layout[] $layouts */
        $layouts = $query->getResult();

        foreach ($layouts as $layout) {
            $options[$layout->getId()] = $layout->getTitle();
        }

        return $options;
    }

    /**
     * @param RenderMessageEvent $event
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function renderMessage(RenderMessageEvent $event)
    {
        if ($event->getPreRenderedMessageTemplate()) {
            return;
        }

        global $container;

        /** @var \Avisota\Contao\Message\Core\Renderer\MessageRendererInterface $renderer */
        $renderer = $container['avisota.message.renderer'];

        $content = $renderer->renderCell($event->getMessage(), 'center', $event->getLayout());

        $preRenderedMessageTemplate = new MutablePreRenderedMessageTemplate(
            $event->getMessage(),
            implode(PHP_EOL, $content)
        );

        $event->setPreRenderedMessageTemplate($preRenderedMessageTemplate);
    }

    /**
     * @param GetSelectModeButtonsEvent $event
     */
    public function deactivateButtonsForEditAll(GetSelectModeButtonsEvent $event)
    {
        if ($event->getEnvironment()->getInputProvider()->getParameter('act') !== 'select'
            || !in_array(
                $event->getEnvironment()->getDataDefinition()->getName(),
                array(
                    'orm_avisota_layout',
                    'orm_avisota_message_category',
                    'orm_avisota_theme',
                )
            )
        ) {
            return;
        }

        $buttons = $event->getButtons();

        foreach (array('cut',) as $button) {
            unset($buttons[$button]);
        }

        $event->setButtons($buttons);
    }
}
