<?php

namespace App\Actions\CardPayment;

interface CardPaymentInterface
{
    /**
     * Return result of entryTran by GMO
     *
     * @param int
     * @param string
     *
     * @return object
     */
    public function entryTran(int $price, string $order_id): object;

    /**
     * Return result of execTran by GMO
     *
     * @param string
     * @param string
     * @param string
     * @param string
     *
     * @return object
     */
    public function execTran(string $payment_method_id, string $access_id, string $access_pass, string $order_id): object;

    /**
     * Return result of refund by GMO
     *
     * @param string
     * @param string
     * @param int
     *
     * @return object
     */
    public function refund(string $access_id, string $access_pass, int $price): object;

    /**
     * Return result of alter sales by GMO
     *
     * @param string
     * @param string
     * @param int
     *
     * @return object
     */
    public function alterSales(string $access_id, string $access_pass, int $price): object;

    public function getPaymentApiName(): string;
}
