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

    /**
     * @param string $ticketType
     * @return $this
     * @author Weida
     */
    public function setType(string $ticketType):static {
        $this->ticketType = $ticketType;
        return $this;
    }

    /**
     * @return string
     * @author Weida
     */
    public function getType():string {
        return $this->ticketType;
    }

    /**
     * @param string $url
     * @return array
     * @throws Throwable
     * @author Weida
     */
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

    /**
     * cardExt本身是一个JSON字符串，是商户为该张卡券分配的唯一性信息
     * 将 api_ticket、timestamp、card_id、code、openid、nonce_str的value值进行字符串的字典序排序
     * 将所有参数字符串拼接成一个字符串进行sha1加密，得到signature
     * @param string $cardId
     * @param string $code
     * @param string $openId
     * @param string $outerStr
     * @param string $fixedBeginTimestamp
     * @return string
     * @throws Throwable
     * @author Weida
     */
    public function getCardExt(
        string $cardId='',string $code='', string $openId='',string $outerStr='',string $fixedBeginTimestamp=''
    ):string{
        $time = time();
        $nonce = md5(session_create_id());
        return json_encode( [
            'card_id'=>$cardId,
            'code'=>$code,
            'openid'=>$openId,
            'timestamp'=>$time,
            'nonce_str'=>$nonce,
            'fixed_begintimestamp'=>$fixedBeginTimestamp,
            'outer_str'=>$outerStr,
            'signature'=>$this->getCardSignature([
                'card_id' => $cardId,
                'code'=>$code,
                'openid'=>$openId,
                'nonceStr' => $nonce,
                'timestamp' => $time
            ])
        ],JSON_UNESCAPED_UNICODE);
    }

    /**
     * api_ticket、appid、location_id、timestamp、nonce_str、card_id、card_type的value值进行字符串的字典序排序。
     * 将所有参数字符串拼接成一个字符串进行sha1加密，得到cardSign。
     * @param string $cardId
     * @param string $cardType
     * @param string $locationId
     * @return string[]
     * @throws Throwable
     * @author Weida
     */
    public function getCardSign(string $cardId='',string $cardType='',string $locationId=''):array{
        $time = time();
        $nonce = md5(session_create_id());
        return [
            'cardId'=> $cardId,
            'timestamp'=>$time,
            'nonceStr'=>$nonce,
            'signType'=>'SHA1',
            'cardSign'=>$this->getCardSignature([
                'appid'=>$this->appId,
                'location_id'=>$locationId,
                'timestamp'=>$time,
                'nonce_str'=>$nonce,
                'card_id'=>$cardId,
                'card_type'=>$cardType
            ])
        ];
    }

    /**
     * @param array $params
     * @return string
     * @throws Throwable
     * @author Weida
     */
    public function getCardSignature(array $params):string{
        if($this->ticketType!='wx_card'){
            throw new InvalidArgumentException("invalid ticket type ");
        }
        $params['api_ticket'] = $this->getTicket();
        $params= array_filter($params);
        sort($params);
        return sha1(implode('',$params));
    }


}
