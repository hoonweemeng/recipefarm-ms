<?php

namespace RecipeFarm\App\Controllers;

use RecipeFarm\App\Models\GenericModels\GenericResponse;
use RecipeFarm\App\Models\BaseModel\ValidationModel;

class RecipeController {
    public function __construct() {    }
    public function getRecipe($id) {
        // Simulate database interaction
        $recipes = [
            1 => ['id' => 1, 'name' => 'Pizza', 'ingredients' => 'Cheese, Tomato Sauce, Dough'],
            2 => ['id' => 2, 'name' => 'Pasta', 'ingredients' => 'Flour, Eggs, Olive Oil'],
        ];

        // Check if recipe exists
        if (isset($recipes[$id])) {
            $response = GenericResponse::success($recipes[$id]);
        } else {
            $response = GenericResponse::error("Recipe not found.");
        }

        // Return response as JSON
        header('Content-Type: application/json');
        echo json_encode($response->toArray());
    }

    public function createRecipe($request) {
        // Validate the request
        $validationErrors = [];
        if (empty($request['name'])) {
            $validationErrors[] = new ValidationModel('name', 'Name is required.');
        }
        if (empty($request['ingredients'])) {
            $validationErrors[] = new ValidationModel('ingredients', 'Ingredients are required.');
        }

        if (!empty($validationErrors)) {
            $response = GenericResponse::validationErrors(array_map(fn($error) => $error->toArray(), $validationErrors));
        } else {
            // Simulate saving to database
            $newRecipe = [
                'id' => rand(3, 100), // Simulate a new ID
                'name' => $request['name'],
                'ingredients' => $request['ingredients'],
            ];

            $response = GenericResponse::success($newRecipe);
        }

        // Return response as JSON
        header('Content-Type: application/json');
        echo json_encode($response->toArray());
    }
}
?>
