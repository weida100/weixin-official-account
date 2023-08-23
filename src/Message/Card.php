<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/24 00:07
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\Message;

use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\Message;

class Card implements MessageInterface
{
    private array $attributes=[];
    public function __construct(string $card_id)
    {
        $this->attributes['card_id'] = $card_id;
    }

    public function setAttributes(array|string $attributes): void
    {
        $this->attributes['card_id'] = strval($attributes);
    }

    public function geAttributes(): array
    {
        return [
            'msgtype'=>Message::TYPE_WXCARD,
            'wxcard'=>[
                'card_id'=>$this->attributes['card_id']
            ]
        ];
    }

}
