<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
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
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class MessageRenderer
 *
 * @package Avisota\Contao\Message\Core\Renderer
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
     * @param Message $message
     * @param string  $cell
     *
     * @param Layout  $layout
     *
     * @return \string[]
     * @SuppressWarnings(PHPMD.LongVariable)
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

        if (TL_MODE != 'BE' && (!defined('BE_USER_LOGGED_IN') || !BE_USER_LOGGED_IN)) {
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
     * @param MessageContent $messageContent
     *
     * @param Layout         $layout
     *
     * @return string
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function renderContent(MessageContent $messageContent, Layout $layout = null)
    {
        if ($messageContent->getInvisible() && TL_MODE != 'BE' && !BE_USER_LOGGED_IN) {
            return '';
        }

        $event = new RenderMessageContentEvent($messageContent, $layout ?: $messageContent->getMessage()->getLayout());

        $replacedTemplates = $this->findMessageContentCustomTemplates($messageContent);

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];
        $eventDispatcher->dispatch(AvisotaMessageEvents::RENDER_MESSAGE_CONTENT, $event);

        $this->removeEachTemplate($replacedTemplates);

        return $event->getRenderedContent();
    }

    protected function findMessageContentCustomTemplates(MessageContent $messageContent)
    {
        if (in_array(
            $messageContent->getType(),
            array(
                'headline',
                'hyperlink',
                'list',
                'salutation',
                'table',
                'image',
                'gallery',
                'text',
            )
        )
        ) {
            return array();
        }

        $contents = $this->handleMessageContent($messageContent);

        return $this->handleFoundedContent($messageContent, $contents);
    }

    protected function handleMessageContent(MessageContent $messageContent)
    {
        switch ($messageContent->getType()) {
            case 'event':
                $elementIdMethod    = 'getEventIdWithTimestamp';
                $containerModelName = \CalendarEventsModel::class;
                break;

            default:
                $elementIdMethod    = 'get' . ucfirst($messageContent->getType()) . 'Id';
                $containerModelName = ucfirst($messageContent->getType()) . 'Model';

                if (!method_exists($messageContent, $elementIdMethod)) {
                    return array();
                }
                break;
        }

        $elementId = explode('@', $messageContent->$elementIdMethod());
        if (count($elementId) === 2) {
            unset($elementId[1]);
        }
        $elementId = implode('', $elementId);

        if ($elementId < 1) {
            return array();
        }

        /** @var \Model $containerModel */
        $containerModel = $containerModelName::findByPk($elementId);

        $contents = array($messageContent);
        $contents = array_merge($contents, $this->findContainerCustomTemplates($containerModel));

        return $contents;
    }

    protected function findContainerCustomTemplates(\Model $containerModel)
    {
        $contents = array();

        $contents[] = $containerModel;

        $contents = array_merge($contents, $this->findChildContainerCustomTemplates($containerModel));

        return $contents;
    }

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

    protected function findChildTableByBackendModule(\Model $containerModel)
    {
        if (!array_key_exists('ctable', $GLOBALS['TL_DCA'][$containerModel::getTable()]['config'])) {
            return null;
        }
        
        return $GLOBALS['TL_DCA'][$containerModel::getTable()]['config']['ctable'][0];
    }

    protected function handleFoundedContent(MessageContent $messageContent, array $contents)
    {
        if (count($contents) < 1) {
            return array();
        }

        $messageCategory     = $messageContent->getMessage()->getCategory();
        $viewOnlinePageModel = \PageModel::findByPk($messageCategory->getViewOnlinePage());
        $viewOnlinePageModel->loadDetails();

        $replaced = array();

        foreach ($contents as $content) {
            foreach (
                array(
                    'type',
                    'galleryTpl',
                    'customTpl',
                    'eventTemplate',
                    'newsTemplate',
                ) as $propertyTemplate
            ) {

                if ($content instanceof \Model) {
                    if (empty($content->$propertyTemplate)) {
                        continue;
                    }

                    $template = $this->findTemplate($content->$propertyTemplate, $messageCategory);

                    if ($content->$propertyTemplate === $template) {
                        continue;
                    }

                    $content->$propertyTemplate = $template;
                    $replaced[]                 = $template;
                }
                if ($content instanceof EntityInterface) {
                    $getPropertyTemplate = 'get' . ucfirst($propertyTemplate);
                    $setPropertyTemplate = 'set' . ucfirst($propertyTemplate);

                    if (!method_exists($content, $getPropertyTemplate) || empty($content->$getPropertyTemplate())) {
                        continue;
                    }

                    $template = $this->findTemplate($content->$getPropertyTemplate(), $messageCategory);

                    if ($content->$getPropertyTemplate() === $template) {
                        continue;
                    }

                    $content->$setPropertyTemplate($template);
                    $replaced[] = $template;
                }
            }
        }

        return $replaced;
    }

    protected function findTemplate($searchTemplate, MessageCategory $messageCategory)
    {
        $messageTheme    = $messageCategory->getLayout()->getTheme();

        $template = null;
        if ($messageTheme->getTemplateDirectory()
            && file_exists(TL_ROOT . '/templates/' . $messageTheme->getTemplateDirectory() . '/' . $searchTemplate . '.html5')
        ) {
            $template = $this->copyTemplateInRootTemplates(
                $messageTheme->getTemplateDirectory() . '/' . $searchTemplate,
                '.' . microtime(true)
            );
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
                $source = $pageTheme->templates;
                $chunks = explode('/', $source);
                if (count($chunks) > 1) {
                    if (in_array('templates', array_values($chunks))) {
                        $unset = array_flip($chunks)['templates'];
                        unset($chunks[$unset]);
                    }
                }
                $source = implode('/', $chunks);

                $template = $this->copyTemplateInRootTemplates(
                    $source . '/' . $searchTemplate,
                    '.' . microtime(true)
                );
            }
        }

        if (!$template) {
            $template = $searchTemplate;
        }

        return $template;
    }

    protected function copyTemplateInRootTemplates($source, $destination)
    {
        $sourceFile = new \File('templates/' . $source . '.html5');
        $sourceFile->copyTo('templates/' . $destination . '.html5');

        return $destination;
    }

    protected function removeEachTemplate(array $removes)
    {
        if (count($removes) < 1) {
            return;
        }

        foreach ($removes as $remove) {
            $removeFile = new \File('templates/' . $remove . '.html5', true);
            if (!$removeFile->exists()) {
                continue;
            }

            $removeFile->delete();
        }
    }
}
