<?php

namespace MvcAuthToken\Adapter;

use MvcAuthToken\Token;

/**
 * Class AdapterInterface
 * This adapter should implement the Token RFC at:
 * http://tools.ietf.org/html/draft-hammer-http-token-auth-01#section-7
 *
 * @package AuthToken
 */
interface AdapterInterface
{

    /**
     * @param $nonce
     *
     * @return mixed
     */
    public function validateNonce($nonce);

    /**
     * @param $timestamp
     *
     * @return mixed
     */
    public function validateTimestamp($timestamp);

    /**
     * @param Token $token
     *
     * @return mixed
     */
    public function validateToken(Token $token);

    /**
     * @param Token $token
     *
     * @return mixed
     */
    public function getUserId(Token $token);

}
