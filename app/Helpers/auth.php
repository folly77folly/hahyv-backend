<?php
function OTP(){
    $result = mt_rand(100000, 999999);
    return $result;
}