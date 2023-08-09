<?php

require_once('./includes/autoload.php');

class UserLoan
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
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

    public function getLoanById($loanId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tb_user_loans WHERE id = :loanId");
            $stmt->bindParam(':loanId', $loanId);
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
   

}
