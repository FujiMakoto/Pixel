<?php namespace Pixel\Http\Requests;

use Pixel\Http\Requests\Request;
use Illuminate\Http\JsonResponse;

class AjaxAccentuationRequest extends Request {

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'hex'   => [
				'required_without_all:red,green,blue',
				'between:4,7',
				'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
			],
			'red'   => [
				'required_without:hex',
				'required_with:green,blue',
				'integer',
				'between:0,255'
			],
			'green' => [
				'required_without:hex',
				'required_with:red,blue',
				'integer',
				'between:0,255'
			],
			'blue'  => [
				'required_without:hex',
				'required_with:red,green',
				'integer',
				'between:0,255'
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
		return true;
	}

	/**
	 * Get the proper failed validation response for the request.
	 *
	 * @param  array  $errors
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function response(array $errors)
	{
		return new JsonResponse($errors, 422);
	}

}
