<?php
/**
 * Created by PhpStorm.
 * User: Lekan Adigun
 * Date: 3/29/2018
 * Time: 5:27 AM
 */

    class DatabaseManager {

        protected $conn;

        public function __construct($dsn, $config) {
            try {
                $this->conn = new PDO($dsn, $config["username"], $config["password"]);
            }catch (Exception $ex) {
                echo $ex->getMessage();
            }
        }

        public function getUser($id) {

            $query = "SELECT * FROM users WHERE user_id = :id";
            $handle = $this->query($query);
            $handle->bindValue(':id', $id);
            $handle->execute();

            if ($handle->rowCount() <= 0)
                return null;

            return $handle->fetch(PDO::FETCH_ASSOC);
        }

        public function getUserByEmail($email) {

            if (empty($email)) return null;

            $handle = $this->query("SELECT * FROM users WHERE email = :m");
            $handle->bindValue(':m', $email);
            $handle->execute();

            if ($handle->rowCount() <= 0)
                return null;

            $user = $handle->fetch(PDO::FETCH_ASSOC);
            return $user;
        }

        public function createUser($data) {

            $query = "INSERT INTO users( phone, fullname, password)
                      VALUES(:phone, :fullname, :password)";

            $handle = $this->query($query);
            $handle->bindValue(':phone', $data['phone']);
            $handle->bindValue(':fullname', $data['fullname']);

            $passwordHashed = password_hash($data['password'], PASSWORD_DEFAULT);
            $handle->bindValue(':password', $passwordHashed);
            $err = $handle->execute();
            if (!$err) {
                $error = $this->conn->errorInfo();
                print_r($error);
                return "Unable to create user. Please retry. Error occurred ";
            }

            $newUser = $this->getUserByPhone($data['phone']);
            $this->createWallet($newUser["user_id"]);
            return $newUser;
        }

        public function query($sql) {
            return $this->conn->prepare($sql);
        }

        public function getUserByPhone($phone) {

            $query = "SELECT * FROM users WHERE phone = :ph";
            $cursor = $this->query($query);
            $cursor->bindValue(':ph', $phone);
            $cursor->execute();

            if ($cursor->rowCount() <= 0)
                return null;

            return $cursor->fetch(PDO::FETCH_ASSOC);
        }

        public function getWallet($id) {

            $cursor = $this->query("SELECT * FROM wallets WHERE user_id = :uid");
            $cursor->bindValue(':uid', $id);
            $cursor->execute();

            if ($cursor->rowCount() <= 0)
                return null;

            return $cursor->fetch(PDO::FETCH_ASSOC);
        }

        function performTransaction($fromUserId, $toUserId, $amount) {

            $from = $this->getUser($fromUserId);
            $to = $toUserId;

            if ($from == null)
                return "Sender not found";

            if ($to == null)
                return "Recipient not found";

            $fromWallet = $this->getWallet($fromUserId);
            $beneficiaryWallet = $this->getWallet($to["user_id"]);
            if ($fromWallet == null) {
                return "Sender wallet not found";
            }
            if ($beneficiaryWallet == null) {
                return "Recipient wallet not found";
            }

            if ($fromWallet["balance"] < $amount) {
                return "Insufficient funds";
            }

            $this->conn->beginTransaction();
            $newBalance = $fromWallet["balance"] - $amount;
            $handle = $this->query("UPDATE wallets SET balance = :bal WHERE user_id = :uid");
            $handle->bindValue(':bal', $newBalance);
            $handle->bindValue(':uid', $fromWallet["user_id"]);
            $r = $handle->execute();
            if (!$r) {
                $this->conn->rollBack();
                return "Failed to perform transaction. Please retry";
            }


            $beneficiaryBalance = $beneficiaryWallet["balance"] + $amount;
            $handle = $this->query("UPDATE wallets SET balance = :bal WHERE user_id = :uid");
            $handle->bindValue(':bal', $beneficiaryBalance);
            $handle->bindValue(':uid', $beneficiaryWallet["user_id"]);
            $r = $handle->execute();
            if (!$r) {
                $this->conn->rollBack();
                return "Failed to perform transaction. Please retry";
            }

            $this->conn->commit();
            return $amount . " sent successfully to " . $to["fullname"];
        }

        public function createWallet($user) {

            $query = "INSERT INTO wallets(user_id, balance)VALUES(:uid, :bal)";
            $handle = $this->query($query);
            $handle->bindValue(':uid', $user);
            $handle->bindValue(':bal', 0);
            $handle->execute();

        }

        public function creditWallet($user, $amount) {

            $wallet = $this->getWallet($user);
            if ($wallet == null || $wallet["user_id"] <= 0)
                return "Wallet not found";
            $query = "UPDATE wallets SET balance = :bal WHERE user_id = :uid";
            $newBalance = $wallet["balance"] + $amount;

            $this->conn->beginTransaction();
            $handle = $this->query($query);
            $handle->bindValue(':uid', $wallet["user_id"]);
            $handle->bindValue(':bal', $newBalance);
            $r = $handle->execute();
            if (!$r) {
                $this->conn->rollBack();
                return $this->getWallet($user);
            }
            $this->conn->commit();
            return $this->getWallet($user);
        }
    }
?>