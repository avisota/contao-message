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

BackendUser::getInstance();

use ContaoCommunityAlliance\Contao\Bindings\Events\System\LoadLanguageFileEvent;
use ContaoCommunityAlliance\Contao\Bindings\ContaoEvents;

/**
 * Class preview
 */
class preview
{
    public function run()
    {
        global $container;

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher */
        $eventDispatcher = $GLOBALS['container']['event-dispatcher'];

        $messageRepository = \Contao\Doctrine\ORM\EntityHelper::getRepository('Avisota\Contao:Message');

        $messageId = \Input::get('id');
        /** @var \Avisota\Contao\Entity\Message $message */
        $message = $messageRepository->find($messageId);

        if (!$message) {
            header("HTTP/1.0 404 Not Found");
            echo '<h1>404 Not Found</h1>';
            exit;
        }

        $user = BackendUser::getInstance();
        $user->authenticate();

        $event = new \Avisota\Contao\Core\Event\CreateFakeRecipientEvent($message);
        $eventDispatcher->dispatch(\Avisota\Contao\Core\CoreEvents::CREATE_FAKE_RECIPIENT, $event);

        $recipient = $event->getRecipient();

        if ($message->getCategory()->getViewOnlinePage()) {
            $event = new LoadLanguageFileEvent('avisota_message');
            $eventDispatcher->dispatch(ContaoEvents::SYSTEM_LOAD_LANGUAGE_FILE, $event);

            $url         = sprintf(
                $GLOBALS['TL_LANG']['avisota_message']['viewOnline'],
                sprintf(
                    '%ssystem/modules/avisota-message/web/preview.php?id=%s',
                    \Environment::get('base'),
                    $message->getId()
                )
            );
        } else {
            $url = false;
        }

        $additionalData = array(
            'view_online_link' => $url,
        );

        /** @var \Avisota\Contao\Message\Core\Renderer\MessageRendererInterface $renderer */
        $renderer        = $container['avisota.message.renderer'];
        $messageTemplate = $renderer->renderMessage($message);
        $messagePreview  = $messageTemplate->renderPreview($recipient, $additionalData);

        header('Content-Type: ' . $messageTemplate->getContentType() . '; charset=' . $messageTemplate->getContentEncoding());
        header('Content-Disposition: inline; filename="' . $messageTemplate->getContentName() . '"');
        echo $messagePreview;
        exit;
    }
}

$preview = new preview();
$preview->run();
