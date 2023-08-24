<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/24 21:51
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\Message;

use Weida\WeixinCore\Contract\EncryptorInterface;
use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\Message as CoreMessage;
use Weida\WeixinCore\XML;

class Message implements MessageInterface
{
    private array $attributes=[];

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $card_id
     * @return Card
     * @author Weida
     */
    public static function Card(string $card_id):Card{
        return new Card($card_id);
    }

    /**
     * @param string $media_id
     * @return Image
     * @author Weida
     */
    public static function Image(string $media_id):Image {
        return new Image($media_id);
    }

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
     * @return MsgMenu
     * @author Weida
     */
    public static function MsgMenu(array $attributes):MsgMenu {
        return new MsgMenu($attributes);
    }

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
     * @return Music
     */
    public static function Music(array $attributes):Music {
        return new Music($attributes);
    }

    /**
     * @return News
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
    public static function News(array $attributes):News {
        return new News($attributes);
    }

    /**
     * @param string $article_id
     * @return NewsArticle
     * @author Weida
     */
    public static function NewsArticle(string $article_id):NewsArticle
    {
        return new NewsArticle($article_id);
    }

    /**
     * @param string $content
     * @return Text
     * @author Weida
     */
    public static function Text(string $content):Text
    {
        return new Text($content);
    }

    /**
     * @param array $attributes
     * [
     *    "media_id":"MEDIA_ID",
     *    "thumb_media_id":"MEDIA_ID",
     *    "title":"TITLE",
     *    "description":"DESCRIPTION"
     * ]
     * @author Weida
     * @return Video
     */
    public static function Video(array $attributes):Video
    {
        return new Video($attributes);
    }

    /**
     * @param string $media_id
     * @return Voice
     * @author Weida
     */
    public static function Voice(string $media_id):Voice {
        return new Voice($media_id);
    }

    public function setAttributes(array|string $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $message
     * @param Encryptor|null $encryptor
     * @return string
     * @author Weida
     */
    public function toXmlReply(array $message, ?EncryptorInterface $encryptor = null):string {
        $ret = [
            'ToUserName' => $message['FromUserName'],
            'FromUserName' => $message['ToUserName'],
            'CreateTime' => time(),
        ];
        //兼容自定义全格式
        if(isset($this->attributes['MsgType'])){
            $ret = array_merge($ret,$this->attributes);
        }else{
            $messageType = $this->attributes['msgtype']??'';
            if(empty($messageType)){
                throw new \RuntimeException('No msgtype found');
            }
            switch ($messageType){
                case CoreMessage::TYPE_TEXT:
                    $ret['MsgType'] = CoreMessage::TYPE_TEXT;
                    $ret['Content'] = $this->attributes['text']['content'];
                    break;
                case CoreMessage::TYPE_IMAGE:
                    $ret['MsgType'] = CoreMessage::TYPE_IMAGE;
                    $ret['Image']['MediaId'] = $this->attributes['image']['media_id'];
                    break;
                case CoreMessage::TYPE_VOICE:
                    $ret['MsgType'] = CoreMessage::TYPE_VOICE;
                    $ret['Voice']['MediaId'] = $this->attributes['voice']['media_id'];
                    break;
                case CoreMessage::TYPE_VIDEO:
                    $ret['MsgType'] = CoreMessage::TYPE_VIDEO;
                    $ret['Video']['MediaId'] = $this->attributes['video']['media_id'];
                    $ret['Video']['Title'] = $this->attributes['video']['title'];
                    $ret['Video']['Description'] = $this->attributes['video']['description'];
                    break;
                case CoreMessage::TYPE_MUSIC:
                    $ret['MsgType'] = CoreMessage::TYPE_MUSIC;
                    $ret['Music']['Title'] = $this->attributes['music']['title'];
                    $ret['Music']['Description'] = $this->attributes['music']['description'];
                    $ret['Music']['MusicUrl'] = $this->attributes['music']['musicurl'];
                    $ret['Music']['HQMusicUrl'] = $this->attributes['music']['hqmusicurl'];
                    $ret['Music']['ThumbMediaId'] = $this->attributes['music']['thumb_media_id'];
                    break;
                case CoreMessage::TYPE_NEWS:
                    $ret['MsgType'] = CoreMessage::TYPE_MUSIC;
                    $ret['ArticleCount'] = isset($this->attributes['news']['articles'])?count($this->attributes['news']['articles']):0;
                    $ret['Articles']['item']=[];
                    if($ret['ArticleCount']){
                        foreach ($this->attributes['news']['articles'] as $v){
                            $ret['Articles']['item'][]=[
                                'Title'=>$v['title'],
                                'PicUrl'=>$v['picurl'],
                                'Url'=>$v['url'],
                            ];
                        }
                    }
                    break;
            }
        }
        $xmlStr = XML::generate(array_filter($ret));
        return $encryptor?$encryptor->encrypt($xmlStr):$xmlStr;
    }

    public function toXml():string{
        return XML::generate($this->attributes);
    }

    public function toJson():string{
        return json_encode($this->attributes,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }




}
