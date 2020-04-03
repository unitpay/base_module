<?php

class UnitPayModel
{
    private $mysqli;

    static function getInstance()
    {
        return new self();
    }

    private function __construct()
    {
        $port = Config::DB_PORT;
        if (empty($port)) {
            $port = ini_get("mysqli.default_port");
        }
        $this->mysqli = @new mysqli (
            Config::DB_HOST, Config::DB_USER, Config::DB_PASS, Config::DB_NAME, $port
        );
        /* проверка подключения */
        if (mysqli_connect_errno()) {
            throw new Exception('Не удалось подключиться к бд');
        }
    }

    public function createPayment($unitpayId, $account, $sum, $itemsCount)
    {
        $query = '
            INSERT INTO
                unitpay_payments (unitpayId, account, sum, itemsCount, dateCreate, status)
            VALUES
                (
                    "'.$this->mysqli->real_escape_string($unitpayId).'",
                    "'.$this->mysqli->real_escape_string($account).'",
                    "'.$this->mysqli->real_escape_string($sum).'",
                    "'.$this->mysqli->real_escape_string($itemsCount).'",
                    NOW(),
                    0
                )
        ';

        return $this->mysqli->query($query);
    }

    public function getPaymentByUnitpayId($unitpayId)
    {
        $query = '
                SELECT * FROM
                    unitpay_payments
                WHERE
                    unitpayId = "'.$this->mysqli->real_escape_string($unitpayId).'"
                LIMIT 1
            ';
            
        $result = $this->mysqli->query($query);

        if (!$result){
            throw new Exception($this->mysqli->error);
        }

        return $result->fetch_object();
    }

    public function confirmPaymentByUnitpayId($unitpayId)
    {
        $query = '
                UPDATE
                    unitpay_payments
                SET
                    status = 1,
                    dateComplete = NOW()
                WHERE
                    unitpayId = "'.$this->mysqli->real_escape_string($unitpayId).'"
                LIMIT 1
            ';
        return $this->mysqli->query($query);
    }
    
    public function getAccountByName($account)
    {
        $sql = "
            SELECT
                *
            FROM
               ".Config::TABLE_ACCOUNT."
            WHERE
               ".Config::TABLE_ACCOUNT_NAME." = '".$this->mysqli->real_escape_string($account)."'
            LIMIT 1
         ";
         
        $result = $this->mysqli
            ->query($sql);

        if (!$result){
            throw new Exception($this->mysqli->error);
        }

        return $result->fetch_object();
    }
    
    public function donateForAccount($account, $count)
    {
        $query = "
            UPDATE
                ".Config::TABLE_ACCOUNT."
            SET
                ".Config::TABLE_ACCOUNT_DONATE." = ".Config::TABLE_ACCOUNT_DONATE." + ".$this->mysqli->real_escape_string($count)."
            WHERE
                ".Config::TABLE_ACCOUNT_NAME." = '".$this->mysqli->real_escape_string($account)."'
        ";
        
        return $this->mysqli->query($query);
    }
}