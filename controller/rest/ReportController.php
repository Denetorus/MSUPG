<?php

namespace controller\rest;

use model\Report;
use sketch\controller\ControllerRest;
use sketch\rest\RequestResult;
use sketch\SK;

class ReportController extends ControllerRest
{

    public function allowMethods(): string
    {
        return "GET";
    }

    public function actionGet($reportName=null)
    {

        $result = new RequestResult();

        if(!isset($_GET['token'])||($_GET['token']!==SK::getProps()['webhookToken'])){
            http_response_code(401);
            $result->addError(1, "token", "token is unavailable or wrong");
            return $result->toJson();
        }

        if ($reportName==="transactions"){

            $start = $_GET['start'];
            $finish = $_GET['finish'];

            if ($finish<$start) $finish = $start;

            $rep = new Report();
            $result->insertData($rep->TransactionsByPeriod($start, $finish));
            return $result->toJson();
        }

        $result->insertData($this->getReportsDeclarations());
        return $result->toJson();

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