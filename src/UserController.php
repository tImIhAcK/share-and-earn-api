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
        switch ($method) {
            case 'GET':
                echo json_encode($this->user->getAll());
                break;
            case "POST":
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($this->user->register($data));
                break;
            default:
                echo "Invalid Request Method";
                break;
        }
    }
}