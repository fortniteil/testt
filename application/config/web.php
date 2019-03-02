<?php

use yii\helpers\ArrayHelper;
use app\settings\LazySettingsValue;

$core = require(__DIR__ . '/core.php');

$config = [
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'default/index',
    'container' => [
        'definitions' => [
            yii\i18n\Formatter::class => [
                'currencyCode' => 'USD',
            ],
        ],
    ],
    'bootstrap' => [
        app\bootstrap\WebBootstrap::class,
        'log',
        'pluginManager',
        'themeManager',
    ],
    'modules' => [
        env('ADMIN_PREFIX') => [
            'class' => app\modules\admin\Module::class,
        ],
    ],
    'components' => [
        'authClientCollection' => [
            'class'   => app\clients\ClientCollection::class,
            'clients' => [
                'facebook' => [
                    'class' => app\clients\Facebook::class,
                    'enabled' => new LazySettingsValue('common', 'facebookEnabled'),
                    'clientId'  => new LazySettingsValue('common', 'facebookAppId', env('SOCIAL_FACEBOOK_APP_ID')),
                    'clientSecret' => new LazySettingsValue('common', 'facebookAppSecret', env('SOCIAL_FACEBOOK_APP_SECRET')),
                ],
                'twitter' => [
                    'class' => app\clients\Twitter::class,
                    'enabled' => new LazySettingsValue('common', 'twitterEnabled'),
                    'consumerKey' => new LazySettingsValue('common', 'twitterConsumerKey', env('SOCIAL_TWITTER_CONSUMER_KEY')),
                    'consumerSecret' => new LazySettingsValue('common', 'twitterConsumerSecret', env('SOCIAL_TWITTER_CONSUMER_SECRET')),
                ],
                'vk' => [
                    'class' => app\clients\VK::class,
                    'enabled' => new LazySettingsValue('common', 'vkEnabled'),
                    'clientId' => new LazySettingsValue('common', 'vkAppId', env('SOCIAL_VK_APP_ID')),
                    'clientSecret' => new LazySettingsValue('common', 'vkAppSecret', env('SOCIAL_VK_APP_SECRET')),
                ]
            ],
        ],
        'request' => [
            'cookieValidationKey' => env('APP_COOKIE_VALIDATION_KEY'),
        ],
        'user' => [
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['/security/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'db' => require(__DIR__ . '/db.php'),
        'view' => [
            'class' => app\base\View::class,
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap']['debug'] = 'debug';
    $config['modules']['debug'] = [
        'class' => yii\debug\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
        'panels' => [
            'httpclient' => [
                'class' => yii\httpclient\debug\HttpClientPanel::class,
            ],
        ],
    ];

    $config['bootstrap']['gii'] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

$config = ArrayHelper::merge($core, $config);

return $config;
