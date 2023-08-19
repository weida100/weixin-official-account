<?php
declare(strict_types=1);

namespace Weida\WeixinOfficialAccount;
/**
 * Author: Weida
 * Date: 2023/7/19 23:48
 * Email: weida_dev@163.com
 */

use Weida\WeixinCore\AbstractApplication;

class Application extends AbstractApplication
{
    protected string $appType='officialAccount';
    protected Oauth2 $oauth2;

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
