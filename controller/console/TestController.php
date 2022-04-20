<?php

namespace controller\console;

use sketch\database\UUID;
use sketch\SK;

class TestController
{
    public function actionIndex()
    {
        return "test console is execute";
    }

    public function actionProps(){
        var_dump(SK::getProps());
        return "";
    }

    public function actionGenerateUUID(){
        return UUID::createUUID();
    }

}