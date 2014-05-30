<?php

namespace Atekia\FlowlyBundle\Helper;

use Doctrine\DBAL\Connection;

class DatabaseHelper
{
    private $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function initializeTable($table) {

        if (gettype($table) !== 'string') return false;

        $table = strtolower($table);

        $conn = $this->connection;

        switch($table) {

            case 'user':

                $conn->executeQuery("
                    CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY,
                        username TEXT UNIQUE NOT NULL,
                        realname TEXT,
                        password TEXT,
                        email TEXT,
                        role TEXT,
                        active INTEGER
                    );
                    ");

                if ($conn->fetchColumn("
                    SELECT
                        COUNT(id)
                    FROM
                        users;
                    ") == 0)
                    $conn->executeQuery("
                        INSERT INTO users (
                            username,
                            realname,
                            password,
                            email,
                            role,
                            active
                        ) VALUES (
                            'admin',
                            'Admin',
                            'x61Ey612Kl2gpFL56FT9weDnpSo4AV8j8+qx2AuTHdRyY036xxzTTrw10Wq3+4qQyB+XURPWx1ONxp3Y3pB37A==',
                            NULL,
                            'ROLE_ADMIN',
                            1
                        );
                        ");

                break;

            default:
                return false;
        }

        return true;

    }

}