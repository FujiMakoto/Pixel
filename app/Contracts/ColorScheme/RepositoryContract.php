<?php namespace Pixel\Contracts\ColorScheme;

use Pixel\Repositories\Collection;

/**
 * Interface RepositoryContract
 * @package Pixel\Contracts\ColorScheme
 */
interface RepositoryContract {

    /**
     * Return all color schemes
     *
     * @return Collection
     */
    public function all();

    /**
     * Retrieve a Color Scheme by its name
     *
     * @param string $name
     *
     * @return static
     * @throws ColorSchemeNotFoundException
     */
    public function getByName($name);

    /**
     * Delete and replace all existing color schemes
     *
     * @param array $schemes
     *
     * @return bool
     */
    public function synchronize(array $schemes);

}