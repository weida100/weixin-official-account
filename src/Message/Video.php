<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/23 23:33
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\Message;

use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\Message;

class Video implements MessageInterface
{
    private array $attributes=[];

    /**
     * @param array $attributes
     * [
     *    "media_id":"MEDIA_ID",
     *    "thumb_media_id":"MEDIA_ID",
     *    "title":"TITLE",
     *    "description":"DESCRIPTION"
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
        return [
            'msgtype'=>Message::TYPE_VIDEO,
            'video'=>[
                'media_id'=>$this->attributes['media_id']??'',
                'thumb_media_id'=>$this->attributes['thumb_media_id']??'',
                'title'=>$this->attributes['title']??'',
                'description'=>$this->attributes['description']??'',
            ]
        ];
    }

}
