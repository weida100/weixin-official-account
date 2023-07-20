<?php
declare(strict_types=1);

namespace Weida\WeixinOfficialAccount;
/**
 * Author: Weida
 * Date: 2023/7/19 23:48
 * Email: weida_dev@163.com
 */

use GuzzleHttp\Psr7\HttpFactory;
use Weida\WeixinCore\AbstractApplication;
use Weida\WeixinCore\Account;
use Weida\WeixinCore\Contract\AccountInterface;
use Weida\WeixinCore\Encoder;
use Weida\WeixinCore\Contract\ResponseInterface;
class Application extends AbstractApplication
{
    protected ?ResponseInterface $response=null;

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

    public function getEncryptor(): Encoder
    {
        // TODO: Implement getEncryptor() method.
    }

}
