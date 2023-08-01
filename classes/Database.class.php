<?php
class Database
{
    private static $host = "localhost";
    private static $dbname = "thri_cont";
    private static $username = "thri_admin";
    private static $password = "thri_admin";

    public static function connect()
    {
        try {
            $pdo = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname, self::$username, self::$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            // handle the error here
        }
    }
}
