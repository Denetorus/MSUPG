<?php

namespace database\DBMain;

use sketch\database\ObjectBase;

class ObjectMyWithId extends ObjectBase
{

    public function __construct($id=null)
    {
        $this->db = DB::getInstance();
        parent::__construct($id);
    }

    public function getFields(): array
    {
        return [
            [ 'name' => 'id', 'type' => 'number'],
        ];
    }


    public function getListWithExtension($gottenSorts=[], $gottenFilters=[])
    {

        $sorts = $this->getSortsByGottenSorts($gottenSorts);
        $filters = $this->getFiltersByGottenFilters($gottenFilters);

        $query_params = [];
        $query_text =
            "SELECT list.* FROM ".$this->table." as list ";


        $filter_text = "";
        $is_first = true;
        $params_number = 0;
        foreach ($filters as $filter){

            if ($is_first){
                $is_first = false;
            }else{
                $filter_text .= ",";
            }

            $params_number += 1;
            $filter_text .= "list.".$filter['field']." ".$filter['type'].' :param'.$params_number;
            if ($filter['type']==='like'){
                $query_params['param'.$params_number] = '%'.$filter['value'].'%';
            }else{
                $query_params['param'.$params_number] = $filter['value'];
            }

        }
        if ($filter_text && !$is_first){
            $query_text .= " WHERE " . $filter_text;
        }

        $sort_text = "";
        $is_first = true;
        foreach ($sorts as $sort){
            if ($is_first) {
                $sort_text = $sort;
                $is_first = false;
            }else{
                $sort_text .= "," . $sort;
            };
        }
        if ($sort_text && !$is_first){
            $query_text .= " ORDER BY " . $sort_text;
        }

        return $this->db->select($query_text, $query_params);

    }

    public function getSortsByGottenSorts($gottenSorts): array
    {
        $result = [];
        $correct_sorts = $this->getCorrectSorts();
        foreach ($gottenSorts as $sort){
            if (strpos($correct_sorts, ",".$sort.",")!==false) {
                $result[] = "list.".$sort;
            }
        }
        return $result;
    }

    public function getFiltersByGottenFilters($gottenFilters): array
    {

        $result = [];

        $correct_fields = $this->getCorrectFilterFields();
        $correct_types = $this->getCorrectFilterTypes();

        foreach ($gottenFilters as $filter){
            if (strpos($correct_fields, ",".$filter->field.",")===false) continue;
            if (strpos($correct_types, ",".$filter->type.",")===false) continue;
            $result[] = [
                'field' => $filter->field,
                'type' => $filter->type,
                'value' => $filter->value
            ];
        }

        return $result;

    }

    public function getCorrectFilterTypes(): string
    {
        return ',=,!=,>,<,>=,<=,like,ilike,not like,not ilike,';
    }

    public function getCorrectSorts(): string
    {
        $result = "";
        $fields = $this->getFields();
        foreach ($fields as $field){
            $result .=",".$field['name']." desc,".$field['name']." asc,";
        }
        return $result;

    }

    public function getCorrectFilterFields(): string
    {
        $result = "";
        $fields = $this->getFields();
        foreach ($fields as $field){
            $result .=",".$field['name'];
        }
        return $result;
    }
}