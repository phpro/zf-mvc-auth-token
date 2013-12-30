<?php

namespace MvcAuthToken;

/**
 * Class Token
 *
 * @package Authentication
 */
class Token
{

    const COVERAGE_NONE = 'none';
    const COVERAGE_BASE = 'base';

    /**
     * @var string
     */
    protected $realm;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $coverage;

    /**
     * @var string
     */
    protected $nonce;

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var string
     */
    protected $auth;

    /**
     * @param string $auth
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    /**
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param string $coverage
     */
    public function setCoverage($coverage)
    {
        $this->coverage = $coverage;
    }

    /**
     * @return string
     */
    public function getCoverage()
    {
        return $this->coverage;
    }

    /**
     * @param string $nonce
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * @param string $realm
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    /**
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = (int)$timestamp;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
