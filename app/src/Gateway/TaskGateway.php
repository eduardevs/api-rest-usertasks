<?php

namespace App\Gateway;

use PDO;
use App\Database\Database;


class TaskGateway
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM task ORDER BY name";

        $stmt = $this->conn->query($sql);

        // return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['is_completed'] = (bool) $row['is_completed'];
            $data[] = $row;
        }
        return $data;
    }

    public function get(string $id): array | false
    {
        $sql = "SELECT * FROM task WHERE id = :id";
        // prepare stmt to avoid sql injection
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data['is_completed'] = (bool) $data['is_completed'];
        }
        // pdo fetch method will return array or boolean false is anything is returned
        return $data;
    }

    public function create(array $data): string
    {
        $sql = "INSERT INTO task (name, priority, is_completed) VALUES (:name, :priority, :is_completed)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":name", $data["name"], PDO::PARAM_STR);

        if (empty($data['priority'])) {
            $stmt->bindValue(":priority", null, PDO::PARAM_NULL);
        } else {

            $stmt->bindValue(":priority", $data["priority"], PDO::PARAM_INT);
        }

        $stmt->bindValue(":is_completed", $data["is_completed"] ?? false, PDO::PARAM_BOOL);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function update(string $id, array $data): int
    {
        $fields = [];
        // * we get values from $data, but before we check them :
        // 1. if name is not empty
        if (!empty($data["name"])) {
            // 2. we add this field to the fields array, the value will be an array, 
            $fields["name"] = [
                $data["name"],
                // 3. PDO_<datatype> : (we'll be using the bindValue method to bind values to placeholders in the SQL)
                PDO::PARAM_STR
            ];
        }
        // * SAME FOR REST OF VALUES
        // if ( ! empty($data["priority"])) {

        if (array_key_exists("priority", $data)) {

            $fields["priority"] = [
                $data["priority"],
                $data["priority"] === null ? PDO::PARAM_NULL : PDO::PARAM_INT
            ];
        }

        if (array_key_exists("is_completed", $data)) {
            $fields["is_completed"] = [
                $data["is_completed"],
                PDO::PARAM_BOOL
            ];
        }
        // * we print to test, and exit the script (test in the controller instead of echo in the PATCH)
        // print_r($fields);
        // exit;
        if (empty($fields)) {
            return 0;
        } else {

            /**
             * this will return an array
             */
            $sets = array_map(function ($value) {

                return "$value = :$value";
            }, array_keys($fields));

            // print_r($sets);exit;
            $sql = "UPDATE task"
                . " SET " . implode(", ", $sets)
                . " WHERE id = :id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id"  , $id, PDO::PARAM_INT);

            foreach($fields as $name => $values) {
                $stmt->bindValue(":$name", $values[0], $values[1]);
            }
            // echo $sql;
            // exit;
            $stmt->execute();
            return $stmt->rowCount();
        }
    }

    public function delete(string $id): int
    {
        $sql = "DELETE from task
            WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();
        // we add same php function to return number of rows deleted
        return $stmt->rowCount();

    }
}
