<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo e($generalSetting->site_title . ' - ' . trans('app.contract')); ?></title>
    <meta charset="utf-8">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo e(asset($generalSetting->site_logo)); ?>" sizes="32x32">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Battambang|Moul">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
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

        @media  print {
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
        @page  {
            size: 21cm 29.7cm;
        }

    </style>
</head>

<body>
    <?php
        $branch = $loan->branch;
        $client = $loan->client;
        $depreciation_amount = ($loan->depreciation_amount/$loan->loan_amount) * 100;
    ?>

    <div class="content-wrapper">
        <div class="row">
            <div class="content-footer">
                <div class="bottom-footer mt-4 row">
                    <div class="col-xs-1">
                        <img src="<?php echo e(asset($branch->logo)); ?>" width="100%" alt="" class="mt-4">
                    </div>
                    <div class="col-xs-11 row">
                        <div class="col-xs-12">
                                <h5 class="branch-name">
                                    <b> <?php echo e($loan->branch->location); ?></b> <br>
                                    <?php echo e($loan->branch->location_en); ?>

                                </h5>
                        </div>
                        <div class="col-xs-12 row">
                            <div class="col-xs-6">
                                <p><?php echo e('Tel: ' . $loan->branch->phone_1
                                    . (isset($loan->branch->phone_2) ? '/' . $loan->branch->phone_2 : '')
                                    . (isset($loan->branch->phone_3) ? '/' . $loan->branch->phone_3 : '')
                                    . (isset($loan->branch->phone_4) ? '/' . $loan->branch->phone_4 : '')); ?></p>
                            </div>
                            <div class="col-xs-6 ">
                                <p class="float-right">
                                    Email: <?php echo e($loan->note); ?>

                                </p>
                            </div>
                        </div>

                    </div>

                </div>
                <hr class="footer-ruler">
            </div>
            <div class="content-body">
                <div class="row">
                    <div class="col">
                        <table>
                            <tbody>
                                <tr>
                                    <td><?php echo e(trans('app.loan_code')); ?></td>
                                    
                                    
                                    <td>:</td>
                                    <td><?php echo e($loan->client_code); ?></td>

                                </tr>
                                
                                <tr>
                                    <td><?php echo e(trans('app.client_code')); ?></td>
                                    <td>:</td>
                                    <td><?php echo $loan->account_number . '/<b>' .str_pad($loan->client_id, 6, '0',
                                            STR_PAD_LEFT) . '</b>'; ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo e(trans('app.start_date')); ?></td>
                                    <td>:</td>
                                    <td><?php echo e(displayDate($loan->loan_start_date)); ?></td>
                                </tr>
                                <tr>
                                    <td><?php echo e(trans('app.end_date')); ?></td>
                                    <td>:</td>
                                    <td> <?php echo e(displayDate($loan->schedules[$loan->installment - 1]->payment_date)); ?>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <table>
                            <tbody>
                                <tr>
                                    <td style="width: 30%"><?php echo e(trans('app.client_name')); ?></td>
                                    <td>:</td>
                                    <td> <span class="moul-font"><?php echo e($client->name); ?></span>
                                        
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 30%"><?php echo e(trans('app.phone_number')); ?></td>
                                    <td>:</td>
                                    <td>
                                        <?php echo e($client->first_phone); ?>

                                        <?php echo e(isset($client->second_phone) ? ' / ' . $client->second_phone : ''); ?>

                                    </td>
                                </tr>

                                <tr>
                                    <td style="vertical-align: top; width: 30%"><?php echo e(trans('app.address')); ?></td>
                                    <td style="vertical-align: top">:</td>
                                    <td style="word-wrap: break-word;">
                                        <?php if(isset($client->address)): ?>
                                            <?php echo e($client->address); ?>

                                        <?php elseif(isset($client->province_id) || isset($client->district_id) ||
                                            isset($client->commune_id)): ?>
                                            <?php echo e(isset($client->commune->name) ? $client->commune->name . ', ' : ''); ?>

                                            <?php echo e(isset($client->district->name) ? $client->district->name . ', ' : ''); ?>

                                            <?php echo e(isset($client->province->name) ? $client->province->name : ''); ?>

                                        <?php else: ?>
                                        <?php echo e(trans('app.n/a')); ?>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <table>
                            <tbody>
                                
                        
                                    <tr>
                                        <td style="width: 50%"><?php echo e(trans('app.sponsor_name')); ?></td>
                                        <td>
                                            : <span class="moul-font"><?php echo e($client->sponsor_name ?? trans('app.none')); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%">
                                            <?php echo e(trans('app.id_card_number')); ?> 
                                        </td>
                                        <td>
                                            :
                                            <?php echo e($client->sponsor_id_card ?? trans('app.none')); ?>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 50%"><?php echo e(trans('app.phone_number')); ?></td>
                                        <td>
                                            <?php if(!empty($client->sponsor_phone) || !empty($client->sponsor_phone_2)): ?>
                                            : <?php echo e($client->sponsor_phone); ?>

                                            <?php echo e(!empty($client->sponsor_phone) && !empty($client->sponsor_phone_2) ? ' / ' : ''); ?>

                                            <?php echo e($client->sponsor_phone_2); ?>

                                            <?php else: ?>
                                            <?php echo e(trans('app.none')); ?>

                                            <?php endif; ?>
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
                                <th style="width:7%;"><?php echo e(trans('app.no_sign')); ?></th>
                                <th style="width:50%;"><?php echo e(trans('app.product')); ?></th>
                                <th style="width:10%;"><?php echo e(trans('app.quantity')); ?></th>
                                <th  style="width:10%;"><?php echo e(trans('app.price')); ?></th>
                                <th style="width:15%;"><?php echo e(trans('app.total_amount')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $loan->transaction->sell_lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="text-center">
                                        <?php echo e($key+1); ?>

                                    </td>
                                    <td>
                                        <?php echo e($item->product->name.(empty($item->variantion->name)||$item->variantion->name=='DUMMY' ? '' : '-'.$item->variantion->name.'-'.$item->variantion->sub_sku)); ?> <b>IME:</b>
                                        <?php $__currentLoopData = $item->transaction->transaction_ime; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if(!$loop->first): ?>
                                                ,
                                            <?php endif; ?>
                                            <?php echo e($ime->ime->code); ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <br>
                                    </td>
                                    <td  class="text-center">
                                        <?php echo e($item->quantity); ?>

                                    </td>
                                    <td  class="text-center">
                                        $ <?php echo e(decimalNumber($item->unit_price,true)); ?>

                                    </td>
                                    <td  class="text-center">
                                        $ <?php echo e(decimalNumber($item->unit_price *$item->quantity,true)); ?>

                                    </td>
                                </tr>
                                    
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td rowspan="3" colspan="2" class="p-5">
                                    <?php echo e(trans('app.duration')); ?> : <?php echo e($loan->installment . ' ' . trans('app.month')); ?> <br>
                                    <?php echo e(__("ភាគរយបង់មុន")); ?> : <?php echo e(decimalNumber($depreciation_amount, true). ' %'); ?>

                                </td>
                                <td colspan="2"><?php echo e(trans('app.product_price')); ?></td>
                                <td> $ <?php echo e(decimalNumber($loan->loan_amount, true)); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php echo e(trans('app.depreciation_amount')); ?></td>
                                <td> $ <?php echo e(decimalNumber($loan->depreciation_amount, true)); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php echo e(trans('app.loan_amount')); ?></td>
                                <td> $ <?php echo e(decimalNumber($loan->down_payment_amount, true)); ?></td>
                            </tr>
                           
                        </tbody>
                      
                    </table>
                </div>
                <div class="table-responsive">
                    <h4 class="text-center">
                        <?php echo e(trans('app.payment_schedule')); ?>

                    </h4>
                    <table id="table-schedule" class="table table-bordered">
                        
                        <thead>
                            <tr  style="background-color: #b4f4ff">
                                <th class="bg-header"><?php echo e(trans('app.no_sign')); ?></th>
                                <th class="bg-header"><?php echo e(trans('app.payment_date')); ?></th>
                                <?php if($loan->schedule_type == PaymentScheduleType::FLAT_INTEREST): ?>
                                <th class="bg-header"><?php echo e(trans('app.payment_amount')); ?></th>
                                <?php else: ?>
                                <th class="bg-header"><?php echo e(trans('app.principal')); ?></th>
                                 <th class="bg-header"><?php echo e(trans('app.interest')); ?></th>
                                <th class="bg-header"><?php echo e(trans('app.total')); ?></th>
                                <?php endif; ?>
                                <th class="bg-header"><?php echo e(trans('app.outstanding')); ?></th>
                                <th class="bg-header"><?php echo e(trans('app.paid_date')); ?></th>
                                <th class="bg-header"><?php echo e(trans('app.paid_amount')); ?></th>
                                <th class="bg-header"><?php echo e(trans('app.signature')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $totalPrincipal=0;
                                $totalInterest=0;
                                $finalTotal=0;
                            ?>
                            <?php $__currentLoopData = $loan->schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $decimalNumber = ($schedule->interest == 0 ? 2 : 0);
                                $totalPrincipal += decimalNumber($schedule->principal, $decimalNumber);
                                $totalInterest+= decimalNumber($schedule->interest, $decimalNumber);
                                $finalTotal+= decimalNumber($schedule->total, $decimalNumber);

                            ?>
                                <tr>
                                    <td class="text-center"><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e(displayDate($schedule->payment_date)); ?></td>
                                    <?php if($loan->schedule_type == PaymentScheduleType::FLAT_INTEREST): ?>

                                    <td><?php echo e(($currencySign ?? '') . decimalNumber($schedule->principal, $decimalNumber)); ?></td>
                                    <?php else: ?>

                                    <td ><?php echo e(($currencySign ?? '') . decimalNumber($schedule->principal, $decimalNumber)); ?></td>
                                     <td><?php echo e(($currencySign ?? '') . decimalNumber($schedule->interest, $decimalNumber)); ?></td>
                                    <td class="bg-total" style="background-color: #ffe69b"><b><?php echo e(($currencySign ?? '') . decimalNumber($schedule->total, $decimalNumber)); ?></b></td>
                                    <?php endif; ?>
                                    <td>$ <?php echo e(decimalNumber($schedule->outstanding, true)); ?></td>
                                    <td><?php echo e(displayDate($schedule->paid_date)); ?></td>
                                    <td ><?php echo e($schedule->paid_total > 0 ? '$ ' . decimalNumber($schedule->paid_total, true) : ''); ?>

                                    </td>
                                    <td></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php for($i = 1; $i <12 - $loan->schedules->count(); $i++): ?>
                                <tr>
                                    <td class="text-center"><?php echo e($loan->schedules->count() + $i +1); ?></td>
                                    <td> </td>
                                    <td> </td>
                                     <td> </td>
                                    <td class="bg-total" style="background-color: #ffe69b"> </td>
                                    <td > </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                            <?php endfor; ?>
                            <tr class="bg-footer" style="background-color:#acffbf">
                                <td class="text-right bg-footer" colspan="2" ><b>សរុប</b></td>
                                <td class="bg-footer"><b>$ <?php echo e(decimalNumber($totalPrincipal, $decimalNumber)); ?></b></td>
                                 <td class="bg-footer"><b>$ <?php echo e(decimalNumber($totalInterest, $decimalNumber)); ?></b></td>
                                <td class="bg-footer"><b>$ <?php echo e(decimalNumber($finalTotal, $decimalNumber)); ?></b> </td>
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
                        <img class="img-fluid w-100" src="<?php echo e(asset($branch->logo_2)); ?>" alt="<?php echo e(trans('app.missing_image')); ?>">
                        <div class="contract-text">
                            <?php echo $loan->branch->contract_text; ?>

                        </div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <?php if(!empty($loan->client->profile_photo)): ?>
                        <img class="img-fluid w-100" src="<?php echo e(asset($loan->client->profile_photo)); ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>

</html>
