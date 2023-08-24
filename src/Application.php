<?php
declare(strict_types=1);

namespace Weida\WeixinOfficialAccount;
/**
 * Author: Weida
 * Date: 2023/7/19 23:48
 * Email: sgenmi@gmail.com
 */

use Weida\WeixinCore\AbstractApplication;
use Weida\WeixinCore\Contract\ResponseInterface;

class Application extends AbstractApplication
{
    protected string $appType='officialAccount';
    protected Oauth2 $oauth2;

    /**
     * @return ResponseInterface
     * @author Weida
     */
    public function getResponse():ResponseInterface
    {
        if(empty($this->response)){
            $this->response = new Response(
                $this->getRequest(),
                $this->getEncryptor(),
                $this->getAppType() //用来区别事件
            );
        }
        $this->getResponseAfter();
        return $this->response;
    }

    /**
     * @return Oauth2
     * @author Sgenmi
     */
    public function getOauth():Oauth2{
        if(empty($this->oauth2)){
            $this->oauth2 = new Oauth2([
                'client_id'=>$this->getAccount()->getAppId(),
                'client_secret'=>$this->getAccount()->getSecret(),
                'redirect'=>$this->getConfig()->get('oauth.redirect_uri')
                ]
            );
            $scopes = $this->getConfig()->get('oauth.scope');
            if(!empty($scopes)){
                $this->oauth2->withScopes(is_array($scopes)?$scopes:[strval($scopes)]);
            }
            $this->oauth2->setHttpClient($this->getHttpClient());
        }
        return $this->oauth2;
    }

}
