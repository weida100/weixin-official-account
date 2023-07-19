<?php
declare(strict_types=1);

/**
 * Author: Weida
 * Date: 2023/7/19 23:48
 * Email: weida_dev@163.com
 */
use Weida\WeixinCore\AbstractApplication;
use Weida\WeixinCore\Account;
use Weida\WeixinCore\Contract\AccountInterface;
use Weida\WeixinCore\Encoder;

class Application extends AbstractApplication
{

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

    public function getEncryptor(): Encoder
    {
        // TODO: Implement getEncryptor() method.
    }

}
