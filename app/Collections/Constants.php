<?php
namespace App\Collections;

class Constants{
    //notification type
    Const NOTIFICATION = [
    "LIKED" => 1,
    "SUBSCRIBED" => 2,
    "TIPPED" => 3,
    "MESSAGED" => 4,
    "FOLLOW" => 5
    ];

    //pagination
    Const PAGE_LIMIT = 10;
}