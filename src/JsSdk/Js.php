<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/28 23:11
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\JsSdk;

use Psr\SimpleCache\CacheInterface;
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
     * @param array $menuList
     * @param bool $debug
     * @return array
     * @author Weida
     */
    public function buildConfig(string $url, array $jsApiList = [], array $menuList = [], bool $debug = false):array{
       return array_merge( [
            'debug'=>$debug,
            'appId'=>$this->appid,
            'jsApiList'=>$jsApiList,
            'menuList'=>$menuList,
        ],
        $this->jsTicket->getSignature($url)
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

}
