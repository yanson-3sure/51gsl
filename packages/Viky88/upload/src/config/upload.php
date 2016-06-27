<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Media Upload Settings
     |--------------------------------------------------------------------------
     |
     | Set the directory wher your uploaded files will be placed.
     |
     */

    'dir' => 'uploads',
    'temp_dir' => 'uploads/temp',
    /*
    |--------------------------------------------------------------------------
    | Max File Size
    |--------------------------------------------------------------------------
    |
    | Make sure you give a limitation of file size uploaded (in KiloBytes).
    |
    */

    'max_size' => 10240,

    /*
     |--------------------------------------------------------------------------
     | Upload Types
     |--------------------------------------------------------------------------
     |
     | It's the flexibility of this package. You can define the type of upload
     | file methods. For example, you want to upload for profile picture,
     | article post, background, etc. Here is
     |
     */

    'types' => [
        'status' => [
            'route' => 'status-upload',
            'action'=> 'UploadController@index',
            'middleware' => 'oauth:1',
            'max_size' => 10240,  //| Make sure you give a limitation of file size uploaded (in KiloBytes).
            'format' => 'image',
            'image' => [
            //    'resize' => [1024, 768],
            //  'crop' => [800, 800],
            //  'fit' => [640, 640],
              'thumbs' => [
                 '66' => [66, 66],
                 '88' => [88, 88]
              ]
            ],
            'multiple' => false,
            'save_original' => true,
        ],
        'avatar' => [
            'route' => 'avatar-upload',
            'action'=> 'UploadController@postAvatar',
            'middleware' => 'auth',
            'max_size' => 10240,  //| Make sure you give a limitation of file size uploaded (in KiloBytes).
            'format' => 'image',
            'image' => [
                //    'resize' => [1024, 768],
                //  'crop' => [800, 800],
                //  'fit' => [640, 640],
                'thumbs' => [
                    '66' => [66, 66],
                    '88' => [88, 88],
                ]
            ],
            'multiple' => false,
            'save_original' => true,
        ],

        // ... put your custom type ...
    ],

];