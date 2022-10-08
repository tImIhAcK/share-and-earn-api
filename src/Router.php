<?php

class Router
{
    /**
     * This is the router class
     * It route all url
     * 
     */

    public static function route($db)
    {
        $url = explode("/", $_SERVER["REQUEST_URI"]);
        echo "here";
        exit;
        switch($url[2]):
            case "user":
                user_func($db, $url);
                break;
            default:
                http_response_code(404);
                echo json_encode(array("message"=>"File not fouond"));
                break;
        endswitch;
    }
}