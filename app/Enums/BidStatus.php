<?php

namespace App\Enums;

enum BidStatus: string
{
    case LOST  = "lost";
    case IN_PROGRESS  = "in progress";
    case WON  = "won";
}
