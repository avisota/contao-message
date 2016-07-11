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
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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

        $replaced = $this->findMessageContentCustomTemplates($messageContent);

        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];
        $eventDispatcher->dispatch(AvisotaMessageEvents::RENDER_MESSAGE_CONTENT, $event);

        $this->removeEachTemplate($replaced[0]);
        $this->resetContent($replaced[1]);

        return $event->getRenderedContent();
    }

    /**
     * @param MessageContent $messageContent
     *
     * @return array
     */
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
        foreach ($messageContent->$elementIdMethod() as $elementId) {

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
        //Fixme if no online set, then search the first page (domain)
        $viewOnlinePageModel = \PageModel::findByPk($messageCategory->getViewOnlinePage());
        $viewOnlinePageModel->loadDetails();

        $replaced   = array();
        $replacedIn = array();

        foreach ($contents as $content) {
            foreach (array('type', 'galleryTpl', 'customTpl', 'eventTemplate', 'newsTemplate',) as $propertyTemplate) {
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
                    $replacedIn[]               = $content;
                }
                if ($content instanceof EntityInterface) {
                    $getPropertyTemplate = 'get' . ucfirst($propertyTemplate);
                    $setPropertyTemplate = 'set' . ucfirst($propertyTemplate);

                    if (!method_exists($content, $getPropertyTemplate) || !$content->$getPropertyTemplate()) {
                        continue;
                    }

                    $template = $this->findTemplate($content->$getPropertyTemplate(), $messageCategory);

                    if ($content->$getPropertyTemplate() === $template) {
                        continue;
                    }

                    $content->$setPropertyTemplate($template);
                    $replaced[]   = $template;
                    $replacedIn[] = $content;
                }
            }
        }

        return array($replaced, $replacedIn);
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
                    '.avisota-' . microtime(true)
                );
            }
        }

        if (!$template) {
            $template = $searchTemplate;
        }

        return $template;
    }

    /**
     * @param $source
     * @param $destination
     *
     * @return mixed
     */
    protected function copyTemplateInRootTemplates($source, $destination)
    {
        $sourceFile = new \File('templates/' . $source . '.html5');
        $sourceFile->copyTo('templates/' . $destination . '.html5');

        return $destination;
    }

    /**
     * @param $removes
     */
    protected function removeEachTemplate($removes)
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

    /**
     * @param $contents
     */
    protected function resetContent($contents)
    {
        if (count($contents) < 1) {
            return;
        }

        $entityManager = EntityHelper::getEntityManager();
        foreach ($contents as $content) {
            if ($content instanceof EntityInterface) {
                $entityManager->refresh($content);
            }

            if ($content instanceof \Model) {
                $content->refresh();
            }
        }
    }
}
