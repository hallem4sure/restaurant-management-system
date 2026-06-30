@extends('layouts.app')

@section('page_title', 'System Settings')

@section('main_content')
<div class="row">
    <div class="col-md-3">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Settings Menu</h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column" id="settings-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" role="tab"><i class="fas fa-store mr-2"></i> General</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="billing-tab" data-toggle="pill" href="#billing" role="tab"><i class="fas fa-file-invoice-dollar mr-2"></i> Billing & POS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="hours-tab" data-toggle="pill" href="#hours" role="tab"><i class="far fa-clock mr-2"></i> Business Hours</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title">Update Settings</h3>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="settings-tabContent">
                        
                        {{-- General Settings --}}
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <h5>General Settings</h5>
                            <hr>
                            
                            <div class="row mb-3">
                                <div class="col-md-12 text-center">
                                    @if(setting('general.restaurant_logo'))
                                        <img src="{{ asset(setting('general.restaurant_logo')) }}" alt="Logo" class="img-thumbnail mb-2" style="max-height: 120px;">
                                    @else
                                        <div class="text-muted mb-2"><i class="fas fa-image fa-3x"></i><br>No Logo Uploaded</div>
                                    @endif
                                    <div>
                                        <label for="logo_file" class="btn btn-sm btn-outline-secondary">Upload New Logo</label>
                                        <input type="file" name="logo_file" id="logo_file" class="d-none" accept="image/*">
                                    </div>
                                    <small class="text-muted">Recommended size: 250x100 pixels</small>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Restaurant Name</label>
                                <input type="text" name="settings[general.restaurant_name]" class="form-control" value="{{ setting('general.restaurant_name', 'My Restaurant') }}">
                            </div>
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text" name="settings[general.restaurant_phone]" class="form-control" value="{{ setting('general.restaurant_phone') }}">
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="settings[general.restaurant_address]" class="form-control" rows="3">{{ setting('general.restaurant_address') }}</textarea>
                            </div>
                        </div>

                        {{-- Billing Settings --}}
                        <div class="tab-pane fade" id="billing" role="tabpanel">
                            <h5>Billing & POS Settings</h5>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Currency Symbol</label>
                                    <input type="text" name="settings[billing.currency_symbol]" class="form-control" value="{{ setting('billing.currency_symbol', '$') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Currency Position</label>
                                    <select name="settings[billing.currency_position]" class="form-control">
                                        <option value="before" {{ setting('billing.currency_position', 'before') == 'before' ? 'selected' : '' }}>Before Amount (e.g. $10)</option>
                                        <option value="after" {{ setting('billing.currency_position') == 'after' ? 'selected' : '' }}>After Amount (e.g. 10$)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Tax Rate (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100" name="settings[billing.tax_rate]" class="form-control" value="{{ setting('billing.tax_rate', 0) }}">
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Service Charge Rate (%)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" max="100" name="settings[billing.service_charge_rate]" class="form-control" value="{{ setting('billing.service_charge_rate', 0) }}">
                                        <div class="input-group-append"><span class="input-group-text">%</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Receipt Width (mm)</label>
                                <select name="settings[billing.receipt_width]" class="form-control">
                                    <option value="80" {{ setting('billing.receipt_width', '80') == '80' ? 'selected' : '' }}>80mm (Standard POS)</option>
                                    <option value="58" {{ setting('billing.receipt_width') == '58' ? 'selected' : '' }}>58mm (Small POS)</option>
                                    <option value="a4" {{ setting('billing.receipt_width') == 'a4' ? 'selected' : '' }}>A4 (Standard Printer)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Receipt Footer Message</label>
                                <textarea name="settings[billing.receipt_footer]" class="form-control" rows="3">{{ setting('billing.receipt_footer') }}</textarea>
                                <small class="text-muted">This message will appear at the bottom of all printed bills.</small>
                            </div>
                        </div>

                        {{-- Business Hours --}}
                        <div class="tab-pane fade" id="hours" role="tabpanel">
                            <h5>Business Hours</h5>
                            <hr>
                            
                            <div class="form-group">
                                <label>Opening Time</label>
                                <input type="time" name="settings[general.opening_time]" class="form-control" value="{{ setting('general.opening_time', '09:00') }}">
                            </div>
                            <div class="form-group">
                                <label>Closing Time</label>
                                <input type="time" name="settings[general.closing_time]" class="form-control" value="{{ setting('general.closing_time', '22:00') }}">
                            </div>
                            <div class="form-group">
                                <label>Closed Days</label>
                                <input type="text" name="settings[general.closed_days]" class="form-control" value="{{ setting('general.closed_days', 'None') }}" placeholder="e.g. Sunday, Monday">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Save All Settings</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop
