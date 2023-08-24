<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/23 23:58
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\Message;

use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\Message;

class NewsArticle implements MessageInterface
{
    private array $attributes=[];
    public function __construct(string $article_id)
    {
        $this->attributes['article_id'] = $article_id;
    }

    public function setAttributes(array|string $attributes): void
    {
        $this->attributes['article_id'] = strval($attributes);
    }

    public function getAttributes(): array
    {
        return [
            'msgtype'=>Message::TYPE_MPNEWSARTICLE,
            'mpnewsarticle'=>[
                'article_id'=>$this->attributes['article_id']
            ]
        ];
    }

}
