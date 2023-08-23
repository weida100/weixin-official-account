<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/24 00:01
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\Message;

use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\Message;

class MsgMenu implements MessageInterface
{
    private array $attributes=[];

    /**
     * @param array $attributes
     * [
         "head_content": "您对本次服务是否满意呢? ",
         "list": [
            [
               "id": "101",
               "content": "满意"
            ],
            [
               "id": "102",
               "content": "不满意"
            ]
         ],
         "tail_content": "欢迎再次光临"
     * ]
     * @author Weida
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function setAttributes(array|string $attributes): void
    {
        $this->attributes = (array)$attributes;
    }

    public function geAttributes(): array
    {
        return [
            'msgtype'=>Message::TYPE_MSGMENU,
            'msgmenu'=>[
                'head_content'=> $this->attributes['head_content']??'',
                'list'=> $this->attributes['list']??[],
                'tail_content'=> $this->attributes['tail_content']??'',
            ]
        ];
    }

}
