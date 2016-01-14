<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message
 * @license    LGPL-3.0+
 * @filesource
 */


// TODO
/*
$GLOBALS['TL_LANG']['avisota_message_preview']['preview_mode']         = array(
	'Vorschaumodus',
	'Den Vorschaumodus wechseln.',
	'HTML Vorschau',
	'Plain Text Vorschau'
);
$GLOBALS['TL_LANG']['avisota_message_preview']['preview_personalized'] = array(
	'Personalisieren',
	'Die Vorschau personalisieren.',
	'Keine',
	'Anonym',
	'Persönlich'
);
*/

/**
 * Fields
 */
$GLOBALS['TL_LANG']['avisota_message_preview']['sendPreviewToUser']  = array(
	'Send to user',
	'Send an example to a user.'
);
$GLOBALS['TL_LANG']['avisota_message_preview']['sendPreviewToEmail'] = array(
	'Send to email',
	'Send an example to an email.'
);


/**
 * Legends
 */
$GLOBALS['TL_LANG']['avisota_message_preview']['headline']       = 'View and send message';
$GLOBALS['TL_LANG']['avisota_message_preview']['previewToUser']  = 'Send preview to user';
$GLOBALS['TL_LANG']['avisota_message_preview']['previewToEmail'] = 'Send preview to email';
$GLOBALS['TL_LANG']['avisota_message_preview']['sendNow']        = 'Send message now';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['avisota_message_preview']['previewInNewWindow'] = 'View in new window';
$GLOBALS['TL_LANG']['avisota_message_preview']['sendPreview']        = 'Send preview';
$GLOBALS['TL_LANG']['avisota_message_preview']['sendMessage']        = 'Send message to recipients now';


/**
 * Help
 */
$GLOBALS['TL_LANG']['avisota_message_preview']['helpSendNow']        = 'Send this message to %d recipients immediately.';


/**
 * Messages
 */
$GLOBALS['TL_LANG']['avisota_message_preview']['previewSend'] = 'Preview send to %s';
$GLOBALS['TL_LANG']['avisota_message_preview']['confirmSend'] = 'Are you sure you wan\'t to send this newsletter now? The sending process will start immediately!';
$GLOBALS['TL_LANG']['avisota_message_preview']['messagesEnqueued'] = '(turn %2$d) %1$d messages enqueued.';
