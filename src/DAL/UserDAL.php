<?php
namespace DAL;

use model\base\User;
use PDO, Database;

class UserDAL
{
    private PDO $conn;
    
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function createUser(string $userId, string $email, string $username, string $password): void
    {
        $sql = "INSERT INTO users (userId, email, username, password, bio) 
                VALUES (:userId, :email, :username, :password, '')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function checkIfEmailExist(string $email): bool
    {
        $sql = "SELECT COUNT(*) FROM users u WHERE u.email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        
        $stmt->execute();
        $count = (int)$stmt->fetchColumn();

        return $count > 0;
    }

    public function checkIfUsernameExist(string $username): bool
    {
        $sql = "SELECT COUNT(*) FROM users u WHERE u.username = :username";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        
        $stmt->execute();
        $count = (int)$stmt->fetchColumn();

        return $count > 0;
    }

    public function getUserDetailByEmail(string $email): ?User
    {
        $sql = "SELECT u.userId, u.email, u.username, u.password, u.bio, u.profileImage, u.profileImageExt 
                FROM users u WHERE u.email = :email";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        
        $stmt->execute();
        $users = $this->toUserListWithPassword($stmt);
        
        return $users ? $users[0] : null;
    }

    public function getUserDetailByUserId(string $userId): ?User
    {
        $sql = "SELECT u.userId, u.email, u.username, u.bio, u.profileImage, u.profileImageExt 
                FROM users u WHERE u.userId = :userId";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        
        $stmt->execute();
        $users = $this->toUserList($stmt);
        
        return $users ? $users[0] : null;
    }

    private function toUserListWithPassword(\PDOStatement $stmt): array
    {
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User(
                $row['userId'],
                $row['email'],
                $row['username'],
                $row['password'],
                $row['bio'] ?? null,
                $row['profileImage'] ?? null,
                $row['profileImageExt'] ?? null
            );
            $users[] = $user;
        }
        return $users;
    }

    private function toUserList(\PDOStatement $stmt): array
    {
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user = new User(
                $row['userId'],
                $row['email'],
                $row['username'],
                null, // Password is not included
                $row['bio'] ?? null,
                $row['profileImage'] ?? null,
                $row['profileImageExt'] ?? null
            );
            $users[] = $user;
        }
        return $users;
    }
    
}











