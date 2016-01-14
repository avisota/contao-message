<?php

/**
 * <project name>
 *
 * PHP Version 5.3
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    <project>
 * @license    LGPL-3.0+
 * @link       <link>
 */
namespace Avisota\Contao\Message\Core\Renderer;

use Avisota\Contao\Core\Message\PreRenderedMessageTemplateInterface;
use Avisota\Contao\Entity\Layout;
use Avisota\Contao\Entity\Message;
use Avisota\Contao\Entity\MessageContent;

/**
 * Interface MessageRendererInterface
 *
 * @package Avisota\Contao\Message\Core\Renderer
 */
interface MessageRendererInterface
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
    public function renderMessage(Message $message, Layout $layout = null);

    /**
     * Render a single message content element.
     *
     * @param MessageContent $messageContent
     *
     * @param Layout         $layout
     *
     * @return string
     */
    public function renderContent(MessageContent $messageContent, Layout $layout = null);

    /**
     * Render content from a cell.
     *
     * @param Message $message
     * @param string  $cell
     *
     * @param Layout  $layout
     *
     * @return \string[]
     */
    public function renderCell(Message $message, $cell, Layout $layout = null);
}
