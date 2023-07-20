<?php
declare(strict_types=1);

/**
 * Author: Weida
 * Date: 2023/7/20 7:34 PM
 * Email: sgenmi@gmail.com
 */

namespace Weida\WeixinOfficialAccount;

use \GuzzleHttp\Psr7\Response as Psr7Response;
use \Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Weida\WeixinCore\Contract\RequestInterface;
use Weida\WeixinCore\Contract\ResponseInterface;
use Weida\WeixinCore\Encoder;

class Response extends Psr7Response implements ResponseInterface
{
    protected RequestInterface $request;
    protected Encoder $encoder;
    protected array $handlers=[];

    public function __construct(RequestInterface $request,Encoder $encoder)
    {
        $this->request = $request;
        $this->encoder = $encoder;
        parent::__construct();
    }

    public function serve():PsrResponseInterface{
        return $this;
    }

    public function with(callable $callback):static{
        return $this;
    }


}
