<?php
require_once('./includes/autoload.php');

class Bank
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function listBanks()
    {
        try {
            // prepare SQL bankment to retrieve all payments from the database
            $stmt = $this->pdo->prepare("SELECT * FROM tb_banks");

            // execute the SQL bankment
            $stmt->execute();

            // fetch all banks and return the result
            $banks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "banks" => $banks);
        } catch (PDOException $e) {
            // if there is an error, return false
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while listing the banks: " );
        }
    }

    
    
    // Additional methods for other payment management functionalities can be added here
}
