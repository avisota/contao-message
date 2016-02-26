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

namespace Avisota\Contao\Message\Core\Template;

use Avisota\Contao\Entity\Message;

/**
 * Class MutablePreRenderedMessageTemplate
 *
 * @package Avisota\Contao\Message\Core\Template
 */
class MutablePreRenderedMessageTemplate extends AbstractPostRenderingMessageTemplate
{
    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var string
     */
    protected $contentEncoding;

    /**
     * @var string
     */
    protected $contentName;

    /**
     * @var string
     */
    protected $content;

    /**
     * MutablePreRenderedMessageTemplate constructor.
     *
     * @param Message $message
     * @param string  $content
     * @param string  $contentName
     * @param string  $contentType
     * @param string  $contentEncoding
     */
    public function __construct(
        Message $message,
        $content = '',
        $contentName = 'message.html',
        $contentType = 'text/html',
        $contentEncoding = 'utf-8'
    ) {
        parent::__construct($message);
        $this->content         = (string) $content;
        $this->contentName     = (string) $contentName;
        $this->contentType     = (string) $contentType;
        $this->contentEncoding = (string) $contentEncoding;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * Return the content type.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentEncoding
     *
     * @return $this
     */
    public function setContentEncoding($contentEncoding)
    {
        $this->contentEncoding = $contentEncoding;
        return $this;
    }

    /**
     * Return the content encoding.
     *
     * @return string
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * @param string $contentName
     *
     * @return $this
     */
    public function setContentName($contentName)
    {
        $this->contentName = $contentName;
        return $this;
    }

    /**
     * Get a name descriptor (file name) for this content,
     * e.g. "newsletter-hello-world.html".
     *
     * @return string
     */
    public function getContentName()
    {
        return $this->contentName;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get the (binary) content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
