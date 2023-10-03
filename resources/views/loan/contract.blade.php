<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $generalSetting->site_title . ' - ' . trans('app.contract') }}</title>
    <meta charset="utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset($generalSetting->site_logo) }}" sizes="32x32">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Battambang|Moul">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <style>

        .content-wrapper {
            margin-top: 10px;
            margin: 0 auto;
            width: 900px;
            /* padding: 10px; */
        }

        .moul-font {
            font-family: 'Moul', 'Arial Black', sans-serif !important;
        }

        table thead th {
            text-align: center;
        }

        .table-bordered {
            border: 1px solid #000;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000 !important;
        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
            padding: 5px;
            line-height: 1.4;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'Moul', 'Arial Black', sans-serif !important;
            line-height: 20px;
        }

        p,
        th,
        td,
        li,
        span {
            font-size: 14px !important;
            font-family: 'Battambang', Arial, sans-serif !important;
        }

        p,
        li,
        span {
            line-height: 1.8;
        }

        .content-header {
            margin-bottom: 20px;
        }

        .content-header .left-logo-wrapper {
            padding-right: 0 !important;
        }

        .content-header .right-logo-wrapper {
            padding-left: 0 !important;
        }

        .content-header .branch-name {
            margin-bottom: 25px;
            font-size: 24px;
        }

        .content-header .sub-title {
            margin-bottom: 30px;
            font-size: 16px;
        }
        .content-body {
            width: 100%;
        }
        .content-body .table-info td {
            padding-right: 7px;
            padding-bottom: 5px;
        }

        .content-body #table-schedule {
            margin-top: 20px;
            margin-bottom: 15px;
        }

        #table-schedule caption {
            font-weight: 700;
            font-size: 13px !important;
            font-family: 'Battambang', Arial, sans-serif !important;
            color: #333;
        }

        .content-body .contract-text {
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .content-body .date-wrapper {
            position: relative;
            right: 108px;
        }

        .content-body .thumbprint-content {
            margin-bottom: 110px;
        }

        .thumbprint-content p {
            font-weight: 600;
            font-size: 18px;
        }

        .thumbprint-footer h6 {
            font-weight: 700;
            font-size: 16px;
            font-family: 'Battambang', Arial, sans-serif !important;
        }

        .content-footer .footer-ruler {
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #000;
        }

        .pl-0 {
            padding-left: 0 !important;
        }

        @media print {
            body {
                width: 21cm;
                height: 29.7cm;
                margin: 5mm;
            }

            .content-footer {
                /* position: fixed; */
                bottom: 0;
                right: 0;
                width: 100%;
            }
            .table .bg-header {
                background-color:#b4f4ff !important;
            }
            .table .bg-total {
                background-color: #ffe69b !important;
            }
            .table .bg-footer {
                background-color:#acffbf !important;
            }
        }
        @page {
            size: 21cm 29.7cm;
        }

    </style>
</head>

<body>
    @php
        $branch = $loan->branch;
        $client = $loan->client;

        $depreciation_amount = ($loan->depreciation_amount/$loan->loan_amount) * 100;
        
    @endphp

    <div class="content-wrapper">
        <div class="row">
            <div class="content-footer">
                <div class="bottom-footer mt-4 row">
                    <div class="col-xs-5">
                        <img src="{{ asset($branch->logo) }}" width="100%" alt="" class="mt-4">
                    </div>
                    <div class="col-xs-11 row">
                        <div class="col-xs-12">
                                <h5 class="branch-name">
                                    <b> {{ $loan->branch->location }}</b> <br>
                                    {{ $loan->branch->location_en }}
                                </h5>
                        </div>
                        <div class="col-xs-12 row">
                            <div class="col-xs-8">
                                <p>{{ 'Tel: ' . $loan->branch->phone_1
                                    . (isset($loan->branch->phone_2) ? '/' . $loan->branch->phone_2 : '')
                                    . (isset($loan->branch->phone_3) ? '/' . $loan->branch->phone_3 : '')
                                    . (isset($loan->branch->phone_4) ? '/' . $loan->branch->phone_4 : '')
                                 }}</p>
                            </div>
                            {{-- <div class="col-xs-6 ">
                                <p class="float-right">
                                    Email: {{ $loan->note }}
                                </p>
                            </div> --}}
                        </div>

                    </div>

                </div>
                <hr class="footer-ruler w-full">
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col">
                        <table>
                            <tbody>
                                <tr>
                                    <td>{{ trans('app.loan_code') }}</td>
                                    {{-- <td>: {!! $loan->account_number . '/<b>' . $loan->client_code . '</b>' !!}</td> --}}
                                    {{-- str_pad($value, 8, '0', STR_PAD_LEFT); --}}
                                    <td>:</td>
                                    <td>{{ $loan->client_code }}</td>

                                </tr>
                                {{-- <tr>
                                    <td>{{ trans('app.wing_account_number') }}</td>
                                <td>: {{ $loan->wing_code }}</td>
                                </tr> --}}
                                <tr>
                                    <td>{{ trans('app.client_code') }}</td>
                                    <td>:</td>
                                    <td>{!! $loan->account_number . '/<b>' .str_pad($loan->client_id, 6, '0',
                                            STR_PAD_LEFT) . '</b>' !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ trans('app.start_date') }}</td>
                                    <td>:</td>
                                    <td>{{ displayDate($loan->loan_start_date) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('app.end_date') }}</td>
                                    <td>:</td>
                                    <td> {{ displayDate($loan->schedules[$loan->installment - 1]->payment_date) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <table>
                            <tbody>
                                <tr>
                                    <td>Email: {{ $loan->note }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">{{ trans('app.client_name') }}</td>
                                    <td>:</td>
                                       
                                    <td> <span class="moul-font">{{ $client->name }}</span>
                                        {{-- {{ trans('app.id_card_number') }} : {{ $client->id_card_number }} --}}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%">{{ trans('app.phone_number') }}</td>
                                    <td>:</td>
                                    <td>
                                        {{ $client->first_phone }}
                                        {{ isset($client->second_phone) ? ' / ' . $client->second_phone : '' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="vertical-align: top; width: 30%">{{ trans('app.address') }}</td>
                                    <td style="vertical-align: top">:</td>
                                    <td style="word-wrap: break-word;">
                                        @if (isset($client->address))
                                            {{ $client->address }}
                                        @elseif (isset($client->province_id) || isset($client->district_id) ||
                                            isset($client->commune_id))
                                            {{ isset($client->commune->name) ? $client->commune->name . ', ' : '' }}
                                            {{ isset($client->district->name) ? $client->district->name . ', ' : '' }}
                                            {{ isset($client->province->name) ? $client->province->name : '' }}
                                        @else
                                        {{ trans('app.n/a') }}
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <table>
                            <tbody>
                                {{-- Sponsor info --}}

                                    <tr>
                                        <td style="width: 50%">{{ trans('app.sponsor_name') }}</td>
                                        <td>
                                            : <span class="moul-font">{{ $client->sponsor_name ?? trans('app.none') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%">
                                            {{ trans('app.id_card_number') }}
                                        </td>
                                        <td>
                                            :
                                            {{ $client->sponsor_id_card ?? trans('app.none') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%">{{ trans('app.phone_number') }}</td>
                                        <td>
                                            @if (!empty($client->sponsor_phone) || !empty($client->sponsor_phone_2))
                                            : {{ $client->sponsor_phone }}
                                            {{ !empty($client->sponsor_phone) && !empty($client->sponsor_phone_2) ? ' / ' : '' }}
                                            {{ $client->sponsor_phone_2 }}
                                            @else
                                            {{ trans('app.none') }}
                                            @endif
                                        </td>
                                    </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width:7%;">{{ trans('app.no_sign') }}</th>
                                <th style="width:50%;">{{ trans('app.product') }}</th>
                                <th style="width:10%;">{{ trans('app.quantity') }}</th>
                                <th  style="width:10%;">{{ trans('app.price') }}</th>
                                <th style="width:15%;">{{ trans('app.total_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loan->transaction->sell_lines as $key => $item)
                                <tr>
                                    <td class="text-center">
                                        {{ $key+1 }}
                                    </td>
                                    <td>
                                        {{ $item->product->name.(empty($item->variantion->name)||$item->variantion->name=='DUMMY' ? '' : '-'.$item->variantion->name.'-'.$item->variantion->sub_sku) }} <b>IME:</b>
                                        @foreach ($item->transaction->transaction_ime as $ime)
                                            @if (!$loop->first)
                                                ,
                                            @endif
                                            {{ $ime->ime->code }}
                                        @endforeach
                                        <br>
                                    </td>
                                    <td  class="text-center">
                                        {{ $item->quantity }}
                                    </td>
                                    <td  class="text-center">
                                        $ {{ decimalNumber($item->unit_price,true) }}
                                    </td>
                                    <td  class="text-center">
                                        $ {{ decimalNumber($item->unit_price *$item->quantity,true) }}
                                    </td>
                                </tr>

                            @endforeach
                            <tr>
                                <td rowspan="3" colspan="2" class="p-5">
                                    {{ trans('app.duration') }} : {{ $loan->installment . ' ' . trans('app.month') }} <br>
                                    Depreciation amount  {{ __("ភាគរយបង់មុន") }} : {{ decimalNumber($depreciation_amount, true). ' %' }}
                                </td>
                                <td colspan="2">Loan Amount {{ trans('app.product_price') }}</td>
                                <td> $ {{ decimalNumber($loan->loan_amount, true) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">Depreciation amount {{ trans('app.depreciation_amount') }}</td>
                                <td> $ {{ decimalNumber($loan->depreciation_amount, true) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">Down payment amount {{ trans('app.loan_amount') }}</td>
                                <td> $ {{ decimalNumber($loan->down_payment_amount, true) }}</td>
                            </tr>

                        </tbody>

                    </table>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <h4 class="text-center">
                        {{ trans('app.payment_schedule') }}
                        </h4>
                        <thead>
                            @php
        
                                $finalTotal=0;
                            @endphp
                            {{-- @foreach ($data->DepreciationAmount as $DepreciationAmount)
                            @php

                                $finalTotal+= decimalNumber($DepreciationAmount);

                            @endphp
                            @endforeach --}}
                            <tr  style="background-color: #b4f4ff">
                                <th style="width:7%;">{{ trans('app.no_sign') }}</th>
                                <th style="width:30%;">{{ trans('app.payment_date') }}</th>
                                <th style="width:10%;">{{ trans('app.amount') }}</th>
                                <th  style="width:10%;">{{ trans('app.payment_method') }}</th>
                                <th  style="width:10%;">{{ trans('app.client_code') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $data)
                                <tr>
                                    <td class="text-center">
                                       {{ $loop->iteration }}
                                    </td>
                                    <td class="text-center">
                                           {{ $data->invoice->id }}
                                    </td>
                                    <td  class="text-center">
                                        $ {{decimalNumber ( $data->DepreciationAmount) }}
                                        @php
                                             $finalTotal+= decimalNumber($data->DepreciationAmount);
                                        @endphp
                                    </td>
                                    <td  class="text-center">
                                         {{ $data->payment_method }}
                                    </td>
                                     <td  class="text-center">
                                        {{ $data->loan->client_code }}
                                    </td>
                                </tr>
                                    
                            @endforeach
                            <tr  style="background-color:#acffbf">
                                <td colspan="2" class=" text-center ">{{ trans('app.balance') }}</td>
                                <td colspan="2" class="text-center"> $ {{ decimalNumber($finalTotal) }}</td>
                                 
                            </tr>
                           
                        </tbody>
                      
                    </table>
                </div>
                <div class="table-responsive">
                    <h4 class="text-center">
                        {{ trans('app.payment_schedule') }}
                    </h4>
                    <table id="table-schedule" class="table table-bordered">
                        {{--<caption class="text-center">{{ trans('app.payment_schedule') }}</caption>--}}
                        <thead>
                            <tr  style="background-color: #b4f4ff">
                                <th class="bg-header">{{ trans('app.no_sign') }}</th>
                                <th class="bg-header">{{ trans('app.payment_date') }}</th>
                                @if ($loan->schedule_type == PaymentScheduleType::FLAT_INTEREST)
                                <th class="bg-header">{{ trans('app.payment_amount') }}</th>
                                @else
                                <th class="bg-header">{{ trans('app.principal') }}</th>
                                 <th class="bg-header">{{ trans('app.interest') }}</th>
                                <th class="bg-header">{{ trans('app.total') }}</th>
                                @endif
                                <th class="bg-header">{{ trans('app.outstanding') }}</th>
                                <th class="bg-header">{{ trans('app.paid_date') }}</th>
                                <th class="bg-header">{{ trans('app.paid_amount') }}</th>
                                <th class="bg-header">{{ trans('app.signature') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalPrincipal=0;
                                $totalInterest=0;
                                $finalTotal=0;
                            @endphp
                            @foreach ($loan->schedules as $schedule)
                            @php
                                $decimalNumber = ($schedule->interest == 0 ? 2 : 0);
                                $totalPrincipal += decimalNumber($schedule->principal, $decimalNumber);
                                $totalInterest+= decimalNumber($schedule->interest, $decimalNumber);
                                $finalTotal+= decimalNumber($schedule->total, $decimalNumber);

                            @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ displayDate($schedule->payment_date) }}</td>
                                    @if ($loan->schedule_type == PaymentScheduleType::FLAT_INTEREST)

                                    <td>{{ ($currencySign ?? '') . decimalNumber($schedule->principal, $decimalNumber) }}</td>
                                    @else

                                    <td >{{ ($currencySign ?? '') . decimalNumber($schedule->principal, $decimalNumber) }}</td>
                                     <td>{{ ($currencySign ?? '') . decimalNumber($schedule->interest, $decimalNumber) }}</td>
                                    <td class="bg-total" style="background-color: #ffe69b"><b>{{ ($currencySign ?? '') . decimalNumber($schedule->total, $decimalNumber) }}</b></td>
                                    @endif
                                    <td>$ {{ decimalNumber($schedule->outstanding, true) }}</td>
                                    <td>{{ displayDate($schedule->paid_date) }}</td>
                                    <td >{{ $schedule->paid_total > 0 ? '$ ' . decimalNumber($schedule->paid_total, true) : '' }}
                                    </td>
                                    <td></td>
                                </tr>
                            @endforeach
                            @for ($i = 1; $i <12 - $loan->schedules->count(); $i++)
                                <tr>
                                    <td class="text-center">{{ $loan->schedules->count() + $i +1 }}</td>
                                    <td> </td>
                                    <td> </td>
                                     <td> </td>
                                    <td class="bg-total" style="background-color: #ffe69b"> </td>
                                    <td > </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                            @endfor
                            <tr class="bg-footer" style="background-color:#acffbf">
                                <td class="text-right bg-footer" colspan="2" ><b>សរុប</b></td>
                                <td class="bg-footer"><b>$ {{  decimalNumber($totalPrincipal, $decimalNumber) }}</b></td>
                                 <td class="bg-footer"><b>$ {{ decimalNumber($totalInterest, $decimalNumber)  }}</b></td>
                                <td class="bg-footer"><b>$ {{ decimalNumber($finalTotal, $decimalNumber)  }}</b> </td>
                                <td class="bg-footer"> </td>
                                <td class="bg-footer"> </td>
                                <td class="bg-footer"> </td>
                                <td class="bg-footer"> </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-6 col-sm-6">
                        <img class="img-fluid w-100" src="{{ asset($branch->logo_2) }}" alt="{{ trans('app.missing_image') }}">
                        <div class="contract-text">
                            {!! $loan->branch->contract_text !!}
                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        @if (!empty($loan->client->profile_photo))
                        <img class="img-fluid w-100" src="{{ asset($loan->client->profile_photo) }}">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
