<?php namespace Pixel;

use Illuminate\Database\Eloquent\Model;

class ColorScheme extends Model {

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

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];

}
