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

namespace Avisota\Contao\Message\Core\Entity;

use Avisota\Contao\Entity\Layout;
use Avisota\Contao\Entity\MessageCategory;
use Avisota\Contao\Entity\Queue;
use Avisota\Contao\Entity\RecipientSource;
use Contao\Doctrine\ORM\AliasableInterface;
use Contao\Doctrine\ORM\Entity;
use Contao\Doctrine\ORM\EntityHelper;
use Contao\Doctrine\ORM\EntityInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Contao\Doctrine\ORM\Annotation\Accessor;

abstract class AbstractMessage implements EntityInterface, AliasableInterface
{
	public function __construct()
	{
		if (isset($GLOBALS['TL_LANGUAGE'])) {
			$this->language = $GLOBALS['TL_LANGUAGE'];
		}
	}

	/**
	 * Get recipients
	 *
	 * @return RecipientSource
	 */
	public function getRecipients()
	{
		$category = $this->getCategory();

		if ($category->getBoilerplates() ||
			$category->getRecipientsMode() == 'byMessage'
		) {
			$recipients = $this->recipients;
		}
		else if ($category->getRecipientsMode() == 'byMessageOrCategory') {
			$recipients = $this->recipients;
			if (!$recipients) {
				$recipients = $category->getRecipients();
			}
		}
		else if ($category->getRecipientsMode() == 'byCategory') {
			$recipients = $category->getRecipients();
		}
		else {
			throw new \RuntimeException('Could not find recipients for message ' . $this->getId());
		}

		return EntityHelper::callGetterCallbacks($this, 'orm_avisota_message', 'recipients', $recipients);
	}

	/**
	 * Get layout
	 *
	 * @return Layout
	 */
	public function getLayout()
	{
		$category = $this->getCategory();

		if ($category->getBoilerplates() ||
			$category->getLayoutMode() == 'byMessage'
		) {
			$layout = $this->layout;
		}
		else if ($category->getLayoutMode() == 'byMessageOrCategory') {
			$layout = $this->layout;
			if (!$layout) {
				$layout = $category->getLayout();
			}
		}
		else if ($category->getLayoutMode() == 'byCategory') {
			$layout = $category->getLayout();
		}
		else {
			throw new \RuntimeException('Could not find layout for message ' . $this->getId());
		}

		return EntityHelper::callGetterCallbacks($this, 'orm_avisota_message', 'layout', $layout);
	}

	/**
	 * Get queue
	 *
	 * @return Queue
	 */
	public function getQueue()
	{
		$category = $this->getCategory();

		if ($category->getBoilerplates() ||
			$category->getQueueMode() == 'byMessage'
		) {
			$queue = $this->queue;
		}
		else if ($category->getQueueMode() == 'byMessageOrCategory') {
			$queue = $this->queue;
			if (!$queue) {
				$queue = $category->getQueue();
			}
		}
		else if ($category->getQueueMode() == 'byCategory') {
			$queue = $category->getQueue();
		}
		else {
			throw new \RuntimeException('Could not find queue for message ' . $this->getId());
		}

		return EntityHelper::callGetterCallbacks($this, 'orm_avisota_message', 'queue', $queue);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @Accessor(ignore=true)
	 */
	public function getAliasParentValue()
	{
		return $this->getSubject();
	}
}
