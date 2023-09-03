<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/28 23:11
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\JsSdk;

use Psr\SimpleCache\CacheInterface;
use Throwable;
use Weida\WeixinCore\Contract\WithAccessTokenClientInterface;

class Js
{
    private string $appid;
    private ?Ticket $jsTicket=null;
    private ?CacheInterface $cache=null;
    private ?WithAccessTokenClientInterface $httpClient=null;

    public function __construct(string $appid,?CacheInterface $cache=null, ?WithAccessTokenClientInterface $httpClient=null)
    {
        $this->appid = $appid;
        $this->cache = $cache;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $url
     * @param array $jsApiList
     * @param array $openTagList
     * @param bool $debug
     * @return array
     * @throws Throwable
     * @author Weida
     */
    public function buildConfig(string $url, array $jsApiList = [], array $openTagList = [], bool $debug = false):array{
       return array_merge( [
            'debug'=>$debug,
            'appId'=>$this->appid,
            'jsApiList'=>$jsApiList,
            'openTagList'=>$openTagList,
        ],
        $this->getJsTicket()->getSignature($url)
       );
    }

    /**
     * @param string $cardId
     * @param string $code
     * @param string $openId
     * @param string $outerStr
     * @param string $fixedBeginTimestamp
     * @return string
     * @throws Throwable
     * @author Weida
     */
    public function buildCardExt(
        string $cardId='',string $code='', string $openId='',string $outerStr='',string $fixedBeginTimestamp=''):string{
        $this->setType('wx_card');
        return $this->getJsTicket()->getCardExt($cardId,$code,$openId,$outerStr,$fixedBeginTimestamp);
    }

    /**
     * @param string $shopId
     * @param string $cardId
     * @param string $cardType
     * @param string $locationId
     * @return string[]
     * @throws Throwable
     * @author Weida
     */
    public function buildCardConfig(string $shopId='',string $cardId='',string $cardType='',string $locationId=''):array{
        $this->setType('wx_card');
        return array_merge([
                'shopId'=>$shopId,
                'cardType'=>$cardType,
            ], $this->getJsTicket()->getCardSign($cardId, $cardType, $locationId)
        );
    }

    /**
     * JS SDK ticket
     * @return Ticket
     * @author Weida
     */
    public function getJsTicket():Ticket {
        if(empty($this->jsTicket)){
            $this->jsTicket = new Ticket(
                $this->appid,
                'jsapi',
                $this->cache,
                $this->httpClient
            );
        }
        return $this->jsTicket;
    }

    /**
     * JS SDK Ticket
     * @param Ticket $ticket
     * @return $this
     * @author Weida
     */
    public function setJsTicket(Ticket $ticket):static {
        $this->jsTicket = $ticket;
        return $this;
    }

    /**
     * @param string $ticketType
     * @return $this
     * @author Weida
     */
    public function setType(string $ticketType):static {
        $this->getJsTicket()->setType($ticketType);
        return $this;
    }

    /**
     * @return string
     * @author Weida
     */
    public function getType():string {
        return $this->getJsTicket()->getType();
    }

}
