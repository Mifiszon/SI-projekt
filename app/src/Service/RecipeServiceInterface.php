<?php
/**
 * Recipe service interface.
 */

namespace App\Service;

use App\Dto\RecipeListInputFiltersDto;
use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Pagination\SlidingPagination;

/**
 * Interface RecipeServiceInterface.
 */
interface RecipeServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int                       $page    Page number
     * @param User                      $author  Recipes author
     * @param RecipeListInputFiltersDto $filters Filters
     *
     * @return PaginationInterface<SlidingPagination> Paginated list
     */
    public function getPaginatedList(int $page, User $author, RecipeListInputFiltersDto $filters): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Recipe $recipe Recipe entity
     */
    public function save(Recipe $recipe): void;

    /**
     * Delete entity.
     *
     * @param Recipe $recipe Recipe entity
     */
    public function delete(Recipe $recipe): void;

    /**
     * Add rating.
     *
     * @param Rating $rating rating
     *
     * @return void void
     */
    public function addRating(Rating $rating): void;

    /**
     * Get average rating.
     *
     * @param array $recipes recipes
     *
     * @return array array
     */
    public function getAverageRatings(array $recipes): array;

    /**
     * Get ratings for recipe.
     *
     * @param Recipe $recipe recipe
     *
     * @return array Array
     */
    public function getRatingsForRecipe(Recipe $recipe): array;

    /**
     * Find recipes by ingreinets.
     *
     * @param array $ingredients ingredients
     *
     * @return array array
     */
    public function findRecipesByIngredients(array $ingredients): array;
}
