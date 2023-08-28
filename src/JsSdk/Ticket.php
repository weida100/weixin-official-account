<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/8/28 23:13
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount\JsSdk;

use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;
use Throwable;
use Weida\WeixinCore\Contract\WithAccessTokenClientInterface;

class Ticket
{
    private string $appId='';
    private ?CacheInterface $cache=null;
    private ?WithAccessTokenClientInterface $httpClient=null;
    private string $cacheKey='';
    private string $ticket='';
    private string $ticketType;

    public function __construct(
        string $appId,$ticketType='jsapi', ?CacheInterface $cache=null, ?WithAccessTokenClientInterface $httpClient=null
    )
    {
        $this->appId = $appId;
        $this->ticketType = $ticketType;
        $this->cache = $cache;
        $this->httpClient = $httpClient;
    }

    public function setCacheKey(string $key):static{
        $this->cacheKey = $key;
        return $this;
    }

    /**
     * @return string
     * @author Weida
     */
    public function getCacheKey(): string
    {
        if(empty($this->cacheKey)){
            $this->cacheKey = sprintf("js:sdk:ticket:%s:%s",$this->ticketType,$this->appId);
        }
        return $this->cacheKey;
    }

    /**
     * @param bool $isRefresh
     * @return string
     * @throws Throwable|InvalidArgumentException
     * @author Weida
     */
    public function getTicket(bool $isRefresh=false): string
    {
        if(!empty($this->ticket)){
            return $this->ticket;
        }
        if(!$isRefresh){
            $ticket = $this->cache->get($this->getCacheKey());
            if (!empty($ticket)) {
                return $ticket;
            }
        }
        return $this->refresh();
    }

    /**
     * 强制设置
     * @param string $ticket
     * @return $this
     * @author Weida
     */
    public function setTicket(string $ticket): static
    {
        $this->ticket = $ticket;
        return $this;
    }

    /**
     * @return string
     * @throws Throwable|InvalidArgumentException
     * @author Weida
     */
    private function refresh():string{
            $apiUrl = '/cgi-bin/ticket/getticket';
            $params = [
                'query' => [
                    'type' =>$this->ticketType,
                ],
            ];
            $method = "GET";

        $resp = $this->httpClient->request($method, $apiUrl,$params);
        if($resp->getStatusCode()!=200){
            throw new RuntimeException('Request access_token exception');
        }
        $arr = json_decode($resp->getBody()->getContents(),true);

        if (empty($arr['ticket'])) {
            throw new RuntimeException('Failed to get ticket: ' . json_encode($arr, JSON_UNESCAPED_UNICODE));
        }
        $this->cache->set($this->getCacheKey(), $arr['ticket'], intval($arr['expires_in']));
        return $arr['ticket'];
    }

    /**
     * @return int
     * @author Weida
     */
    public function expiresTime(): int
    {
        return  $this->cache->ttl($this->getCacheKey());
    }

    /**
     * @return array
     * @author Weida
     */
    public function getParams(): array
    {
        return [
            'app_id'=>$this->appId,
            'ticketType'=>$this->ticketType,
            'cache'=>$this->cache,
            'httpClient'=>$this->httpClient,
        ];
    }

    public function getSignature(string $url):array {
        $time = time();
        $nonce = md5(session_create_id());
        return [
            'url' => $url,
            'nonceStr' => $nonce,
            'timestamp' => $time,
            'appId' => $this->appId,
            'signature' => sha1(sprintf(
                'jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s', $this->getTicket(), $nonce, $time, $url)),
        ];
    }

}
