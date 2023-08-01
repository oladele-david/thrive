<?php
require_once('./includes/autoload.php');

class Investor
{

    private $pdo;


    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function getInvestorById($investorId)
    {
        try {
            // establish database connection

            // prepare SQL statement to retrieve the investor by ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_investors WHERE id = :investorId");
            $stmt->bindParam(':investorId', $investorId);

            // execute the SQL statement
            $stmt->execute();

            // fetch the investor record
            $investor = $stmt->fetch(PDO::FETCH_ASSOC);

            // if investor record found, return it
            if ($investor) {
                return $investor;
            } else {
                // if no investor record found, return false
                return false;
            }
        } catch (PDOException $e) {
            // if there is an error, return false
            return false;
        }
    }

    public function getInvestorBalance($investorId)
    {
        try {
            // establish database connection

            // prepare SQL statement to retrieve the investor by ID
            $stmt = $this->pdo->prepare("SELECT balance FROM tb_investors WHERE id = :investorId");
            $stmt->bindParam(':investorId', $investorId);

            // execute the SQL statement
            $stmt->execute();

            // fetch the investor record
            $investor = $stmt->fetch(PDO::FETCH_ASSOC);

            // if investor record found, return it
            if ($investor) {
                return $investor['balance'];;
            } else {
                // if no investor record found, return false
                return false;
            }
        } catch (PDOException $e) {
            // if there is an error, return false
            return false;
        }
    }

    public function listInvestors()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM tb_investors");
            $stmt->execute();
            $investors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array("response" => "success", "investors" => $investors);
        } catch (PDOException $e) {
            // if there is an error, return false
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " . $e->getMessage());
        }
    }

    public function createInvestor($firstName, $middleName, $lastName, $dateOfBirth, $maritalStatus, $stateOfOrigin, $stateOfResidence, $officeAddress, $emailAddress, $permanentHomeAddress, $phoneNumber, $password)
    {
        try {
            // filter inputs for malicious things
            $firstName = htmlspecialchars($firstName);
            $middleName = htmlspecialchars($middleName);
            $lastName = htmlspecialchars($lastName);
            $dateOfBirth = htmlspecialchars($dateOfBirth);
            $maritalStatus = htmlspecialchars($maritalStatus);
            $stateOfOrigin = htmlspecialchars($stateOfOrigin);
            $stateOfResidence = htmlspecialchars($stateOfResidence);
            $officeAddress = htmlspecialchars($officeAddress);
            $emailAddress = htmlspecialchars($emailAddress);
            $permanentHomeAddress = htmlspecialchars($permanentHomeAddress);
            $phoneNumber = htmlspecialchars($phoneNumber);
            $password = htmlspecialchars($password);

            // check if the email address already exists
            $stmt = $this->pdo->prepare("SELECT id FROM tb_investors WHERE email_address = :emailAddress");
            $stmt->bindParam(':emailAddress', $emailAddress);
            $stmt->execute();
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                // if email address exists, return an error message in JSON format
                $valueReturn = array("response" => "error", "title" => "Oops!", "msg" => "Email address already exists.");
                return json_encode($valueReturn);
            }

            // generate unique ID for the new investor
            $id = time();
            $referralCode = $this->generateReferralCode($id);

            // prepare SQL statement to insert a new investor into the database
            $stmt = $this->pdo->prepare("INSERT INTO tb_investors (id, first_name, middle_name, last_name, date_of_birth, marital_status, state_of_origin, state_of_residence, office_address, email_address, permanent_home_address, phone_number, password, referral_code) VALUES (:id, :firstName, :middleName, :lastName, :dateOfBirth, :maritalStatus, :stateOfOrigin, :stateOfResidence, :officeAddress, :emailAddress, :permanentHomeAddress, :phoneNumber, :password, :referralCode)");

            // bind the investor inputs to the placeholders in the SQL statement
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':middleName', $middleName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':dateOfBirth', $dateOfBirth);
            $stmt->bindParam(':maritalStatus', $maritalStatus);
            $stmt->bindParam(':stateOfOrigin', $stateOfOrigin);
            $stmt->bindParam(':stateOfResidence', $stateOfResidence);
            $stmt->bindParam(':officeAddress', $officeAddress);
            $stmt->bindParam(':emailAddress', $emailAddress);
            $stmt->bindParam(':permanentHomeAddress', $permanentHomeAddress);
            $stmt->bindParam(':phoneNumber', $phoneNumber);
            $stmt->bindParam(':password', base64_encode($password)); // hash the password before storing
            $stmt->bindParam(':referralCode', $referralCode); // hash the password before storing

            // execute the SQL statement to create a new investor
            $stmt->execute();

            // if successful, return a success message in JSON format
            $valueReturn = array("response" => "success", "title" => "Success!", "msg" => "Investor created successfully.");
            return json_encode($valueReturn);
        } catch (PDOException $e) {
            // if there is an error, return an error
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " . $e->getMessage());
            return json_encode($value_return);
        }
    }

    public function updatePersonalDetails($id, $firstName, $middleName, $lastName, $dateOfBirth, $maritalStatus, $stateOfOrigin, $stateOfResidence, $officeAddress, $emailAddress, $permanentHomeAddress, $phoneNumber, $status, $investorAccountName, $investorAccountNumber, $investorSortCode, $investorBankName, $investorBankBranch)
    {
        try {
            // filter inputs for malicious things
            $firstName = htmlspecialchars($firstName);
            $middleName = htmlspecialchars($middleName);
            $lastName = htmlspecialchars($lastName);
            $maritalStatus = htmlspecialchars($maritalStatus);
            $stateOfOrigin = htmlspecialchars($stateOfOrigin);
            $stateOfResidence = htmlspecialchars($stateOfResidence);
            $officeAddress = htmlspecialchars($officeAddress);
            $emailAddress = htmlspecialchars($emailAddress);
            $permanentHomeAddress = htmlspecialchars($permanentHomeAddress);
            $phoneNumber = htmlspecialchars($phoneNumber);
            $status = htmlspecialchars($status);

            $investorAccountName = htmlspecialchars($investorAccountName);
            $investorAccountNumber = htmlspecialchars($investorAccountNumber);
            $investorSortCode = htmlspecialchars($investorSortCode);
            $investorBankName = htmlspecialchars($investorBankName);
            $investorBankBranch = htmlspecialchars($investorBankBranch);


            // prepare SQL statement to update investor's personal details
            $stmt = $this->pdo->prepare("UPDATE tb_investors SET first_name = :firstName, middle_name = :middleName, last_name = :lastName, date_of_birth = :dateOfBirth, marital_status = :maritalStatus, state_of_origin = :stateOfOrigin, state_of_residence = :stateOfResidence, office_address = :officeAddress, email_address = :emailAddress, permanent_home_address = :permanentHomeAddress, phone_number = :phoneNumber, status = :status,  investor_account_name = :investorAccountName, investor_account_number = :investorAccountNumber, investor_sort_code = :investorSortCode, investor_bank_name = :investorBankName, investor_bank_branch = :investorBankBranch WHERE id = :id");

            // bind the investor personal details to the placeholders in the SQL statement
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':middleName', $middleName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':dateOfBirth', $dateOfBirth);
            $stmt->bindParam(':maritalStatus', $maritalStatus);
            $stmt->bindParam(':stateOfOrigin', $stateOfOrigin);
            $stmt->bindParam(':stateOfResidence', $stateOfResidence);
            $stmt->bindParam(':officeAddress', $officeAddress);
            $stmt->bindParam(':emailAddress', $emailAddress);
            $stmt->bindParam(':permanentHomeAddress', $permanentHomeAddress);
            $stmt->bindParam(':phoneNumber', $phoneNumber);
            $stmt->bindParam(':status', $status);

            $stmt->bindParam(':investorAccountName', $investorAccountName);
            $stmt->bindParam(':investorAccountNumber', $investorAccountNumber);
            $stmt->bindParam(':investorSortCode', $investorSortCode);
            $stmt->bindParam(':investorBankName', $investorBankName);
            $stmt->bindParam(':investorBankBranch', $investorBankBranch);

            // execute the SQL statement to update investor's personal details
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Investor personal details updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " . $e->getMessage());
            return json_encode($value_return);
        }
    }


    public function updateNextOfKinDetails($investorId, $nextOfKinFirstName, $nextOfKinMiddleName, $nextOfKinLastName, $nextOfKinStateOfOrigin, $nextOfKinStateOfResidence, $nextOfKinEmailAddress, $nextOfKinPermanentHomeAddress, $nextOfKinPhoneNumberOffice, $nextOfKinPhoneNumberMobile, $nextOfKinRelationshipToInvestor, $nextOfKinAccountName, $nextOfKinAccountNumber, $nextOfKinSortCode, $nextOfKinBankName, $nextOfKinBankBranch)
    {
        try {
            // prepare SQL statement to update next of kin details in the database
            $stmt = $this->pdo->prepare("UPDATE tb_investors SET next_of_kin_first_name = :nextOfKinFirstName, next_of_kin_middle_name = :nextOfKinMiddleName, next_of_kin_last_name = :nextOfKinLastName, next_of_kin_state_of_origin = :nextOfKinStateOfOrigin, next_of_kin_state_of_residence = :nextOfKinStateOfResidence, next_of_kin_email_address = :nextOfKinEmailAddress, next_of_kin_permanent_home_address = :nextOfKinPermanentHomeAddress, next_of_kin_phone_number_office = :nextOfKinPhoneNumberOffice, next_of_kin_phone_number_mobile = :nextOfKinPhoneNumberMobile, next_of_kin_relationship_to_investor = :nextOfKinRelationshipToInvestor, next_of_kin_account_name = :nextOfKinAccountName, next_of_kin_account_number = :nextOfKinAccountNumber, next_of_kin_sort_code = :nextOfKinSortCode, next_of_kin_bank_name = :nextOfKinBankName, next_of_kin_bank_branch = :nextOfKinBankBranch WHERE id = :investorId");

            // bind the investor and next of kin inputs to the placeholders in the SQL statement
            $stmt->bindParam(':investorId', $investorId);
            $stmt->bindParam(':nextOfKinFirstName', $nextOfKinFirstName);
            $stmt->bindParam(':nextOfKinMiddleName', $nextOfKinMiddleName);
            $stmt->bindParam(':nextOfKinLastName', $nextOfKinLastName);
            $stmt->bindParam(':nextOfKinStateOfOrigin', $nextOfKinStateOfOrigin);
            $stmt->bindParam(':nextOfKinStateOfResidence', $nextOfKinStateOfResidence);
            $stmt->bindParam(':nextOfKinEmailAddress', $nextOfKinEmailAddress);
            $stmt->bindParam(':nextOfKinPermanentHomeAddress', $nextOfKinPermanentHomeAddress);
            $stmt->bindParam(':nextOfKinPhoneNumberOffice', $nextOfKinPhoneNumberOffice);
            $stmt->bindParam(':nextOfKinPhoneNumberMobile', $nextOfKinPhoneNumberMobile);
            $stmt->bindParam(':nextOfKinRelationshipToInvestor', $nextOfKinRelationshipToInvestor);

            $stmt->bindParam(':nextOfKinAccountName', $nextOfKinAccountName);
            $stmt->bindParam(':nextOfKinAccountNumber', $nextOfKinAccountNumber);
            $stmt->bindParam(':nextOfKinSortCode', $nextOfKinSortCode);
            $stmt->bindParam(':nextOfKinBankName', $nextOfKinBankName);
            $stmt->bindParam(':nextOfKinBankBranch', $nextOfKinBankBranch);


            // execute the SQL statement to update the next of kin details
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Next of kin details updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " . $e->getMessage());
            return json_encode($value_return);
        }
    }


    public function getAllInvestorsBalance() {
        try {
            $stmt = $this->pdo->prepare("SELECT SUM(balance) AS total_balance FROM tb_investors");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_balance'];
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }


    // Get investor next of kin details
    public function getNextOfKinDetails($id)
    {
        $stmt = $this->pdo->prepare("SELECT next_of_kin_first_name, next_of_kin_middle_name, next_of_kin_last_name, next_of_kin_state_of_origin, next_of_kin_state_of_residence, next_of_kin_email_address, next_of_kin_permanent_home_address, next_of_kin_phone_number_office, next_of_kin_phone_number_mobile, next_of_kin_relationship_to_investor FROM tb_investors WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function generateReferralCode($investorId)
    {
        // generate referral code using investor id
        return "KSTMS" . str_pad($investorId, 5, "0", STR_PAD_LEFT);
    }

    public function updatePassword($id, $password)
    {
        try {
            // hash the password before storing
            $hashedPassword = base64_encode($password);

            // prepare SQL statement to update the admin user's password in the database
            $stmt = $this->pdo->prepare("UPDATE tb_investors SET password = :password WHERE id = :id");

            // bind the admin user's ID and hashed password to the placeholders in the SQL statement
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);

            // execute the SQL statement to update the admin user's password
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Investor's password updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " . $e->getMessage());
            return json_encode($value_return);
        }
    }
}
