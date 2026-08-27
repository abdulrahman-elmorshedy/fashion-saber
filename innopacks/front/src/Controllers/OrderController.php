<?php
namespace InnoShop\Front\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use InnoShop\Common\Models\Order;
use InnoShop\Common\Repositories\OrderRepo;
use InnoShop\Front\Services\PaymentService;

class OrderController extends Controller
{
    /**
     * @param  Request  $request
     * @return mixed
     * @throws Exception
     */
    public function pay(Request $request): mixed
    {
        try {
            $order      = Order::query()->where('number', $request->number)->firstOrFail();
            
            // Redirect COD or custom payments directly to success page
            if ($order->billing_method_code === 'cod' || str_starts_with($order->billing_method_code, 'custom_payment')) {
                return redirect(front_route('payment.success', ['order_number' => $order->number]));
            }

            $customerID = current_customer_id();

            // Logged-in user's order: must be the owner
            // Guest order (customer_id=0): order number is the access credential
            if ($order->customer_id > 0 && $order->customer_id !== $customerID) {
                abort(403, 'Unauthorized access to order payment');
            }

            return PaymentService::getInstance($order)->pay();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Order detail
     *
     * @param  int  $number
     * @return mixed
     */
    public function numberShow(int $number): mixed
    {
        $order = OrderRepo::getInstance()->getOrderByNumber($number);

        if ($order->customer_id !== current_customer_id()) {
            abort(403, 'Unauthorized access to order details');
        }

        $order->load(['items', 'fees']);
        $data = [
            'order' => $order,
        ];

        return inno_view('orders.show', $data);
    }
}
