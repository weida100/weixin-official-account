<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/23 23:32
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\Message;

use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\Message;

class Voice implements MessageInterface
{
    private array $attributes=[];
    public function __construct(string $media_id)
    {
        $this->attributes['media_id'] = $media_id;
    }

    public function setAttributes(array|string $attributes): void
    {
        $this->attributes['media_id'] = strval($attributes);
    }

    public function geAttributes(): array
    {
        return [
            'msgtype'=>Message::TYPE_VOICE,
            'voice'=>[
                'media_id'=>$this->attributes['media_id']
            ]
        ];
    }

}
