@extends('layouts.app')

@section('page_title', 'Invoice — ' . $bill->bill_number)

@section('custom_css')
<style>
    @media print {
        .no-print, .content-header, .main-sidebar, .main-header, .main-footer { display: none !important; }
        .content-wrapper { margin: 0 !important; padding: 0 !important; }
        .invoice-card { box-shadow: none !important; border: none !important; }
    }
    .invoice-card { max-width: 800px; margin: 0 auto; }
    .invoice-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: white; border-radius: 8px 8px 0 0; padding: 30px; }
    .invoice-header h2 { font-size: 2.5rem; font-weight: 700; letter-spacing: 2px; }
    .invoice-body { padding: 30px; }
    .invoice-table th { background: #f4f6f8; font-weight: 600; }
    .totals-table td { padding: 6px 12px; }
    .grand-total { font-size: 1.4rem; font-weight: 700; background: #f4f6f8; }
    .status-paid { color: #28a745; font-weight: 700; font-size: 1.1rem; }
    .status-pending { color: #ffc107; font-weight: 700; font-size: 1.1rem; }
    .watermark-paid {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%) rotate(-30deg);
        font-size: 5rem; font-weight: 900; color: rgba(40,167,69,0.08);
        pointer-events: none; white-space: nowrap; z-index: 0;
    }
</style>
@stop

@section('main_content')
<div class="mb-3 no-print d-flex gap-2">
    <a href="{{ route('admin.bills.index') }}" class="btn btn-secondary mr-2">
        <i class="fas fa-arrow-left mr-1"></i> Back to Bills
    </a>
    @if($bill->isPending())
        @can('processPayment', $bill)
        <a href="{{ route('admin.bills.edit', $bill) }}" class="btn btn-success mr-2">
            <i class="fas fa-cash-register mr-1"></i> Process Payment
        </a>
        @endcan
    @endif
    <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print mr-1"></i> Print Invoice
    </button>
</div>

<div class="card invoice-card" style="position:relative; overflow:hidden;">
    @if($bill->isPaid())
    <div class="watermark-paid">PAID</div>
    @endif

    {{-- Invoice Header --}}
    <div class="invoice-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                @if(setting('general.restaurant_logo'))
                    <img src="{{ asset(setting('general.restaurant_logo')) }}" alt="Logo" style="max-height:70px; max-width:200px; margin-bottom:8px;"><br>
                @else
                    <h2><i class="fas fa-utensils mr-2"></i>{{ setting('general.restaurant_name', config('app.name')) }}</h2>
                @endif
                @if(setting('general.restaurant_address'))
                    <p class="mb-0 text-light"><small>{{ setting('general.restaurant_address') }}</small></p>
                @endif
                @if(setting('general.restaurant_phone'))
                    <p class="mb-0 text-light"><small><i class="fas fa-phone mr-1"></i>{{ setting('general.restaurant_phone') }}</small></p>
                @endif
                <p class="mb-0 text-light mt-1">Official Tax Invoice</p>
            </div>
            <div class="col-md-6 text-md-right">
                <h4 class="mb-1">INVOICE</h4>
                <p class="mb-1"><strong>{{ $bill->bill_number }}</strong></p>
                <p class="mb-1"><small>Generated: {{ $bill->created_at->format('d M Y, H:i') }}</small></p>
                @if($bill->isPaid())
                <p class="mb-0"><small>Paid: {{ $bill->paid_at->format('d M Y, H:i') }}</small></p>
                @endif
            </div>
        </div>
    </div>

    <div class="invoice-body">
        {{-- Order / Cashier Info --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="font-weight-bold text-muted text-uppercase mb-2">Order Details</h6>
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted">Order #</td><td><strong>{{ $bill->order->order_number ?? '—' }}</strong></td></tr>
                    <tr><td class="text-muted">Table</td><td>{{ $bill->order->table->table_number ?? 'N/A' }}</td></tr>
                    <tr><td class="text-muted">Type</td><td>{{ ucfirst($bill->order->type ?? '—') }}</td></tr>
                    <tr><td class="text-muted">Waiter</td><td>{{ $bill->order->waiter->name ?? 'System' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="font-weight-bold text-muted text-uppercase mb-2">Payment Info</h6>
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($bill->isPaid())
                                <span class="status-paid"><i class="fas fa-check-circle mr-1"></i>PAID</span>
                            @elseif($bill->isCancelled())
                                <span class="text-danger font-weight-bold"><i class="fas fa-ban mr-1"></i>CANCELLED</span>
                            @else
                                <span class="status-pending"><i class="fas fa-clock mr-1"></i>PENDING</span>
                            @endif
                        </td>
                    </tr>
                    @if($bill->isPaid())
                    <tr><td class="text-muted">Method</td><td>{{ ucfirst(str_replace('_', ' ', $bill->payment_method)) }}</td></tr>
                    <tr><td class="text-muted">Amount Paid</td><td><strong>{{ number_format($bill->amount_paid, 2) }}</strong></td></tr>
                    @if($bill->change_amount > 0)
                    <tr><td class="text-muted">Change</td><td class="text-info">{{ number_format($bill->change_amount, 2) }}</td></tr>
                    @endif
                    <tr><td class="text-muted">Cashier</td><td>{{ $bill->cashier->name ?? 'System' }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- Items Table --}}
        <h6 class="font-weight-bold text-muted text-uppercase mb-2">Order Items</h6>
        <div class="table-responsive mb-4">
            <table class="table invoice-table border">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->order->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{ $item->menuItem->name ?? 'Item' }}
                            @if($item->special_instructions)
                                <br><small class="text-muted fst-italic">{{ $item->special_instructions }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">
                            @php $sym = setting('billing.currency_symbol', '$'); $pos = setting('billing.currency_position', 'before'); @endphp
                            {{ $pos === 'before' ? $sym . number_format($item->unit_price, 2) : number_format($item->unit_price, 2) . $sym }}
                        </td>
                        <td class="text-right"><strong>
                            {{ $pos === 'before' ? $sym . number_format($item->unit_price * $item->quantity, 2) : number_format($item->unit_price * $item->quantity, 2) . $sym }}
                        </strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="row justify-content-end">
            <div class="col-md-5">
                <table class="table totals-table border">
                    @php $sym = setting('billing.currency_symbol', '$'); $pos = setting('billing.currency_position', 'before'); @endphp
                    <tr>
                        <td class="text-muted">Subtotal</td>
                        <td class="text-right">{{ $pos === 'before' ? $sym . number_format($bill->subtotal, 2) : number_format($bill->subtotal, 2) . $sym }}</td>
                    </tr>
                    @if($bill->discount_amount > 0)
                    <tr>
                        <td class="text-danger">Discount</td>
                        <td class="text-right text-danger">− {{ $pos === 'before' ? $sym . number_format($bill->discount_amount, 2) : number_format($bill->discount_amount, 2) . $sym }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Tax</td>
                        <td class="text-right">{{ $pos === 'before' ? $sym . number_format($bill->tax_amount, 2) : number_format($bill->tax_amount, 2) . $sym }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Service Charge</td>
                        <td class="text-right">{{ $pos === 'before' ? $sym . number_format($bill->service_charge_amount, 2) : number_format($bill->service_charge_amount, 2) . $sym }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td><strong>GRAND TOTAL</strong></td>
                        <td class="text-right"><strong>{{ $pos === 'before' ? $sym . number_format($bill->total_amount, 2) : number_format($bill->total_amount, 2) . $sym }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        @if($bill->notes)
        <div class="alert alert-light border mt-3">
            <strong>Notes:</strong> {{ $bill->notes }}
        </div>
        @endif

        <hr>
        <p class="text-center text-muted mb-0">
            <small>{{ setting('billing.receipt_footer', 'Thank you for dining with us!') }} &bull; {{ setting('general.restaurant_name', config('app.name')) }}</small>
        </p>
    </div>
</div>
@stop
