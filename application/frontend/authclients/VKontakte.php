<?php

namespace frontend\authclients;

use Yii;

class VKontakte extends \yii\authclient\clients\VKontakte {
    public function apiInternal($accessToken, $url, $method, array $params, array $headers) {
        $params['lang'] = 'en';
        return parent::apiInternal($accessToken, $url, $method, $params, $headers);
    }
}
