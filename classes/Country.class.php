<?php
class Country
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }


    public function listCountries()
    {
        try {
            // prepare SQL statement to retrieve all payments from the database
            $stmt = $this->pdo->prepare("SELECT * FROM tb_countries");

            // execute the SQL statement
            $stmt->execute();

            // fetch all countries and return the result
            $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "countries" => $countries);
        } catch (PDOException $e) {
            // if there is an error, return false
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while listing the Countries: " );
        }
    }

    
    
    // Additional methods for other payment management functionalities can be added here
}
