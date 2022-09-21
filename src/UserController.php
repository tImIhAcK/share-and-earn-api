<?php

class UserController
{
    public function __construct(private User $user)
    {
        
    }

    public function processRequest(string $method, ?string $id) : void
    {
        if($id){
            $this->processResourcesRequest($method, $id);
        }
        else{
            $this->processCollectionRequest($method);
        }
    }

    public function processResourcesRequest(string $method, string $sting): void
    {

    }

    public function processCollectionRequest(string $method)
    {
        switch ($method):
            case 'GET':
                echo json_encode($this->user->getAll());
                break;
            case "POST":
                $data = json_decode(file_get_contents("php://input"));
                $errors = $this->validateRegisterData($data);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode(array("errors"=>$errors));
                    break;
                }

                http_response_code(201);
                echo json_encode($this->user->register($data));
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
                echo "Invalid Request Method";
                break;
        endswitch;
    }

    public function validateRegisterData($data): array
    {
        $errors = [];
        if(!preg_match('/^[0-9]{11}+$/', $data->phone_number)){
            $errors[] = "Inavlid phone number";
        }
        if($data->password == $data->confirm_password){
            if (strlen($data->password) > 6) {
                $errors = "Password length too short. Must be greater than 6";
            }
        }else{
            $errors[] = "Password not matching";
        }
        
        return $errors;
    }
}