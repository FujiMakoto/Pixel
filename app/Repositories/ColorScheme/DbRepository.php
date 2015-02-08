<?php namespace Pixel\Repositories\ColorScheme;

use Pixel\Contracts\ColorScheme\RepositoryContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Pixel\Exceptions\ColorScheme\ColorSchemeNotFoundException;
use Pixel\Repositories\Repository;
use Pixel\Repositories\Collection;
use Pixel\ColorSchemes as ColorSchemesModel;

/**
 * Class DbRepository
 * @package Pixel\Repositories\ColorScheme
 */
class DbRepository extends Repository implements RepositoryContract {

    /**
     * Return all color schemes
     *
     * @return Collection
     */
    public function all()
    {
        // Set up a new Collection instance and get our Color Schemes
        $collection   = new Collection();
        $colorSchemes = ColorSchemesModel::all();

        foreach ($colorSchemes as $colorScheme)
            $collection->add( new static($colorScheme->toArray()) );

        // Return the collection
        return $collection;
    }

    /**
     * Retrieve a Color Scheme by its name
     *
     * @param string $name
     *
     * @return static
     * @throws ColorSchemeNotFoundException
     */
    public function getByName($name)
    {
        try {
            $colorScheme = ColorSchemesModel::whereName($name)->firstOrFail()->toArray();
        } catch (ModelNotFoundException $e) {
            throw new ColorSchemeNotFoundException("No color scheme by the name \"{$name}\" could be found");
        }
        $this->fill($colorScheme);

        return $this;
    }

    /**
     * Delete and replace all existing color schemes
     *
     * @param array $schemes
     *
     * @return bool
     */
    public function synchronize(array $schemes)
    {
        // Delete any existing records
        ColorSchemesModel::truncate();

        // Bulk insert our new color schemes
        ColorSchemesModel::insert($schemes);

        return true;
    }

}