<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2017 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2017
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Renderer;

use Avisota\Contao\Core\Message\PreRenderedMessageTemplateInterface;
use Avisota\Contao\Entity\Layout;
use Avisota\Contao\Entity\Message;
use Avisota\Contao\Entity\MessageCategory;
use Avisota\Contao\Entity\MessageContent;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Avisota\Contao\Message\Core\Event\RenderMessageEvent;
use Contao\Doctrine\ORM\EntityHelper;
use Contao\Doctrine\ORM\EntityInterface;
use Contao\Frontend;
use Contao\TemplateLoader;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * The message renderer.
 */
class MessageRenderer implements MessageRendererInterface
{
    /**
     * Render a complete message.
     *
     * @param Message $message
     *
     * @param Layout  $layout
     *
     * @return PreRenderedMessageTemplateInterface
     */
    public function renderMessage(Message $message, Layout $layout = null)
    {
        global $container;

        $event = new RenderMessageEvent($message, $layout ?: $message->getLayout());

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $container['event-dispatcher'];
        $eventDispatcher->dispatch(AvisotaMessageEvents::RENDER_MESSAGE, $event);

        return $event->getPreRenderedMessageTemplate();
    }

    /**
     * Render content from a cell.
     *
     * @param Message $message The message.
     *
     * @param string  $cell    The cell.
     *
     * @param Layout  $layout  The layout.
     *
     * @return array
     */
    public function renderCell(Message $message, $cell, Layout $layout = null)
    {
        $messageContentRepository = EntityHelper::getRepository('Avisota\Contao:MessageContent');
        $queryBuilder             = $messageContentRepository->createQueryBuilder('mc');
        $queryBuilder
            ->select('mc')
            ->where('mc.message=:message')
            ->andWhere('mc.cell=:cell')
            ->orderBy('mc.sorting')
            ->setParameter('message', $message->getId())
            ->setParameter('cell', $cell);

        if (('BE' !== TL_MODE)
            && (!defined('BE_USER_LOGGED_IN') || !BE_USER_LOGGED_IN)
        ) {
            $queryBuilder
                ->andWhere('mc.invisible=:invisible')
                ->setParameter('invisible', false);
        }

        $query    = $queryBuilder->getQuery();
        $contents = $query->getResult();

        $elements = array();
        foreach ($contents as $content) {
            $elements[] = $this->renderContent($content, $layout ?: $message->getLayout());
        }

        return $elements;
    }

    /**
     * Render a single message content element.
     *
     * @param MessageContent $messageContent The message content.
     *
     * @param Layout         $layout         The layout.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function renderContent(MessageContent $messageContent, Layout $layout = null)
    {
        if ($messageContent->getInvisible()
            && 'BE' !== TL_MODE
            && !BE_USER_LOGGED_IN
        ) {
            return '';
        }

        $event = new RenderMessageContentEvent($messageContent, $layout ?: $messageContent->getMessage()->getLayout());

        $this->findMessageContentCustomTemplates($messageContent);

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];
        $eventDispatcher->dispatch(AvisotaMessageEvents::RENDER_MESSAGE_CONTENT, $event);

        return $event->getRenderedContent();
    }

    /**
     * Find the custom templates in message content.
     *
     * @param MessageContent $messageContent The message content.
     *
     * @return array
     */
    protected function findMessageContentCustomTemplates(MessageContent $messageContent)
    {
        if (in_array(
            $messageContent->getType(),
            array('headline', 'hyperlink', 'list', 'salutation', 'table', 'image', 'gallery', 'text',)
        )) {
            return array();
        }

        $contents = $this->handleMessageContent($messageContent);

        $this->handleFoundedContent($messageContent, $contents);
    }

    /**
     * @param MessageContent $messageContent
     *
     * @return array
     */
    protected function handleMessageContent(MessageContent $messageContent)
    {
        switch ($messageContent->getType()) {
            case 'event':
                $elementIdMethod    = 'getEventIdWithTimestamp';
                $containerModelName = 'Contao\CalendarEventsModel';
                break;

            default:
                $elementIdMethod    = 'get' . ucfirst($messageContent->getType()) . 'Id';
                $containerModelName = ucfirst($messageContent->getType()) . 'Model';

                if (!method_exists($messageContent, $elementIdMethod)) {
                    return array();
                }
                break;
        }

        $contents = array();
        foreach (array_keys($messageContent->$elementIdMethod()) as $elementId) {
            /** @var \Model $containerModel */
            $containerModel = $containerModelName::findByPk($elementId);

            $elements = array($messageContent);
            if ($containerModel) {
                $elements = array_merge($elements, $this->findContainerCustomTemplates($containerModel));
            }

            $contents = array_merge($contents, $elements);
        }

        return $contents;
    }

    /**
     * @param \Model $containerModel
     *
     * @return array
     */
    protected function findContainerCustomTemplates(\Model $containerModel)
    {
        $contents = array();

        $contents[] = $containerModel;

        $contents = array_merge($contents, $this->findChildContainerCustomTemplates($containerModel));

        return $contents;
    }

    /**
     * @param \Model $containerModel
     *
     * @return array
     */
    protected function findChildContainerCustomTemplates(\Model $containerModel)
    {
        $childTable = $this->findChildTableByBackendModule($containerModel);
        if (!$childTable) {
            return array();
        }

        $childModelClass = \Model::getClassFromTable($childTable);
        if (!class_exists($childModelClass)) {
            return array();
        }

        $childModels = $childModelClass::findByPid($containerModel->id);
        if (!$childModels) {
            return array();
        }

        $contents = array();

        while ($childModels->next()) {
            if (!array_key_exists('ptable', $childModels->row())
                || (!empty($childModels->ptable) && $childModels->ptable !== $containerModel::getTable())
            ) {
                continue;
            }

            //Todo simulate in child table for content elements, to test go deeper
            $contents[] = $childModels->current();
        }

        return $contents;
    }

    /**
     * @param \Model $containerModel
     *
     * @return null
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function findChildTableByBackendModule(\Model $containerModel)
    {
        if (!array_key_exists('ctable', $GLOBALS['TL_DCA'][$containerModel::getTable()]['config'])) {
            return null;
        }

        return $GLOBALS['TL_DCA'][$containerModel::getTable()]['config']['ctable'][0];
    }

    /**
     * @param MessageContent $messageContent
     * @param array          $contents
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function handleFoundedContent(MessageContent $messageContent, array $contents)
    {
        if (count($contents) < 1) {
            return array();
        }

        $messageCategory = $messageContent->getMessage()->getCategory();

        $viewOnlinePageModel = \PageModel::findByPk($messageCategory->getViewOnlinePage());
        if (!$viewOnlinePageModel) {
            $viewOnlinePageModel = \PageModel::findByPk(Frontend::getRootIdFromUrl());
        }
        $viewOnlinePageModel->loadDetails();

        foreach ($contents as $content) {
            foreach (array('type', 'galleryTpl', 'customTpl', 'eventTemplate', 'newsTemplate') as $propertyTemplate) {
                if ($content instanceof \Model) {
                    if (empty($content->$propertyTemplate)) {
                        continue;
                    }

                    $this->findTemplate($content->$propertyTemplate, $messageCategory);
                }
                if ($content instanceof EntityInterface) {
                    $getPropertyTemplate = 'get' . ucfirst($propertyTemplate);

                    if (!method_exists($content, $getPropertyTemplate) || !$content->$getPropertyTemplate()) {
                        continue;
                    }

                    $this->findTemplate($content->$getPropertyTemplate(), $messageCategory);
                }
            }
        }
    }

    /**
     * @param                 $searchTemplate
     * @param MessageCategory $messageCategory
     *
     * @return null
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function findTemplate($searchTemplate, MessageCategory $messageCategory)
    {
        $messageTheme = $messageCategory->getLayout()->getTheme();

        $template = null;
        if ($messageTheme->getTemplateDirectory()
            && file_exists(
                TL_ROOT . '/templates/' . $messageTheme->getTemplateDirectory() . '/' . $searchTemplate . '.html5'
            )
        ) {
            $this->copyTemplateInRootTemplates($messageTheme->getTemplateDirectory() . '/' . $searchTemplate);
        }

        if (!$template
            && $messageCategory->getViewOnlinePage() > 0
        ) {
            $viewOnlinePage = \PageModel::findByPk($messageCategory->getViewOnlinePage());

            $pageTheme = null;
            if ($viewOnlinePage) {
                $viewOnlinePage->loadDetails();
                $pageTheme = $viewOnlinePage->getRelated('layout')->getRelated('pid');
            }

            if ($pageTheme
                && file_exists(TL_ROOT . '/' . $pageTheme->templates . '/' . $searchTemplate . '.html5')
            ) {
                $this->copyTemplateInRootTemplates($pageTheme->templates . '/' . $searchTemplate);
            }
        }
    }

    /**
     * @param $source
     *
     * @return mixed
     * @internal param $destination
     *
     */
    protected function copyTemplateInRootTemplates($source)
    {
        $chunks       = explode('/', $source);
        $templateName = array_pop($chunks);
        $templatePath = implode('/', $chunks);

        TemplateLoader::addFile($templateName, $templatePath);
    }
}
