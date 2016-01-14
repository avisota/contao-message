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

    function __construct(Message $message, $content = '', $contentName = 'message.html', $contentType = 'text/html', $contentEncoding = 'utf-8')
    {
        parent::__construct($message);
        $this->content         = (string) $content;
        $this->contentName     = (string) $contentName;
        $this->contentType     = (string) $contentType;
        $this->contentEncoding = (string) $contentEncoding;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentEncoding
     */
    public function setContentEncoding($contentEncoding)
    {
        $this->contentEncoding = $contentEncoding;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentEncoding()
    {
        return $this->contentEncoding;
    }

    /**
     * @param string $contentName
     */
    public function setContentName($contentName)
    {
        $this->contentName = $contentName;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentName()
    {
        return $this->contentName;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }
}
