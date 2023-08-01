<?php
require_once('./includes/autoload.php');

class Plan
{
    private $pdo;


    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function listPlans()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tb_plans");
            $stmt->execute();
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array("response" => "success", "plans" => $plans);
        } catch (PDOException $e) {
            // if there is an error, return false
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " . $e->getMessage());
        }
    }


    public function getPlanById($planId) {
        try {
            // establish database connection

            // prepare SQL statement to retrieve the plan by ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_plans WHERE id = :planId");
            $stmt->bindParam(':planId', $planId);

            // execute the SQL statement
            $stmt->execute();

            // fetch the plan record
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);

            // if plan record found, return it
            if ($plan) {
                return $plan;
            } else {
                // if no plan record found, return false
                return false;
            }
        } catch (PDOException $e) {
            // if there is an error, return false
            return false;
        }
    }


    public function createPlan($planName, $duration, $interestRate)
    {
        try {
            // filter inputs for malicious things
            $planName = htmlspecialchars($planName);
            $duration = htmlspecialchars($duration);
            $interestRate = htmlspecialchars($interestRate);

        

            // prepare SQL statement to insert a new admin user into the database
            $stmt = $this->pdo->prepare("INSERT INTO tb_plans (plan_name, duration, interest_rate) VALUES (:planName, :duration, :interestRate)");

            // bind the admin user inputs to the placeholders in the SQL statement
            // $stmt->bindParam(':id', $id);
            $stmt->bindParam(':planName', $planName);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':interestRate', $interestRate);

            // execute the SQL statement to create a new admin user
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Plan created successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " . $e->getMessage());
            return json_encode($value_return);
        }
    }

    public function updatePlan($id, $planName, $duration, $interestRate)
    {
        try {
            // filter inputs for malicious things
            $id = htmlspecialchars($id);
            $planName = htmlspecialchars($planName);
            $duration = htmlspecialchars($duration);
            $interestRate = htmlspecialchars($interestRate);


            // prepare SQL statement to update the admin user's information in the database
            $stmt = $this->pdo->prepare("UPDATE tb_plans SET plan_name = :planName, duration = :duration, interest_rate = :interestRate WHERE id = :id");

            // bind the admin user inputs to the placeholders in the SQL statement
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':planName', $planName);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':interestRate', $interestRate);

            // execute the SQL statement to update the admin user's information
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Plan updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " . $e->getMessage());
            return json_encode($value_return);
        }
    }
}