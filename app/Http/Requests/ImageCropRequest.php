<?php namespace Pixel\Http\Requests;

use Pixel\Contracts\Image\RepositoryContract;
use Pixel\Http\Requests\Request;

class ImageCropRequest extends Request {

    /**
     * @var RepositoryContract
     */
    protected $image;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->image = ($this->image instanceof RepositoryContract)
            ? $this->image
            : $this->image = \Image::get( $this->route('sid') );

        $coordsDefault = [
            'required',
            'numeric',
            'min:0',
        ];

        return [
            'coords' => [
                'required',
                'array'
            ],

            'coords.w' => ['max:'.$this->image->width]  + $coordsDefault,
            'coords.h' => ['max:'.$this->image->height] + $coordsDefault,
            'coords.x' => ['max:65535'] + $coordsDefault,
            'coords.y' => ['max:65535'] + $coordsDefault
        ];
    }

    /**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
        $this->image = ($this->image instanceof RepositoryContract)
            ? $this->image
            : $this->image = \Image::get( $this->route('sid') );

		return $this->image->canEdit();
	}

}
