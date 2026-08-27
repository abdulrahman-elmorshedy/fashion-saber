<?php
namespace InnoShop\Common\Models;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use InnoShop\Common\Models\Order\Fee;
use InnoShop\Common\Models\Order\History;
use InnoShop\Common\Models\Order\Item;
use InnoShop\Common\Models\Order\Payment;
use InnoShop\Common\Models\Order\Shipment;
use InnoShop\Common\Notifications\OrderNewNotification;
use InnoShop\Common\Notifications\OrderUpdateNotification;
use InnoShop\Common\Services\CartService;
use InnoShop\Common\Services\OrderService;
use InnoShop\Common\Services\StateMachineService;
use Illuminate\Support\Facades\Notification;
use Throwable;

class Order extends BaseModel
{
    use Notifiable;

    protected $table = 'orders';

    protected $fillable = [
        'number', 'customer_id', 'customer_group_id', 'shipping_address_id', 'billing_address_id', 'customer_name',
        'email', 'calling_code', 'telephone', 'total', 'locale', 'currency_code', 'currency_value', 'ip', 'user_agent',
        'status', 'shipping_method_code', 'shipping_method_name', 'shipping_customer_name', 'shipping_calling_code',
        'shipping_telephone', 'shipping_telephone_2', 'shipping_country', 'shipping_country_id', 'shipping_state_id', 'shipping_state',
        'shipping_city', 'shipping_address_1', 'shipping_address_2', 'shipping_zipcode', 'billing_method_code',
        'billing_method_name', 'billing_customer_name', 'billing_calling_code', 'billing_telephone', 'billing_telephone_2', 'billing_country',
        'billing_country_id', 'billing_state_id', 'billing_state', 'billing_city', 'billing_address_1',
        'billing_address_2', 'billing_zipcode', 'comment', 'admin_note', 'created_at', 'updated_at',
    ];

    protected $appends = [
        'total_format',
        'status_format',
    ];

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Order::class, 'parent_id', 'id')->whereRaw('id != parent_id');
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'parent_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function shippingCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * Order items.
     *
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'order_id', 'id');
    }

    /**
     * Order fees.
     *
     * @return HasMany
     */
    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class, 'order_id', 'id');
    }

    /**
     * Order histories.
     *
     * @return HasMany
     */
    public function histories(): HasMany
    {
        return $this->hasMany(History::class, 'order_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'order_id', 'id');
    }

    /**
     * Order payments.
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }

    /**
     * Calculate order subtotal.
     *
     * @return float
     */
    public function calcSubtotal(): float
    {
        $decimal = currency_decimal_place($this->currency_code);

        return round($this->items->sum('subtotal'), $decimal);
    }

    /**
     * Calculate order total.
     *
     * @return float
     */
    public function calcTotal(): float
    {
        $decimal = currency_decimal_place($this->currency_code);

        return round($this->fees->sum('value'), $decimal);
    }

    /**
     * Format total by currency.
     *
     * @return string
     */
    public function getTotalFormatAttribute(): string
    {
        return currency_format($this->total, $this->currency_code, $this->currency_value);
    }

    /**
     * Get dynamic billing method name if empty in database.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getBillingMethodNameAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $code = $this->billing_method_code;
        if ($code && str_starts_with($code, 'custom_payment')) {
            $index = str_replace(['custom_payment_', 'custom_payment'], '', $code);
            $customPayments = system_setting('custom_payments');
            if (is_string($customPayments)) {
                $customPayments = json_decode($customPayments, true) ?: [];
            }
            return $customPayments[$index]['name'] ?? 'Custom Payment';
        }

        return $value;
    }

    /**
     * Get dynamic shipping method name if empty in database.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getShippingMethodNameAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $code = $this->shipping_method_code;
        if ($code && str_starts_with($code, 'manual_logistic_')) {
            $codeOrIndex = str_replace('manual_logistic_', '', $code);
            $logistics = system_setting('logistics');
            if (is_string($logistics)) {
                $logistics = json_decode($logistics, true) ?: [];
            }
            foreach ((array) $logistics as $index => $logistic) {
                if (($logistic['code'] ?: $index) == $codeOrIndex) {
                    return $logistic['company'] ?? 'Manual Logistic';
                }
            }
        }

        return $value;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getStatusColorAttribute(): string
    {
        $statusCode = $this->status;
        if ($statusCode == null) {
            return '';
        }

        if ($statusCode == StateMachineService::UNPAID) {
            return 'warning';
        } elseif (in_array($statusCode, [StateMachineService::CREATED, StateMachineService::CANCELLED])) {
            return 'danger';
        } else {
            return 'success';
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getStatusFormatAttribute(): string
    {
        $statusCode = $this->status;
        if ($statusCode == null) {
            return '';
        }

        $statusMap = array_column(StateMachineService::getAllStatuses(), 'name', 'status');

        return $statusMap[$statusCode] ?? '';
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function addToCart(): void
    {
        CartService::getInstance($this->customer_id)->addOrderToCart($this);
    }

    /**
     * @return Order
     * @throws Throwable
     */
    public function reorder(): Order
    {
        return OrderService::getInstance($this->id)->reorder();
    }

    /**
     * Send a new order notification.
     *
     * @return void
     */
    public function notifyNewOrder(): void
    {
        try {
            $useQueue      = system_setting('use_queue', false);
            $notifications = system_setting('email_notifications', []);

            // 1. Notify Customer
            if ($useQueue) {
                $this->notify(new OrderNewNotification($this));
            } else {
                $this->notifyNow(new OrderNewNotification($this));
            }

            // 2. Notify Admins if enabled
            if (in_array('new_order', $notifications)) {
                $admins = Admin::query()->where('active', 1)->get();
                if ($admins->isNotEmpty()) {
                    if ($useQueue) {
                        Notification::send($admins, new OrderNewNotification($this));
                    } else {
                        Notification::sendNow($admins, new OrderNewNotification($this));
                    }
                }
            }
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString());
        }
    }

    /**
     * Send an order status update notification.
     *
     * @param  $fromCode
     * @return void
     */
    public function notifyUpdateOrder($fromCode): void
    {
        try {
            $useQueue = system_setting('use_queue', false);
            if ($useQueue) {
                $this->notify(new OrderUpdateNotification($this, $fromCode));
            } else {
                $this->notifyNow(new OrderUpdateNotification($this, $fromCode));
            }
        } catch (Throwable $th) {
            Log::error($th->getMessage());
            Log::error($th->getTraceAsString());
        }
    }
}
