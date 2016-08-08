<?php

class UnitPay
{
    private $event;

    public function __construct(UnitPayEvent $event)
    {
        $this->event = $event;
    }

    public function getResult()
    {
        $request = $_GET;

        if (empty($request['method'])
            || empty($request['params'])
            || !is_array($request['params'])
        )
        {
            return $this->getResponseError('Invalid request');
        }

        $method = $request['method'];
        $params = $request['params'];

        if ($params['signature'] != $this->getSha256SignatureByMethodAndParams($method, $params, Config::SECRET_KEY))
        {
            return $this->getResponseError('Incorrect digital signature');
        }

        $unitPayModel = UnitPayModel::getInstance();

        if ($method == 'check')
        {
            if ($unitPayModel->getPaymentByUnitpayId($params['unitpayId']))
            {
                // Платеж уже существует
                return $this->getResponseSuccess('Payment already exists');
            }

            $itemsCount = floor($params['sum'] / Config::ITEM_PRICE);

            if ($itemsCount <= 0)
            {
                return $this->getResponseError('Суммы ' . $params['sum'] . ' руб. не достаточно для оплаты товара ' .
                    'стоимостью ' . Config::ITEM_PRICE . ' руб.');
            }

            if (!$unitPayModel->createPayment(
                $params['unitpayId'],
                $params['account'],
                $params['sum'],
                $itemsCount
            ))
            {
                return $this->getResponseError('Unable to create payment database');
            }

            $checkResult = $this->event->check($params);
            if ($checkResult !== true)
            {
                return $this->getResponseError($checkResult);
            }

            return $this->getResponseSuccess('CHECK is successful');
        }

        if ($method == 'pay')
        {
            $payment = $unitPayModel->getPaymentByUnitpayId(
                $params['unitpayId']
            );

            if ($payment && $payment->status == 1)
            {
                return $this->getResponseSuccess('Payment has already been paid');
            }

            if (!$unitPayModel->confirmPaymentByUnitpayId($params['unitpayId']))
            {
                return $this->getResponseError('Unable to confirm payment database');
            }

            $this->event
                ->pay($params);

            return $this->getResponseSuccess('PAY is successful');
        }

	return $this->getResponseError($method.' not supported');
    }

    private function getResponseSuccess($message)
    {
        return json_encode(array(
            "jsonrpc" => "2.0",
            "result" => array(
                "message" => $message
            ),
            'id' => 1,
        ));
    }

    private function getResponseError($message)
    {
        return json_encode(array(
            "jsonrpc" => "2.0",
            "error" => array(
                "code" => -32000,
                "message" => $message
            ),
            'id' => 1
        ));
    }

    private function getMd5Sign($params, $secretKey)
    {
        ksort($params);
        unset($params['sign']);
        return md5(join(null, $params).$secretKey);
    }

    /**
     * @param $method
     * @param array $params
     * @param $secretKey
     * @return string
     */
    private function getSha256SignatureByMethodAndParams($method, array $params, $secretKey)
    {
        $delimiter = '{up}';
        ksort($params);
        unset($params['sign']);
        unset($params['signature']);

        return hash('sha256', $method.$delimiter.join($delimiter, $params).$delimiter.$secretKey);
    }
}
