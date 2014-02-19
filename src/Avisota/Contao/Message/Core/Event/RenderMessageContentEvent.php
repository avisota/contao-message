<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Event;

use Avisota\Contao\Entity\Message;
use Avisota\Contao\Entity\MessageContent;
use Avisota\Contao\Message\Core\Message\Renderer;
use Symfony\Component\EventDispatcher\Event;

class RenderMessageContentEvent extends Event
{
	/**
	 * @var MessageContent
	 */
	protected $messageContent;

	/**
	 * @var string
	 */
	protected $renderedContent;

	/**
	 * @param MessageContent $messageContent
	 */
	public function setMessageContent(MessageContent $messageContent)
	{
		$this->messageContent = $messageContent;
		return $this;
	}

	/**
	 * @return MessageContent
	 */
	public function getMessageContent()
	{
		return $this->messageContent;
	}

	/**
	 * @param string $renderedContent
	 */
	public function setRenderedContent($renderedContent)
	{
		$this->renderedContent = (string) $renderedContent;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRenderedContent()
	{
		return $this->renderedContent;
	}
}