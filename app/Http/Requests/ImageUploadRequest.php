<?php namespace Pixel\Http\Requests;

use Illuminate\Http\JsonResponse;

class ImageUploadRequest extends Request
{

    /**
     * Validation rules
     *
     * @return array
     */
    public function rules()
    {
        // Required
        $rules[] = 'required';

        // Mime types
        if ( $mimeTypes = config('pixel.upload.mimes') )
            $rules[] = 'mimes:' . implode(',', $mimeTypes);

        // Maximum filesize
        if ( $maxFilesize = config('pixel.upload.max_size') )
            $rules[] = 'max:' . $maxFilesize;

        // Valid image file
        $rules[] = 'valid_image';

        return [
            'image' => $rules
        ];
    }

    /**
     * Make sure image uploads are authorized
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
        if ($this->ajax() || $this->wantsJson())
        {
            // @todo: This is ridiculous
            $firstError = head(head($errors));
            return new JsonResponse($firstError, 422);
        }

        return $this->redirector->to($this->getRedirectUrl())
                                ->withInput($this->except($this->dontFlash))
                                ->withErrors($errors, $this->errorBag);
    }

}