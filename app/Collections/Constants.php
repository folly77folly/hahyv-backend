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
    Const ADMIN_PAGE_LIMIT = 30;

    //Currency
    Const CURRENCY = 'ngn';

    //Stripe cents conversion
    Const STRIPE_VALUE = 100;

    //Transaction Type
    Const TRANSACTION = [
        "CREDIT" => 1,
        "DEBIT" => 2,
        ];
    
    //Earning Type
    Const EARNING = [
        "CARD" => 1,
        "WALLET" => 2,
        "REFERRAL" => 3,
        "CRYPTO" => 4,
        ];

    // Job delay minute
    Const JOB_DELAY_TIME = [
        "ONE" => 1,
        "TWO" => 2,
        "THREE" => 3,
    ];

    // subscription days
    Const SUBSCRIPTION_EXPIRY_NOTIFICATION = [
        "ONE_MONTH" => 30,
        "HALF_MONTH" => 15,
        "DAYS" => 5,
    ];

    // user roles
    Const ROLE = [
        "ADMIN" => 1,
        "USER" => 2,
    ];

    // message token charge
    Const TOKEN = [
        "DEBIT" => 1,
    ];

    // check active status
    Const ACTIVE = [
        "TRUE" => 1,
        "FALSE" => 0,
    ];
}