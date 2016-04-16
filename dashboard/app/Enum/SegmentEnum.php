<?php

namespace App\Enum;
class SegmentEnum {
    public static function conditionSubOperators(){
        return [
            'lt' => 'Less than',
            'eq' => 'Equal',
            'gt' => 'Greater than',
            'in' => 'From..to..',
        ];
    }
}
