<?php

namespace gamitg\scheduling;

use yii\base\BootstrapInterface;
use yii\base\Application;

/**
 * Class Bootstrap
 * @author <Pavel Agalecky:pavel.agalecky@gmail.com>
 * @modifiedBy <GAMITG>
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            if (!isset($app->controllerMap['schedule'])) {
                $app->controllerMap['schedule'] = 'gamitg\scheduling\ScheduleController';
            }
        }
    }
}