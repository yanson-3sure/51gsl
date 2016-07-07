<?php
return [
    'name' => "股思录",
    'title' => "股思录",
    'subtitle' => '股思录',
    'description' => '股思录',
    'author' => 'hx',

    'admin_user_ids' =>[1],
    'black_role' => [-1],
    'home_timeline_size' => env('HOME_TIMELINE_SIZE',1000),
    'posts_per_pass'=>env('POSTS_PER_PASS',1000),
    'status_praise_size' => env('STATUS_PRAISE_SIZE',30),
    'status_comment_size' => env('STATUS_COMMENT_SIZE',20),
    'status_home_follow_size' => env('STATUS_HOME_follow_SIZE',300), //我的关注 ,获取每位分析师的直播数量

    'object_type' => [
        'praise'=>['status','answer'],
        'comment'=>['status'],
    ],

    'filter_analyst_user_ids' => ['4'],

    'debug' => [
        'name'=>env('DEBUG_NAME','debug'),
        'value'=>env('DEBUG_VALUE',1)
    ],


    'posts_per_page' => 10,
    'page_size' => env('PAGE_SIZE',10),
    'rss_size' => 25,
    'contact_email' => env('MAIL_FROM',''),
    'uploads' => [
        'storage' => 'local',
        'webpath' => '/uploads/',
    ],
    'product_price'=>[
        'analyst'=>[
            '2'=>'1000',
            '3'=>'1000',
            '5'=>'0',
            '6'=>'0',
            '7'=>'1000',
            '8'=>'800',
            '61'=>'1000'
        ]
    ],
    //同步第一现场配置
    'dyxc'=>[
        'debug' => env('DYXC_DEBUG',true),
        'url'=> env('DYXC_URL',''),
        'image_uploadurl'=> env('DYXC_IMAGE_UPLOADURL',''),
        'image_prefix'=>'',
        'key'=>'dyxc',
        'users'=>[
            '2'=>'黄蓉儿',
            '3'=>'股海侠客',
            '5'=>'段誉',
            '6'=>'杨过',
            '7'=>'福牛歌',
            '8'=>'风清扬',
            '61'=>'财富至尊'
        ],
    ],
    'auto_remember_me' => env('AUTO_REMEMBER_ME','true'),

    'version' =>[
        'main'=>env('VERSION_MAIN','1.0'),
        'css' =>env('VERSION_CSS','20160517'),
        'js'  =>env('VERSION_JS','20160517'),
    ]
];