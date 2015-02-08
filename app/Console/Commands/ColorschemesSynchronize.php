<?php namespace Pixel\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Danmichaelo\Coma\sRGB;

class ColorschemesSynchronize extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'colorschemes:synchronize';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Synchronize color schemes in the headers.less template into the database';

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		// Get the contents of our headers.less template
		$lessTemplate = \File::get( $lessPath = base_path('resources/assets/less/pixel/header.less') );

		// Parse the color schemes using regular expression matching
		$colorSchemes = [];
		$pattern      = '/#header\\.(?P<names>[A-Za-z-]+) {\\n\\s+@primaryColor: (?P<colors>[#0-9A-Za-z]+);/';
		preg_match_all($pattern, $lessTemplate, $colorSchemes);

		// Empty result?
		if ( ! $count = count($colorSchemes['names']) ) {
			return $this->error("No valid color schemes found in {$lessPath}");
		}

		// Loop through the color schemes and insert them into our database
		$toBeInserted = [];
		foreach ($colorSchemes['names'] as $key => $name) {
			// Get the RGB values from our hex string
			$color = $colorSchemes['colors'][$key];
			$sRGB  = new sRGB($color);

			// Add to our insert array
			$toBeInserted[] = [
				'name'  => $name,
				'red'   => $sRGB->r,
				'green' => $sRGB->g,
				'blue'  => $sRGB->b,
				'hex'   => $sRGB->toHex()
			];

			$this->comment("[{$color}] {$name} to be created");
		}

		// Synchronize our color schemes with the backend
		if ( \ColorScheme::synchronize($toBeInserted) )
			return $this->info("{$count} color schemes saved");
	}
}
