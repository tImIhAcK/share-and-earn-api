<?php

class OrderController
{
    public function __construct(private Order $order)
    {
    }

    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processResourcesRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    public function processResourcesRequest(string $method, string $id): void
    {
        $result = $this->order->get($id);
        if (!$result) {
            http_response_code(404);
            echo json_encode(array("message" => "Order not found"));
            return;
        }

        switch ($method):
            case "GET":
                echo json_encode($result);
                break;
            // case "PUT":
            //     $data = json_decode(file_get_contents("php://input", true));
    
            //     $rows = $this->order->update($result, $data);
            //     http_response_code(200);
            //     echo json_encode([
            //         "message"=> "Bank details updated",
            //         "rows"=>$rows
            //     ]);
            //     break;

            case "DELETE":
                $row = $this->order->delete($id);
                echo json_encode([
                    "deleted" => [
                        "message" => "Order with " . $id . " deleted",
                        "row" => $row

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
                echo json_encode($this->order->getAll());
                break;
            case "POST":
                $data = json_decode(file_get_contents("php://input", true));

                if(!$this->validateAmount($data)){
                    return array('status'=>0, 'message'=>'insufficient balance');
                }
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

    public function validateAmount($data){
        $query = "SELECT balance FROM wallet WHERE user_id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $data->user_id);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            if($data->order_price > $balance){
                return true;
            }
        }
        return false;
    }
}
