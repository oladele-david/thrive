<?php
class Payment
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function createPayment($loanId, $paymentDate, $amount)
    {
        try {
            // prepare SQL statement to insert a new payment into the database
            $stmt = $this->pdo->prepare("INSERT INTO tb_payments (loan_id, payment_date, amount) 
                                        VALUES (:loanId, :paymentDate, :amount)");

            // bind the payment inputs to the placeholders in the SQL statement
            $stmt->bindParam(':loanId', $loanId);
            $stmt->bindParam(':paymentDate', $paymentDate);
            $stmt->bindParam(':amount', $amount);

            // execute the SQL statement to create a new payment
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Payment created successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while creating the payment. Please try again later.");
            return json_encode($value_return);
        }
    }

    public function updatePayment($paymentId, $paymentDate, $amount)
    {
        try {
            // check if payment exists with the given ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_payments WHERE id = :paymentId");
            $stmt->bindParam(':paymentId', $paymentId);
            $stmt->execute();
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$payment) {
                // if payment doesn't exist, return an error message in JSON format
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Payment not found.");
                return json_encode($value_return);
            }

            // prepare SQL statement to update the payment's information in the database
            $stmt = $this->pdo->prepare("UPDATE tb_payments SET payment_date = :paymentDate, amount = :amount WHERE id = :paymentId");

            // bind the payment inputs to the placeholders in the SQL statement
            $stmt->bindParam(':paymentId', $paymentId);
            $stmt->bindParam(':paymentDate', $paymentDate);
            $stmt->bindParam(':amount', $amount);

            // execute the SQL statement to update the payment's information
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Payment updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while updating the payment. Please try again later.");
            return json_encode($value_return);
        }
    }

    public function getPaymentsByLoanId($loanId)
    {
        try {
            // prepare SQL statement to retrieve payments by loan ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_payments WHERE loan_id = :loanId");
            $stmt->bindParam(':loanId', $loanId);

            // execute the SQL statement
            $stmt->execute();

            // fetch all payments associated with the loan and return the result
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "payments" => $payments);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while retrieving the payments: " );
            return json_encode($value_return);
        }
    }
    
    public function getPaymentById($paymentId)
    {
        try {
            // prepare SQL statement to retrieve the payment by ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_payments WHERE id = :paymentId");
            $stmt->bindParam(':paymentId', $paymentId);

            // execute the SQL statement
            $stmt->execute();

            // fetch the payment record
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);

            // if payment record found, return it
            if ($payment) {
                return $payment;
            } else {
                // if no payment record found, return false
                return false;
            }
        } catch (PDOException $e) {
            // if there is an error, return false
            return false;
        }
    }

    public function listPayments()
    {
        try {
            // prepare SQL statement to retrieve all payments from the database
            $stmt = $this->pdo->prepare("SELECT * FROM tb_payments");

            // execute the SQL statement
            $stmt->execute();

            // fetch all payments and return the result
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "payments" => $payments);
        } catch (PDOException $e) {
            // if there is an error, return false
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while listing the payments: " );
        }
    }

    
    
    // Additional methods for other payment management functionalities can be added here
}
