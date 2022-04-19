<?php

namespace controller\rest;

use model\Report;
use sketch\controller\ControllerRest;

class ReportController extends ControllerRest
{

    public function allowMethods()
    {
        return "GET";
    }

    public function actionGet()
    {
        $start = $_GET['start'];
        $finish = $_GET['finish'];

        if ($finish<$start) $finish = $start;

        $rep = new Report();

        return $rep->TransactionsByPeriod($start, $finish);

    }


}