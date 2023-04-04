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
            if ($method == "GET") {
                // echo "index";
                echo json_encode($this->gateway->getAll());
            } elseif ($method == "POST") {
                // echo "create";
                // print_r($_POST);
                // echo file_get_contents("php://input");
                $data = (array) json_decode(file_get_contents("php://input"), true);

                $errors = $this->getValidationErrors($data);

                if (!empty($errors)) {
                    $this->respondUnprocessableEntity($errors);
                    return;
                }

                $id = $this->gateway->create($data);

                $this->respondCreated($id);
            } else {
                $this->respondMethodNotAllowed("GET, POST");
            }
        } else {

            $task = $this->gateway->get($id);

            if ($task === false) {
                // to keep here clean we use the method below respondNotFound.
                $this->respondNotFound($id);
                return;
            }
            // if isn't null we are dealing with existing task
            switch ($method) {
                case "GET":
                    // echo "show $id";
                    // echo json_encode($this->gateway->get($id));
                    echo json_encode($task);
                    break;
                case "PATCH":

                    $data = (array) json_decode(file_get_contents("php://input"), true);

                    $errors = $this->getValidationErrors($data, false);
                    // var_dump($errors);

                    if (!empty($errors)) {
                        $this->respondUnprocessableEntity($errors);
                        return;
                    }

                    // echo "update $id";
                    $rows = $this->gateway->update($id, $data);
                    echo json_encode(["message" => "Task updated", "rows" => $rows]);
                    break;

                case "DELETE":
                    $rows = $this->gateway->delete($id);
                    // as before with the update 200 status code will be return by default

                    echo json_encode(["message" => "Task deleted", "rows" => $rows]);
                    // echo "delete $id";
                    break;
                default:
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
            }
        }
    }

    private function respondUnprocessableEntity(array $errors): void
    {
        http_response_code(422);
        echo json_encode(["errors" => $errors]);
    }

    private function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
    }

    private function respondNotFound($id)
    {
        http_response_code(404);
        echo json_encode(['message' => 'Task with ID ' . $id . ' not found']);
    }

    private function respondCreated(string $id): void
    {
        http_response_code(201);
        echo json_encode(["message" => "Task created", "id" => $id]);
    }

    private function getValidationErrors(array $data, bool $is_new = true): array
    {
        $errors = [];
        // var_dump($is_n  ew);die();
        if ($is_new && empty($data["name"])) {
            $errors[] = "name is required";
        }

        if (!empty($data["priority"])) {
            if (filter_var($data["priority"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "priority must be an integer";
            }
        }

        return $errors;
    }
}
