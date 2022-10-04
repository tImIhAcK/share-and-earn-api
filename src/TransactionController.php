<?php

class TransactionController
{
    public function __construct(private Transaction $trans)
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
        $result = $this->bank->trans($id);
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
                $row = $this->trans->delete($id);
                echo json_encode([
                    "deleted"=>[
                        "message"=>"Transaction deleted",
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
                echo json_encode($this->trans->getAll());
                break;
            case "POST":
                $data = json_decode(file_get_contents("php://input", true));
                http_response_code(201);
                echo json_encode($this->trans->create($data));
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



    public function charge($data){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.commerce.coinbase.com/charges/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $post = array(
            "name" => $data->trans_type,
            "description" => '',
            "local_price" => array(
                'amount' => $data->amt,
                'currency' => 'USD'
            ),
            "pricing_type" => "fixed_price",
            "metadata" => array(
                'customer_id' => $data->user_id,
            )
        );

        $post = json_encode($post);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = "Content-Type: application/json";
        $headers[] = "X-Cc-Api-Key: ebe7dbe0-664b-4445-9d45-4847098b5a4d";
        $headers[] = "X-Cc-Version: 2018-03-22";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        curl_close ($ch);
        $response = json_decode($result);
        echo $response->data->hosted_url;
    }
}