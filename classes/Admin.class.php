<?php
require_once('./includes/autoload.php');

class Admin
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function getAdminById($adminId)
    {
        try {
            // establish database connection

            // prepare SQL statement to retrieve the admin by ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_admins WHERE id = :adminId");
            $stmt->bindParam(':adminId', $adminId);

            // execute the SQL statement
            $stmt->execute();

            // fetch the admin record
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // if admin record found, return it
            if ($admin) {
                return $admin;
            } else {
                // if no admin record found, return false
                return false;
            }
        } catch (PDOException $e) {
            // if there is an error, return false
            return false;
        }
    }

    public function listAdmins()
    {
        try {
            // prepare SQL statement to retrieve all admins from the database
            $stmt = $this->pdo->prepare("SELECT * FROM tb_admins");

            // execute the SQL statement
            $stmt->execute();

            // fetch all admins and return the result
            $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array("response" => "success", "admins" => $admins);
        } catch (PDOException $e) {
            // if there is an error, return false
            return array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " );
        }
    }

    public function createAdmin($fullName, $username, $password, $role, $status)
    {
        try {
            // filter inputs for malicious things
            $fullName = htmlspecialchars($fullName);
            $username = htmlspecialchars($username);
            $password = htmlspecialchars($password);
            $role = htmlspecialchars($role);
            $status = htmlspecialchars($status);

            // check if the username already exists
            $stmt = $this->pdo->prepare("SELECT id FROM tb_admins WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $existing_user = $stmt->fetch();

            if ($existing_user) {
                // if username exists, return an error message in JSON format
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Username already exists.");
                return json_encode($value_return);
            }

            // generate unique ID for the new admin user
            $id = time();

            // prepare SQL statement to insert a new admin user into the database
            $stmt = $this->pdo->prepare("INSERT INTO tb_admins (id, full_name, username, password, role, status) VALUES (:id, :fullName, :username, :password, :role, :status)");

            // bind the admin user inputs to the placeholders in the SQL statement
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':fullName', $fullName);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', base64_encode($password)); // hash the password before storing
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':status', $status);

            // execute the SQL statement to create a new admin user
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Admin user created successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " );
            return json_encode($value_return);
        }
    }

    public function updateAdmin($id, $fullName, $username, $role, $status)
    {
        try {
            // filter inputs for malicious things
            $fullName = htmlspecialchars($fullName);
            $username = htmlspecialchars($username);
            $role = htmlspecialchars($role);
            $status = htmlspecialchars($status);

            // check if admin user exists with the given ID
            $stmt = $this->pdo->prepare("SELECT * FROM tb_admins WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                // if admin user doesn't exist, return an error message in JSON format
                $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Admin user not found.");
                return json_encode($value_return);
            }

            // prepare SQL statement to update the admin user's information in the database
            $stmt = $this->pdo->prepare("UPDATE tb_admins SET full_name = :fullName, username = :username, role = :role, status = :status WHERE id = :id");

            // bind the admin user inputs to the placeholders in the SQL statement
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':fullName', $fullName);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':status', $status);

            // execute the SQL statement to update the admin user's information
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Admin user updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " );
            return json_encode($value_return);
        }
    }

    public function updatePassword($id, $password)
    {
        try {
            // hash the password before storing
            $hashedPassword = base64_encode($password);

            // prepare SQL statement to update the admin user's password in the database
            $stmt = $this->pdo->prepare("UPDATE tb_admins SET password = :password WHERE id = :id");

            // bind the admin user's ID and hashed password to the placeholders in the SQL statement
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':id', $id);

            // execute the SQL statement to update the admin user's password
            $stmt->execute();

            // if successful, return a success message in JSON format
            $value_return = array("response" => "success", "title" => "Success!", "msg" => "Admin user's password updated successfully.");
            return json_encode($value_return);
        } catch (PDOException $e) {
            // if there is an error, return an error message in JSON format
            $value_return = array("response" => "error", "title" => "Oops!", "msg" => "Something went wrong: " );
            return json_encode($value_return);
        }
    }

    public function login($username, $password)
    {
        // Check if the username exists
        if (!$this->usernameExists($username)) {
            return array("response" => "error", "title" => "Login Failed", "msg" => "Invalid username or password.");
        }

        // Retrieve the user from the database
        $stmt = $this->pdo->prepare("SELECT * FROM tb_admins WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the account is suspended
        if ($admin['status'] == 0) {
            return array("response" => "error", "title" => "Login Failed", "msg" => "Your account has been suspended.");
        }

        // Verify the password
        if ($password != $admin['password']) {
            return array("response" => "error", "title" => "Login Failed", "msg" => "Invalid username or password.");
        }

        // Generate a new session ID
        session_regenerate_id(true);

        // Store the admin's ID in the session
        $_SESSION['userInSession'] = $admin['id'];
        $_SESSION['status'] = $admin['status'];
        $_SESSION['fullName'] = $admin['full_name'];
        $_SESSION['role'] = $admin['role'];
        $_SESSION['username'] = $admin['username'];

        return array("response" => "success", "title" => "Login Successful", "msg" => "You have successfully logged in.");
    }

    private function usernameExists($username)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tb_admins WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0);
    }
}
