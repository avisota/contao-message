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

namespace Avisota\Contao\Core\Message\Core\Renderer;

use Avisota\Contao\Entity\MessageContent;
use Avisota\Recipient\RecipientInterface;

interface MessageContentPreRendererInterface
{
	/**
	 * Render a single message content element.
	 *
	 * @param MessageContent     $content
	 * @param RecipientInterface $recipient
	 *
	 * @return mixed
	 */
	public function renderContent(MessageContent $content);

	/**
	 * Check if this renderer can render the given message content element.
	 *
	 * @param MessageContent     $content
	 * @param RecipientInterface $recipient
	 *
	 * @return bool
	 */
	public function canRenderContent(MessageContent $content);
}
