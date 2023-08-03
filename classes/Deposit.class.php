<?php
require_once('./includes/autoload.php');

class Deposit
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function createDeposit($accountId, $amount, $status)
    {
        try {
            // Generate a unique reference number
            $refNo = time();

            // Prepare SQL statement to insert a new deposit into the database
            $stmt = $this->pdo->prepare("INSERT INTO tb_deposits (ref_no, account_id, deposit_date, amount, status, created_at) 
                                         VALUES (:refNo, :accountId, NOW(), :amount, :status, NOW())");

            // Bind the deposit inputs to the placeholders in the SQL statement
            $stmt->bindParam(':refNo', $refNo);
            $stmt->bindParam(':accountId', $accountId);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':status', $status);

            // Execute the SQL statement to create a new deposit
            $stmt->execute();

            // If successful, return the generated reference number
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Deposit created successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // If there is an error, return false
            // If successful, return the generated reference number
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Deposit could not be created.". $e->getMessage());
            return json_encode($value_return);
        }
    }

    public function updateDepositStatus($depositId, $status)
    {
        try {
            // Prepare SQL statement to update the status of a deposit in the database
            $stmt = $this->pdo->prepare("UPDATE tb_deposits SET status = :status WHERE id = :depositId");

            // Bind the deposit ID and status to the placeholders in the SQL statement
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':depositId', $depositId);

            // Execute the SQL statement to update the deposit status
            $stmt->execute();

            // If successful, return true
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Deposit status updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // If there is an error, return false
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Deposit status could not be updated.");
            return json_encode($value_return);
        }
    }

    public function getDepositById($depositId)
    {
        try {
            // Prepare the SQL statement to retrieve a deposit record by ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_deposits WHERE id = :depositId");

            // Bind the parameter
            $stmt->bindParam(':depositId', $depositId);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the deposit record
            $deposit = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the deposit record
            return $deposit;
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }
    public function getDepositsByAccountId($accountId)
    {
        try {
            // Prepare the SQL statement to retrieve deposit records by account ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_deposits WHERE account_id = :accountId ORDER BY deposit_date DESC");

            // Bind the parameter
            $stmt->bindParam(':accountId', $accountId);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch all deposit records
            $deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the deposit records
            return $deposits;
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }
}
