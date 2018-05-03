<?php
namespace Taichuchu\AwsSms\Sms;

class Message
{
    const TYPE_TRANSACTIONAL = 'Transactional';
    const TYPE_PROMOTIONAL = 'Promotional';

    public $content;

    public $type = self::TYPE_PROMOTIONAL;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }
}