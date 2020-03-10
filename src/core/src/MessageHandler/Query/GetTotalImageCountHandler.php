<?php

namespace App\MessageHandler\Query;

use App\Message\Query\GetTotalImageCount;

class GetTotalImageCountHandler
{
    public function __invoke(GetTotalImageCount $getTotalImageCount)
    {
       return 50;
    }
}