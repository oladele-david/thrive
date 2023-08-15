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
                $stmt = $this->pdo->prepare("SELECT w.*, a.first_name, a.last_name, a.email_id
                                            FROM tb_withdrawals w
                                            INNER JOIN tb_accounts a ON w.account_id = a.id");
            } else {
                $stmt = $this->pdo->prepare("SELECT w.*, a.first_name, a.last_name, a.email_id
                                            FROM tb_withdrawals w
                                            INNER JOIN tb_accounts a ON w.account_id = a.id
                                            WHERE w.status = :status");
                $stmt->bindParam(':status', $status);
            }

            // Execute the SQL statement
            $stmt->execute();

            // Fetch all withdrawals with user details and return the result
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
            $stmt = $this->pdo->prepare("SELECT w.*, a.first_name, a.last_name, a.email_id
                                        FROM tb_withdrawals w
                                        INNER JOIN tb_accounts a ON w.account_id = a.id
                                        WHERE w.id = :withdrawalId");

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

    public function processWithdrawal($withdrawalId, $status)
    {
        try {
            // Start a transaction
            $this->pdo->beginTransaction();

            // Get the withdrawal record by ID
            $withdrawal = $this->getWithdrawalById($withdrawalId);

            if (!$withdrawal) {
                // If the withdrawal record does not exist, return an error message
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Withdrawal record not found.");
                return json_encode($value_return);
            }

            // Update the status of the withdrawal
            $updateStatusResult = $this->updateWithdrawalStatus($withdrawalId, $status);

            if (!$updateStatusResult) {
                // If updating status fails, rollback the transaction and return an error message
                $this->pdo->rollBack();
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Failed to update withdrawal status.");
                return json_encode($value_return);
            }

            // If the status is 'Cancelled', update the user account balance
            if ($status === 'cancelled') {
                $account = new Account();
                $accountId = $withdrawal['account_id'];
                $amount = $withdrawal['amount'];

                $account = new Account();
                $updateBalanceResult = $account->updateBalance($accountId, $amount);

                if (!$updateBalanceResult) {
                    // If updating balance fails, rollback the transaction and return an error message
                    $this->pdo->rollBack();
                    $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Failed to update account balance.");
                    return json_encode($value_return);
                }
            }

            // Commit the transaction
            $this->pdo->commit();

            // Return a success message
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Withdrawal processed successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // If there is an error, rollback the transaction and return an error message
            $this->pdo->rollBack();
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "An error occurred while processing the withdrawal.");
            return json_encode($value_return);
        }
    }

}

