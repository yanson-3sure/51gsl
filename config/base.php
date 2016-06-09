<?php
return [
    'name' => "股思录",
    'title' => "股思录",
    'subtitle' => '股思录',
    'description' => '股思录',
    'author' => 'hx',

    'admin_user_ids' =>[4],
    'black_role' => [-1],
    'home_timeline_size' => env('HOME_TIMELINE_SIZE',1000),
    'posts_per_pass'=>env('POSTS_PER_PASS',1000),
    'status_praise_size' => env('STATUS_PRAISE_SIZE',30),
    'status_comment_size' => env('STATUS_COMMENT_SIZE',20),

    'object_type' => [
        'praise'=>['status'],
        'comment'=>['status'],
    ],

    'posts_per_page' => 10,
    'page_size' => 10,
    'rss_size' => 25,
    'contact_email' => env('MAIL_FROM',''),
    'uploads' => [
        'storage' => 'local',
        'webpath' => '/uploads/',
    ],
    //同步第一现场配置
    'dyxc'=>[
        'debug' => env('DYXC_DEBUG',true),
        'url'=> env('DYXC_URL',''),
        'image_uploadurl'=> env('DYXC_IMAGE_UPLOADURL',''),
        'image_prefix'=>'',
        'key'=>'dyxc',
        'users'=>[
            '7'=>'福牛歌',
        ],
    ],
    'auto_remember_me' => env('AUTO_REMEMBER_ME','true'),

    'version' =>[
        'main'=>env('VERSION_MAIN','1.0'),
        'css' =>env('VERSION_CSS','20160517'),
        'js'  =>env('VERSION_JS','20160517'),
    ]
];