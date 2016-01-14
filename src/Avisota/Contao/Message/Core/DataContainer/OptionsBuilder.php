<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-core
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Core\DataContainer;

use Avisota\Contao\Core\Event\CreateOptionsEvent;
use Avisota\Contao\Entity\Layout;
use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\CollectStylesheetsEvent;
use Avisota\Contao\Message\Core\MessageEvents;
use Contao\Doctrine\ORM\DataContainer\General\EntityModel;
use Contao\Doctrine\ORM\EntityHelper;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent;
use ContaoCommunityAlliance\DcGeneral\Contao\Compatibility\DcCompat;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\Event\GetPropertyOptionsEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OptionsBuilder
 *
 * @package Avisota\Contao\Message\Core\DataContainer
 */
class OptionsBuilder implements EventSubscriberInterface
{
	/**
	 * Returns an array of event names this subscriber wants to listen to.
	 *
	 * The array keys are event names and the value can be:
	 *
	 *  * The method name to call (priority defaults to 0)
	 *  * An array composed of the method name to call and the priority
	 *  * An array of arrays composed of the method names to call and respective
	 *    priorities, or 0 if unset
	 *
	 * For instance:
	 *
	 *  * array('eventName' => 'methodName')
	 *  * array('eventName' => array('methodName', $priority))
	 *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
	 *
	 * @return array The event names to listen to
     */
	static public function getSubscribedEvents()
	{
		return array(
			// Theme related options
			'avisota.create-template-directory-options'        => 'createTemplateDirectoryOptions',
			'avisota.create-theme-options'                     => 'createThemeOptions',
			// Layout related options
			'avisota.create-layout-type-options'               => 'createLayoutTypeOptions',
			'avisota.create-layout-stylesheet-options'         => 'crateLayoutStylesheetOptions',
			'avisota.create-layout-options'                    => 'createLayoutOptions',
			// Message category related options
			'avisota.create-message-category-options'          => 'createMessageCategoryOptions',
			// Message related options
			'avisota.create-boilerplate-message-options'       => 'createBoilerplateMessages',
			'avisota.create-non-boilerplate-message-options'   => 'createNonBoilerplateMessages',
			'avisota.create-message-options'                   => 'createMessageOptions',
			// Message content related options
			MessageEvents::CREATE_MESSAGE_CONTENT_TYPE_OPTIONS => 'createMessageContentTypeOptions',
			MessageEvents::CREATE_MESSAGE_CONTENT_CELL_OPTIONS => 'createMessageContentCellOptions',
			'avisota.create-article-options'                   => 'createArticleAliasOptions',
			'avisota.create-content-type-options'              => 'createContentTypeOptions',
		);
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createTemplateDirectoryOptions(CreateOptionsEvent $event)
	{
		$basePath = TL_ROOT . '/templates/';

		$iterator = new \RecursiveDirectoryIterator(
			$basePath,
			\FilesystemIterator::KEY_AS_PATHNAME |
			\FilesystemIterator::CURRENT_AS_FILEINFO |
			\RecursiveDirectoryIterator::FOLLOW_SYMLINKS
		);
		$iterator = new \RecursiveIteratorIterator($iterator);
		$iterator = new \CallbackFilterIterator(
			$iterator,
			function (\SplFileInfo $file) {
				return $file->getBasename() != '..' && $file->isDir();
			}
		);

		$directories = array();

		/** @var \SplFileInfo $directory */
		foreach ($iterator as $directory) {
			$path = str_replace($basePath, '', $directory->getPathname());
			$path = rtrim($path, '.');
			$path = rtrim($path, '/');

			if ($path) {
				$directories[] = $path;
			}
		}

		usort($directories, 'strnatcasecmp');

		$options = $event->getOptions();
		foreach ($directories as $directory) {
			/** @var string $directory */
			$options[$directory] = $directory;
		}
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createThemeOptions(CreateOptionsEvent $event)
	{
		$this->getThemeOptions($event->getOptions());
	}

	/**
	 * @param array $options
	 *
	 * @return array
     */
    public function getThemeOptions($options = array())
	{
		$themeRepository = EntityHelper::getRepository('Avisota\Contao:Theme');
		$themes          = $themeRepository->findBy(array(), array('title' => 'ASC'));
		/** @var \Avisota\Contao\Entity\Theme $theme */
		foreach ($themes as $theme) {
			$options[$theme->getId()] = $theme->getTitle();
		}
		return $options;
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createLayoutTypeOptions(CreateOptionsEvent $event)
	{
		static::getLayoutTypeOptions($event->getOptions());
	}

	/**
	 * @param array $options
	 *
	 * @return array
     */
    public function getLayoutTypeOptions($options = array())
	{
		foreach ($GLOBALS['AVISOTA_MESSAGE_RENDERER'] as $rendererKey) {
			$label = isset($GLOBALS['TL_LANG']['orm_avisota_layout'][$rendererKey])
				? $GLOBALS['TL_LANG']['orm_avisota_layout'][$rendererKey]
				: $rendererKey;

			$options[$rendererKey] = $label;
		}

		return $options;
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function crateLayoutStylesheetOptions(CreateOptionsEvent $event)
	{
		$this->getLayoutStylesheetOptions($event->getOptions());
	}

	/**
	 * @param array $options
	 *
	 * @return array
     */
    public function getLayoutStylesheetOptions($options = array())
	{
		/** @var EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

		if ($options instanceof \ArrayObject) {
			$stylesheets = $options;
		}
		else {
			$stylesheets = new \ArrayObject();
		}

		$eventDispatcher->dispatch(AvisotaMessageEvents::COLLECT_STYLESHEETS, new CollectStylesheetsEvent($stylesheets));

		return $stylesheets->getArrayCopy();
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createLayoutOptions(CreateOptionsEvent $event)
	{
		$this->getLayoutOptions($event->getOptions());
	}

	/**
	 * @param array $options
	 *
	 * @return array
     */
    public function getLayoutOptions($options = array())
	{
		$layoutRepository = EntityHelper::getRepository('Avisota\Contao:Layout');
		$layouts          = $layoutRepository->findBy(array(), array('title' => 'ASC'));
		/** @var \Avisota\Contao\Entity\Layout $layout */
		foreach ($layouts as $layout) {
			$options[$layout
				->getTheme()
				->getTitle()][$layout->getId()] = $layout->getTitle();
		}
		return $options;
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createMessageCategoryOptions(CreateOptionsEvent $event)
	{
		$this->getMessageCategoryOptions($event->getOptions());
	}

	/**
	 * @param array $options
	 *
	 * @return array
     */
    public function getMessageCategoryOptions($options = array())
	{
		$messageCategoryRepository = EntityHelper::getRepository('Avisota\Contao:MessageCategory');
		$messageCategories         = $messageCategoryRepository->findBy(array(), array('title' => 'ASC'));
		/** @var \Avisota\Contao\Entity\MessageCategory $messageCategory */
		foreach ($messageCategories as $messageCategory) {
			$options[$messageCategory->getId()] = $messageCategory->getTitle();
		}
		return $options;
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createBoilerplateMessages(CreateOptionsEvent $event)
	{
		$this->getBoilerplateMessages($event->getOptions());
	}

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	public function getBoilerplateMessages($options = array())
	{
		$entityManager = EntityHelper::getEntityManager();
		$queryBuilder  = $entityManager->createQueryBuilder();

		/** @var Message[] $messages */
		$messages = $queryBuilder
			->select('m')
			->from('Avisota\Contao:Message', 'm')
			->innerJoin('Avisota\Contao:MessageCategory', 'c', 'c.id=m.category')
			->where('c.boilerplates=:boilerplate')
			->orderBy('c.title', 'ASC')
			->addOrderBy('m.subject', 'ASC')
			->setParameter(':boilerplate', true)
			->getQuery()
			->getResult();

		foreach ($messages as $message) {
			$options[$message->getCategory()
				->getTitle()][$message->getId()] = $message->getSubject();
		}

		return $options;
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createNonBoilerplateMessages(CreateOptionsEvent $event)
	{
		$this->getNonBoilerplateMessages($event->getOptions());
	}

	/**
	 * @param array $options
	 *
	 * @return array
	 */
	public function getNonBoilerplateMessages($options = array())
	{
		$entityManager = EntityHelper::getEntityManager();
		$queryBuilder  = $entityManager->createQueryBuilder();

		/** @var Message[] $messages */
		$messages = $queryBuilder
			->select('m')
			->from('Avisota\Contao:Message', 'm')
			->innerJoin('Avisota\Contao:MessageCategory', 'c', 'c.id=m.category')
			->where('c.boilerplates=:boilerplate')
			->orderBy('c.title', 'ASC')
			->addOrderBy('m.subject', 'ASC')
			->setParameter(':boilerplate', false)
			->getQuery()
			->getResult();

		foreach ($messages as $message) {
			$options[$message->getCategory()
				->getTitle()][$message->getId()] = $message->getSubject();
		}

		return $options;
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createMessageOptions(CreateOptionsEvent $event)
	{
		if (!$event->isDefaultPrevented()) {
			$this->getMessageOptions($event->getOptions());
		}
	}

	/**
	 * @param array $options
	 *
	 * @return array
     */
    public function getMessageOptions($options = array())
	{
		$messageRepository = EntityHelper::getRepository('Avisota\Contao:Message');
		$messages          = $messageRepository->findBy(array(), array('sendOn' => 'DESC'));
		/** @var \Avisota\Contao\Entity\Message $message */
		foreach ($messages as $message) {
			$options[$message
				->getCategory()
				->getTitle()][$message->getId()] = sprintf(
				'[%s] %s',
				$message->getSendOn() ? $message
					->getSendOn()
					->format($GLOBALS['TL_CONFIG']['datimFormat']) : '-',
				$message->getSubject()
			);
		}
		return $options;
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createMessageContentTypeOptions(CreateOptionsEvent $event)
	{
		if (!$event->isDefaultPrevented()) {
			$this->getMessageContentTypeOptions($event->getDataContainer(), $event->getOptions());
		}
	}

	/**
	 * Return all newsletter elements as array
	 *
	 * @param       $dc
	 * @param array $options
	 *
	 * @return array
	 */
	public function getMessageContentTypeOptions($dc, $options = array())
	{
		if (!count($options)) {
			foreach ($GLOBALS['TL_MCE'] as $elementGroup => $elements) {
				if (isset($GLOBALS['TL_LANG']['MCE'][$elementGroup])) {
					$elementGroup = $GLOBALS['TL_LANG']['MCE'][$elementGroup];
				}

				if (!isset($options[$elementGroup])) {
					$options[$elementGroup] = array();
				}

				foreach ($elements as $elementType) {
					$label = isset($GLOBALS['TL_LANG']['MCE'][$elementType])
						? $GLOBALS['TL_LANG']['MCE'][$elementType]
						: $elementType;

					if (is_array($label)) {
						$label = $label[0];
					}

					$options[$elementGroup][$elementType] = $label;
				}
			}
		}

		return $options;
	}

	/**
	 * Get a list of areas from the parent category.
	 *
	 * @param CreateOptionsEvent $event
	 *
	 * @internal param DC_General $dc
	 */
	public function createMessageContentCellOptions(CreateOptionsEvent $event)
	{
		$this->getMessageContentCellOptions($event->getOptions());
	}

	/**
	 * Get a list of areas from the parent category.
	 *
	 * @param array $options
	 *
	 * @return array
	 * @internal param DC_General $dc
	 */
	public function getMessageContentCellOptions($options = array())
	{
		if (!count($options)) {
			$options[] = 'center';
		}

		return $options;
	}

	/**
	 * Get all articles and return them as array (article alias)
	 *
	 * @param CreateOptionsEvent $event
	 *
	 * @return array
	 * @internal param $object
	 *
	 */
	public function createArticleAliasOptions(CreateOptionsEvent $event)
	{
		$this->getArticleAliasOptions($event->getOptions());
	}

	/**
	 * Get all articles and return them as array (article alias)
	 *
	 * @param array $options
	 *
	 * @return array
	 * @internal param $object
	 *
	 */
	public function getArticleAliasOptions($options = array())
	{
		$pids = array();

		$user = \BackendUser::getInstance();

		if (!$user->isAdmin) {
			foreach ($user->pagemounts as $id) {
				$pids[] = $id;
				$pids   = array_merge($pids, $this->getChildRecords($id, 'tl_page', true));
			}

			if (empty($pids)) {
				return $options;
			}

			$alias = \Database::getInstance()->execute(
				"SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid WHERE a.pid IN(" . implode(
					',',
					array_map('intval', array_unique($pids))
				) . ") ORDER BY parent, a.sorting"
			);
		}
		else {
			$alias = \Database::getInstance()->execute(
				"SELECT a.id, a.title, a.inColumn, p.title AS parent FROM tl_article a LEFT JOIN tl_page p ON p.id=a.pid ORDER BY parent, a.sorting"
			);
		}

		if ($alias->numRows) {
			/** @var EventDispatcher $eventDispatcher */
			$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

			$eventDispatcher->dispatch(
				ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE,
				new LoadLanguageFileEvent('tl_article')
			);

			while ($alias->next()) {
				$options[$alias->parent][$alias->id] = $alias->title . ' (' . (strlen(
						$GLOBALS['TL_LANG']['tl_article'][$alias->inColumn]
					) ? $GLOBALS['TL_LANG']['tl_article'][$alias->inColumn]
						: $alias->inColumn) . ', ID ' . $alias->id . ')';
			}
		}

		return $options;
	}

	/**
	 * @param CreateOptionsEvent $event
     */
	public function createContentTypeOptions(CreateOptionsEvent $event)
	{
		$this->getContentTypeOptions($event->getOptions());
	}

	/**
	 * @param array $options
	 *
	 * @return array
     */
    public function getContentTypeOptions($options = array())
	{
		foreach ($GLOBALS['TL_MCE'] as $elementGroup => $elements) {
			if (isset($GLOBALS['TL_LANG']['MCE'][$elementGroup])) {
				$elementGroupLabel = $GLOBALS['TL_LANG']['MCE'][$elementGroup];
			}
			else {
				$elementGroupLabel = $elementGroup;
			}
			foreach ($elements as $elementType) {
				if (isset($GLOBALS['TL_LANG']['MCE'][$elementType])) {
					$elementLabel = $GLOBALS['TL_LANG']['MCE'][$elementType][0];
				}
				else {
					$elementLabel = $elementType;
				}

				$options[$elementGroupLabel][$elementType] = sprintf(
					'%s',
					$elementLabel
				);
			}
		}

		return $options;
	}
}
