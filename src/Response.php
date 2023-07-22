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
use Psr\Http\Message\ServerRequestInterface;
use Weida\WeixinCore\Contract\EncryptorInterface;
use Weida\WeixinCore\Contract\RequestInterface;
use Weida\WeixinCore\Contract\ResponseInterface;
use Weida\WeixinCore\Middleware;
use Weida\WeixinCore\XML;
use Closure;

class Response extends Psr7Response implements ResponseInterface
{
    protected RequestInterface|ServerRequestInterface $request;
    protected ?EncryptorInterface $encoder=null;
    protected array $params=[];
    protected Middleware $middleware;
    public function __construct(RequestInterface|ServerRequestInterface $request,?EncryptorInterface $encoder=null)
    {
        $this->request = $request;
        $this->encoder = $encoder;
        $this->params = $this->request->getQueryParams();
        $this->middleware = new Middleware();
        parent::__construct();
    }

    /**
     * @return PsrResponseInterface
     * @author Sgenmi
     */
    public function serve():PsrResponseInterface{
        if (!empty($this->params['echostr'])) {
            $this->withBody($this->params['echostr']);
            return $this;
        }
//        $message = $this->getDecryptedMessage();
        $message="aaaaa";
        $response = $this->middleware->handler($this,$message);
        //todo message类型自动包装
        return $this;
    }

    /**
     * @param callable|string|array|object $callback
     * @return $this
     * @author Sgenmi
     */
    public function with(callable|string|array|object $callback):static{
        $this->middleware->addHandler($callback);
        return $this;
    }

    /**
     * @param string $msgType
     * @param callable|string|array|object $handler
     * @return $this
     * @author Sgenmi
     */
    public function addMessageListener(string $msgType,callable|string|array|object $handler):static{
        $handler = $this->middleware->addHandler($handler,$msgType);
        return $this;
    }

    /**
     * @param string $msgType
     * @param callable|string|array|object $handler
     * @return $this
     * @author Sgenmi
     */
    public function addEventListener(string $msgType,callable|string|array|object $handler):static{
        $handler = $this->middleware->addHandler($handler,$msgType);
        return $this;
    }

    /**
     * @return string
     * @author Sgenmi
     */
    public function getRequestMessage():string{
        return $this->request->getBody()->getContents();
    }

    /**
     * @return array|string
     * @author Sgenmi
     */
    public function getDecryptedMessage():array{
        $message = $this->getRequestMessage();
        if (empty($this->encryptor) || empty($this->params['msg_signature'])) {
            return $message;
        }
        $str = $this->encoder->decrypt(
            $message,$this->params['msg_signature'],$this->params['nonce']??'',$this->params['timestamp']??0
        );
        return XML::parse($str);
    }

}
