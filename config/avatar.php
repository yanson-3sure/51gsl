<?php
return [
    'default-image'=>'home-bg.jpg',
    'hash-key'=>env('AVATAR_HASH_KEY',''),
    'url-pre'=>'/uploads/avatar/',
    'uploads'=> [
        'path'=>'/uploads/avatar/'
    ],
    'wechat-size'=>[
        '0' => '640',
        '46'=> '46',
        '64'=> '64',
        '96'=> '96',
    ],

    'size'=>[
        '0'  => ['640','/uploads/avatar/0.jpg'],
        '28' => ['46','/uploads/avatar/28.jpg'],
        '46' => ['46','/uploads/avatar/46.jpg'],
        '64' => ['64','/uploads/avatar/64.jpg'],
        '80' => ['96','/uploads/avatar/80.jpg'],
        '96' => ['96','/uploads/avatar/80.jpg'],
    ],

];