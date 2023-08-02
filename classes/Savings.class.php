<?php
require_once('./includes/autoload.php');


class Savings
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function createSaving($accountId, $amount, $minimumAmount, $savingInterval, $startDate, $duration, $special)
    {
        try {

            $activeSaving = $this->getActiveSaving($accountId, $special);
            if ($activeSaving) {
                if ($special == true) {
                    // If there is an active saving, return an error message
                    $value_return = array("response" => "error", "title" => "Oops!", "msg" => "You already have an active special saving.".$special);
                    return json_encode($value_return);
                } else {
                    // If there is an active saving, return an error message
                    $value_return = array("response" => "error", "title" => "Oops!", "msg" => "You already have an active saving.".$special);
                    return json_encode($value_return);
                }
                
               
            }

            // Check if the amount to save is greater than or equal to the minimum amount
            if ($amount < $minimumAmount) {
                // If the amount is less than the minimum, return an error message
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Amount to save cannot be less than the minimum required amount.");
                return json_encode($value_return);
            }

            $account = new Account();
            $accountBalance = $account->getAccountBalance($accountId);

            if ($accountBalance === false) {
                // If there is an error retrieving the account balance, return an error message
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Error retrieving account balance.");
                return json_encode($value_return);
            }


            if ($amount > $accountBalance) {
                // If the amount to save is greater than the account balance, return an error message
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Amount to save cannot be greater than the account balance.");
                return json_encode($value_return);
            }

            // Prepare SQL statement to insert a new saving into the database
            $stmt = $this->pdo->prepare(
                "INSERT INTO tb_savings (ref_no, account_id, amount, saving_interval, start_date, ending_date, special, minimum_amount, next_removing_date) 
                                         VALUES (:refNo, :accountId, :amount, :savingInterval, :startDate, :endingDate, :special, :minimumAmount, :nextRemovingDate)"
            );
            
            // Generate the refNo using time()
            $refNo = time();

            // Calculate the next removing date based on the saving_interval and start_date
            $nextRemovingDate = Custom::calculateNextDate($savingInterval, $startDate);

            $endingDate = date('Y-m-d', strtotime($startDate . " + $duration months"));

            // Bind the saving inputs to the placeholders in the SQL statement
            $stmt->bindParam(':refNo', $refNo);
            $stmt->bindParam(':accountId', $accountId);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':savingInterval', $savingInterval);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endingDate', $endingDate); // Bind the endingDate parameter
            $stmt->bindParam(':special', $special, PDO::PARAM_BOOL);
            $stmt->bindParam(':minimumAmount', $minimumAmount);
            $stmt->bindParam(':nextRemovingDate', $nextRemovingDate);

            // Execute the SQL statement to create a new saving
            $stmt->execute();

            // Get the ID of the newly inserted saving (assuming you have an auto-incrementing primary key column)
            $newSavingId = $this->pdo->lastInsertId();

            // Call the addSavingsHistory method to add the savings history record
            $this->addSavingsHistory($accountId, $newSavingId, $amount);

            // Deduct the amount from the account balance
            $account = new Account();
            $account->deductBalance($accountId, $amount);

            // If successful, return a success message
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Saving created successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // If there is an error, return an error message
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Saving could not be created: " . $e->getMessage());
            return json_encode($value_return);
        }
    }


    public function updateSaving($savingId, $amount, $accountId)
    {
        try {

            $account = new Account();
            $accountBalance = $account->getAccountBalance($accountId);

            if ($accountBalance === false) {
                // If there is an error retrieving the account balance, return an error message
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Error retrieving account balance.");
                return json_encode($value_return);
            }

            // Get the minimum amount from the tb_savings table for the specified savingId
            $stmt = $this->pdo->prepare("SELECT * FROM tb_savings WHERE id = :savingId");
            $stmt->bindParam(':savingId', $savingId);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the specified amount is less than the minimum amount
            $minimumAmount = $row['minimum_amount'];
            $oldAmount = $row['amount'];
            if ($amount < $minimumAmount) {
                // If the amount to save is less than the minimum amount, return an error message
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Amount to save cannot be less than the minimum amount.");
                return json_encode($value_return);
            }

            if ($amount > $accountBalance) {
                // If the amount to save is greater than the account balance, return an error message
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Amount to save cannot be greater than the account balance.");
                return json_encode($value_return);
            }

            // Prepare SQL statement to update the saving's amount in the database
            $stmt = $this->pdo->prepare("UPDATE tb_savings SET amount = :amount WHERE id = :savingId");

            $newAmount = $oldAmount + $amount;
            // Bind the saving inputs to the placeholders in the SQL statement
            $stmt->bindParam(':amount', $newAmount);
            $stmt->bindParam(':savingId', $savingId);

            // Execute the SQL statement to update the saving's amount
            $stmt->execute();

            $this->addSavingsHistory($accountId, $savingId, $amount);
            $account->deductBalance($accountId, $amount);

            // If successful, return a success message
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Saving updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // If there is an error, return an error message
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Saving could not be updated");
            return json_encode($value_return);
        }
    }

    public function getSavingHistory($accountId)
    {
        try {
            // Prepare SQL statement to retrieve the saving history for the specified account
            $stmt = $this->pdo->prepare("SELECT * FROM tb_savings WHERE account_id = :accountId");

            // Bind the account ID to the placeholder in the SQL statement
            $stmt->bindParam(':accountId', $accountId);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the saving history records
            $savingHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the saving history
            return $savingHistory;
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }

    public function getSaving($savingId)
    {
        try {
            // Prepare SQL statement to retrieve the saving history for the specified account
            $stmt = $this->pdo->prepare("SELECT * FROM tb_savings WHERE id = :savingId");

            // Bind the account ID to the placeholder in the SQL statement
            $stmt->bindParam(':savingId', $savingId);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the saving history records
            $saving = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the saving history
            return $saving;
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }

    public function getActiveSaving($accountId, $special = null)
    {
        try {
            $query = "SELECT * FROM tb_savings WHERE account_id = :accountId AND status = 'active'";

            // If $special is provided and is a boolean value, add the condition for the 'special' column
            if (isset($special)) {
                $query .= " AND special = :special";
            }

            // Prepare SQL statement to retrieve the active saving for the specified account
            $stmt = $this->pdo->prepare($query);

            // Bind the account ID to the placeholder in the SQL statement
            $stmt->bindParam(':accountId', $accountId);

            // If $special is provided and is a boolean value, bind the 'special' parameter
            if (isset($special)) {
                $stmt->bindParam(':special', $special, PDO::PARAM_BOOL);
            }

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the active saving record
            $activeSaving = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the active saving
            return $activeSaving;
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }

    private function addSavingsHistory($accountId, $savingsId, $amount)
    {
        try {
            $transactionDate = date('Y-m-d H:i:s'); // Get the current date and time

            // Insert a new savings history record into the database
            $stmt = $this->pdo->prepare("INSERT INTO tb_savings_history (account_id, savings_id, amount, transaction_date) 
                                         VALUES (:accountId, :savingsId, :amount, :transactionDate)");

            $stmt->bindParam(':accountId', $accountId);
            $stmt->bindParam(':savingsId', $savingsId);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':transactionDate', $transactionDate);
            $stmt->execute();

            // Return the newly created savings history record if needed
            // return $this->getSavingsHistory($accountId, $savingsId);
            return true;
        } catch (PDOException $e) {
            // Handle any errors
        }
    }
}
