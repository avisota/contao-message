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

namespace Avisota\Contao\Message\Core\Backend;

use Avisota\Contao\Entity\Message;
use Avisota\Contao\Entity\MessageCategory;
use Avisota\Contao\Entity\MessageContent;
use BackendTemplate;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\IdSerializer;

class Helper
{
	/**
	 * @return MessageCategory|null
	 */
	static public function resolveCategoryFromInput()
	{
		$input         = \Input::getInstance();
		$id            = $input->get('id');
		$pid           = $input->get('pid');
		$modelId       = null;
		$parentModelId = null;
		/** @var MessageCategory $category */
		$category = null;
		/** @var Message $message */
		$message = null;
		/** @var MessageContent $content */
		$content = null;

		if ($id) {
			$modelId = IdSerializer::fromSerialized($id);
		}
		if ($pid) {
			$parentModelId = IdSerializer::fromSerialized($pid);
		}

		// $id or $pid is a category ID
		if ($modelId && $modelId->getDataProviderName() == 'orm_avisota_message_category') {
			$repository = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
			$category   = $repository->find($modelId->getId());
		}
		else if ($parentModelId && $parentModelId->getDataProviderName() == 'orm_avisota_message_category') {
			$repository = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
			$category   = $repository->find($parentModelId->getId());
		}

		// $id or $pid is a message ID
		else if ($modelId && $modelId->getDataProviderName() == 'orm_avisota_message') {
			$repository = EntityHelper::getRepository('Avisota\Contao:Message');
			$message    = $repository->find($modelId->getId());
			$category   = $message->getCategory();
		}
		else if ($parentModelId && $parentModelId->getDataProviderName() == 'orm_avisota_message') {
			$repository = EntityHelper::getRepository('Avisota\Contao:Message');
			$message    = $repository->find($parentModelId->getId());
			$category   = $message->getCategory();
		}

		// $id or $pid is a message content ID
		else if ($modelId && $modelId->getDataProviderName() == 'orm_avisota_message_content') {
			$repository = EntityHelper::getRepository('Avisota\Contao:MessageContent');
			$content    = $repository->find($modelId->getId());
			$message    = $content->getMessage();
			$category   = $message->getCategory();
		}
		else if ($parentModelId && $parentModelId->getDataProviderName() == 'orm_avisota_message_content') {
			$repository = EntityHelper::getRepository('Avisota\Contao:MessageContent');
			$content    = $repository->find($parentModelId->getId());
			$message    = $content->getMessage();
			$category   = $message->getCategory();
		}

		return $category;
	}
}
