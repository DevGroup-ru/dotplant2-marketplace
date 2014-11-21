<?php

namespace frontend\authclients;

use Yii;

class Facebook extends \yii\authclient\clients\Facebook {
    public $scope = 'public_profile,email';

    public function defaultViewOptions()
    {
        return [
            'popupWidth' => 1000,
            'popupHeight' => 600,
        ];
    }
}
