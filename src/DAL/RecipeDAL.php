<?php
namespace DAL;

use model\base\Recipe;
use model\genericmodel\Pagination;
use PDO, Database;

class RecipeDAL
{
    private PDO $conn;
    
    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    public function createRecipe(Recipe $recipe): void
    {
        $sql = "INSERT INTO recipes (recipeId, title, description, duration, servings, ingredients, instructions, recipeImage, recipeImageExt, userId) 
                VALUES (:recipeId, :title, :description, :duration, :servings, :ingredients, :instructions, :recipeImage, :recipeImageExt, :userId)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':recipeId', $recipe->recipeId, PDO::PARAM_STR);
        $stmt->bindValue(':title', $recipe->title, PDO::PARAM_STR);
        $stmt->bindValue(':description', $recipe->description, PDO::PARAM_STR);
        $stmt->bindValue(':duration', $recipe->duration, PDO::PARAM_INT);
        $stmt->bindValue(':servings', $recipe->servings, PDO::PARAM_INT);
        $stmt->bindValue(':ingredients', $recipe->ingredients, PDO::PARAM_STR);
        $stmt->bindValue(':instructions', $recipe->instructions, PDO::PARAM_STR);
        $stmt->bindValue(':recipeImage', $recipe->recipeImage, PDO::PARAM_STR);
        $stmt->bindValue(':recipeImageExt', $recipe->recipeImageExt, PDO::PARAM_STR);
        $stmt->bindValue(':userId', $recipe->userId, PDO::PARAM_STR);
        
        $this->conn->beginTransaction(); // Start transaction
        
        try {
            $stmt->execute(); // Execute the query
            $this->conn->commit(); // Commit changes

        } catch (\Exception $e) {
            $this->conn->rollBack(); // Rollback in case of error
            throw $e;
        }
    }

    public function updateRecipe(Recipe $recipe): void
    {
        $sql = "UPDATE recipes 
                SET title = :title, 
                    description = :description, 
                    duration = :duration, 
                    servings = :servings, 
                    ingredients = :ingredients, 
                    instructions = :instructions, 
                    recipeImage = :recipeImage, 
                    recipeImageExt = :recipeImageExt 
                WHERE recipeId = :recipeId";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':title', $recipe->title, PDO::PARAM_STR);
        $stmt->bindValue(':description', $recipe->description, PDO::PARAM_STR);
        $stmt->bindValue(':duration', $recipe->duration, PDO::PARAM_INT);
        $stmt->bindValue(':servings', $recipe->servings, PDO::PARAM_INT);
        $stmt->bindValue(':ingredients', $recipe->ingredients, PDO::PARAM_STR);
        $stmt->bindValue(':instructions', $recipe->instructions, PDO::PARAM_STR);
        $stmt->bindValue(':recipeImage', $recipe->recipeImage, PDO::PARAM_STR);
        $stmt->bindValue(':recipeImageExt', $recipe->recipeImageExt, PDO::PARAM_STR);
        $stmt->bindValue(':recipeId', $recipe->recipeId, PDO::PARAM_STR);
        
        $this->conn->beginTransaction(); // Start a transaction
        
        try {
            $stmt->execute(); // Execute the query
            $this->conn->commit(); // Commit changes
        } catch (\Exception $e) {
            $this->conn->rollBack(); // Rollback if an error occurs
            throw $e; // Rethrow the exception
        }
    }

    public function deleteRecipe(string $recipeId): void
    {
        $sql = "CALL DeleteRecipe(:recipeId)";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bindValue(':recipeId', $recipeId, PDO::PARAM_STR);
        
        $this->conn->beginTransaction(); // Start a transaction
        
        try {
            $stmt->execute(); // Execute the stored procedure
            $this->conn->commit(); // Commit changes
        } catch (\Exception $e) {
            $this->conn->rollBack(); // Rollback if an error occurs
            throw $e; // Rethrow the exception
        }
    }

    public function getRecipeDetail(string $recipeId): ?Recipe
    {
        $sql = "SELECT r.recipeId, r.title, r.description, r.duration, r.servings, r.ingredients, r.instructions, 
                    r.recipeImage, r.recipeImageExt, r.timestamp, r.userId, r.likes 
                FROM recipes r 
                WHERE r.recipeId = :recipeId";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':recipeId', $recipeId, PDO::PARAM_STR);
        
        try {
            $stmt->execute(); // Execute stored procedure
            $recipes = $this->toRecipeList($stmt); // Convert result set to list of Recipe objects
    
            $stmt->closeCursor(); // Free up the result set (important!)
    
            return $recipes[0] ?? null; // Return the first recipe or null if none found;
        } catch (\Exception $e) {
            throw $e; // Rethrow the exception
        }
    }

    public function getBookmarkRecipes(string $userId, Pagination $pagination): array
    {
        $sql = "CALL GetBookmarkRecipes(:userId, :pageNo, :pageSize)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':pageNo', $pagination->currentPage, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', $pagination->pageSize, PDO::PARAM_INT);
    
        try {
            $stmt->execute(); // Execute stored procedure
            $recipes = $this->toRecipeList($stmt); // Convert result set to list of Recipe objects
    
            $stmt->closeCursor(); // Free up the result set (important!)
    
            return $recipes;
        } catch (\Exception $e) {
            throw $e; // Rethrow the exception
        }
    }
    
    public function getLatestRecipes(Pagination $pagination): array
    {
        $sql = "CALL GetLatestRecipes(:pageNo, :pageSize)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':pageNo', $pagination->currentPage, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', $pagination->pageSize, PDO::PARAM_INT);
    
        try {
            $stmt->execute(); // Execute stored procedure
            $recipes = $this->toRecipeList($stmt); // Convert result set to list of Recipe objects
    
            $stmt->closeCursor(); // Free up the result set (important!)
    
            return $recipes;
        } catch (\Exception $e) {
            throw $e; // Rethrow the exception
        }
    }

    public function getUserRecipes(string $userId, Pagination $pagination): array
    {
        $sql = "CALL GetUserRecipes(:userId, :pageNo, :pageSize)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':pageNo', $pagination->currentPage, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', $pagination->pageSize, PDO::PARAM_INT);
    
        try {
            $stmt->execute(); // Execute stored procedure
            $recipes = $this->toRecipeList($stmt); // Convert result set to list of Recipe objects
    
            $stmt->closeCursor(); // Free up the result set (important!)
    
            return $recipes;
        } catch (\Exception $e) {
            throw $e; // Rethrow the exception
        }
    }

    public function searchRecipe(string $name, Pagination $pagination): array
    {
        $sql = "CALL SearchRecipesByTitle(:name, :pageNo, :pageSize)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':pageNo', $pagination->currentPage, PDO::PARAM_INT);
        $stmt->bindValue(':pageSize', $pagination->pageSize, PDO::PARAM_INT);
        
        try {
            $stmt->execute(); // Execute stored procedure
            $recipes = $this->toRecipeList($stmt); // Convert result set to list of Recipe objects
    
            $stmt->closeCursor(); // Free up the result set (important!)
    
            return $recipes;
        } catch (\Exception $e) {
            throw $e; // Rethrow the exception
        }
    }
    

    private function toRecipeList(\PDOStatement $stmt): array
    {
        $recipes = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recipes[] = new Recipe(
                $row['recipeId'],
                $row['title'],
                $row['description'],
                (int)$row['duration'],
                (int)$row['servings'],
                $row['ingredients'],
                $row['instructions'],
                $row['recipeImage'] ?? null,
                $row['recipeImageExt'] ?? null,
                $row['timestamp'],
                $row['userId'],
                (int)$row['likes']
            );
        }
        
        return $recipes;
    }
    
}











