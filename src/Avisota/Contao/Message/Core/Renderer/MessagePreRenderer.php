<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-renderer-backend
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\RendererBackend\Message\Renderer;

use Avisota\Contao\Entity\Message;
use Avisota\Contao\Entity\MessageContent;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\InitializeMessageRendererEvent;
use Avisota\Contao\Message\Core\Renderer\MessageContentPreRendererChain;
use Avisota\Contao\Message\Core\Renderer\MessageContentPreRendererInterface;
use Avisota\Contao\Message\Core\Renderer\MessagePreRendererInterface;
use Avisota\Contao\Message\Core\Renderer\TagReplacementService;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MessagePreRenderer implements MessagePreRendererInterface
{
	/**
	 * @var MessageContentPreRendererInterface
	 */
	protected $contentRenderer = null;

	function __construct()
	{
		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
		$eventDispatcher->dispatch(AvisotaMessageEvents::INITIALIZE_MESSAGE_RENDERER, new InitializeMessageRendererEvent($this));
	}

	/**
	 * @return MessageContentPreRendererInterface
	 */
	public function getContentRenderer()
	{
		if (!$this->contentRenderer) {
			$this->contentRenderer = new MessageContentPreRendererChain($GLOBALS['AVISOTA_CONTENT_RENDERER']['backend']);
		}
		return $this->contentRenderer;
	}

	/**
	 * {@inheritdoc}
	 */
	public function renderMessage(Message $message)
	{
		throw new \RuntimeException('This renderer cannot render a complete message');
	}

	/**
	 * {@inheritdoc}
	 */
	public function renderContent(MessageContent $content)
	{
		return $this->getContentRenderer()->renderContent($content);
	}

	/**
	 * {@inheritdoc}
	 */
	public function canRenderMessage(Message $message)
	{
		return TL_MODE == 'BE';
	}

	/**
	 * {@inheritdoc}
	 */
	public function canRenderContent(MessageContent $content)
	{
		return TL_MODE == 'BE';
	}
}
