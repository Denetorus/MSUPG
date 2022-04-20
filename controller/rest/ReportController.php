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

    public function actionGet($reportName=null)
    {

        if ($reportName==="transactions"){

            $start = $_GET['start'];
            $finish = $_GET['finish'];

            if ($finish<$start) $finish = $start;

            $rep = new Report();
            return $rep->TransactionsByPeriod($start, $finish);

        }

        return $this->getReportsDeclarations();

    }

    private function getReportsDeclarations(){
        return [
            [
              "method" => "POST",
              "path" => "/rest/report/transactions",
              "params" => [
                  "start"=>"Unixtime",
                  "finish"=>"Unixtime"
              ]
            ],
            [
                "method" => "POST",
                "path" => "/rest/report/last_transactions",
                "params" => [
                    "start"=>"Unixtime",
                ]
            ],
        ];
    }

}