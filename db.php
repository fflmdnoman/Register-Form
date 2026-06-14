<?php
// Data base setting (Update if needed)

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'signupdb');

//create connection and setup table

try{
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($mysqli->connect_error){
        throw new Exception("Connection Failed" . $mysqli->connect_error);
    }

    // Create data base if not exists

    $mysqli->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $mysqli->select_db(DB_NAME);


    //Create user table if not exists

    $table_query = "CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL, 
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(225) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
    ENGINE = InnoDB DEFAULT CHARSET = utf8mb4";
    $mysqli->query($table_query);
} catch (Exception $e){
    die('Database setup error:' . $e->getMessage());
}

?>