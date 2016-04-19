<?php

namespace App\Enum;
class SegmentEnum {
    const CHART_COLOR_DEFAULT = 'red';
    public static function conditionSubOperators(){
        return [
            'lt' => 'Less than',
            'eq' => 'Equal',
            'gt' => 'Greater than',
            'in' => 'From..to..',
        ];
    }
    
    public static function chartColor(){
        return [
            'red', 'yellow', 'black', 'blue', 'pink'
        ];
    }
}
