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

use Avisota\Contao\Entity\Message;
use ContaoCommunityAlliance\DcGeneral\Contao\View\Contao2BackendView\IdSerializer;

$dir = dirname(isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : __FILE__);

while ($dir && $dir != '.' && $dir != '/' && !is_file($dir . '/system/initialize.php')) {
	$dir = dirname($dir);

}

if (!is_file($dir . '/system/initialize.php')) {
	header("HTTP/1.0 500 Internal Server Error");
	header('Content-Type: text/html; charset=utf-8');
	echo '<h1>500 Internal Server Error</h1>';
	echo '<p>Could not find initialize.php!</p>';
	exit(1);
}

define('TL_MODE', 'FE');
require($dir . '/system/initialize.php');

class send_preview_to_user extends \Avisota\Contao\Message\Core\Send\AbstractWebRunner
{
	protected function execute(Message $message, \BackendUser $user)
	{
		global $container;

		$input = \Input::getInstance();
		$user  = $input->get('recipient_user');

		/** @var \Doctrine\DBAL\Connection $connection */
		$connection   = $container['doctrine.connection.default'];
		$queryBuilder = $connection->createQueryBuilder();
		/** @var \Doctrine\DBAL\Statement $statement */
		$statement = $queryBuilder
			->select('u.*')
			->from('tl_user', 'u')
			->where('id=:id')
			->setParameter(':id', $user)
			->execute();
		$data      = $statement->fetch();

		$idSerializer = new IdSerializer();
		$idSerializer->setDataProviderName('orm_avisota_message');
		$idSerializer->setId($message->getId());

		$pidSerializer = new IdSerializer();
		$pidSerializer->setDataProviderName('orm_avisota_message_category');
		$pidSerializer->setId($message->getCategory()->getId());

		$environment = Environment::getInstance();
		$url         = sprintf(
			'%scontao/main.php?do=avisota_newsletter&table=orm_avisota_message&act=preview&id=%s',
			$environment->base,
			$idSerializer->getSerialized(),
			$pidSerializer->getSerialized()
		);

		if (!$data) {
			$_SESSION['AVISOTA_SEND_PREVIEW_TO_USER_EMPTY'] = true;

			header('Location: ' . $url);
			exit;
		}

		$recipient = new \Avisota\Recipient\MutableRecipient($data['email'], $data);

		$additionalData = array('view_online_link' => $url);

		/** @var \Avisota\Contao\Message\Core\Renderer\MessageRendererInterface $renderer */
		$renderer        = $container['avisota.message.renderer'];
		$messageTemplate = $renderer->renderMessage($message);
		$messageMail     = $messageTemplate->render($recipient, $additionalData);

		/** @var \Avisota\Transport\TransportInterface $transport */
		$transport = $GLOBALS['container']['avisota.transport.' . $message
			->getQueue()
			->getTransport()
			->getId()];

		$transport->send($messageMail);

		$event = new \ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent('avisota_message_preview');

		/** @var \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];
		$eventDispatcher->dispatch(\ContaoCommunityAlliance\Contao\Bindings\ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE, $event);

		$_SESSION['TL_CONFIRM'][] = sprintf($GLOBALS['TL_LANG']['avisota_message_preview']['previewSend'], $data['email']);

		header('Location: ' . $url);
		exit;
	}
}

$send_preview_to_user = new send_preview_to_user();
$send_preview_to_user->run();
