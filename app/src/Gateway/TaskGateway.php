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

    public function getAll() : array
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

        if($data !==false) {
            $data['is_completed'] = (bool) $data['is_completed'];
        }
        // pdo fetch method will return array or boolean false is anything is returned
        return $data;
    }

}


