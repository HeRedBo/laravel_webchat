<?php
/**
 * @see https://github.com/hhxsv5/laravel-s/blob/master/Settings-CN.md  Chinese
 * @see https://github.com/hhxsv5/laravel-s/blob/master/Settings.md  English
 */
return [
    'listen_ip'                => env('LARAVELS_LISTEN_IP', '127.0.0.1'),
    'listen_port'              => env('LARAVELS_LISTEN_PORT', 5200),
    'socket_type'              => defined('SWOOLE_SOCK_TCP') ? SWOOLE_SOCK_TCP : 1,
    'enable_coroutine_runtime' => false,
    'server'                   => env('LARAVELS_SERVER', 'LaravelS'),
    'handle_static'            => env('LARAVELS_HANDLE_STATIC', false),
    'laravel_base_path'        => env('LARAVEL_BASE_PATH', base_path()),
    'inotify_reload'           => [
        'enable'        => env('LARAVELS_INOTIFY_RELOAD', false),
        'watch_path'    => base_path(),
        'file_types'    => ['.php'],
        'excluded_dirs' => [],
        'log'           => true,
    ],
    'event_handlers'           => [
        'WorkerStart' => \App\Events\WorkerStartEvent::class,
    ],
    'websocket'                => [
        'enable' => true,
        'handler' => \App\Services\WebSocket\WebSocketHandler::class,
        'middleware' => [
            //\Illuminate\Auth\Middleware\Authenticate::class,
            //\App\Http\Middleware\VerifyCsrfToken::class,
        ],
        'parser' => \App\Services\WebSocket\SocketIO\SocketIOParser::class,
        'drivers' => [
            'default' => 'redis',
            'table' => \App\Services\Websocket\Rooms\TableRoom::class,
            'redis' => \App\Services\Websocket\Rooms\RedisRoom::class,
            'settings' => [
                'table' => [
                    'room_rows' => 4096,
                    'room_size' => 2048,
                    'client_rows' => 8192,
                    'client_size' => 2048,
                ],
                'redis' => [
                    'server' => [
                        'host' => env('REDIS_HOST', '127.0.0.1'),
                        'password' => env('REDIS_PASSWORD', null),
                        'port' => env('REDIS_PORT', 6379),
                        'database' => 0,
                        'persistent' => true,
                    ],
                    'options' => [
                        //
                    ],
                    'prefix' => 'swoole:',
                ],
            ],
        ],
    ],
    'sockets'                  => [],
    'processes'                => [
//        [
//            'class'    => \App\Processes\TestProcess::class,
//            'redirect' => false, //是否将输入输出重定向到 stdin/stdout, true or false Whether redirect stdin/stdout, true or false
//            'pipe'     => 0 ,// 管道类型, 0: 不使用管道 1: SOCK_STREAM 2: SOCK_DGRAM The type of pipeline, 0: no pipeline 1: SOCK_STREAM 2: SOCK_DGRAM
//           #  'enable'   => true // Whether to enable, default true
//        ],
    ],
    'timer'                    => [
        'enable'        => true,
        'jobs'          => [
            // Enable LaravelScheduleJob to run `php artisan schedule:run` every 1 minute, replace Linux Crontab
            //\Hhxsv5\LaravelS\Illuminate\LaravelScheduleJob::class,
            // Two ways to configure parameters:
            // [\App\Jobs\XxxCronJob::class, [1000, true]], // Pass in parameters when registering
            //\App\Jobs\Timer\TestCronJob::class, // Override the corresponding method to return the configuration
        ],
        'max_wait_time' => 5,
    ],
    'events'                   => [
        \App\Events\TestEvent::class => [
            \App\Listeners\TestEventListener::class,
        ]
    ],
    'swoole_tables'            => [
        'ws' => [ // 表名，会加上 Table 后缀，比如这里是 wsTable
            'size'   => 102400, //  表容量
            'column' => [ // 表字段，字段名为 value
                ['name' => 'value', 'type' => \Swoole\Table::TYPE_INT, 'size' => 8],
            ],
        ],

    ],
    'register_providers'       => [],
    'cleaners'                 => [
        // If you use the session/authentication/passport in your project
        // Hhxsv5\LaravelS\Illuminate\Cleaners\SessionCleaner::class,
        Hhxsv5\LaravelS\Illuminate\Cleaners\AuthCleaner::class,

        // If you use the package "tymon/jwt-auth" in your project
        // Hhxsv5\LaravelS\Illuminate\Cleaners\SessionCleaner::class,
        // Hhxsv5\LaravelS\Illuminate\Cleaners\AuthCleaner::class,
        // Hhxsv5\LaravelS\Illuminate\Cleaners\JWTCleaner::class,

        // If you use the package "spatie/laravel-menu" in your project
        // Hhxsv5\LaravelS\Illuminate\Cleaners\MenuCleaner::class,
        // ...
    ],
    'destroy_controllers'      => [
        'enable'        => false,
        'excluded_list' => [
            //\App\Http\Controllers\TestController::class,
        ],
    ],
    'swoole'                   => [
        'daemonize'          => env('LARAVELS_DAEMONIZE', false),
        'dispatch_mode'      => 2,
        'reactor_num'        => function_exists('swoole_cpu_num') ? swoole_cpu_num() * 2 : 4,
        'worker_num'         => function_exists('swoole_cpu_num') ? swoole_cpu_num() * 2 : 8,
        'task_worker_num'    => function_exists('swoole_cpu_num') ? swoole_cpu_num() * 2 : 8,
        'task_ipc_mode'      => 1,
        'task_max_request'   => 8000,
        'task_tmpdir'        => @is_writable('/dev/shm/') ? '/dev/shm' : '/tmp',
        'max_request'        => 8000,
        'open_tcp_nodelay'   => true,
        'pid_file'           => storage_path('laravels.pid'),
        'log_file'           => storage_path(sprintf('logs/swoole-%s.log', date('Y-m'))),
        'log_level'          => 4,
        'document_root'      => base_path('public'),
        'buffer_output_size' => 2 * 1024 * 1024,
        'socket_buffer_size' => 128 * 1024 * 1024,
        'package_max_length' => 4 * 1024 * 1024,
        'reload_async'       => true,
        'max_wait_time'      => 60,
        'enable_reuse_port'  => true,
        'enable_coroutine'   => true,
        'http_compression'   => false,

        // Slow log
        // 'request_slowlog_timeout' => 2,
        // 'request_slowlog_file'    => storage_path(sprintf('logs/slow-%s.log', date('Y-m'))),
        // 'trace_event_worker'      => true,

        /**
         * More settings of Swoole
         * @see https://wiki.swoole.com/wiki/page/274.html  Chinese
         * @see https://www.swoole.co.uk/docs/modules/swoole-server/configuration  English
         */

        // 每隔 10s 检测一次所有连接，如果某个连接在 10s 内都没有发送任何数据，则关闭该连接
        'heartbeat_idle_time'      => 60,
        'heartbeat_check_interval' => 60,
    ],
];
