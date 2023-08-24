<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/23 23:06
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\Message;
use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\Message;

class Text implements MessageInterface
{
    private array $attributes=[];
    public function __construct(string $content)
    {
        $this->attributes['content'] = $content;
    }

    public function setAttributes(array|string $attributes): void
    {
        $this->attributes['content'] = strval($attributes);
    }

    public function getAttributes(): array
    {
       return [
           'msgtype'=>Message::TYPE_TEXT,
           'text'=>[
               'content'=>$this->attributes['content']
           ]
       ];
    }

}
