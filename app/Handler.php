<?php
class Handler
{
    /**
     * Basic router for the app
     */
    public function route() 
    {
        $parts = Helper::getPathParts();      
        try {
            if (count($parts) > 3) {
                throw new Exception('Too many path parts.');
            }
            // handle bad path requests
            if ($parts[1] != 'api') {
                throw new Exception('Not an "api" call.');
            }
            // routing
            switch ($parts[2]) {
                case 'auth':
                    self::handleAuth();
                    break;
                case 'generate':
                    self::handleGenerate();
                    break;
                case 'retrieve':
                    self::handleRetrieve();
                    break;
                default:
                    throw new Exception('Bad method.');
                    break;
            }
        } catch (Exception $e) {
            ExceptionsHandler::statusCodeHandle(404);
        }
    }

    /**
     * API method handler
     */
    private function handleAuth() 
    {
        $fields = ['login', 'password'];
        $data = Helper::sanitize($_POST, $fields);
        $token = AuthHeadersProcessor::setAuthToken($data);
        $result = [
            "token" => $token,
        ];
        Helper::respond($result);
    }

    /**
     * API method handler
     */
    private function handleGenerate() 
    {
        $authToken = AuthHeadersProcessor::validateAuthToken();
        
        $bytes = random_bytes(16);
        $gid = bin2hex($bytes);
        $number = rand(1, 100000);
        $data = [
            'gid' => $gid,
            'number' => $number,
            'authToken' => $authToken,
        ];

        DBAL::writeNumber($data);
        
        $result = [
            'gid' => $gid,
            'number' => $number,
        ];
        Helper::respond($result);
    }

    /**
     * API method handler
     */
    private function handleRetrieve() 
    {
        $authToken = AuthHeadersProcessor::validateAuthToken();

        $fields = ['gid'];
        $data = Helper::sanitize($_GET, $fields);

        $number = DBAL::readNumber($data);
        $result = [
            'value' => $number,
        ];
        Helper::respond($result);
    }

}