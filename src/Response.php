<?php
declare(strict_types=1);

/**
 * Author: Weida
 * Date: 2023/7/20 7:34 PM
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount;


use Psr\Http\Message\ResponseInterface;

class Response implements ResponseInterface
{

    public function serve():ResponseInterface{
        return $this;
    }


}