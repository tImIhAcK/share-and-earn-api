<?php

class UserController
{
    public function __construct(private User $user)
    {
        
    }

    public function processRequest(string $method, ?string $id, ?string $url) : void
    {
        if($id){
            $this->processResourcesRequest($method, $id);
        }
        else{
            $this->processCollectionRequest($method, $url);
        }
    }

    public function processResourcesRequest(string $method, string $id): void
    {
        $result = $this->user->get($id);
        if(! $result){
            http_response_code(404);
            echo json_encode(array("message"=>"user not found"));
            return;
        }

        switch($method):
            case "GET":
                echo json_encode($result);
                break;
            case "DELETE":
                $row = $this->user->get($id);
                echo json_encode([
                    "deleted"=>[
                        "message"=>"User with ".$id." deleted",
                        "row"=>$row

                    ]
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, DELETE");
                break;
        endswitch;
    }

    public function processCollectionRequest(string $method, string $url)
    {
        switch ($method):
            case 'GET':
                echo json_encode($this->user->getAll());
                break;
            case "POST":
                if ($url == 'register'){
                    $data = json_decode(file_get_contents("php://input", true));
                    $error = $this->validateRegisterData($data);
                    if(!empty($error)){
                        http_response_code(422);
                        echo json_encode(array("error"=>$error));
                        break;
                    }
                    $result = $this->user->register($data);
                    http_response_code(201);
                    echo json_encode($result);
                    break;
                }

                if($url == 'login'){
                    $data = json_decode(file_get_contents("php://input", true));
                    $result = $this->user->login($data);
                    http_response_code(201);
                    echo json_encode($result);
                    break;
                }
            default:
                http_response_code(405);
                header("Allow: GET, POST");
                echo "Invalid Request Method";
                break;
        endswitch;
    }

    public function validateRegisterData($data): array
    {
        $error = [];
        if (! empty($data)){
            if(preg_match('/^[0-9]{11}+$/', $data->phone_number)){
                if($data->password == $data->confirm_password){
                    if (strlen($data->password) < 6) {
                        $error = "Password length too short. Must be greater than 6";
                    }else{
                        $error = [];
                    }
                }else{
                    $error[] = "Password not matching";
                }
            }else{
                $error[] = "Inavlid phone number";
            }
        }else{
            $error[] = "All fields are required";
        }
        return $error;
    }
}