<?php
include_once "./vendor/autoload.php";

use jamshidbekakhlidinov\FreeKassa;

$shop_id = "";
$api_key ="";
$api = new FreeKassa($shop_id,$api_key);

$balance = $api->getBalance();

print_r($balance);
?>