<?php namespace Pixel\Contracts\ColorScheme;

use Pixel\Contracts\Image\RepositoryContract as ImageRepositoryContract;
use Pixel\Repositories\Collection;

/**
 * Interface ColorSchemeContract
 * @package Pixel\Contracts\ColorScheme
 */
interface ColorSchemeContract {

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
     * @return RepositoryContract
     */
    public function get($name);

    /**
     * Return the closest matching color scheme
     * Accepts an Image Repository, RGB array or Hex string
     *
     * @param ImageRepositoryContract|array|string $value
     *
     * @return RepositoryContract
     */
    public function getClosest($value);

    /**
     * Delete and replace all existing color schemes
     *
     * @param array $schemes
     *
     * @return bool
     */
    public function synchronize(array $schemes);

    /**
     * Convert a hex color code to a RGB array
     *
     * @param string $hex
     *
     * @return array
     */
    public function hexToRgb($hex);

    /**
     * Convert a hex color code to a RGB array
     *
     * @param string $red
     * @param string $green
     * @param string $blue
     *
     * @return array
     * @internal param string $hex
     *
     */
    public function rgbToHex($red, $green, $blue);

}