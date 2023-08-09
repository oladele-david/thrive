<?php
class LoanPlan
{
    
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function createLoanPlan($name, $duration, $interestRate, $amountToCollect)
    {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO tb_loan_plans (name, duration, interest_rate, amount_to_collect) 
                                        VALUES (:name, :duration, :interestRate, :amountToCollect)");

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':interestRate', $interestRate);
            $stmt->bindParam(':amountToCollect', $amountToCollect);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getLoanPlanById($loanPlanId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tb_loan_plans WHERE id = :loanPlanId");
            $stmt->bindParam(':loanPlanId', $loanPlanId);
            $stmt->execute();
            $loanPlan = $stmt->fetch(PDO::FETCH_ASSOC);
            return $loanPlan;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getAllLoanPlans()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tb_loan_plans");
            $stmt->execute();
            $loanPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $loanPlans;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateLoanPlan($loanPlanId, $name, $duration, $interestRate, $amountToCollect)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE tb_loan_plans 
                                        SET name = :name, duration = :duration, interest_rate = :interestRate, amount_to_collect = :amountToCollect 
                                        WHERE id = :loanPlanId");

            $stmt->bindParam(':loanPlanId', $loanPlanId);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':interestRate', $interestRate);
            $stmt->bindParam(':amountToCollect', $amountToCollect);

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteLoanPlan($loanPlanId)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM tb_loan_plans WHERE id = :loanPlanId");
            $stmt->bindParam(':loanPlanId', $loanPlanId);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
