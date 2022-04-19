<?php


namespace router;

class RouterRest extends \sketch\router\RouterRest
{

    public function routesAvailableWithoutSignIn(){
        return [

            'report' => [
                'path' => 'report',
                'status' => -1
            ],

            'test' => [
                'path' => 'test',
                'status' => -1
            ],


        ];
    }
}