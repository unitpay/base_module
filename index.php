<?php

error_reporting(0);

include 'config.php';
include 'lib/UnitPayModel.php';
include 'lib/UnitPay.php';

class UnitPayEvent
{
    public function check($params)
    {
         $unitPayModel = UnitPayModel::getInstance();

         if ($unitPayModel->getAccountByName($params['account']))
         {
            return true;
         }
         return 'Character not found';
    }

    public function pay($params)
    {
         $unitPayModel = UnitPayModel::getInstance();
         $countItems = floor($params['sum'] / Config::ITEM_PRICE);
         $unitPayModel->donateForAccount($params['account'], $countItems);
    }
}

$payment = new UnitPay(
    new UnitPayEvent()
);

echo $payment->getResult();
