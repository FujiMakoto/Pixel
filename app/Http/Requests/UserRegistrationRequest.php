<?php namespace Pixel\Http\Requests;

use Pixel\Http\Requests\Request;

class UserRegistrationRequest extends Request {

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'alpha_dash',
                'between:3,20',
                'unique:users'
            ],
            'email' => [
                'required',
                'between:7,255',
                'email',
                'unique:users'
            ],
            'password' => [
                'required',
                'between:6,4096',
                'confirmed'
            ],
            'g-recaptcha-response' => [
                'required',
                'recaptcha'
            ]
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
