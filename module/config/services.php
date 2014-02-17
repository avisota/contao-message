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


/**
 * Define message renderer
 */
$container['avisota.renderer'] = $container->share(
	function () {
		return new \Avisota\Contao\Core\Message\Renderer\MessagePreRendererChain($GLOBALS['AVISOTA_MESSAGE_RENDERER']);
	}
);
