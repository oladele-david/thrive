<?php
require_once('./includes/autoload.php');


class Investment
{
  private $pdo;

  public function __construct()
  {
    $this->pdo = Database::connect();
  }
  // Get all investments
  public function listInvestments()
  {
    $stmt = $this->pdo->prepare(
      "SELECT i.id, i.plan_id, p.plan_name, i.amount, i.monthly_roi, i.start_date, i.expiration, p.interest_rate, p.duration, i.status, i.investor_id, iv.first_name, iv.middle_name, iv.last_name 
      FROM tb_investments i JOIN tb_plans p ON i.plan_id = p.id JOIN tb_investors iv ON i.investor_id = iv.id;");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  public function getInvestmentById($investmentId)
  {
    try {
      // establish database connection

      // prepare SQL statement to retrieve the investor by ID
      $stmt = $this->pdo->prepare("SELECT * FROM tb_investors WHERE id = :investmentId");
      $stmt->bindParam(':investmentId', $investmentId);

      // execute the SQL statement
      $stmt->execute();

      // fetch the investor record
      $investment = $stmt->fetch(PDO::FETCH_ASSOC);

      // if investor record found, return it
      if ($investment) {
        return $investment;
      } else {
        // if no investment record found, return false
        return false;
      }
    } catch (PDOException $e) {
      // if there is an error, return false
      return false;
    }
  }

  // Create new investment
  public function createInvestment($planId, $amount, $investorId)
  {
    try {
      // filter inputs for malicious things
      $planId = htmlspecialchars($planId);
      $amount = htmlspecialchars($amount);
      $investorId = htmlspecialchars($investorId);

      $status = "Active";


      $plans = new Plan();
      $data_plan = $plans->getPlanById($planId);


      $interest = $data_plan['interest_rate'];
      $duration = $data_plan['duration'];

      $monthlyRoi = (($interest / 100) * $amount);
      $totalRoi = $monthlyRoi * $duration;

      $date = new DateTime();
      $startDate = $date->format('Y-m-d');
      $expirationDate =  $date->add(new DateInterval('P' . $duration . 'M'));
      $expiration = $expirationDate->format('Y-m-d');



      $stmt = $this->pdo->prepare("SELECT id FROM tb_investments WHERE investor_id = :investorId AND status = :status");
      $stmt->bindParam(':investorId', $investorId);
      $stmt->bindParam(':status', $status);
      $stmt->execute();
      $existingInvestment = $stmt->fetch();

      if ($existingInvestment) {
        // if email address exists, return an error message in JSON format
        $valueReturn = array("response" => "error", "title" => "Oops!", "msg" => "Investor has an active plan.");
        return json_encode($valueReturn);
      }

      $investor = new Investor();
      $data_investor_balance = $investor->getInvestorBalance($investorId);

      if ($amount > $data_investor_balance) {
        // if email address exists, return an error message in JSON format
        $valueReturn = array("response" => "error", "title" => "Oops!", "msg" => "Investor has an Insufficent Capital.");
        return json_encode($valueReturn);
      }


      $stmt = $this->pdo->prepare("INSERT INTO tb_investments (plan_id, amount,	start_date,	expiration,	monthly_roi,	total_roi,	status,	investor_id) VALUES (:planId, :amount, :startDate, :expiration, :monthlyRoi, :totalRoi, :status, :investorId)");
      $stmt->bindParam(':planId', $planId);
      $stmt->bindParam(':amount', $amount);
      $stmt->bindParam(':startDate', $startDate);
      $stmt->bindParam(':expiration', $expiration);
      $stmt->bindParam(':monthlyRoi', $monthlyRoi);
      $stmt->bindParam(':totalRoi', $totalRoi);
      $stmt->bindParam(':status', $status);
      $stmt->bindParam(':investorId', $investorId);
      // execute the SQL statement to create a new Investment
      $stmt->execute();
      // if successful, return a success message in JSON format
      $value_return = array("response" => "success", "title" => "Success!", "msg" => "Investment created successfully.");
      return json_encode($value_return);
    } catch (PDOException $e) {
      // if there is an error, return an error message in JSON format
      $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " . $e->getMessage());
      return json_encode($value_return);
    }
  }

  // Update investment
  public function updateInvestment($id, $planId, $amount, $interest, $expiration, $duration, $status, $investorId)
  {
    $stmt = $this->pdo->prepare("UPDATE tb_investments SET plan_id = :planId, amount = :amount, interest = :interest, start_date = :start_date, expiration = :expiration, duration = :duration, status = :status, investor_id = :investorId WHERE id = :id");
    $stmt->bindParam(':planId', $planId);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':interest', $interest);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':expiration', $expiration);
    $stmt->bindParam(':duration', $duration);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':investorId', $investorId);
    $stmt->bindParam(':id', $id);
    if ($stmt->execute()) {
      return array('success' => true, 'message' => 'Investment updated successfully');
    } else {
      return array('success' => false, 'message' => 'Error updating investment');
    }
  }
}
