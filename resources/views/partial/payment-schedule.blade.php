@extends('layouts/contract-invoice')
@section('title', trans('app.contract'))
@section('content')
    <div class="row">
        <div class="col-md-12">
            <table style="margin-right: 20px;">
                <tr>
                    <td>លេខគណនី</td>
                    <td>:</td>
                    <td>(LID) {{ $loan->account_number }}</td>
                    <th rowspan="5" style="position: relative; left: 5%;">
                        ឈ្មោះ​ និងលេខទូរស័ព្ទ<br>
                        <span style="padding-right:7px;">ផ្ទាល់ខ្លួន</span>:
                        {{ $loan->client->first_phone }}
                        @isset ($loan->client->second_phone)) / {{ $loan->client->second_phone }} @endisset
                        <br>
                        <span style="padding-right:10px;">អ្នកធានា</span>: 
                        {{ $loan->client->sponsor_name }}
                        {{ $loan->client->sponsor_phone ? '(' . $loan->client->sponsor_phone . ')' : trans('app.none') }}
                        <br>
                        <span style="padding-right:10px;">គ្រួសារ​​</span>: 
                        {{ $loan->client->family_name }}
                        {{ $loan->client->family_phone ? '(' . $loan->client->family_phone . ')' : trans('app.none') }}
                        <br>
                        <span style="padding-right:10px;"> មិត្តភ័ក្ភ</span>:
                        {{ $loan->client->friend_name }}
                        {{ $loan->client->friend_phone ? '(' . $loan->client->friend_phone . ')' : trans('app.none') }}
                        <br>
                        <span style="padding-right:10px;"> បងប្អូន</span>:
                        {{ $loan->client->relative_name }}
                        {{ $loan->client->relative_phone ? '(' . $loan->client->relative_phone . ')' : trans('app.none') }}<br>
                    </th>
                </tr>
                <tr>
                    <td>ឈ្មោះ</td>
                    <td>:</td>
                    <td>{{ $loan->client->name }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>លេខអត្តសញ្ញាណប័ណ្ណ</td>
                    <td>:</td>
                    <td>{{ $loan->client->id_card_number ?? trans('app.none') }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td>កន្លែងធ្វើការងារ</td>
                    <td>:</td>
                    <td>{{ $loan->client->company_name ?? trans('app.n/a') }}</td>
                </tr>
                <tr>
                    <td>ភ្នាក់ងារ</td>
                    <td>:</td>
                    <td>{{ ($loan->staff->name) }}</td>
                </tr>
                <tr>
                    <td>អាស័យដ្ឋាន</td>
                    <td>:</td>
                    <td>{{ $loan->client->address ?? trans('app.n/a') }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-12 render"​>
            <br>
            @php $loanStartDate = strtotime($loan->loan_start_date ?? $loan->created_at); @endphp
            <span>បានយល់ព្រមបង់រំលស់ទំនិញពីហាងលក់ទូរស័ព្ទដៃខូវ ឡៃភឿន ១៦៨ នៅ ថ្ងៃទី 
                {{ date('d', $loanStartDate) }} ខែ {{ khmerMonths(date('m', $loanStartDate)) }} ឆ្នាំ {{ date('Y', $loanStartDate) }}
            </span>
            <table style="width:80%; margin:10px auto;">​
                <tr>
                    <th>មុខ​ទំនិញ</th>
                    <th>ចំនួន</th>
                    <th>IME</th>
                    <th>តម្លៃ</th>
                    <th>បង់ដើម</th>
                    <th>នៅសល់ </th>
                </tr>
                <tr>
                    <td>{{ $loan->product->name }}</td>
                    <td>1</td>
                    <td>{{ $loan->product_ime }}</td>
                    <td>{{ decimalNumber($loan->product->price) }}</td>
                    <td>{{ decimalNumber($loan->depreciation_amount) }}</td>
                    <td>{{ decimalNumber($loan->loan_amount) }}</td>
                </tr>
            </table>
        </div>
        <br>
        <div class="col-md-12"​>
            <table>
                <tr>
                    <td width="2%" style="font-size: 14px; font-weight: 600; vertical-align: top;">ចំណាំ៖</td>
                    <td>
                        <ul>
                            <li>ខ្ញុំត្រូវទទួលខុសត្រូវចំពោះការបង់ប្រាក់ឲ្យបានទៀងទាត់ ករណីយឺតយាវ ខ្ញុំបាទយល់ព្រមឲ្យហាង ខូវ ឡៃភឿន ១៦៨
                                <br><b>ផាកពិន័យ {{ number_format(2000) }} រៀលក្នុងមួយថ្ងៃ។</b></li>
                            <li>ខ្ញុំយល់ព្រម​ទទួល​ខុសត្រូវ​ចំពោះ​មុខច្បាប់ក្នុង​ករណី​គេច​វេស​មិន​ព្រម​បង់ប្រាក់ឲ្យ​ហាងខូវ ឡៃភឿន ១៦៨។</li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row render">
        <div class="col-md-12">
            <h3 class="services">តារាងបង់ប្រាក់ (1 ដុល្លា = 4100 រៀល)</h3>
            <table>
                <thead>
                <tr style="background: #EEE;">
                    <th style="width:5%;">ល.រ</th>
                    <th style="width:25%;">កាលបរិច្ឆេទ</th>
                    <th style="width:20%;">ប្រាក់​ត្រូវ​បង់​សរុបជាដុល្លារ</th>
                    <th style="width:20%;">ប្រាក់​ត្រូវ​បង់​សរុប​ជារៀល</th>
                    <th style="width:10%;">ប្រាក់ដើម</th>
                    <th style="width:10%;">ការប្រាក់</th>
                    <th style="width:10%;">សមតុល្យប្រាក់ដើម</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($loan->schedules as $schedule)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ 'ថ្ងៃ ' . khmerDay($schedule->payment_date) . ' ' .displayDate($schedule->payment_date) }}</td>
                        <td style="font-weight: 800;">{{ decimalNumber($schedule->total) }}</td>
                        <td style="font-weight: 800;">{{ number_format($schedule->total * 4100) }} ៛</td>
                        <td>{{ decimalNumber($schedule->principal) }}</td>
                        <td style="font-size: 9px;">{{ decimalNumber($schedule->interest) }}</td>
                        <td>{{ decimalNumber($schedule->outstanding) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <br>
        </div>
        <div class="col-md-12 text-center">
            <img src="{{ asset('images/wing.jpg') }}" width="50">&nbsp;
            <img src="{{ asset('images/truemoney.jpg') }}" width="50">
            <b>បងប្រាក់តាមរយៈ​លេខទូរស៍ព្ទ : 09​9 99 3006 / 010 99 3006 / 070 99 3006</b>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-xs-offset-1 col-xs-4">
            អ្នកខ្ចី - {{ $loan->client->name }}
        </div>
        <div class="col-xs-offset-1 col-xs-2">
            អ្នកធានា
        </div>
        <div class="col-xs-4">
            ថ្ងៃទី {{ date('d') }} ខែ {{ khmerMonths(date('m')) }} ឆ្នាំ {{ date('Y') }}
        </div>
    </div>
@endsection
