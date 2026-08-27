@extends('layouts.app')
@section('body-class', 'page-checkout-success')

@section('content')

  @if($order)
    <x-front-breadcrumb type="order" :value="$order"/>
  @endif

  @hookinsert('checkout.success.top')

  <div class="container">
    <div class="checkout-success-box">
      @if($order)
        <div class="order-success-icon text-success mb-4 text-center">
          <i class="bi bi-check-circle-fill animate-success" style="font-size: 100px; display: inline-block; filter: drop-shadow(0 10px 15px rgba(25, 135, 84, 0.2));"></i>
        </div>

        <style>
          @keyframes success-pop {
            0% { transform: scale(0.5); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
          }
          .animate-success {
            animation: success-pop 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
          }
        </style>
       

        @if($order->billing_method_code == 'bank_transfer')
          <div class="checkout-success-title"><span>{{ trans('front/payment.bank_transfer_success_title') }}</span></div>
        @else
          <div class="checkout-success-title"><span>{{ trans('front/payment.success_title') }}</span></div>
        @endif
        <table class="table w-max-700 mx-auto mb-3 mb-md-5 checkout-success-table">
          <thead>
          <tr>
            <th>{{ trans('front/order.customer_name') }}</th>
            <th>{{ trans('front/order.order_number') }}</th>
            <th>{{ trans('front/order.order_date') }}</th>
            <th>{{ trans('front/order.order_total') }}</th>
            <th>{{ trans('front/order.order_status') }}</th>
          </tr>
          </thead>
          <tbody>
          <tr>
            <td>{{ $order->billing_customer_name }}</td>
            <td>{{ $order->number }}</td>
            <td>{{ $order->created_at->format('Y-m-d') }}</td>
            <td>{{ currency_format($order->total) }}</td>
            <td>{{ $order->status_format }}</td>
          </tr>
          </tbody>
        </table>

        <div class="checkout-success-btns d-flex flex-column justify-content-center w-max-400 mx-auto">
          @if(current_customer())
            <a href="{{ account_route('orders.number_show', ['number'=>$order->number]) }}"
               class="btn btn-lg btn-primary mb-3">{{ trans('front/payment.view_order') }}</a>
          @else
            <a href="{{ front_route('orders.number_show', ['number'=>$order->number]) }}"
               class="btn btn-lg btn-primary mb-3">{{ trans('front/payment.view_order') }}</a>
          @endif
          <a href="{{ front_route('home.index') }}" class="btn btn-lg btn-outline-primary">{{ trans('front/payment.continue_shopping') }}</a>
        </div>
      @else
        No order.
      @endif
    </div>
  </div>
  @hookinsert('checkout.success.bottom')
@endsection
