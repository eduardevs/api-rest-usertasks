<?php

namespace App\Database;

use PDO;


class Database
{
    public function __construct(
        private string $host,
        private string $name,
        private string $user,
        private string $password
    ) {
      // var_dump($host, $name, $user, $password);
    }

    public function getConnection(): \PDO
    {

      $dsn = "mysql:host={$this->host};dbname={$this->name};charset=utf8";
      
      return new \PDO ($dsn, $this->user, $this->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES=> false,
        PDO::ATTR_STRINGIFY_FETCHES => false
      ]);
    }

}