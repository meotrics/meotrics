<?php

namespace App\Enum;
class SegmentEnum {
    const CHART_COLOR_DEFAULT = 'red';
    public static function conditionSubOperators(){
        return [
//            'lt' => 'Less than',
//            'eq' => 'Equal',
//            'gt' => 'Greater than',
//            'in' => 'From..to..',
            '>' => 'greater than',
            '>=' => 'less or equal',
            '=' => 'equal',
            '<' => 'less than',
            '<=' => 'greater or equal',
        ];
    }
    
    public static function chartColor(){
        return [
            '#5884FF', '#4B74E0', '#4164C2', '#3855A5', '#25396E',
        ];
    }
}
