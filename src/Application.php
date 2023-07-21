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
    protected ?ResponseInterface $response=null;

    /**
     * @return AccountInterface
     * @author Sgenmi
     */
    public function getAccount(): AccountInterface
    {
        if (!$this->account){
            $this->account = new Account(
                $this->config->get('app_id'),
                $this->config->get('secret'),
                $this->config->get('token'),
                $this->config->get('aes_key'),
            );
        }
        return $this->account;
    }

    /**
     * @return ResponseInterface
     * @author Sgenmi
     */
    public function getResponse():ResponseInterface
    {
        if(empty($this->response)){
            $this->response = new Response(
                $this->getRequest(),
                $this->getEncryptor()
            );
        }
        return $this->response;
    }
    public function getServer():Response{
        return $this->getResponse();
    }

    /**
     * @return EncryptorInterface
     * @author Sgenmi
     */
    public function getEncryptor(): EncryptorInterface
    {
        if(empty($this->encryptor)){
            $this->encryptor = new Encryptor(
                $this->getAccount()->getAppId(),
                $this->getAccount()->getToken(),
                $this->getAccount()->getAesKey(),
                $this->getAccount()->getAppId()
            );
        }
        return $this->encryptor;
    }

}
