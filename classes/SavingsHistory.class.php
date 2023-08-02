<?php
require_once('./includes/autoload.php');

// savings_history.php

class SavingsHistory
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function getSavingsHistoryBySavingId($savingId)
    {
        try {
            // Prepare SQL statement to retrieve the saving history for the specified saving ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_savings_history WHERE savings_id = :savingId");

            // Bind the saving ID to the placeholder in the SQL statement
            $stmt->bindParam(':savingId', $savingId);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the saving history records
            $savingsHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the saving history
            return $savingsHistory;
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }

    public function getSavingsHistoryByAccountId($accountId)
    {
        try {
            // Prepare SQL statement to retrieve the saving history for the specified account ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_savings_history WHERE account_id = :accountId");

            // Bind the account ID to the placeholder in the SQL statement
            $stmt->bindParam(':accountId', $accountId);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the saving history records
            $savingsHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the saving history
            return $savingsHistory;
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }
}
?>
