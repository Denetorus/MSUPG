<?php

namespace controller\console;

use model\Report;

class ReportController
{

    public function actionIndex(){
        return
            "Available reports:
            POST    /report/transactions         ['start'=>Unixtime, 'finish'=>Unixtime]
            POST    /report/last_transactions    ['start'=>Unixtime]
            ";
    }

    public function actionTransactions($start, $finish){

        if ($finish<$start) $finish = $start;

        $rep = new Report();
        $res = $rep->TransactionsByPeriod($start, $finish);

        return json_encode($res);

    }


}