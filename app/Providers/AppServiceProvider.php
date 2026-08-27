<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        if (env('APP_FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // Register OpenAI-compatible providers that use PrismGateway
        // (chat/completions) instead of OpenAiGateway (/responses)
        if (class_exists(\Laravel\Ai\AiManager::class)) {
            \Laravel\Ai\Ai::extend('glm', function ($app, array $config) {
                $config['driver'] = 'deepseek'; // Uses /chat/completions
                return new \Laravel\Ai\Providers\DeepSeekProvider(
                    new \Laravel\Ai\Gateway\Prism\PrismGateway($app['events']),
                    $config,
                    $app->make(\Illuminate\Contracts\Events\Dispatcher::class)
                );
            });

            \Laravel\Ai\Ai::extend('minimax', function ($app, array $config) {
                $config['driver'] = 'deepseek'; // Uses /chat/completions
                return new \Laravel\Ai\Providers\DeepSeekProvider(
                    new \Laravel\Ai\Gateway\Prism\PrismGateway($app['events']),
                    $config,
                    $app->make(\Illuminate\Contracts\Events\Dispatcher::class)
                );
            });
        }

        // Inject manual payment methods into checkout
        listen_hook_filter('service.checkout.billing.methods', function (array $methods): array {
            $customPayments = system_setting('custom_payments');
            \Illuminate\Support\Facades\Log::info('Checking custom payments', ['data' => $customPayments]);
            if (is_array($customPayments)) {
                foreach ($customPayments as $index => $payment) {
                    $methods[] = [
                        'type'        => 'payment',
                        'code'        => 'custom_payment_' . $index,
                        'name'        => $payment['name'] ?? 'Custom Payment',
                        'description' => $payment['description'] ?? '',
                        'icon'        => asset('images/payment/custom.png'),
                    ];
                }
            }
            return $methods;
        });

        // Handle payment page view for custom methods
        listen_hook_filter('service.payment.pay.view', function (string $viewPath, $originCode): string {
            if ($originCode && str_starts_with($originCode, 'custom_payment')) {
                return 'payments.custom';
            }
            return $viewPath;
        }, 10, 2);

        // Handle payment page data for custom methods
        listen_hook_filter('service.payment.pay.data', function (array $data, $originCode): array {
            if ($originCode && str_starts_with($originCode, 'custom_payment')) {
                $index = str_replace(['custom_payment_', 'custom_payment'], '', $originCode);
                $customPayments = system_setting('custom_payments');
                if (is_string($customPayments)) {
                    $customPayments = json_decode($customPayments, true) ?: [];
                }
                $payment = $customPayments[$index] ?? null;
                $data['custom_payment'] = $payment;
            }
            return $data;
        }, 10, 2);

        // Fix missing names for custom methods during checkout
        listen_hook_filter('resource.checkout.simple', function (array $data): array {
            $billingCode = $data['billing_method_code'] ?? '';
            if ($billingCode && str_starts_with($billingCode, 'custom_payment')) {
                $index = str_replace(['custom_payment_', 'custom_payment'], '', $billingCode);
                $customPayments = system_setting('custom_payments');
                if (is_string($customPayments)) {
                    $customPayments = json_decode($customPayments, true) ?: [];
                }
                if (isset($customPayments[$index]['name'])) {
                    $data['billing_method_name'] = $customPayments[$index]['name'];
                }
            }

            $shippingCode = $data['shipping_method_code'] ?? '';
            if ($shippingCode && str_starts_with($shippingCode, 'manual_logistic_')) {
                $codeOrIndex = str_replace('manual_logistic_', '', $shippingCode);
                $logistics = system_setting('logistics');
                if (is_string($logistics)) {
                    $logistics = json_decode($logistics, true) ?: [];
                }
                foreach ((array) $logistics as $index => $logistic) {
                    if (($logistic['code'] ?: $index) == $codeOrIndex) {
                        $data['shipping_method_name'] = $logistic['company'] ?? '';
                        break;
                    }
                }
            }

            return $data;
        });

        // Inject manual logistics companies as shipping methods
        listen_hook_filter('common.service.checkout.shipping.methods', function (array $methods): array {
            $logistics = system_setting('logistics');
            if (is_array($logistics) && count($logistics) > 0) {
                $quotes = [];
                foreach ($logistics as $index => $logistic) {
                    $companyName = $logistic['company'] ?? '';
                    if (empty($companyName)) {
                        continue;
                    }
                    $cost = $logistic['cost'] ?? 0;
                    $quotes[] = [
                        'code'        => 'manual_logistic_' . ($logistic['code'] ?: $index),
                        'name'        => $companyName,
                        'cost'        => $cost,
                        'cost_format' => currency_format($cost),
                        'icon'        => '',
                    ];
                }

                if (! empty($quotes)) {
                    $methods[] = [
                        'code'   => 'manual_logistics',
                        'name'   => panel_trans('setting.logistics_information'),
                        'quotes' => $quotes,
                    ];
                }
            }

            return $methods;
        });
    }
}
