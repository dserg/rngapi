<?php
namespace RngAPI;
class Helper 
{
    /**
     * Get URI path parts from server
     */
    public function getPathParts(): array 
    {
        $uri = $_SERVER['REQUEST_URI'];
        $path = parse_url($uri, PHP_URL_PATH);
        $result = explode('/', $path);
        return $result;
    }

    /**
     * Get filtered fields from POST
     */
    public function sanitize(array $array, array $requiredFields): array 
    {
        $result = [];
        try {
            foreach ($requiredFields as $field) {
                if (isset($array[$field])) {
                    $result[$field] = $array[$field];
                } else {
                    throw new \Exception('Not enough data to process this request.');
                }
            }
        } catch (\Exception $e) {
            ExceptionsHandler::basicHandle($e->getMessage());
        }
        return $result;
    }

    /**
     * Prepare and output execution results
     */
    public function respond(array $array) 
    {
        $encodedArray = json_encode($array);
        $result = $encodedArray;
        echo $result;
    }
}