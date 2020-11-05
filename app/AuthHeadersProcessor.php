<?php
class AuthHeadersProcessor 
{
    /**
     * Create and record a new auth token
     */
    public function setAuthToken($data) 
    {
        $input = $data['login'] . $data['password'];
        $authToken = md5($input);
        $data = [
            'authToken' => $authToken
        ];
        DBAL::writeAuthToken($data);
        $result = $authToken;
        return $result;
    }

    /**
     * Returns valid auth token header or throws an exception
     */
    public function validateAuthToken() 
    {
        $headers = getallheaders();
        try {

            if (!isset($headers['Authorization'])) {
                throw new Exception('Auth header not set.');
            }
            $authHeader = $headers['Authorization'];
            $parts = explode(" ", $authHeader);
            $type = $parts[0];
            $token = $parts[1];
            if ($type != "Bearer") {
                throw new Exception('Wrong auth header type.');
            }
            $result = null;
            $validToken = DBAL::checkAuthToken($token);
            if (is_null($validToken)) {
                throw new Exception('Auth header expired.');
            }

        } catch (Exception $e) {
            ExceptionsHandler::statusCodeHandle(401);
        }
        $result = $token;
        return $result;
    }
}