<?php

namespace database\DBMain;

use \sketch\database\DBBase;

class DB extends DBBase
{
    protected static $DB = null;

    public static function getAttributes()
    {
        return include('db_config.php');
    }
}
