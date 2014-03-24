<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\Renderer;

use Avisota\Contao\Entity\Layout;
use Avisota\Contao\Entity\Message;
use Avisota\Contao\Entity\MessageContent;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Avisota\Contao\Message\Core\Event\RenderMessageEvent;
use Contao\Doctrine\ORM\EntityHelper;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MessageRenderer implements MessageRendererInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function renderMessage(Message $message, Layout $layout = null)
	{
		$event = new RenderMessageEvent($message, $layout);

		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
		$eventDispatcher->dispatch(AvisotaMessageEvents::RENDER_MESSAGE, $event);

		return $event->getPreRenderedMessageTemplate();
	}

	/**
	 * {@inheritdoc}
	 */
	public function renderCell(Message $message, $cell, Layout $layout = null)
	{
		$messageContentRepository = EntityHelper::getRepository('Avisota\Contao:MessageContent');
		$queryBuilder = $messageContentRepository->createQueryBuilder('mc');
		$queryBuilder
			->select('mc')
			->where('mc.message=:message')
			->andWhere('mc.cell=:cell')
			->setParameter('message', $message->getId())
			->setParameter('cell', $cell);
		$query = $queryBuilder->getQuery();
		$contents = $query->getResult();

		$elements = array();
		foreach ($contents as $content) {
			$elements[] = $this->renderContent($content, $layout);
		}

		return $elements;
	}

	/**
	 * {@inheritdoc}
	 */
	public function renderContent(MessageContent $messageContent, Layout $layout = null)
	{
		$event = new RenderMessageContentEvent($messageContent, $layout);

		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
		$eventDispatcher->dispatch(AvisotaMessageEvents::RENDER_MESSAGE_CONTENT, $event);

		return $event->getRenderedContent();
	}
}
