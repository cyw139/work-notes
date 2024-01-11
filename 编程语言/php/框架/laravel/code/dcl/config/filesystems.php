<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
        ],
        'upyun_kspx_video' => [
            'driver'        => 'upyun',
            'serviceName'   => env('UPYUN_VIDEO_SERVICE_NAME'),// 服务名字
            'operator'      => env('UPYUN_VIDEO_OPERATOR'), // 操作员的名字
            'password'      => env('UPYUN_VIDEO_PASSWORD'), // 操作员的密码
            'domain'        => env('UPYUN_VIDEO_DOMAIN'), // 服务分配的域名
            'protocol'     => env('UPYUN_VIDEO_PROTOCOL', 'http'), // 服务使用的协议，如需使用 http，在此配置 http
        ],
        'upyun_exam_system_picture' => [
            'driver'        => 'upyun',
            'serviceName'   => env('UPYUN_EXAM_SYSTEM_PICTURE_SERVICE_NAME'),// 服务名字
            'operator'      => env('UPYUN_EXAM_SYSTEM_PICTURE_OPERATOR'), // 操作员的名字
            'password'      => env('UPYUN_EXAM_SYSTEM_PICTURE_PASSWORD'), // 操作员的密码
            'domain'        => env('UPYUN_EXAM_SYSTEM_PICTURE_DOMAIN'), // 服务分配的域名
            'protocol'     => env('UPYUN_EXAM_SYSTEM_PICTURE_PROTOCOL', 'http'), // 服务使用的协议，如需使用 http，在此配置 http
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
