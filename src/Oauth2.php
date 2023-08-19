<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/26 22:16
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount;

 use GuzzleHttp\Exception\GuzzleException;
 use RuntimeException;
 use Weida\Oauth2Core\AbstractApplication;
 use Weida\Oauth2Core\Contract\UserInterface;
 use Weida\Oauth2Core\User;

 class Oauth2 extends AbstractApplication
{
     protected array $scopes=['snsapi_base','snsapi_userinfo'];
     protected string $openid="";

    /**
     * @return string
     * @author Weida
     */
     protected function getAuthUrl(): string
     {
         $params=[
             'appid'=>$this->getConfig()->get('client_id'),
             'redirect_uri'=>$this->getConfig()->get('redirect'),
             'response_type'=>'code',
             'scope'=>implode(',',$this->scopes),
             'state'=> $this->state,
         ];
         //开放平台 登录网站
         if($params['scope']=='snsapi_login'){
             return sprintf('https://open.weixin.qq.com/connect/qrconnect?%s#wechat_redirect',http_build_query($params));
         }
         return sprintf('https://open.weixin.qq.com/connect/oauth2/authorize?%s#wechat_redirect',http_build_query($params));
     }

    /**
     * @param string $code
     * @return string
     * @author Weida
     */
     protected function getTokenUrl(string $code): string
     {
         $component_appid = $this->getConfig()->get('component_appid','');
         if(!empty($component_appid)){
             $params=[
                 'appid'=>$this->getConfig()->get('client_id'),
                 'code'=>$code,
                 'grant_type'=>'authorization_code',
                 'component_appid'=>$component_appid,
                 'component_access_token'=>$this->getConfig()->get('component_access_token'),
             ];
             return 'https://api.weixin.qq.com/sns/oauth2/component/access_token?'.http_build_query($params);
         }else{
             $params=[
                 'appid'=>$this->getConfig()->get('client_id'),
                 'secret'=>$this->getConfig()->get('client_secret'),
                 'code'=>$code,
                 'grant_type'=>'authorization_code'
             ];
             return 'https://api.weixin.qq.com/sns/oauth2/access_token?'.http_build_query($params);
         }
     }

    /**
     * @param string $accessToken
     * @return string
     * @author Weida
     */
     protected function getUserInfoUrl(string $accessToken): string
     {
         $params=[
             'access_token'=>$accessToken,
             'openid'=>$this->openid,
             'lang'=>'zh_CN'
         ];
         return 'https://api.weixin.qq.com/sns/userinfo?'.http_build_query($params);
     }

     /**
      * @param string $accessToken
      * @return UserInterface
      * @throws GuzzleException
      * @author Weida
      */
     public function userFromToken(string $accessToken): UserInterface
     {
         $url = $this->getUserInfoUrl($accessToken);
         $resp = $this->getHttpClient()->request('GET',$url);
         if($resp->getStatusCode()!=200){
             throw new RuntimeException('Request userinfo exception');
         }
         $arr = json_decode($resp->getBody()->getContents(),true);
         if (empty($arr['openid'])) {
             throw new RuntimeException('Failed to get userinfo: ' . json_encode($arr, JSON_UNESCAPED_UNICODE));
         }
         return new User([
             'uid'=>$arr['openid'],
             'nickname'=>$arr['nickname'],
             'headimgurl'=>$arr['avatar'],
             'unionid'=>$arr['unionid']??'',
         ]);
     }

     /**
      * @param string $code
      * @return UserInterface
      * @throws GuzzleException
      * @author Sgenmi
      */
     public function userFromCode(string $code): UserInterface
     {
         $tokenArr = $this->tokenFromCode($code);
         if(!empty($tokenArr['openid'])){
             $this->openid = $tokenArr['openid'];
         }
         return $this->userFromToken($tokenArr['access_token']);
     }
 }
