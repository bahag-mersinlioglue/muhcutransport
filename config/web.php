<?php

use kartik\datecontrol\Module;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'name' => 'Muhcu Transport',
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'modules' => [
        'datecontrol' =>  [
            'class' => 'kartik\datecontrol\Module',

            // format settings for displaying each date attribute (ICU format example)
            'displaySettings' => [
                Module::FORMAT_DATE => 'dd.MM.yyyy',
                Module::FORMAT_TIME => 'hh:mm',
                Module::FORMAT_DATETIME => 'dd.MM.yyyy hh:mm:ss a',
            ],

            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                //Module::FORMAT_DATE => 'php:U', // saves as unix timestamp
                Module::FORMAT_DATE => 'php:Y-m-d', // saves as mysql date
                Module::FORMAT_TIME => 'php:H:i',
                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

            // set your display timezone
            'displayTimezone' => 'Europe/Berlin',

            // set your timezone for date saved to db
            //'saveTimezone' => 'UTC',
            'saveTimezone' => 'Europe/Berlin',

            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,

            // default settings for each widget from kartik\widgets used when autoWidget is true
            'autoWidgetSettings' => [
                Module::FORMAT_DATE => ['type'=>2, 'pluginOptions'=>['autoclose'=>true]], // example
                Module::FORMAT_DATETIME => [], // setup if needed
                Module::FORMAT_TIME => [], // setup if needed
            ],

            // custom widget settings that will be used to render the date input instead of kartik\widgets,
            // this will be used when autoWidget is set to false at module or widget level.
            'widgetSettings' => [
                Module::FORMAT_DATE => [
                    'class' => 'yii\jui\DatePicker', // example
                    'options' => [
                        //'dateFormat' => 'php:d-M-Y',
                        'dateFormat' => 'php:Y-m-d',
                        'options' => ['class'=>'form-control'],
                    ]
                ]
            ]
            // other settings
        ],
    ],
//    'modules' => [
//        'datecontrol' => [
//            'class' => 'kartik\datecontrol\Module',
//            // format settings for displaying each date attribute (ICU format example)
//            'dateControlDisplay' => [
//                Module::FORMAT_DATE => 'dd.MM.yyyy',
//                Module::FORMAT_TIME => 'hh:mm:ss a',
//                Module::FORMAT_DATETIME => 'dd-MM-yyyy hh:mm:ss',
////                Module::FORMAT_DATE => 'php:d.m.Y',
////                Module::FORMAT_TIME => 'php:H:i',
////                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
//            ],
//
//            // format settings for saving each date attribute (PHP format example)
//            'dateControlSave' => [
//                Module::FORMAT_DATE => 'php:Y-m-d', // saves as unix timestamp
//                Module::FORMAT_TIME => 'php:H:i:s',
//                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
//            ]
//        ]
//    ],
    'components' => [
        'formatter' => [
            'dateFormat' => 'dd.mm.yyyy',
            'datetimeFormat' => 'php:d.m.Y H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => 'EUR',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'cookieValidationKey' => 'xxxxxxx',
        ],
//        'view' => [
//            'theme' => [
//                'pathMap' => [
//                   '@app/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app'
//                ],
//            ],
//       ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['components']['assetManager']['forceCopy'] = true;

}

return $config;
