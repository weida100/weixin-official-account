<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/23 23:44
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\Message;

use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\Message;

class Music implements MessageInterface
{
    private array $attributes=[];

    /**
     * @param array $attributes
     * [
     *   "title":"MUSIC_TITLE",
     *   "description":"MUSIC_DESCRIPTION",
     *   "musicurl":"MUSIC_URL",
     *   "hqmusicurl":"HQ_MUSIC_URL",
     *   "thumb_media_id":"THUMB_MEDIA_ID"
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
            'msgtype'=>Message::TYPE_MUSIC,
            'music'=>[
                'title'=>$this->attributes['title']??'',
                'description'=>$this->attributes['description']??'',
                'musicurl'=>$this->attributes['musicurl']??'',
                'hqmusicurl'=>$this->attributes['hqmusicurl']??'',
                'thumb_media_id'=>$this->attributes['thumb_media_id']??'',
            ]
        ];
    }

}
