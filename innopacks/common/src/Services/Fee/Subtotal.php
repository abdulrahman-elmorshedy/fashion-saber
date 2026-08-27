<?php
namespace InnoShop\Common\Services\Fee;

class Subtotal extends BaseService
{
    /**
     * @return void
     */
    public function addFee(): void
    {
        $total = round($this->getSubtotal(), 2);

        $subtotalFee = [
            'code'         => 'subtotal',
            'title'        => __('front/checkout.subtotal'),
            'total'        => $total,
            'total_format' => currency_format($total),
        ];

        $this->checkoutService->addFeeList($subtotalFee);
    }

    /**
     * @return float
     */
    public function getSubtotal(): float
    {
        $cartList = $this->checkoutService->getCartList();
        $decimal  = currency_decimal_place();

        return round(collect($cartList)->sum('subtotal'), $decimal);
    }
}
