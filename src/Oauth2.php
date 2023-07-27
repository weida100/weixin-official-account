<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/26 22:16
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount;

 use Weida\WeixinCore\Contract\HttpClientInterface;

 class Oauth2
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $componentAppid='';
    private string|array $scope="";
    private string $state="";
    private ?HttpClientInterface $httpClient=null;
    private User $user;
    public function __construct(
        string $clientId,string $clientSecret,string $redirectUri='',string|array $scope='snsapi_base',
        ?HttpClientInterface $httpClient=null
    )
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri=$redirectUri;
        $this->scope = $scope;
    }

     /**
      * @param string|array $scope
      * @return $this
      * @author Weida
      */
    public function setScope(string|array $scope):static{
        $this->scope = $scope;
        return $this;
    }

     /**
      * @param string $state
      * @return $this
      * @author Weida
      */
    public function setState(string $state):static{
        $this->state = $state;
        return $this;
    }

    /**
     * 设置第三方平台网页授权
     * @return $this
     * @author Weida
     */
    public function setComponentAppid(string $appid):static{
        $this->componentAppid = $appid;
        return $this;
    }

     /**
      * 为后面weida/oauth2-core/interface做准备,
      * 各平台oauth2一个类继承公共abstract implements Oauth2interface
      *
      * @param string $redirectUri
      * @return string
      * @author Weida
      */
    public function redirect(string $redirectUri=''):string{
        $params=[
            'appid'=>$this->clientId,
            'redirect_uri'=>!empty($redirectUri) ?$redirectUri:$this->redirectUri,
            'response_type'=>'code',
            'scope'=>is_array($this->scope)?implode(',',$this->scope):$this->scope,
            'state'=>$this->state,
            'component_appid'=>$this->componentAppid
        ];
        if(empty($params['redirect_uri'])){
            throw new \RuntimeException("redirect_uri is empty");
        }
        $url = sprintf("https://open.weixin.qq.com/connect/oauth2/authorize?%s#wechat_redirect",
            http_build_query(array_filter($params)));
        return $url;
    }

    //以下为了兼容

     public function withRedirectUrl(string $redirectUri=''):static{
        $this->redirectUri = $this->redirectUri;
        return $this;
     }

     public function withState(string $state): static
     {
         $this->state = $state;
         return $this;
     }
     public function scopes(array $scopes): static
     {
         $this->scopes = $scopes;
         return $this;
     }

     protected function getAccessToken($code):string{
        $params=[
            'query'=>[
                'appid'=>$this->clientId,
                'secret'=>$this->clientSecret,
                'code'=>$code,
                'grant_type'=>'authorization_code'
            ]
        ];
        $resp = $this->httpClient->request('GET','/sns/oauth2/access_token',$params);
        $resp = $this->httpClient->request($method, $apiUrl,$params);
        if($resp->getStatusCode()!=200){
         throw new RuntimeException('Request oauth2 access_token exception');
        }
        $arr = json_decode($resp->getBody()->getContents(),true);

        if (empty($arr['access_token'])) {
         throw new RuntimeException('Failed to get access_token: ' . json_encode($arr, JSON_UNESCAPED_UNICODE));
        }
        $this->getUser()->setAccessToken($arr['access_token']);
        $this->getUser()->setAttribute('open_id',$arr['open_id']??'');
        return $arr['access_token'];
     }

     /**
      * @param string $token
      * @return User
      * @throws \Throwable
      * @author Weida
      */
     public function userFromToken(string $token):User{
         $params=[
             'query'=>[
                 'access_token'=>$token,
                 'openid'=>$this->getUser()->getOpenId(),
                 'lang'=>'zh_CN'
             ]
         ];
         $resp = $this->httpClient->request('GET','/sns/userinfo',$params);
         if($resp->getStatusCode()!=200){
             throw new RuntimeException('Request sns/userinfo exception');
         }
         $arr = json_decode($resp->getBody()->getContents(),true);
         if (empty($arr['openid'])) {
             throw new RuntimeException('Failed to get userinfo: ' . json_encode($arr, JSON_UNESCAPED_UNICODE));
         }
         $this->getUser()->setAttributes($arr);
         return $this->getUser();
     }

     /**
      * @return User
      * @author Weida
      */
     protected function getUser():User{
        if(empty($this->user)){
            $this->user =  new User();
        }
        return $this->user;
     }
     /**
      * @param string $code
      * @return User
      * @author Weida
      */
     public function userFromCode(string $code):User{
        $token = $this->getAccessToken($code);
        return $this->userFromToken($token);
     }

}
