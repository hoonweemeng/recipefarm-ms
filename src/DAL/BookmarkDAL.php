<?php
namespace DAL;

use model\base\Bookmark;
use PDO, Database;

class BookmarkDAL
{
    private PDO $conn;
    
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function createBookmark(Bookmark $bookmark): void
    {
        $sql = "INSERT INTO bookmarks (bookmarkId, recipeId, userId) VALUES (:bookmarkId, :recipeId, :userId);";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':bookmarkId', $bookmark->bookmarkId, PDO::PARAM_STR);
        $stmt->bindValue(':recipeId', $bookmark->recipeId, PDO::PARAM_STR);
        $stmt->bindValue(':userId', $bookmark->userId, PDO::PARAM_STR);
        
        $this->conn->beginTransaction(); // Start transaction
        
        try {
            $stmt->execute(); // Execute the query
            $this->conn->commit(); // Commit changes

        } catch (\Exception $e) {
            $this->conn->rollBack(); // Rollback in case of error
            throw $e;
        }
    }

    public function deleteBookmark(string $bookmarkId): void
    {
        $sql = "DELETE FROM bookmarks WHERE bookmarkId = :bookmarkId;";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':bookmarkId', $bookmarkId, PDO::PARAM_STR);
        
        $this->conn->beginTransaction(); // Start a transaction
        
        try {
            $stmt->execute(); // Execute the stored procedure
            $this->conn->commit(); // Commit changes
        } catch (\Exception $e) {
            $this->conn->rollBack(); // Rollback if an error occurs
            throw $e; // Rethrow the exception
        }
    }

    public function GetBookmarkId(string $userId, string $recipeId): ?Bookmark
    {
        $sql = "SELECT b.bookmarkId, b.recipeId, b.userId, b.timestamp FROM bookmarks b WHERE b.recipeId = :recipeId AND b.userId = :userId;";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':recipeId', $recipeId, PDO::PARAM_STR);
    
        try {
            $stmt->execute(); // Execute stored procedure
            $bookmarks = $this->toBookmarkList($stmt); // Convert result set to list of Bookmark objects
    
            $stmt->closeCursor(); // Free up the result set (important!)
    
            return $bookmarks[0] ?? null;
        } catch (\Exception $e) {
            throw $e; // Rethrow the exception
        }
    }
    

    private function toBookmarkList(\PDOStatement $stmt): array
    {
        $bookmarks = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookmarks[] = new Bookmark(
                $row['bookmarkId'],
                $row['recipeId'],
                $row['userId'],
                $row['timestamp']
            );
        }
        
        return $bookmarks;
    }
    
}











