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


/** @var \Pimple $container */

/**
 * Define message renderer
 */
$container['avisota.message.renderer'] = $container->share(
	function () {
		return new \Avisota\Contao\Message\Core\Renderer\MessageRenderer();
	}
);

$container['avisota.message.tagReplacementEngine'] = $container->share(
	function () {
		$loader = new \Twig_Loader_Array(array());
		$twig   = new \Twig_Environment($loader, array('autoescape' => false));

		$lexer = new Twig_Lexer($twig, array(
			'tag_comment'   => array('{#', '#}'),
			'tag_block'     => array('{%', '%}'),
			'tag_variable'  => array('##', '##'),
			'interpolation' => array('#{', '}'),
		));
		$twig->setLexer($lexer);

		return new \Avisota\Contao\Message\Core\Renderer\TagReplacementService($twig);
	}
);
