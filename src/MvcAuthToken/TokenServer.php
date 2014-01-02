<?php

namespace MvcAuthToken;

use MvcAuthToken\Adapter\AdapterInterface;
use MvcAuthToken\Exception\TokenException;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Stdlib\Hydrator\ClassMethods;

/**
 * Class TokenServer
 *
 * @package AuthToken
 */
class TokenServer
{

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var array
     */
    protected $defaultParameters = array(
        'coverage' => Token::COVERAGE_BASE,
    );

    /**
     * @param AdapterInterface $adapter
     */
    public function setAdapter($adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param \Zend\Http\Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Zend\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Zend\Http\Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * @return \Zend\Http\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \MvcAuthToken\Token $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return Token|null
     * @throws \Exception
     */
    public function getToken()
    {
        if (!$this->token) {
            $this->token = $this->createToken();
        }
        return $this->token;
    }

    /**
     * @return Token|null
     * @throws \Exception
     */
    public function createToken()
    {
        $authorization = $this->getRequest()->getHeader('Authorization');
        if (!$authorization) {
            // TODO: challenge client (HTTP 401 - WWW-Authenticate)
            throw new TokenException('No authentication type detected');
        }

        list($type, $credential) = preg_split('# #', $authorization->getFieldValue(), 2);
        if ($type != 'Token') {
            // Invalid authorisation type ..
            // Todo: clean handling
            throw new TokenException(sprintf('Invalid authentication type "%s" detected. Required: "Token"', $type));
        }

        // Get parameters:
        $parameters = $this->getTokenParameters($credential);
        $token = new Token();

        // Hydrate params:
        $hydrator = new ClassMethods();
        $hydrator->hydrate($parameters, $token);

        return $token;
    }

    /**
     * @param $credential
     *
     * @return array
     */
    public function getTokenParameters($credential)
    {
        $parts = explode(',', $credential);
        $token = array_merge(array(), $this->defaultParameters);
        foreach ($parts as $parameter) {
            $parameter = trim($parameter);
            list($key, $value) = explode('=', $parameter);
            $key = trim($key);
            $value = trim($value, '" ');

            $token[$key] = $value;
        }
        return $token;
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    public function validateToken(Token $token)
    {
        $adapter = $this->getAdapter();

        if (!$adapter->validateTimestamp($token->getTimestamp())) {
            return false;
        }

        if (!$adapter->validateNonce($token->getNonce())) {
            return false;
        }

        if (!$adapter->validateToken($token)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function authenticate()
    {
        $token = $this->getToken();
        if (!$this->validateToken($token)) {
            return false;
        }

        return true;
    }

    /**
     * @return string|\Zf\MvcAuth\Identity\IdentityInterface
     */
    public function getUserId()
    {
        $token = $this->getToken();
        return $this->adapter->getUserId($token);
    }

}
