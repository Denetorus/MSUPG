<?php

namespace controller\rest;

use module\Report;
use sketch\controller\ControllerRest;

class ReportController extends ControllerRest
{

    public function actionGet()
    {
        $start = $_GET['start'];
        $finish = $_GET['finish'];

        if ($finish<$start) $finish = $start;

        $rep = new Report();

        return $rep->TransactionsByPeriod($start, $finish);

    }


}