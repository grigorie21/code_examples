<?php


namespace app\Contracts;

interface SmsContract
{

    /**
     * @param $phoneArr
     * @param $message
     * @return mixed
     */
    public function smsSend($phoneArr, $message);

    /**
     * @param $phoneArr
     * @param $message
     * @return mixed
     */
    public function smsGetStatus($phoneArr, $message);

    /**
     * @return mixed
     */
    public function smsGetBalance();
}