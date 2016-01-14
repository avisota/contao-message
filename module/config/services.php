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
        $debug = $GLOBALS['TL_CONFIG']['debugMode'] || $GLOBALS['TL_CONFIG']['twigDebugMode'];

        $loader = new \Twig_Loader_Array(array());
        $twig   = new \Twig_Environment(
            $loader,
            array(
                'autoescape' => false,
                'debug'      => $debug,
            )
        );

        // Add debug extension
        if ($debug || $GLOBALS['TL_CONFIG']['twigDebugExtension']) {
            $twig->addExtension(new Twig_Extension_Debug());
        }

        $lexer = new Twig_Lexer(
            $twig, array(
            'tag_comment'   => array('{#', '#}'),
            'tag_block'     => array('{%', '%}'),
            'tag_variable'  => array('##', '##'),
            'interpolation' => array('#{', '}'),
        )
        );
        $twig->setLexer($lexer);

        return new \Avisota\Contao\Message\Core\Renderer\TagReplacementService($twig);
    }
);
