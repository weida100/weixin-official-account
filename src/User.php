<?php
declare(strict_types=1);
/**
 * Author: Weida
 * Date: 2023/7/27 23:34
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount;

class User
{
    private array $attributes=[];
    private string $accessToken="";
    public function __construct()
    {
    }

    public function setAttributes(array $params):static{
        $this->attributes = $params;
        return $this;
    }

    public function setAttribute(string $key,mixed $val):static{
        $this->attributes[$key] = $val;
        return $this;
    }

    public function getAttributes():array{
        return $this->attributes;
    }

    public function getOpenId(){
        return $this->attributes['open_id']??'';
    }

    public function setAccessToken(string $accessToken):static{
        $this->accessToken = $accessToken;
        return ;
    }

}
