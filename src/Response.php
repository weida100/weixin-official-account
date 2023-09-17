<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/24 22:23
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount;

use Weida\WeixinCore\Contract\MessageInterface;
use Weida\WeixinCore\AbstractResponse;
use Weida\WeixinOfficialAccount\Message\Message;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Psr\Http\Message\MessageInterface as PsrMessageInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
class Response extends AbstractResponse
{
    /**
     * @return PsrMessageInterface|PsrResponseInterface
     * @author Weida
     */
    public function response():PsrResponseInterface
    {
        $resp = new Psr7Response(200,[],'success');
        if (!empty($this->params['echostr'])) {
            return $resp->withBody($this->createBody($this->params['echostr']));
        }
        $message = $this->getDecryptedMessage();
        $response = $this->middleware->handler($this,$message);
        if(empty($response)){
            return $resp;
        }
        if(is_string($response) || is_numeric($response)){
            $response = Message::Text((string)$response);
        }elseif (is_array($response)){
            if(!empty($response['msgtype']) || !empty($response['MsgType'])){
                $response = new Message($response);
            }
        }
        if ($response instanceof MessageInterface){
            $resp=$resp->withHeader('Content-Type', 'application/xml;charset=utf-8');
            if(!($response instanceof Message)){
                $response = new Message($response->getAttributes());
            }
            $content = $response->toXmlReply($message,$this->encryptor);
            $resp = $resp->withBody($this->createBody($content));
        }
        return $resp;
    }

}
