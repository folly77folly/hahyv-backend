<?php
namespace App\Collections;

class Constants{
    //notification type
    Const NOTIFICATION = [
    "LIKED" => 1,
    "SUBSCRIBED" => 2,
    "TIPPED" => 3,
    "MESSAGED" => 4,
    "FOLLOW" => 5,
    "BOOKMARK" => 6
    ];

    //pagination
    Const PAGE_LIMIT = 10;

    //Currency
    Const CURRENCY = 'usd';

    //Stripe cents conversion
    Const STRIPE_VALUE = 100;

    //Transaction Type
    Const TRANSACTION = [
        "CREDIT" => 1,
        "DEBIT" => 2,
        ];
}