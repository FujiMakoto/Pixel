<?php namespace Pixel\Http\Requests;

use Pixel\Http\Requests\Request;

class UserLoginRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                'between:7,255',
                'email',
            ],
            'password' => [
                'required',
                'between:6,4096',
            ],
        ];
    }

    /**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
        return \Auth::guest();
	}

}
