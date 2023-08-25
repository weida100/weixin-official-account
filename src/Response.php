<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/24 22:23
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount;

use Psr\Http\Message\ResponseInterface;
use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\AbstractResponse;
use Weida\WeixinOfficialAccount\Message\Message;
class Response extends AbstractResponse
{
    /**
     * @return ResponseInterface
     * @author Weida
     */
    public function serve():ResponseInterface{
        if (!empty($this->params['echostr'])) {
            return $this->sendBody($this->params['echostr']);
        }
        $message = $this->getDecryptedMessage();
        $response = $this->middleware->handler($this,$message);
        if(empty($response)){
            return $this;
        }
        if(is_string($response) || is_numeric($response)){
            $response = Message::Text((string)$response);
        }elseif (is_array($response)){
            if(!empty($response['msgtype']) || !empty($response['MsgType'])){
                $response = new Message($response);
            }
        }
        if ($response instanceof MessageInterface){
            $this->withHeader('Content-Type', 'application/xml;charset=utf-8');
            if(!($response instanceof Message)){
                $response = new Message($response->getAttributes());
            }
            $content = $response->toXmlReply($message,$this->encryptor);
            return $this->sendBody($content);
        }
        return $this;
    }

}
