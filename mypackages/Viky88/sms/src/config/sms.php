<?php
return [

    'default'=>[
        'debug'=>true,
        'debug_code'=>'123456',
        'length' => 6,
        'send_url' =>env('SMS_URL',''),
        'max_times'=>3,
    ]
];