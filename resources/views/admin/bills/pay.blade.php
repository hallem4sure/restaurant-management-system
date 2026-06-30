@extends('layouts.app')

@section('page_title', 'Process Payment — ' . $bill->bill_number)

@section('custom_css')
<style>
    .pos-summary { background: #1e1e2f; color: #fff; border-radius: 8px; padding: 20px; }
    .pos-summary .total-line { font-size: 1.8rem; font-weight: 700; color: #28a745; border-top: 2px solid #444; padding-top: 10px; margin-top: 10px; }
    .pos-summary .line { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 0.95rem; }
    .pos-summary .line .label { color: #aaa; }
    .pos-summary .item-row { border-bottom: 1px solid #333; padding: 6px 0; font-size: 0.88rem; }
    .payment-btn { cursor: pointer; border: 2px solid transparent; transition: all .15s; }
    .payment-btn:hover, .payment-btn.active { border-color: #28a745; background: #e9fbe9; }
    .change-display { font-size: 2rem; font-weight: 700; color: #17a2b8; }
</style>
@stop

@section('main_content')
<div class="row">
    {{-- LEFT: Bill Summary --}}
    <div class="col-lg-5 mb-4">
        <div class="pos-summary">
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-receipt mr-1"></i> {{ $bill->bill_number }}</h5>
                    <span class="badge badge-warning">Pending Payment</span>
                </div>
                <small class="text-muted">Order: {{ $bill->order->order_number ?? '—' }} &bull; Table: {{ $bill->order->table->table_number ?? 'N/A' }}</small>
            </div>

            <hr style="border-color:#444">

            {{-- Items --}}
            @foreach($bill->order->items as $item)
            <div class="item-row">
                <div class="d-flex justify-content-between">
                    <span>{{ $item->menuItem->name ?? 'Item' }} <small class="text-muted">×{{ $item->quantity }}</small></span>
                    <span>{{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                </div>
                @if($item->special_instructions)
                    <small class="text-muted fst-italic">{{ $item->special_instructions }}</small>
                @endif
            </div>
            @endforeach

            <hr style="border-color:#444">

            <div class="line"><span class="label">Subtotal</span><span>{{ number_format($bill->subtotal, 2) }}</span></div>
            @if($bill->discount_amount > 0)
            <div class="line"><span class="label">Discount</span><span class="text-danger">− {{ number_format($bill->discount_amount, 2) }}</span></div>
            @endif
            <div class="line"><span class="label">Tax</span><span>{{ number_format($bill->tax_amount, 2) }}</span></div>
            <div class="line"><span class="label">Service Charge</span><span>{{ number_format($bill->service_charge_amount, 2) }}</span></div>

            <div class="total-line">
                <span>TOTAL</span>
                <span>{{ number_format($bill->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- RIGHT: Payment Form --}}
    <div class="col-lg-7">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cash-register mr-1"></i> Payment Details</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.bills.update', $bill) }}" method="POST" id="paymentForm">
                    @csrf @method('PUT')

                    {{-- Payment Method --}}
                    <div class="form-group">
                        <label class="d-block font-weight-bold mb-2">Payment Method <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-4">
                                <div class="card text-center payment-btn p-3 {{ old('payment_method') === 'cash' ? 'active' : '' }}"
                                     onclick="selectPayment('cash', this)">
                                    <i class="fas fa-money-bill-wave fa-2x text-success mb-1"></i>
                                    <strong>Cash</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card text-center payment-btn p-3 {{ old('payment_method') === 'card' ? 'active' : '' }}"
                                     onclick="selectPayment('card', this)">
                                    <i class="fas fa-credit-card fa-2x text-primary mb-1"></i>
                                    <strong>Card</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card text-center payment-btn p-3 {{ old('payment_method') === 'digital_wallet' ? 'active' : '' }}"
                                     onclick="selectPayment('digital_wallet', this)">
                                    <i class="fas fa-mobile-alt fa-2x text-info mb-1"></i>
                                    <strong>E-Wallet</strong>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method" id="payment_method" value="{{ old('payment_method') }}" required>
                        @error('payment_method')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Amount Paid --}}
                    <div class="form-group">
                        <label for="amount_paid" class="font-weight-bold">Amount Paid <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend"><span class="input-group-text">{{ setting('billing.currency_symbol', '$') }}</span></div>
                            <input type="number" step="0.01" min="{{ $bill->total_amount }}" name="amount_paid" id="amount_paid"
                                   class="form-control @error('amount_paid') is-invalid @enderror"
                                   value="{{ old('amount_paid', number_format($bill->total_amount, 2, '.', '')) }}"
                                   placeholder="{{ number_format($bill->total_amount, 2) }}" required>
                            @error('amount_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- Quick Amount Buttons --}}
                        <div class="mt-2">
                            @foreach([0, 5, 10, 20, 50] as $extra)
                                @php $val = $bill->total_amount + $extra; @endphp
                                <button type="button" class="btn btn-sm btn-outline-secondary mr-1 mb-1"
                                        onclick="document.getElementById('amount_paid').value='{{ number_format($val, 2, '.', '') }}'; calcChange();">
                                    ${{ number_format($val, 2) }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Change Display --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Change Due</label>
                        <div class="change-display text-center p-2 bg-light rounded" id="changeDisplay">$0.00</div>
                    </div>

                    {{-- Notes --}}
                    <div class="form-group">
                        <label for="notes">Notes <small class="text-muted">(optional)</small></label>
                        <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Payment notes…">{{ old('notes') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col">
                            <a href="{{ route('admin.bills.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left mr-1"></i> Back
                            </a>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-success btn-block" id="submitPayment">
                                <i class="fas fa-check-circle mr-1"></i> Confirm Payment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('custom_js')
<script>
    const totalAmount = {{ $bill->total_amount }};

    function selectPayment(method, el) {
        document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('payment_method').value = method;
    }

        const changeSymbol = '{{ setting('billing.currency_symbol', '$') }}';
        const changePos = '{{ setting('billing.currency_position', 'before') }}';

        function calcChange() {
            const paid = parseFloat(document.getElementById('amount_paid').value) || 0;
            const change = Math.max(0, paid - totalAmount);
            const formatted = change.toFixed(2);
            const display = changePos === 'before' ? changeSymbol + formatted : formatted + changeSymbol;
            document.getElementById('changeDisplay').textContent = display;
            document.getElementById('changeDisplay').style.color = change > 0 ? '#28a745' : '#6c757d';
        }

    document.getElementById('amount_paid').addEventListener('input', calcChange);

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const method = document.getElementById('payment_method').value;
        if (!method) {
            e.preventDefault();
            alert('Please select a payment method.');
        }
    });

    // Init
    calcChange();
</script>
@stop
