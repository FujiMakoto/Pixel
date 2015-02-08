<?php namespace Pixel;

use Illuminate\Database\Eloquent\Model;

class ColorSchemes extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'colorschemes';

    /**
     * The columns guarded from mass assignment
     *
     * @var array
     */
    protected $guarded = ['id'];

}
