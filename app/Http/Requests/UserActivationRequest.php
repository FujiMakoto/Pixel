<?php namespace Pixel\Http\Requests;

use Pixel\Http\Requests\Request;

class UserActivationRequest extends Request {

    /**
     * The route to redirect to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'users.auth.activate';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uid' => [
                'required_without:email'
            ],
            'email' => [
                'required_without:uid',
                'between:7,255',
                'email'
            ],
            'code' => [
                'required',
                'size:40',
                'alpha_num'
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
