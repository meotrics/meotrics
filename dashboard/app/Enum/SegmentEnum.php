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
//            'red', 'yellow', 'black', 'blue', 'pink'
            '#3357AF', '#758ED5', '#B7AHGA', '#FBEKJ5'
//            '#0000ff', '#6666ff', '#ccccff',
        ];
    }
}
