<?php


namespace app\Services\Sms;

class SmsSender
{
    protected $gateway;

    /**
     * @param $gatewayName
     * @param $phoneArr
     * @param $message
     * @return bool|mixed
     */
    public function send($phoneArr, $message, $gatewayName)
    {
        if (!env('SMS_LOG')) {
            try {
                $this->gateway = \app()->make($gatewayName);
            } catch (\Exception $e) {
                return false;
            }

            return $this->gateway->smsSend($phoneArr, $message);
        } else {
            info("Телефоны: $phoneArr/n Сообщение: $message");
            return true;
        }
    }
}