<?php
require_once('./includes/autoload.php');

class Loan
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function createLoan($accountId, $loanAmount, $interestRate, $loanTerm, $startDate, $endDate)
    {
        try {
            // prepare SQL statement to insert a new loan into the database
            $stmt = $this->pdo->prepare("INSERT INTO tb_loans (account_id, loan_amount, interest_rate, loan_term, start_date, end_date) 
                                        VALUES (:accountId, :loanAmount, :interestRate, :loanTerm, :startDate, :endDate)");

            // bind the loan inputs to the placeholders in the SQL statement
            $stmt->bindParam(':accountId', $accountId);
            $stmt->bindParam(':loanAmount', $loanAmount);
            $stmt->bindParam(':interestRate', $interestRate);
            $stmt->bindParam(':loanTerm', $loanTerm);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endDate', $endDate);

            // execute the SQL statement to create a new loan
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Loan created successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while creating the loan. Please try again later.");
            return json_encode($value_return);
        }
    }

    public function updateLoan($loanId, $loanAmount, $interestRate, $loanTerm, $startDate, $endDate)
    {
        try {
            // check if loan exists with the given ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_loans WHERE id = :loanId");
            $stmt->bindParam(':loanId', $loanId);
            $stmt->execute();
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$loan) {
                // if loan doesn't exist, return an error message in JSON format
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Loan not found.");
                return json_encode($value_return);
            }

            // prepare SQL statement to update the loan's information in the database
            $stmt = $this->pdo->prepare("UPDATE tb_loans SET loan_amount = :loanAmount, interest_rate = :interestRate, loan_term = :loanTerm,
                                        start_date = :startDate, end_date = :endDate WHERE id = :loanId");

            // bind the loan inputs to the placeholders in the SQL statement
            $stmt->bindParam(':loanId', $loanId);
            $stmt->bindParam(':loanAmount', $loanAmount);
            $stmt->bindParam(':interestRate', $interestRate);
            $stmt->bindParam(':loanTerm', $loanTerm);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endDate', $endDate);

            // execute the SQL statement to update the loan's information
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Loan updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while updating the loan. Please try again later.");
            return json_encode($value_return);
        }
    }

    public function getLoanById($loanId)
    {
        try {
            // prepare SQL statement to retrieve the loan by ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_loans WHERE id = :loanId");
            $stmt->bindParam(':loanId', $loanId);

            // execute the SQL statement
            $stmt->execute();

            // fetch the loan record
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);

            // if loan record found, return it
            if ($loan) {
                return $loan;
            } else {
                // if no loan record found, return false
                return false;
            }
        } catch (PDOException $e) {
            // if there is an error, return false
            return false;
        }
    }

    public function listLoans()
    {
        try {
            // prepare SQL statement to retrieve all loans from the database
            $stmt = $this->pdo->prepare("SELECT * FROM tb_loans");

            // execute the SQL statement
            $stmt->execute();

            // fetch all loans and return the result
            $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "loans" => $loans);
        } catch (PDOException $e) {
            // if there is an error, return false
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while listing the loans");
        }
    }

    public function closeLoan($loanId)
    {
        try {
            // check if loan exists with the given ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_loans WHERE id = :loanId");
            $stmt->bindParam(':loanId', $loanId);
            $stmt->execute();
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$loan) {
                // if loan doesn't exist, return an error message in JSON format
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Loan not found.");
                return json_encode($value_return);
            }

            // prepare SQL statement to update the loan's status to "closed"
            $stmt = $this->pdo->prepare("UPDATE tb_loans SET status = 'closed' WHERE id = :loanId");
            $stmt->bindParam(':loanId', $loanId);
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Loan closed successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while closing the loan. Please try again later.");
            return json_encode($value_return);
        }
    }
    
    public function listAccountLoans($accountId)
    {
        try {
            // prepare SQL statement to retrieve loans by account ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_loans WHERE account_id = :accountId");
            $stmt->bindParam(':accountId', $accountId);

            // execute the SQL statement
            $stmt->execute();

            // fetch all loans associated with the account and return the result
            $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "loans" => $loans);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while listing the account loans");
            return json_encode($value_return);
        }
    }
}
