<?php

class OrderController
{
    public function __construct(private Order $order)
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
                echo json_encode($this->order->getAll());
                break;
            case "POST":
                $data = json_decode(file_get_contents("php://input", true));
                http_response_code(201);
                echo json_encode($this->order->create($data));
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
                echo "Invalid Request Method";
                break;
        endswitch;
    }
}