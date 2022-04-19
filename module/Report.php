<?php

namespace module;

use PHPExcel_IOFactory;
use sketch\SK;
use DateTime;

class Report
{

    private $connect;
    private $token="";

    private function Connect(){

        if ($this->token!=="") return;

        $this->connect = curl_init();
        curl_setopt($this->connect, CURLOPT_COOKIEFILE, ROOT . '/temp/cookie.txt');
        curl_setopt($this->connect, CURLOPT_COOKIEJAR, ROOT . '/temp/cookie.txt');
        curl_setopt($this->connect, CURLOPT_URL, "https://upgcard.com.ua/ua/auth/login");
        curl_setopt($this->connect, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->connect, CURLOPT_COOKIESESSION, true);

        $response1 = curl_exec($this->connect);

        $result1 = Strstr($response1, '<form id="yw0" action="/ua/auth/login" method="post">');
        $result2 = Strstr($result1, 'value="');
        $this->token = mb_substr($result2, 7,88);


        $ugp_auth = SK::getProp("ugp_auth");
        $params = [
            'tkn'=>$this->token,
            'MFormLogin[login]'=>$ugp_auth->login,
            'MFormLogin[password]'=>$ugp_auth->password
        ];

        curl_setopt($this->connect, CURLOPT_POST, 1);
        curl_setopt($this->connect, CURLOPT_POSTFIELDS, $params);
        curl_setopt($this->connect, CURLOPT_URL, "https://upgcard.com.ua/ua/auth/login");
        curl_setopt($this->connect, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->connect, CURLOPT_COOKIESESSION, true);

        curl_exec($this->connect);

    }

    private function GetReportFile($filename, $report_url, $params){

        $params['tkn'] = $this->token;

        $file = @fopen($filename, 'w');

        curl_setopt($this->connect, CURLOPT_URL, $report_url);
        curl_setopt($this->connect, CURLOPT_POSTFIELDS, $params);
        curl_setopt($this->connect, CURLOPT_FILE, $file);

        curl_exec($this->connect);


    }

    private function ExcelToArray($filename){

        require_once('module/PHPExcel.php');
        $pExcel = PHPExcel_IOFactory::load($filename);
        $pExcel->setActiveSheetIndex(0);
        $worksheet = $pExcel->getActiveSheet();

        return $worksheet->toArray();

    }

    public function TransactionsByPeriod($start, $finish){

        $this->Connect();

        if ($finish<$start) $finish = $start;

        $filename = ROOT . '/temp/report_transactions_by_period.xls';

        $params = [
            'MFormReport5[d_min]'=>date('d.m.Y', $start),
            'MFormReport5[d_max]'=>date('d.m.Y', $finish)
        ];

        $this->GetReportFile(
            $filename,
            "https://upgcard.com.ua/ua/owner/report/index",
            $params
        );

        $table = $this->ExcelToArray($filename);

        unset($table[0]);

        $result = [];
        foreach ($table as $value){

            $dt = DateTime::createFromFormat('d.m.Y H:i:s', $value[0]);
            $ts = $dt->getTimestamp();
            if ($ts<$start || $ts>$finish) continue;

            $result[] = [
                "date" => $ts,
                "good" => $value[5],
                "quantity" => $value[1],
                "price" => $value[2],
                "sum" => $value[3],
                "simWitTax" => $value[4],
                "card_number" => $value[6],
                "address" => $value[7]
            ];
        }

        return $result;

    }

}