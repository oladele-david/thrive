<?php
class Withdrawal
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function listWithdrawals($status = null)
    {
        try {
            // Prepare SQL statement to retrieve withdrawals from the database based on status
            if ($status === null) {
                $stmt = $this->pdo->prepare("SELECT * FROM tb_withdrawals");
            } else {
                $stmt = $this->pdo->prepare("SELECT * FROM tb_withdrawals WHERE status = :status");
                $stmt->bindParam(':status', $status);
            }

            // Execute the SQL statement
            $stmt->execute();

            // Fetch all withdrawals and return the result
            $withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "withdrawals" => $withdrawals);
        } catch (PDOException $e) {
            // If there is an error, return an error message
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while listing the withdrawals");
        }
    }

    public function createWithdrawal($accountId, $amount, $withdrawalDate)
    {
        try {
            // Get the current account balance
            $account = new Account();
            $currentBalance = $account->getAccountBalance($accountId);
            // Check if the account balance is sufficient for the withdrawal
            if ($currentBalance >= $amount) {
                // Prepare the SQL statement to insert a new withdrawal record
                $stmt = $this->pdo->prepare("INSERT INTO tb_withdrawals (account_id, amount, withdrawal_date, status) 
                                            VALUES (:accountId, :amount, :withdrawalDate, 'pending')");

                // Bind the parameters
                $stmt->bindParam(':accountId', $accountId);
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':withdrawalDate', $withdrawalDate);

                // Execute the SQL statement
                if ($stmt->execute()) {
                    $account->deductBalance($accountId, $amount);
                    // Return the ID of the newly inserted withdrawal record
                    // return $this->pdo->lastInsertId();
                    $value_return = array("response" => "success", "title" => "Success!", "msg" => "Withdrawal request successfully created");
                    return json_encode($value_return);
                } else {
                    return false;
                }
            } else {
                // If the account balance is not sufficient, return false
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Account  balance is not sufficient.");
                return json_encode($value_return);
            }
        } catch (PDOException $e) {
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something Went wrongn.");
            return json_encode($value_return);
        }
    }


    public function getWithdrawalById($withdrawalId)
    {
        try {
            // Prepare the SQL statement to retrieve a withdrawal record by ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_withdrawals WHERE id = :withdrawalId");

            // Bind the parameter
            $stmt->bindParam(':withdrawalId', $withdrawalId);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the withdrawal record
            $withdrawal = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the withdrawal record
            return $withdrawal;
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }

    public function getWithdrawalsByAccountId($accountId)
    {
        try {
            // Prepare the SQL statement to retrieve withdrawal records by account ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_withdrawals WHERE account_id = :accountId ORDER BY withdrawal_date DESC");

            // Bind the parameter
            $stmt->bindParam(':accountId', $accountId);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch all withdrawal records
            $withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the withdrawal records
            return $withdrawals;
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }

    public function updateWithdrawalStatus($withdrawalId, $status)
    {
        try {
            // Prepare the SQL statement to update the status of a withdrawal record
            $stmt = $this->pdo->prepare("UPDATE tb_withdrawals SET status = :status WHERE id = :withdrawalId");

            // Bind the parameters
            $stmt->bindParam(':withdrawalId', $withdrawalId);
            $stmt->bindParam(':status', $status);

            // Execute the SQL statement
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // If there is an error, return false
            return false;
        }
    }
}

