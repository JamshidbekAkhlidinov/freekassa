<?php

namespace jamshidbekakhlidinov;

class FreeKassa
{
    public string $shop_id;
    public string $api_key;

    public function __construct($shop_id, $api_key)
    {
        $this->api_key = $api_key;
        $this->shop_id = $shop_id;
    }

    public function getShops()
    {
        return $this->api('shops');
    }

    public function getWithdrawalsCurrencies()
    {
        return $this->api('withdrawals/currencies');
    }

    public function checkCurrencies($id)
    {
        return $this->api('currencies/{id}/status');
    }

    public function getCurrencies()
    {
        return $this->api('currencies');
    }

    public function createWithdrawals($payment_id, $account, $amount, $currency, $options = [])
    {
        $data = [
            'i' => $payment_id,
            'account' => $account,
            'amount' => $amount,
            'currency' => $currency,
        ];
        $array = array_merge($data, $options);
        return $this->api('withdrawals/create', $array);
    }

    public function getWithdrawals($options = [])
    {
        return $this->api('withdrawals', $options);
    }

    public function createOrder($payment_id, $email, $ip, $amount, $currency, $options = [])
    {
        $data = [
            'i' => $payment_id,
            'email' => $email,
            'ip' => $ip,
            'amount' => $amount,
            'currency' => $currency,
        ];
        $array = array_merge($data, $options);
        return $this->api('orders/create', $array);
    }

    public function getOrders()
    {
        return $this->api('orders');
    }

    public function getBalance()
    {
        return $this->api('balance');
    }

    public function api($request, $options = [])
    {
        $data = array_merge(
            [
                'shopId' => $this->shop_id,
                'nonce' => time(),
            ],
            $options
        );
        ksort($data);
        $sign = hash_hmac('sha256', implode('|', $data), $this->api_key);
        $data['signature'] = $sign;
        $request = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.freekassa.ru/v1/' . $request);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $result = trim(curl_exec($ch));
        curl_close($ch);
        return json_decode($result, true);
    }
}