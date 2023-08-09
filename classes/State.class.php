<?php
class State
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function listStates()
    {
        try {
            // prepare SQL statement to retrieve all payments from the database
            $stmt = $this->pdo->prepare("SELECT * FROM tb_states");

            // execute the SQL statement
            $stmt->execute();

            // fetch all states and return the result
            $states = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "states" => $states);
        } catch (PDOException $e) {
            // if there is an error, return false
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while listing the states: " );
        }
    }

    
    
    // Additional methods for other payment management functionalities can be added here
}
