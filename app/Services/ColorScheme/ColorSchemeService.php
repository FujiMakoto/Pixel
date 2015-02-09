<?php namespace Pixel\Services\ColorScheme;

use Pixel\Contracts\ColorScheme\ColorSchemeContract;
use Pixel\Contracts\ColorScheme\RepositoryContract;
use Pixel\Contracts\Image\RepositoryContract as ImageRepositoryContract;
use Pixel\Repositories\Collection;
use Danmichaelo\Coma\ColorDistance;
use Danmichaelo\Coma\sRGB;

/**
 * Class ColorSchemeService
 * @package Pixel\Services\ColorScheme
 */
class ColorSchemeService implements ColorSchemeContract {

    /**
     * @var RepositoryContract
     */
    protected $colorSchemeRepo;

    /**
     * Constructor
     *
     * @param RepositoryContract $colorSchemeRepo
     */
    public function __construct(RepositoryContract $colorSchemeRepo)
    {
        $this->colorSchemeRepo = $colorSchemeRepo;
    }

    /**
     * Return all color schemes
     *
     * @return Collection
     */
    public function all()
    {
        return $this->colorSchemeRepo->all();
    }

    /**
     * Retrieve a Color Scheme by its name
     *
     * @param string $name
     *
     * @return RepositoryContract
     */
    public function get($name)
    {
        return $this->colorSchemeRepo->getByName($name);
    }

    /**
     * Return the closest matching color scheme
     * Accepts an Image Repository, RGB array or Hex string
     *
     * @param ImageRepositoryContract|array|string $value
     *
     * @return RepositoryContract
     */
    public function getClosest($value)
    {
        $color = [];
        $colorDistance = new ColorDistance();

        // Is our value an instance of our Image repository?
        if ($value instanceof ImageRepositoryContract)
        {
            $color['red']   = $value->red;
            $color['green'] = $value->green;
            $color['blue']  = $value->blue;
        }
        // Is it already an array of RGB values?
        elseif ( is_array($value) )
        {
            $color = $value;
        }
        // If we're still here, assume we have a hex string
        else
        {
            $color = $this->hexToRgb($value);
        }

        // First, fetch all of our available color schemes
        $colorSchemes = $this->all();

        // Now, loop through them and calculate the color distance between each
        $closest['name']     = 'purple';
        $closest['distance'] = 0;

        foreach ($colorSchemes as $colorScheme)
        {
            // Calculate the color distance
            $color1   = new sRGB($color['red'], $color['green'], $color['blue']);
            $color2   = new sRGB($colorScheme->red, $colorScheme->green, $colorScheme->blue);
            $distance = $colorDistance->cie94($color1, $color2);

            // Is this color scheme closer than our current one?
            if ( ($distance < $closest['distance']) || ! $closest['distance'] ) {
                $closest['scheme']   = $colorScheme;
                $closest['distance'] = $distance;
            }
        }

        // Return the closest color scheme
        return $closest['scheme'];
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
        return $this->colorSchemeRepo->synchronize($schemes);
    }

    /**
     * Convert a hex color code to a RGB array
     *
     * @param string $hex
     *
     * @return array
     */
    public function hexToRgb($hex)
    {
        // Convert the hex string to an array of RGB values
        $sRGB = new sRGB($hex);
        $color['red']   = $sRGB->r;
        $color['green'] = $sRGB->g;
        $color['blue']  = $sRGB->b;

        return $color;
    }

    /**
     * Convert a hex color code to a RGB array
     *
     * @param string $red
     * @param string $green
     * @param string $blue
     *
     * @return string
     */
    public function rgbToHex($red, $green, $blue)
    {
        // Convert the RGB array to a hex string
        $sRGB = new sRGB($red, $green, $blue);
        return $sRGB->toHex();
    }

}