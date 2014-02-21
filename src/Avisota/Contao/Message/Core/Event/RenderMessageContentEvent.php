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

use Avisota\Contao\Entity\Layout;
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
	 * @var Layout
	 */
	protected $layout;

	/**
	 * @var string
	 */
	protected $renderedContent;

	public function __construct(MessageContent $messageContent, Layout $layout = null)
	{
		$this->messageContent = $messageContent;
		$this->layout         = $layout;
	}

	/**
	 * @return MessageContent
	 */
	public function getMessageContent()
	{
		return $this->messageContent;
	}

	/**
	 * @param Layout $layout
	 */
	public function setLayout(Layout $layout = null)
	{
		$this->layout = $layout;
		return $this;
	}

	/**
	 * @return Layout
	 */
	public function getLayout()
	{
		if ($this->layout) {
			return $this->layout;
		}

		return $this->messageContent
			->getMessage()
			->getLayout();
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