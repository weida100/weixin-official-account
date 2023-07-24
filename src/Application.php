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


}
