@extends('layouts/contract-invoice')
@section('title', trans('app.receipt'))
@section('content')
    <div class="row">
        <div class="col-md-12 render">
            <h3 class="services" style="margin-top:20px;">
                បង្កាន់ដៃបង់ប្រាក់
            </h3>
            <p>លេខៈ {{ $invoice->invoice_number }}</p>
            <table>
                <tr>
                    <td>លេខគណនី</td>
                    <td>{{ $invoice->loan->account_number }}</td>
                    <td>បង់ប្រាក់តាមរយៈ</td>
                    <td>{{ paymentMethods($invoice->payment_method) }}</td>
                </tr>
                <tr>
                    <td>ឈ្មោះ</td>
                    <td>{{ $invoice->loan->client->name }}</td>
                    <td>លេខយោង (Ref.)</td>
                    <td>{{ $invoice->reference_number ?? trans('app.none') }}</td>
                </tr>
                <tr>
                    <td>ចំនួនប្រាក់​ពិន័យ</td>
                    <td>$ {{ decimalNumber($invoice->penalty) }}</td>
                    <td>កាលបរិឆ្ឆេទ</td>
                    <td>{{ khmerDate($invoice->payment_date) }}</td>
                </tr>
                <tr>
                    <td>ចំនួនទឹកប្រាក់សរុប</td>
                    <td>$ {{ decimalNumber($invoice->payment_amount) }}</td>
                </tr>
            </table>
            <p style="font-size: 12px;">(1 ដុល្លា = 4100 រៀល)</p>
        </div>
        <div class="col-md-12">
            <p class="text-right" style="margin: 10px 100px 10px 0">
                ថ្ងៃទី {{ date('d') }} ខែ {{ khmerMonths(date('m')) }} ឆ្នាំ {{ date('Y') }}
            </p>
            <p class="text-center" style="margin: 10px 0 80px 0">
                <span style="padding:20px; padding-right:300px;"> អ្នកទទួលប្រាក់ </span>អ្នកបង់ប្រាក់
            </p>
        </div>
    </div>
@endsection
