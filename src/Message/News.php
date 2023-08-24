<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/23 23:51
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\Message;

use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\Message;

class News implements MessageInterface
{
    private array $attributes=[];

    /**
     * @param array $attributes
     * [
            [
              "title"=>"Happy Day",
              "description"=>"Is Really A Happy Day",
              "url"=>"URL", //可以外链地址
              "picurl"=>"PIC_URL" //可以外链地址
            ]
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

    public function getAttributes(): array
    {
        $items =[];
        foreach ($this->attributes  as $v){
            $items[]=[
                'title'=>strval($v['title']??''),
                'description'=>strval($v['description']??''),
                'url'=>strval($v['url']??''),
                'picurl'=>strval($v['picurl']??''),
            ];
        }
        return [
            'msgtype'=>Message::TYPE_NEWS,
            'news'=>[
                'articles'=>$items
            ]
        ];
    }

}
