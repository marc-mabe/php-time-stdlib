<?php declare(strict_types=1);

namespace time;

enum Month:int {
    case January = 1;
    case February = 2;
    case March = 3;
    case April = 4;
    case May = 5;
    case June = 6;
    case July = 7;
    case August = 8;
    case September = 9;
    case October = 10;
    case November = 11;
    case December = 12;

    public function getAbbreviation():string {
        return \substr($this->name, 3);
    }
}
