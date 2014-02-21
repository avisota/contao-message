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

use Avisota\Contao\Core\Message\Renderer\MessagePreRendererInterface;
use Avisota\Contao\Message\Core\Renderer\MessageRenderer;
use Avisota\Renderer\MessageRendererInterface;
use Contao\Doctrine\ORM\DataContainer\General\EntityModel;
use Contao\Doctrine\ORM\EntityAccessor;
use Contao\Doctrine\ORM\EntityHelper;
use DcGeneral\Contao\View\Contao2BackendView\Event\GetGroupHeaderEvent;
use DcGeneral\Contao\View\Contao2BackendView\Event\ParentViewChildRecordEvent;
use DcGeneral\DC_General;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageContent implements EventSubscriberInterface
{
	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents()
	{
		return array(
			GetGroupHeaderEvent::NAME . '[orm_avisota_message_content]'        => 'getGroupHeader',
			ParentViewChildRecordEvent::NAME . '[orm_avisota_message_content]' => 'parentViewChildRecord',
		);
	}

	/**
	 * Return the send button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function sendMessageButton($href, $label, $title, $icon, $attributes)
	{
		$user = \BackendUser::getInstance();

		if (!($user->isAdmin || $user->hasAccess('send', 'avisota_newsletter_permissions'))) {
			$label = $GLOBALS['TL_LANG']['orm_avisota_message']['view_only'][0];
			$title = $GLOBALS['TL_LANG']['orm_avisota_message']['view_only'][1];
		}
		return ' &#160; :: &#160; <a href="' . $this->addToUrl(
			$href . '&amp;id=' . $this->Input->get('id')
		) . '" title="' . specialchars($title) . '"' . $attributes . ' class="header_send">' . $label . '</a> ';
	}

	/**
	 * Return the "toggle visibility" button
	 *
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen($this->Input->get('tid'))) {
			$this->toggleVisibility($this->Input->get('tid'), ($this->Input->get('state') == 1));
			$this->redirect($this->getReferer());
		}

		$href .= '&amp;id=' . $this->Input->get('id') . '&amp;tid=' . $row['id'] . '&amp;state=' . $row['invisible'];

		if ($row['invisible']) {
			$icon = 'invisible.gif';
		}

		return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars(
			$title
		) . '"' . $attributes . '>' . $this->generateImage($icon, $label) . '</a> ';
	}


	/**
	 * Toggle the visibility of an element
	 *
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($contentId, $visible)
	{
		/*
		// Check permissions to edit
		$this->Input->setGet('id', $contentId);
		$this->Input->setGet('act', 'toggle');
		$this->checkPermission();

		$this->createInitialVersion('orm_avisota_message_content', $contentId);

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['invisible']['save_callback'])) {
			foreach (
				$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['invisible']['save_callback'] as
				$callback
			) {
				$this->import($callback[0]);
				$visible = $this->$callback[0]->$callback[1]($visible, $this);
			}
		}

		// Update the database
		\Database::getInstance()
			->prepare(
			"UPDATE orm_avisota_message_content SET tstamp=" . time() . ", invisible='" . ($visible ? ''
				: 1) . "' WHERE id=?"
		)
			->execute($contentId);

		$this->createNewVersion('orm_avisota_message_content', $contentId);
		*/
	}

	public function getGroupHeader(GetGroupHeaderEvent $event)
	{
		$model = $event->getModel();
		$cell  = $model->getProperty('cell');

		if (isset($GLOBALS['TL_LANG']['orm_avisota_message_content']['cells'][$cell])) {
			$cell = $GLOBALS['TL_LANG']['orm_avisota_message_content']['cells'][$cell];
		}

		$event->setValue($cell);
	}

	/**
	 * Add the recipient row.
	 *
	 * @param array
	 */
	public function parentViewChildRecord(ParentViewChildRecordEvent $event)
	{
		/** @var MessageRenderer $renderer */
		$renderer = $GLOBALS['container']['avisota.message.renderer'];

		/** @var EntityModel $model */
		$model = $event->getModel();
		/** @var \Avisota\Contao\Entity\MessageContent $content */
		$content = $model->getEntity();

		$key = $content->getInvisible() ? 'unpublished' : 'published';

		try {
			$element = $renderer->renderContent($content);
		}
		catch (\Exception $exception) {
			$element = sprintf(
				"<span style=\"color:red\">%s</span>",
				$exception->getMessage()
			);
		}

		/** @var EntityAccessor $entityAccessor */
		$entityAccessor = $GLOBALS['container']['doctrine.orm.entityAccessor'];

		$context            = $entityAccessor->getProperties($content);
		$context['key']     = $key;
		$context['element'] = $element;

		$template = new \TwigTemplate('avisota/backend/mce_element', 'html5');
		$event->setHtml($template->parse($context));
	}
}
