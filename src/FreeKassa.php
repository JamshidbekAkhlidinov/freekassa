<?php

namespace jamshidbekakhlidinov;

class FreeKassa{

    public $shop_id;
    public $api_key;

    public function __construct($shop_id,$api_key)
    {
        $this->api_key = $api_key;
        $this->shop_id = $shop_id;
    }

    
    public function getShops(){
        return $this->request('shops');
    }
   
    public function getWithdrawalsCurrentcies(){
        return $this->request('withdrawals/currencies');
    }

    public function checkCurrentcies($id){
        return $this->request('currencies/{id}/status');
    }
    
    public function getCurrentcies(){
        return $this->request('currencies');
    }

    public function createWithdrawals($payment_id, $account, $amount, $currency, $options = []){
        $data = [
            'i'=>$payment_id,
            'account'=>$account,
            'amount'=>$amount,
            'currency'=>$currency,
        ];
        $array = array_merge($data,$options); 
        return $this->request('withdrawals/create',$array);
    }

    public function getWithdrawals($options = []){
        return $this->request('withdrawals',$options);
    }

    public function createOrder($payment_id, $email, $ip, $amount, $currency, $options = []){
        $data = [
            'i'=>$payment_id,
            'email'=>$email,
            'ip'=>$ip,
            'amount'=>$amount,
            'currency'=>$currency,
        ];
        $array = array_merge($data,$options); 
        return $this->request('orders/create',$array);
    }


    public function getOrders(){
        return $this->request('orders');
    }

    public function getBalance(){
        return $this->request('balance');
    }

    public function request($post,$options = [])
    {
        $required_options = [
            'shopId'=>$this->shop_id,
            'nonce'=>time(),
        ];
        $data = array_merge($options,$required_options);
        ksort($data);
        $sign = hash_hmac('sha256', implode('|', $data), $this->api_key);
        $data['signature'] = $sign;
        $request = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.freekassa.ru/v1/'.$post);
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

   

    ?>