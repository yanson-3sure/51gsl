<?php

return [
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
            'action'=> 'UploadController@postImage',
            'middleware' => 'oauth:1',
            'max_size' => 10240,  //| Make sure you give a limitation of file size uploaded (in KiloBytes).
            'format' => 'image',
        ],
        'avatar' => [
            'route' => 'avatar-upload',
            'action'=> 'UploadController@postAvatar',
            'middleware' => 'oauth',
            'max_size' => 10240,  //| Make sure you give a limitation of file size uploaded (in KiloBytes).
            'format' => 'image',
        ],

        // ... put your custom type ...
    ],

];