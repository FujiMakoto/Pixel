<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Scaling
    |--------------------------------------------------------------------------
    |
    | Each uploaded image will have two scaled copies generated for it.
    | A preview image, which is displayed on the images.show view, and a
    | thumbnail, which is used on album and other directory views.
    |
    | Here, you can configure the size and quality of these scaled images.
    |   Note: The quality setting is only relevant to jpeg uploads
    |
    | The preserve format setting will allow uploaded PNG / GIF images to be scaled while
    | keeping their native upload format. Disabling these settings will cause scaled images
    | to be converted to JPEG format instead.
    |   WARNING: Please re-generate scales if changing this setting on a production website.
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
            'width'   => 171,
            'height'  => 180,
            'method'  => 'crop',
            'preserve_format' => false

        ],

    ]

];