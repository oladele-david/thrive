<?php

class UserLoan
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function listUserLoans($status = null)
    {
        try {
            // Prepare SQL statement to retrieve loans from the database based on status
            if ($status === null) {
                $stmt = $this->pdo->prepare("SELECT * FROM tb_user_loans");
            } else {
                $stmt = $this->pdo->prepare("SELECT * FROM tb_user_loans WHERE status = :status");
                $stmt->bindParam(':status', $status);
            }

            // Execute the SQL statement
            $stmt->execute();

            // Fetch all loans and return the result
            $userLoans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "userLoans" => $userLoans);
        } catch (PDOException $e) {
            // If there is an error, return an error message
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while listing the loans");
        }
    }

    public function createLoan($accountId, $loanPlanId)
    {
        try {

            $savings = new Savings();
            $activeSaving = $savings->getActiveSaving($accountId);
            $savedAmount = $activeSaving['amount'];
            if (!$activeSaving) {
                // If there is an active saving, return an error message
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "You Need to have an active savings to access loan plan.");
                return json_encode($value_return);
            }

            $plan = new LoanPlan();
            $loanPlan = $plan->getLoanPlanById($loanPlanId);
            
            $loanType = $loanPlan['type'];
            $duration = $loanPlan['duration'];
            $interestRate = $loanPlan['interest_rate'];

            if ($loanType == "normal") {
                $loanAmount =  ($savedAmount * 2);
            } elseif ($loanType == "electronic"){
                $loanAmount = $savedAmount;
            } else {
                $loanAmount = 100000;
            }

            $validatLoan = $this->loanConditon($accountId, $loanPlanId);

            if (!$validatLoan['status']) {
                // If there is an active saving, return an error message
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => $validatLoan['msg']);
                return json_encode($value_return);
            }
            $refNo = time();
            $stmt = $this->pdo->prepare("INSERT INTO tb_user_loans (ref_no, account_id, loan_plan_id, amount, duration, interest_rate) 
                                        VALUES (:refNo, :accountId, :loanPlanId, :loanAmount, :duration, :interestRate)");

            $stmt->bindParam(':refNo', $refNo);
            $stmt->bindParam(':accountId', $accountId);
            $stmt->bindParam(':loanPlanId', $loanPlanId);
            $stmt->bindParam(':loanAmount', $loanAmount);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':interestRate', $interestRate);

            $stmt->execute();
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Loan application successful.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Loan application unsuccessful.");
            return json_encode($value_return);
        }
    }

    public function getUserLoanById($userLoanId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tb_user_loans WHERE id = :userLoanId");
            $stmt->bindParam(':userLoanId', $userLoanId);
            $stmt->execute();
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);
            return $loan;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getLoansByAccountId($accountId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tb_user_loans WHERE account_id = :accountId");
            $stmt->bindParam(':accountId', $accountId);
            $stmt->execute();
            $loan = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $loan;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function loanConditon($accountId, $loanPlanId) 
    {
        try {
            // Check if the user has the same plan active or pending
            $stmt = $this->pdo->prepare("SELECT * FROM tb_user_loans WHERE account_id = :accountId AND loan_plan_id = :loanPlanId AND (status = 'active' OR status = 'pending')");
            $stmt->bindParam(':accountId', $accountId);
            $stmt->bindParam(':loanPlanId', $loanPlanId);
            $stmt->execute();
            $existingLoan = $stmt->fetch(PDO::FETCH_ASSOC);

            
            if ($existingLoan) {
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "You already have the same loan plan active or pending.", "status" => false);
                return $value_return;
            }

            // Check if the user has more than 2 active loans
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as totalActiveLoans FROM tb_user_loans WHERE account_id = :accountId AND status = 'active'");
            $stmt->bindParam(':accountId', $accountId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalActiveLoans = $result['totalActiveLoans'];

            if ($totalActiveLoans >= 2) {
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => " You have 2 active loans.", "status" => false);
                return $value_return;
            }


            // Check if the user has more than 2 active loans
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as totalPendingLoans FROM tb_user_loans WHERE account_id = :accountId AND status = 'pending'");
            $stmt->bindParam(':accountId', $accountId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalPendingLoans = $result['totalPendingLoans'];

            if ($totalPendingLoans >= 2) {
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => " You have  2 Pending loans.", "status" => false);
                return $value_return;
            }

            // If none of the conditions are met, return true to indicate that the loan condition is met
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => " You haves  2 Pending loans.", "status" => true);
            return $value_return;

        } catch (PDOException $e) {
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Validation error.", "status" => false);
            return $value_return;
        }
    }
   

    public function listUserLoansWithAccountAndPlanInfo($status = null)
    {
        try {
            // Prepare SQL statement to retrieve user loans from the database based on status
            if ($status === null) {
                $stmt = $this->pdo->prepare("SELECT ul.*, a.first_name, a.last_name, a.account_balance, lp.name 
                                            FROM tb_user_loans ul
                                            INNER JOIN tb_accounts a ON ul.account_id = a.id
                                            INNER JOIN tb_loan_plans lp ON ul.loan_plan_id = lp.id");
            } else {
                $stmt = $this->pdo->prepare("SELECT ul.*, a.first_name, a.last_name, a.account_balance, lp.name 
                                            FROM tb_user_loans ul
                                            INNER JOIN tb_accounts a ON ul.account_id = a.id
                                            INNER JOIN tb_loan_plans lp ON ul.loan_plan_id = lp.id
                                            WHERE ul.status = :status");
                $stmt->bindParam(':status', $status);
            }

            // Execute the SQL statement
            $stmt->execute();

            // Fetch all user loans with account and loan plan information and return the result
            $userLoans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "userLoans" => $userLoans);
        } catch (PDOException $e) {
            // If there is an error, return an error message
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while listing the user loans");
        }
    }

    public function processLoan($userLoanId, $status)
    {
        try {
            $loan = $this->getUserLoanById($userLoanId);
            if (!$loan) {
                // If the loan doesn't exist, return an error message
                return array("response" => "error", "title" => "Oops!", "msg" => "Loan not found.");
            }

            if ($status === "active") {
                $this->pdo->beginTransaction();

                // Update loan status
                $this->updateStatus($userLoanId, $status);


                // Update user account balance
                $accountId = $loan['account_id'];
                $loanAmount = $loan['amount'];

                $account = new Account();
                $account->updateBalance($accountId, $loanAmount);

                // Set loan start and end dates based on loan plan duration
                $startDate = date('Y-m-d');
                $endDate = date('Y-m-d', strtotime($startDate . " +{$loan['duration']} months"));
                $stmt = $this->pdo->prepare("UPDATE tb_user_loans SET start_date = :startDate, end_date = :endDate WHERE id = :userLoanId");
                $stmt->bindParam(':startDate', $startDate);
                $stmt->bindParam(':endDate', $endDate);
                $stmt->bindParam(':userLoanId', $userLoanId);
                $stmt->execute();

                $this->pdo->commit();

                // Return success message
                $value_return = array("response" => "success", "title" => "Success!", "msg" => "Loan processed successfully.");
                return json_encode($value_return);
            } elseif ($status === "cancelled") {
               
                $this->updateStatus($userLoanId, $status);

                // Return success message
                $value_return = array("response" => "success", "title" => "Success!", "msg" => "Loan cancelled successfully.");
                return json_encode($value_return);
            } else {
                // Invalid status provided
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Invalid status provided.");
                return json_encode($value_return);
            }
        } catch (PDOException $e) {
            // If there is an error, return an error message
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Loan processing failed.");
            return json_encode($value_return);
        }
    }

    public function updateStatus($userLoanId, $status) 
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE tb_user_loans SET status = :status WHERE id = :userLoanId");
            $stmt->bindParam(':userLoanId', $userLoanId);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            return true;

        } catch (PDOException $e) {
            return false;
        }
    }


}
