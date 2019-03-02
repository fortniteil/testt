<?php

use hauntd\core\migrations\Migration;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 */
class m190110_100001_17_to_171 extends Migration
{
    public function up()
    {
        $settings = Yii::$app->settings;

        if (env('SOCIAL_FACEBOOK_APP_ID') !== null) {
            $settings->set('common', 'facebookEnabled', true);
            $settings->set('common', 'facebookClientId', env('SOCIAL_FACEBOOK_APP_ID'));
            $settings->set('common', 'facebookClientSecret', env('SOCIAL_FACEBOOK_APP_SECRET'));
        }
        if (env('SOCIAL_TWITTER_CONSUMER_KEY') !== null) {
            $settings->set('common', 'twitterEnabled', true);
            $settings->set('common', 'twitterConsumerKey', env('SOCIAL_TWITTER_CONSUMER_KEY'));
            $settings->set('common', 'twitterConsumerSecret', env('SOCIAL_TWITTER_CONSUMER_SECRET'));
        }
    }

    public function down()
    {
    }
}
