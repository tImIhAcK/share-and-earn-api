<?php

class BankController
{
    public function __construct(private Bank $bank)
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

    public function processResourcesRequest(string $method, string $id): void
    {
        $result = $this->bank->get($id);
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
                $row = $this->bank->delete($id);
                echo json_encode([
                    "deleted"=>[
                        "message"=>"Bank with user id ".$id." deleted",
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

    public function processCollectionRequest(string $method)
    {
       switch ($method):
            case 'GET':
                echo json_encode($this->bank->getAll());
                break;
            case "POST":
                $data = json_decode(file_get_contents("php://input", true));
                http_response_code(201);
                echo json_encode($this->bank->create($data));
                break;
            // case "PUT":
            //     $data = json_decode(file_get_contents("php://input", true));
            //     http_response_code(201);
            //     echo json_encode($this->bank->update($data));
            //     break;
            // default:
                http_response_code(405);
                header("Allow: GET, POST, PUT");
                echo "Invalid Request Method";
                break;
        endswitch;
    }
}