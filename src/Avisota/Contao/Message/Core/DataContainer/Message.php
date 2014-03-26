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

namespace Avisota\Contao\Message\Core\DataContainer;

use Avisota\Contao\Entity\RecipientSource;
use Avisota\Contao\Message\Core\Backend\Helper;
use Contao\Doctrine\ORM\DataContainer\General\EntityModel;
use Contao\Doctrine\ORM\EntityHelper;
use Contao\Doctrine\ORM\EntityInterface;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\Backend\GetThemeEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetGroupHeaderEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\ParentViewChildRecordEvent;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Message implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			GetGroupHeaderEvent::NAME . '[orm_avisota_message]'        => 'getGroupHeader',
			ParentViewChildRecordEvent::NAME . '[orm_avisota_message]' => 'parentViewChildRecord',
		);
	}

	public function addHeader($add, $dc)
	{
		// TODO refactore for DCG
		return;

		$newsletterCategoryRepository = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
		/** @var \Avisota\Contao\Entity\MessageCategory $newsletterCategory */
		$newsletterCategory = $newsletterCategoryRepository->find($dc->id);

		$key = $GLOBALS['TL_LANG']['orm_avisota_message_category']['recipients'][0];
		if (!$newsletterCategory->getBoilerplates() && $newsletterCategory->getRecipientsMode() != 'byMessage') {
			$fallback = $newsletterCategory->getRecipientsMode() == 'byMessageOrCategory';

			/** @var RecipientSource $recipientSource */
			$recipientSource = $newsletterCategory->getRecipients();
			if ($recipientSource) {
				$add[$key] = sprintf(
					'<a href="contao/main.php?do=avisota_recipient_source&act=edit&id=%d">%s</a>%s',
					$recipientSource->getId(),
					$recipientSource->getTitle(),
					$fallback ? ' ' . $GLOBALS['TL_LANG']['orm_avisota_message']['fallback'] : ''
				);
			}
			else {
				unset($add[$key]);
			}
		}
		else {
			unset($add[$key]);
		}

		$key = $GLOBALS['TL_LANG']['orm_avisota_message_category']['layout'][0];
		if (!$newsletterCategory->getBoilerplates() && $newsletterCategory->getLayoutMode() != 'byMessage') {
			$add[$key] = $newsletterCategory
				->getLayout()
				->getTitle();
			if ($newsletterCategory->getLayoutMode() == 'byMessageOrCategory') {
				$add[$key] .= ' ' . $GLOBALS['TL_LANG']['orm_avisota_message']['fallback'];
			}
		}
		else {
			unset($add[$key]);
		}

		$key = $GLOBALS['TL_LANG']['orm_avisota_message_category']['queue'][0];
		if (!$newsletterCategory->getBoilerplates() && $newsletterCategory->getQueueMode() != 'byMessage') {
			$add[$key] = $newsletterCategory
				->getQueue()
				->getTitle();
			if ($newsletterCategory->getQueueMode() == 'byMessageOrCategory') {
				$add[$key] .= ' ' . $GLOBALS['TL_LANG']['orm_avisota_message']['fallback'];
			}
		}
		else {
			unset($add[$key]);
		}

		return $add;
	}

	public function getGroupHeader(GetGroupHeaderEvent $event)
	{
		$model = $event->getModel();

		if ($model->getProperty('sendOn') > 0) {
			$event->setValue($this->parseDate('F Y', $model->getProperty('sendOn')));
		}
		else {
			$event->setValue($GLOBALS['TL_LANG']['orm_avisota_message']['notSend']);
		}
	}

	/**
	 * Add the recipient row.
	 *
	 * @param array
	 */
	public function parentViewChildRecord(ParentViewChildRecordEvent $event)
	{
		/** @var EntityModel $model */
		$model = $event->getModel();

		$icon = $model->getProperty('sendOn') ? 'visible' : 'invisible';

		$label = $model->getProperty('subject');

		if ($model->getProperty('sendOn')) {
			$label .= ' <span style="color:#b3b3b3; padding-left:3px;">(' . sprintf(
					$GLOBALS['TL_LANG']['orm_avisota_recipient']['sended'],
					$this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $model->getProperty('sendOn'))
				) . ')</span>';
		}

		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
		$getThemeEvent   = new GetThemeEvent();
		$eventDispatcher->dispatch(ContaoEvents::BACKEND_GET_THEME, $getThemeEvent);

		$event->setHtml(
			sprintf(
				'<div class="list_icon" style="background-image:url(\'system/themes/%s/images/%s.gif\');">%s</div>',
				$getThemeEvent->getTheme(),
				$icon,
				$label
			)
		);
	}
}
