@extends('layouts.app')

@section('page_title', 'Edit Order')

@section('main_content')
<form action="{{ route('admin.orders.update', $order) }}" method="POST" id="order-form">
@csrf @method('PUT')

<div class="row">
    <div class="col-lg-8">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Order Items</h3>
                <span class="badge badge-info">{{ $order->order_number }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="items-table">
                        <thead>
                            <tr>
                                <th>Menu Item <span class="text-danger">*</span></th>
                                <th style="width: 15%">Qty <span class="text-danger">*</span></th>
                                <th style="width: 25%">Instructions</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            @foreach(old('items', $order->items) as $i => $item)
                            <tr>
                                <td>
                                    <select name="items[{{ $i }}][menu_item_id]" class="form-control item-select" required>
                                        <option value="">-- Select Item --</option>
                                        @foreach($menuItems as $m)
                                            <option value="{{ $m->id }}" {{ (isset($item['menu_item_id']) && $item['menu_item_id'] == $m->id) || (is_object($item) && $item->menu_item_id == $m->id) ? 'selected' : '' }}>
                                                {{ $m->name }} ({{ number_format($m->price, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $i }}][quantity]" class="form-control text-center" 
                                           value="{{ is_object($item) ? $item->quantity : ($item['quantity'] ?? 1) }}" min="1" max="99" required>
                                </td>
                                <td>
                                    <input type="text" name="items[{{ $i }}][special_instructions]" class="form-control" 
                                           value="{{ is_object($item) ? $item->special_instructions : ($item['special_instructions'] ?? '') }}" placeholder="Notes">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" id="add-item-btn">
                    <i class="fas fa-plus mr-1"></i> Add Item
                </button>
                @error('items')<div class="text-danger mt-2"><small>{{ $message }}</small></div>@enderror
            </div>
        </div>
        
        <div class="card card-secondary card-outline">
            <div class="card-header"><h3 class="card-title">Order Notes</h3></div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label for="special_instructions">Special Instructions</label>
                    <textarea name="special_instructions" id="special_instructions" rows="2"
                              class="form-control @error('special_instructions') is-invalid @enderror">{{ old('special_instructions', $order->special_instructions) }}</textarea>
                    @error('special_instructions')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title">Order Details</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="type">Order Type <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="walk_in" {{ old('type', $order->type) == 'walk_in' ? 'selected' : '' }}>Walk In</option>
                        <option value="reservation" {{ old('type', $order->type) == 'reservation' ? 'selected' : '' }}>Reservation</option>
                    </select>
                    @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="table_id">Select Table</label>
                    <select name="table_id" id="table_id" class="form-control @error('table_id') is-invalid @enderror">
                        <option value="">-- Choose Table --</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ old('table_id', $order->table_id) == $table->id ? 'selected' : '' }}>
                                Table {{ $table->table_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('table_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="reservation_id">Select Reservation</label>
                    <select name="reservation_id" id="reservation_id" class="form-control @error('reservation_id') is-invalid @enderror">
                        <option value="">-- None --</option>
                        @foreach($reservations as $res)
                            <option value="{{ $res->id }}" {{ old('reservation_id', $order->reservation_id) == $res->id ? 'selected' : '' }}>
                                Res #{{ $res->id }} - {{ $res->customer_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('reservation_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        @foreach(['pending','confirmed','preparing','ready','served','completed','cancelled'] as $st)
                            <option value="{{ $st }}" {{ old('status', $order->status) == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                        @endforeach
                    </select>
                    @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <button type="submit" class="btn btn-info btn-block">
                    <i class="fas fa-save mr-1"></i> Update Order
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-default btn-block">Cancel</a>
            </div>
        </div>
    </div>
</div>
</form>

{{-- Template for JS --}}
<template id="item-row-template">
    <tr>
        <td>
            <select name="items[__INDEX__][menu_item_id]" class="form-control item-select" required>
                <option value="">-- Select Item --</option>
                @foreach($menuItems as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} ({{ number_format($item->price, 2) }})</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="items[__INDEX__][quantity]" class="form-control text-center" value="1" min="1" max="99" required>
        </td>
        <td>
            <input type="text" name="items[__INDEX__][special_instructions]" class="form-control" placeholder="Notes (optional)">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-times"></i></button>
        </td>
    </tr>
</template>
@stop

@section('custom_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 999;
        const tbody = document.getElementById('items-body');
        const template = document.getElementById('item-row-template').innerHTML;

        function addRow() {
            const html = template.replace(/__INDEX__/g, itemIndex);
            tbody.insertAdjacentHTML('beforeend', html);
            itemIndex++;
        }

        document.getElementById('add-item-btn').addEventListener('click', addRow);

        tbody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                e.target.closest('tr').remove();
            }
        });
    });
</script>
@endsection
