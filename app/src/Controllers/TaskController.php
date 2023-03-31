<?php 

namespace App\Controllers;

use App\Gateway\TaskGateway;

class TaskController 
{
    public function __construct(private TaskGateway $gateway)
    {
    }
    public function processRequest(string $method, ?string $id): void
    {
        if ($id === null) {
            if($method == "GET") 
            {
                // echo "index";
                echo json_encode($this->gateway->getAll());
            } elseif ($method == "POST")
            {
                echo "create";
            } else {
              $this->respondMethodNotAllowed("GET, POST");
            }
        } else {

            $task = $this->gateway->get($id);

            if($task === false) {
                // to keep here clean we use the method below respondNotFound.
                $this->respondNotFound($id);
                return;
            }
            // if isn't null we are dealing with existing task
            switch($method)
            {
                case "GET":
                    // echo "show $id";
                    // echo json_encode($this->gateway->get($id));
                    echo json_encode($task);
                    break;
                case "PATCH":
                    echo "update $id";
                    break;
                
                case "DELETE":
                    echo "delete $id";
                    break;
                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
            }
        }


    }

    private function respondMethodNotAllowed(string $allowed_methods): void 
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    private function respondNotFound($id) 
    {
        http_response_code(404);
        echo json_encode(['message' => 'Task with ID '. $id .' not found']);
    }
}