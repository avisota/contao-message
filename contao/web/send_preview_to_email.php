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

use Avisota\Contao\Entity\Message;

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
define('BE_USER_LOGGED_IN', false);
require($dir . '/system/initialize.php');

BackendUser::getInstance();

use ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;
use ContaoCommunityAlliance\DcGeneral\Data\ModelId;

/**
 * Class send_preview_to_email
 */
class send_preview_to_email extends \Avisota\Contao\Message\Core\Send\AbstractWebRunner
{
    /**
     * @param Message     $message
     * @param BackendUser $user
     *
     * @return mixed|void
     */
    protected function execute(Message $message, \BackendUser $user)
    {
        global $container;

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        $email = \Input::get('recipient_email');

        $idSerializer = new ModelId('orm_avisota_message', $message->getId());
        $pidSerializer = new ModelId('orm_avisota_message_category', $message->getCategory()->getId());

        if (!$email) {
            $_SESSION['AVISOTA_SEND_PREVIEW_TO_EMAIL_EMPTY'] = true;

            header(
                'Location: ' . sprintf(
                    '%scontao/main.php?do=avisota_newsletter&table=orm_avisota_message&act=preview&id=%s&pid=%s',
                    \Environment::get('base'),
                    $idSerializer->getSerialized(),
                    $pidSerializer->getSerialized()
                )
            );
            exit;
        }

        $url = sprintf(
            '%scontao/main.php?do=avisota_newsletter&table=orm_avisota_message&act=preview&id=%s&pid=%s',
            \Environment::get('base'),
            $idSerializer->getSerialized(),
            $pidSerializer->getSerialized()
        );

        if ($message->getCategory()->getViewOnlinePage()) {
            $event = new LoadLanguageFileEvent('avisota_message');
            $eventDispatcher->dispatch(ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE, $event);

            $viewOnlineLink = sprintf($GLOBALS['TL_LANG']['avisota_message']['viewOnline'], $url);
        } else {
            $viewOnlineLink = false;
        }

        $event = new \Avisota\Contao\Core\Event\CreateFakeRecipientEvent($message);
        $eventDispatcher->dispatch(\Avisota\Contao\Core\CoreEvents::CREATE_FAKE_RECIPIENT, $event);

        $recipient = $event->getRecipient();
        $recipient->setEmail($email);

        $additionalData = array('view_online_link' => $viewOnlineLink);

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

        $event = new \ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent(
            'avisota_message_preview'
        );

        $eventDispatcher->dispatch(
            \ContaoCommunityAlliance\Contao\Bindings\ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE,
            $event
        );

        $_SESSION['TL_CONFIRM'][] = sprintf($GLOBALS['TL_LANG']['avisota_message_preview']['previewSend'], $email);

        header('Location: ' . $url);
        exit;
    }
}

$send_preview_to_email = new send_preview_to_email();
$send_preview_to_email->run();
