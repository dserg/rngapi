<?php
class DBAL 
{
    /**
     * DBA singleton instance
     */
    private static $dba = null;

    /**
     * DBA singleton wrapper over PDO
     */
    public static function getDBA() 
    {
        // instantiate if doesn't exist
        if (is_null(DBAL::$dba)) {
            try {

                // must be set from ENV or a protected config file instead
                // must use a separate user with appropriate rights on the DB
                $params = [
                    'dsn' => 'mysql:host=127.0.0.1;dbname=rngapi',
                    'user' => 'root',
                    'password' => 'password'
                ];
                $pdo = new PDO($params['dsn'], $params['user'], $params['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]);
                DBAL::$dba = $pdo;
            } catch (Exception $e) {
                ExceptionsHandler::basicHandle('Could not connect to the database');
            }
        }
        return DBAL::$dba;
    }

    /**
     * Auth token write
     */
    public function writeAuthToken($data) {
        $authToken = $data['authToken'];
        $dba = self::getDBA();

        $dba->beginTransaction();

        // remove the token to clear TTL
        $sth = $dba->prepare("DELETE FROM auth_tokens WHERE token = :authToken");
        $sth->bindParam(':authToken', $authToken, PDO::PARAM_STR);
        $sth->execute();
        // insert new token
        $sth = $dba->prepare("INSERT INTO auth_tokens (`token`) VALUES (:authToken)");
        $sth->bindParam(':authToken', $authToken, PDO::PARAM_STR);
        $sth->execute();

        $dba->commit();
    }

    /**
     * Check if the auth token exists and is valid
     */
    public function checkAuthToken($authToken) {
        $dba = self::getDBA();

        $dba->beginTransaction();

        // delete expired tokens
        $sth = $dba->prepare("DELETE FROM `auth_tokens` WHERE created_dt < DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
        $sth->execute();   

        // smells of in-line constants; interval should be in the config
        $sth = $dba->prepare("SELECT COUNT(*) FROM auth_tokens WHERE token = :authToken AND created_dt >= DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
        $sth->bindParam(':authToken', $authToken, PDO::PARAM_STR);
        $sth->execute();
        // the result is in row 1, column 1
        $count = $sth->fetchColumn();

        $dba->commit();

        // return the token if it exists, otherwise null
        $result = null;
        if ($count > 0) {
            $result = $authToken;
        }
        return $result;
    }

    /**
     * RNG number write
     */
    public function writeNumber($data) {
        $dba = self::getDBA();

        $sth = $dba->prepare("INSERT INTO `numbers` VALUES (:authToken, :gid, :number)");
        $sth->bindParam(':authToken', $data['authToken'], PDO::PARAM_STR);
        $sth->bindParam(':gid', $data['gid'], PDO::PARAM_STR);
        $sth->bindParam(':number', $data['number'], PDO::PARAM_INT);
        $sth->execute();
    }

    /**
     * RNG number read (by generation ID)
     */
    public function readNumber($data) {
        $dba = self::getDBA();
        $sth = $dba->prepare("SELECT `number` FROM `numbers` WHERE `generation_id` = :gid");
        $sth->bindParam(':gid', $data['gid'], PDO::PARAM_STR);
        $sth->execute();
        // the result is in row 1, column 1
        $result = $sth->fetchColumn();
        return $result;
    }

}