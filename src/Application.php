<?php
declare(strict_types=1);

namespace Weida\WeixinOfficialAccount;
/**
 * Author: Weida
 * Date: 2023/7/19 23:48
 * Email: weida_dev@163.com
 */

use Weida\WeixinCore\AbstractApplication;
use Weida\WeixinCore\Account;
use Weida\WeixinCore\Contract\AccountInterface;
use Weida\WeixinCore\Contract\EncryptorInterface;
use Weida\WeixinCore\Encoder;
use Weida\WeixinCore\Contract\ResponseInterface;
use Weida\WeixinCore\Encryptor;

class Application extends AbstractApplication
{
    protected string $appType='officialAccount';
    protected Oauth2 $oauth2;


    public function getOauth():Oauth2{
        if(empty($this->oauth2)){
            $this->oauth2 = new Oauth2(
              $this->getAccount()->getAppId(),
                $this->getAccount()->getSecret(),
                $this->getConfig()->get('redirect_uri'),
                $this->getConfig()->get('scope','snsapi_base'),
            );
        }
        return $this->oauth2;
    }

}
