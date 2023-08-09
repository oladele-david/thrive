<?php
class Account
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }


    public function listAccounts()
    {
        try {
            // prepare SQL statement to retrieve all accounts from the database
            $stmt = $this->pdo->prepare("SELECT * FROM tb_accounts");

            // execute the SQL statement
            $stmt->execute();

            // fetch all accounts and return the result
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "accounts" => $accounts);
        } catch (PDOException $e) {
            // if there is an error, return false
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " );
        }
    }

    public function createAccount($id, $accountNumber, $phoneNo, $lastName, $firstName, $emailId, $password)
    {
        try {
            // filter inputs for malicious things
            $accountNumber = substr($accountNumber, -10);
            $accountNumber = htmlspecialchars($accountNumber);
            $phoneNo = htmlspecialchars($phoneNo);
            $lastName = htmlspecialchars($lastName);
            $firstName = htmlspecialchars($firstName);
            $emailId = htmlspecialchars($emailId);
            $password = $password;

            // check if the email exists
            if ($this->emailExists($emailId)) {
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Email already exists.");
                return json_encode($value_return);
            }

            // check if the phone number exists
            if ($this->phoneNoExists($phoneNo)) {
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Phone number already exists.");
                return json_encode($value_return);
            }

            // prepare SQL statement to insert a new account into the database
            $stmt = $this->pdo->prepare("INSERT INTO tb_accounts (id, account_number, phone_no, last_name, first_name, email_id, password) 
                                         VALUES (:id, :accountNumber, :phoneNo, :lastName, :firstName, :emailId, :password)");

            // bind the account inputs to the placeholders in the SQL statement
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':accountNumber', $accountNumber);
            $stmt->bindParam(':phoneNo', $phoneNo);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':emailId', $emailId);
            $stmt->bindParam(':password', base64_encode($password)); // hash the password before storing

            // execute the SQL statement to create a new account
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Account created successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: ");
            return json_encode($value_return);
        }
    }

    public function updateAccount($id, $accountNumber, $lastName, $firstName, $otherName, $motherMaidenName, $bvn, $nin, $gender, $dateOfBirth, $address, $city, $stateId, $countryId, $phoneNo, $emailId, $defaultBankId, $bankAccountNo, $bankAccountName)
    {
        try {
            // filter inputs for malicious things
            $accountNumber = htmlspecialchars($accountNumber);
            $lastName = htmlspecialchars($lastName);
            $firstName = htmlspecialchars($firstName);
            $otherName = htmlspecialchars($otherName);
            $motherMaidenName = htmlspecialchars($motherMaidenName);
            $bvn = htmlspecialchars($bvn);
            $nin = htmlspecialchars($nin);
            $gender = htmlspecialchars($gender);
            $dateOfBirth = htmlspecialchars($dateOfBirth);
            $address = htmlspecialchars($address);
            $city = htmlspecialchars($city);
            $stateId = htmlspecialchars($stateId);
            $countryId = htmlspecialchars($countryId);
            $phoneNo = htmlspecialchars($phoneNo);
            $emailId = htmlspecialchars($emailId);
            $defaultBankId = htmlspecialchars($defaultBankId);
            $bankAccountNo = htmlspecialchars($bankAccountNo);
            $bankAccountName = htmlspecialchars($bankAccountName);

            // check if account exists with the given ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_accounts WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$account) {
                // if account doesn't exist, return an error message in JSON format
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Account not found.");
                return json_encode($value_return);
            }

            // prepare SQL statement to update the account's information in the database
            $stmt = $this->pdo->prepare("UPDATE tb_accounts SET 
                                        account_number = :accountNumber,
                                        last_name = :lastName,
                                        first_name = :firstName,
                                        other_name = :otherName,
                                        mother_maiden_name = :motherMaidenName,
                                        bvn = :bvn,
                                        nin = :nin,
                                        gender = :gender,
                                        date_of_birth = :dateOfBirth,
                                        address = :address,
                                        city = :city,
                                        state_id = :stateId,
                                        country_id = :countryId,
                                        phone_no = :phoneNo,
                                        email_id = :emailId,
                                        default_bank_id = :defaultBankId,
                                        bank_account_no = :bankAccountNo,
                                        bank_account_name = :bankAccountName
                                    WHERE id = :id");

            // bind the account inputs to the placeholders in the SQL statement
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':accountNumber', $accountNumber);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':otherName', $otherName);
            $stmt->bindParam(':motherMaidenName', $motherMaidenName);
            $stmt->bindParam(':bvn', $bvn);
            $stmt->bindParam(':nin', $nin);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':dateOfBirth', $dateOfBirth);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':stateId', $stateId);
            $stmt->bindParam(':countryId', $countryId);
            $stmt->bindParam(':phoneNo', $phoneNo);
            $stmt->bindParam(':emailId', $emailId);
            $stmt->bindParam(':defaultBankId', $defaultBankId);
            $stmt->bindParam(':bankAccountNo', $bankAccountNo);
            $stmt->bindParam(':bankAccountName', $bankAccountName);

            // execute the SQL statement to update the account's information
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Account updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return a generic error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while updating the account. Please try again later.");
            return json_encode($value_return);
        }
    }

    public function updatePassword($id, $password)
    {
        try {
            // hash the password before storing
            $hashedPassword = base64_encode($password);

            // prepare SQL statement to update the account's password in the database
            $stmt = $this->pdo->prepare("UPDATE tb_accounts SET password = :password WHERE id = :id");

            // bind the account's ID and hashed password to the placeholders in the SQL statement
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);

            // execute the SQL statement to update the account's password
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Account password updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: ");
            return json_encode($value_return);
        }
    }

    public function updateBalance($id, $amount)
    {
        try {
            // Retrieve the current balance from the database
            $stmt = $this->pdo->prepare("SELECT account_balance FROM tb_accounts WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Calculate the new balance
            $currentBalance = $row['account_balance'];
            $newBalance = $currentBalance + $amount;

            // Update the balance in the database
            $stmt = $this->pdo->prepare("UPDATE tb_accounts SET account_balance = :newBalance WHERE id = :id");
            $stmt->bindParam(':newBalance', $newBalance);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // If successful, return true
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Balance updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // If there is an error, return false
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Balance not updated.");
            return json_encode($value_return);
        }
    }

    public function deductBalance($id, $amount)
    {
        try {
            // Retrieve the current balance from the database
            $stmt = $this->pdo->prepare("SELECT account_balance FROM tb_accounts WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Calculate the new balance
            $currentBalance = $row['account_balance'];
            $newBalance = $currentBalance - $amount;

            // Update the balance in the database
            $stmt = $this->pdo->prepare("UPDATE tb_accounts SET account_balance = :newBalance WHERE id = :id");
            $stmt->bindParam(':newBalance', $newBalance);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // If successful, return true
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Balance updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // If there is an error, return false
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Balance not updated.");
            return json_encode($value_return);
        }
    }

    public function updatePin($id, $pin)
    {
        try {
            // hash the pin before storing
            $hashedPin = base64_encode($pin);

            // prepare SQL statement to update the account's pin in the database
            $stmt = $this->pdo->prepare("UPDATE tb_accounts SET transaction_pin = :pin WHERE id = :id");

            // bind the account's ID and hashed pin to the placeholders in the SQL statement
            $stmt->bindParam(':pin', $hashedPin);
            $stmt->bindParam(':id', $id);

            // execute the SQL statement to update the account's pin
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Account pin updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: ");
            return json_encode($value_return);
        }
    }

    public function updateSecurityQuestion($id, $securityQuestion, $securityAnswer)
    {
        try {
            // filter inputs for malicious things
            $securityQuestion = htmlspecialchars($securityQuestion);
            $securityAnswer = htmlspecialchars($securityAnswer);
            // hash the pin before storing
            $hashedSecurityAnswer = base64_encode($securityAnswer);
            // prepare SQL statement to update the account's security question and answer in the database
            $stmt = $this->pdo->prepare("UPDATE tb_accounts SET security_question = :question, security_answer = :answer WHERE id = :id");

            // bind the account's ID, security question, and security answer to the placeholders in the SQL statement
            $stmt->bindParam(':question', $securityQuestion);
            $stmt->bindParam(':answer', $hashedSecurityAnswer);
            $stmt->bindParam(':id', $id);

            // execute the SQL statement to update the account's security question and answer
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Account security question and answer updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: ");
            return json_encode($value_return);
        }
    }

    public function getAccountById($id)
    {
        try {
            // prepare SQL statement to retrieve the account by ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_accounts WHERE id = :id");
            $stmt->bindParam(':id', $id);

            // execute the SQL statement
            $stmt->execute();

            // fetch the account record
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            // if account record found, return it
            if ($account) {
                return $account;
            } else {
                // if no account record found, return false
                return false;
            }
        } catch (PDOException $e) {
            // if there is an error, return false
            return false;
        }
    }

    // Account class (Accounts.php)
    public function getAccountBalance($accountId)
    {
        try {
            // Prepare SQL statement to retrieve the account balance for the specified account
            $stmt = $this->pdo->prepare("SELECT account_balance FROM tb_accounts WHERE id = :accountId");

            // Bind the account ID to the placeholder in the SQL statement
            $stmt->bindParam(':accountId', $accountId);

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the account balance
            $accountBalance = $stmt->fetchColumn();

            // Return the account balance
            return $accountBalance;
        } catch (PDOException $e) {
            // If there is an error, return false or handle the error as needed
            return false;
        }
    }


    private function emailExists($emailId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tb_accounts WHERE email_id = :emailId");
        $stmt->bindParam(':emailId', $emailId);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }

    private function phoneNoExists($phoneNo)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tb_accounts WHERE phone_no = :phoneNo");
        $stmt->bindParam(':phoneNo', $phoneNo);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }

    public function isEmailExists($emailId)
    {
        return $this->emailExists($emailId);
    }

    public function isPhoneNoExists($phoneNo)
    {
        return $this->phoneNoExists($phoneNo);
    }

    public function accountLogin($phoneNo, $password)
    {
        try {
            // Filter inputs for malicious things
            $phoneNo = htmlspecialchars($phoneNo);
            $password = htmlspecialchars($password);

            // Prepare SQL statement to retrieve the account by phone number and password
            $stmt = $this->pdo->prepare("SELECT * FROM tb_accounts WHERE phone_no = :phoneNo AND password = :password");
            $stmt->bindParam(':phoneNo', $phoneNo);
            $stmt->bindParam(':password', base64_encode($password));

            // Execute the SQL statement
            $stmt->execute();

            // Fetch the account record
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the account exists and the password is correct
            if ($account) {
                // Account login successful
                $value_return = array("response" => "success", "title" => "Success!", "msg" => "Account login successful.", "account" => $account);
                return json_encode($value_return);
            } else {
                // Account login failed
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Invalid login details.");
                return json_encode($value_return);
            }
        } catch (PDOException $e) {
            // If there is an error, return a generic error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong while logging in. Please try again later.");
            return json_encode($value_return);
        }
    }

    public function validatePin($id, $pin)
    {
        try {
            // Get the account details from the database
            $stmt = $this->pdo->prepare("SELECT transaction_pin, pin_attempts, last_pin_attempt FROM tb_accounts WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$account || !isset($account['transaction_pin'])) {
                // If the account does not exist or the PIN is not set, return false with locked status as false
                return array('valid' => false, 'locked' => false);
            }

            // Compare the provided PIN with the stored hashed PIN
            $hashedPin = base64_encode($pin);
            if ($hashedPin === $account['transaction_pin']) {
                // If the PIN is valid, reset the PIN attempts
                $this->updatePinAttempts($id, 0);
                return array('valid' => true, 'locked' => false);
            } else {
                // If the PIN is invalid, update the PIN attempts and lock the account if needed
                $pinAttempts = $account['pin_attempts'] + 1;
                $lastAttempt = strtotime($account['last_pin_attempt']);
                $now = time();
                $timeDifference = $now - $lastAttempt;

                // If more than 5 failed attempts within 5 minutes, lock the account for 5 minutes
                if ($pinAttempts >= 5 && $timeDifference <= 300) {
                    $this->updatePinAttempts($id, $pinAttempts);
                    return array('valid' => false, 'locked' => true);
                }

                // Update the PIN attempts and last PIN attempt time
                $updateStmt = $this->pdo->prepare("UPDATE tb_accounts SET last_pin_attempt = NOW() WHERE id = :id");
                $updateStmt->bindParam(':id', $id);
                $updateStmt->execute();

                $this->updatePinAttempts($id, $pinAttempts);
                return array('valid' => false, 'locked' => false);
            }
        } catch (PDOException $e) {
            // If there is an error, return false with locked status as false
            return array('valid' => false, 'locked' => false);
        }
    }

    

    public function updatePinAttempts($accountId, $attempts)
    {
        try {
            // Prepare SQL statement to update the PIN attempts in the database
            $stmt = $this->pdo->prepare("UPDATE tb_accounts SET pin_attempts = :attempts WHERE id = :accountId");

            // Bind the account ID and attempts to the placeholders in the SQL statement
            $stmt->bindParam(':attempts', $attempts);
            $stmt->bindParam(':accountId', $accountId);

            // Execute the SQL statement to update the PIN attempts
            $stmt->execute();
        } catch (PDOException $e) {
            // Handle the error if necessary
        }
    }

}
