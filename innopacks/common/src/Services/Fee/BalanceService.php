<?php
namespace InnoShop\Common\Services\Fee;

use Throwable;

class BalanceService extends BaseService
{
    /**
     * @return void
     * @throws Throwable
     */
    public function addFee(): void
    {
        $checkoutData = $this->checkoutService->getCheckoutData();
        $reference    = $checkoutData['reference'];
        $usedBalance  = $reference['balance'] ?? 0;
        if (empty($usedBalance)) {
            return;
        }

        $subtotalFee = [
            'code'         => 'balance',
            'title'        => 'Balance',
            'total'        => round(-$usedBalance, 2),
            'total_format' => currency_format($usedBalance),
        ];

        $this->checkoutService->addFeeList($subtotalFee);
    }
}
