<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Image Driver
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the image drivers you wish to use when
    | processing image manipulation tasks within the application.
    |
    | By default support is offered for Imagick and Gd, with Imagick being the
    | recommended default.
    |
    */

    'driver' => 'imagick',

    /*
    |--------------------------------------------------------------------------
    | Upload Settings
    |--------------------------------------------------------------------------
    |
    | Defines the validation rules enforced for image uploads.
    |
    | max_size is the maximum allowed filesize in kilobytes
    |
    */

    'upload' => [

        'max_size' => 10000,
        'mimes'    => ['jpeg', 'png', 'gif']

    ],

    /*
    |--------------------------------------------------------------------------
    | Image Scaling
    |--------------------------------------------------------------------------
    |
    | Each uploaded image will by default have two scaled copies generated for it.
    | A preview image, which is displayed on the images.show view, and a
    | thumbnail, which is used on album and other directory views.
    |
    | Here, you can configure the size and quality of these scaled images.
    |   Note: The quality setting is only relevant to jpeg uploads
    |
    | The preserve format setting will allow uploaded PNG / GIF images to be scaled while
    | keeping their native upload format. Disabling these settings will cause scaled images
    | to be converted to JPEG format instead.
    |   WARNING: Please re-generate images if changing this setting on a production website.
    |
    */

    'scaling' => [

        'preview' => [

            'quality' => 90,
            'width'   => '1130',
            'height'  => '636',
            'method'  => 'scale',
            'preserve_format' => true

        ],

        'thumbnail' => [

            'quality' => 87,
            'width'   => 300,
            'height'  => 225,
            'method'  => 'crop',
            'preserve_format' => false

        ],

    ]

];