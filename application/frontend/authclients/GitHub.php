<?php

namespace frontend\authclients;

use Yii;

class GitHub extends \yii\authclient\clients\GitHub {
    public $scope = 'user,user:email';

    public function defaultViewOptions()
    {
        return [
            'popupWidth' => 1000,
            'popupHeight' => 600,
        ];
    }
}
