CREATE DATABASE recipefarm;
USE recipefarm

CREATE TABLE users (
    userId CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password varchar(255) NOT NULL,
    bio text,
    profileImage char(36),
    profileImageExt varchar(4)
)
ENGINE = INNODB;


CREATE TABLE recipes (
    recipeId CHAR(36) PRIMARY KEY DEFAULT (uuid()),
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    duration INT NOT NULL,
    servings INT NOT NULL,
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    recipeImage CHAR(36),
    recipeImageExt VARCHAR(4),
    timestamp DATETIME NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
    userId CHAR(36) NOT NULL,
    likes INT NOT NULL DEFAULT 0,
    CONSTRAINT FOREIGN KEY (userId) REFERENCES users(userId),
    FULLTEXT(title, description) -- Add FULLTEXT index here
) ENGINE = InnoDB;


CREATE TABLE likes (
    likeId CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    recipeId CHAR(36) NOT NULL,
    userId CHAR(36)NOT NULL,
    CONSTRAINT FOREIGN KEY (recipeId) REFERENCES recipes(recipeId),
    CONSTRAINT FOREIGN KEY (userId) REFERENCES users(userId),
    CONSTRAINT unique_user_recipe_like UNIQUE (userId, recipeId)
)
ENGINE = INNODB;

CREATE TABLE bookmarks (
    bookmarkId CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    recipeId CHAR(36) NOT NULL,
    userId CHAR(36)NOT NULL,
    timestamp datetime NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT FOREIGN KEY (recipeId) REFERENCES recipes(recipeId),
    CONSTRAINT FOREIGN KEY (userId) REFERENCES users(userId),
    CONSTRAINT unique_user_recipe_bookmarks UNIQUE (userId, recipeId)
)
ENGINE = INNODB;

CREATE TRIGGER trigAddRecipeLikes
AFTER INSERT
ON likes
FOR EACH ROW
BEGIN
    UPDATE recipes
    SET likes = likes + 1
    WHERE recipeId = NEW.recipeId;
END;

CREATE TRIGGER trigRemoveRecipeLikes
AFTER DELETE
ON likes
FOR EACH ROW
BEGIN
    UPDATE recipes
    SET likes = likes - 1
    WHERE recipeId = OLD.recipeId;
END;

CREATE PROCEDURE SearchRecipes (
    IN searchQuery VARCHAR(255), -- The keyword(s) to search for
    IN page INT,                 -- The page number for pagination
    IN pageSize INT              -- The number of results per page
)
BEGIN
    DECLARE offsetValue INT;

    -- Calculate the offset for pagination
    SET offsetValue = (page - 1) * pageSize;

    -- Search query with pagination, keyword relevance, and timestamp sorting
    SELECT 
        recipeId,
        title,
        description,
        duration,
        servings,
        ingredients,
        instructions,
        recipeImage,
        recipeImageExt,
        timestamp,
        userId,
        likes,
        MATCH(title, description) AGAINST(searchQuery IN NATURAL LANGUAGE MODE) AS relevance
    FROM 
        recipes
    WHERE 
        MATCH(title, description) AGAINST(searchQuery IN NATURAL LANGUAGE MODE)
    ORDER BY 
        relevance DESC, -- Most keyword matches at the top
        likes DESC,  -- Most likes recipes next
        timestamp DESC  -- Most recent recipes next
    LIMIT 
        offsetValue, pageSize; -- Pagination

END;


CREATE PROCEDURE GetLatestRecipes(
    IN page INT,
    IN pageSize INT
)
BEGIN
    -- Declare variables for offset calculation
    DECLARE offsetValue INT;

    -- Calculate the offset for pagination
    SET offsetValue = (page - 1) * pageSize;

    -- Perform the query to get the latest recipes, sorted by timestamp
    SELECT r.recipeId, r.title, r.description, r.duration, r.servings, r.instructions, r.recipeImage, r.recipeImageExt, r.timestamp, r.userId, r.likes
    FROM recipes r
    ORDER BY r.timestamp DESC 
    LIMIT pageSize OFFSET offsetValue;
END;

CREATE PROCEDURE GetBookmarkRecipes(
    IN currentUserId CHAR(36),
    IN page INT,
    IN pageSize INT
)
BEGIN
    -- Declare variables for offset calculation
    DECLARE offsetValue INT;

    -- Calculate the offset for pagination
    SET offsetValue = (page - 1) * pageSize;

    -- Perform the query to get the latest recipes, sorted by timestamp
    SELECT r.recipeId, r.title, r.description, r.duration, r.servings, r.instructions, r.recipeImage, r.recipeImageExt, r.timestamp, r.userId, r.likes
    FROM recipes r 
    INNER JOIN bookmarks b ON r.recipeId = b.recipeId 
    WHERE b.userId = currentUserId 
    ORDER BY b.timestamp DESC
    LIMIT pageSize OFFSET offsetValue;
END;

CREATE PROCEDURE DeleteRecipe(IN recipe_id CHAR(36))
BEGIN
    START TRANSACTION;

    -- Delete associated likes
    DELETE FROM likes WHERE recipeId = recipe_id;

    -- Delete associated bookmarks
    DELETE FROM bookmarks WHERE recipeId = recipe_id;

    -- Delete the recipe itself
    DELETE FROM recipes WHERE recipeId = recipe_id;

    COMMIT;
END;
