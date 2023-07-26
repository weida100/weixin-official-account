<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/26 22:16
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount;

 class Oauth2
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $componentAppid='';
    private string|array $scope="";
    private string $state="";

    public function __construct(string $clientId,string $clientSecret,string $redirectUri='',string $scope='snsapi_base')
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
     * @param string $redirect_uri
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

}
