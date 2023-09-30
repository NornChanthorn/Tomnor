@extends('layouts/backend')

@section('title', trans('app.loan'))

@section('content')
  <main class="app-content">
    <div class="tile">
      <h3 class="page-heading">{{ trans('app.loan_report') . ' - ' . reportLoanStatuses($status) }}</h3>

      <form method="get" action="">
        <div class="card">
          <div class="card-header">
            <div class="row">
              @if(isAdmin() || empty(auth()->user()->staff))
                <div class="col-sm-6 col-md-4 form-group">
                  <label for="branch" class="control-label">{{ trans('app.branch') }}</label>
                  <select name="branch" id="branch" class="form-control select2">
                    <option value="">{{ trans('app.all_branches') }}</option>
                    @foreach ($branches as $branch)
                      <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->location }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-sm-6 col-md-4 form-group">
                  <label for="agent" class="control-label">{{ trans('app.agent') }}</label>
                  <select name="agent" class="form-control select2">
                    <option value="">{{ trans('app.agent') }}</option>
                    @foreach ($agents as $agent)
                      <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>
                        {{ $agent->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              @endif
              <div class="col-sm-6 col-md-4">
                <label for="branch" class="control-label">{{ trans('app.search') }}</label>
                <input type="text" name="q" class="form-control" value="{{ request('q') ?? '' }}" placeholder="{{ __('app.search-client-code') }}">
              </div>
              <div class="col-lg-12 text-right">
                @include('partial.button-search', ['class' => 'btn-lg'])
              </div>
            </div>
          </div>
        </div>
      </form>
      <br>

      <div class="row">
        <div class="col-sm-6 col-lg-3">
          <a href="{{ route('report.loan', 'all') }}">
            <div class="widget-small primary coloured-icon">
              <i class="icon fa fa-money fa-3x"></i>
              <div class="info">
                <h6>{{ trans('app.loan_list') }}</h6>
                <b>{{ $loanCount }}</b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="{{ route('report.loan', ReportLoanStatus::PENDING) }}">
            <div class="widget-small warning coloured-icon">
              <i class="icon fa fa-money fa-3x"></i>
              <div class="info">
                <h6>{{ trans('app.pending_loan') }}</h6>
                <b>{{ $pendingLoanCount }}</b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="{{ route('report.loan', ReportLoanStatus::ACTIVE) }}">
            <div class="widget-small primary coloured-icon">
              <i class="icon fa fa-money fa-3x"></i>
              <div class="info">
                <h6>{{ trans('app.active_loan') }}</h6>
                <b>{{ $activeLoanCount }}</b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="{{ route('report.overdue_loan') }}">
            <div class="widget-small warning coloured-icon">
              <i class="icon fa fa-clock-o fa-3x"></i>
              <div class="info">
                <h6>{{ trans('app.overdue_loan') }}</h6>
                <b>{{ $overdueLoanCount }}</b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="{{ route('report.loan', ReportLoanStatus::PAID) }}">
            <div class="widget-small success coloured-icon">
              <i class="icon fa fa-money fa-3x"></i>
              <div class="info">
                <h6>{{ trans('app.paid_loan') }}</h6>
                <b>{{ $paidLoanCount }}</b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="{{ route('report.loan', ReportLoanStatus::REJECTED) }}">
            <div class="widget-small danger coloured-icon">
              <i class="icon fa fa-ban fa-3x"></i>
              <div class="info">
                <h6>{{ trans('app.rejected_loan') }}</h6>
                <b>{{ $rejectedLoanCount }}</b>
              </div>
            </div>
          </a>
        </div>
        <div class="col-sm-6 col-lg-3">
          <a href="{{ route('report.client_registration') }}">
            <div class="widget-small success coloured-icon">
              <i class="icon fa fa-address-book fa-3x"></i>
              <div class="info">
                <h6>{{ trans('app.client') }}</h6>
                <b>{{ $clientCount }}</b>
              </div>
            </div>
          </a>
        </div>
      </div>
      <hr>

      @include('partial.flash-message')
      @include('partial.item-count-label')
      <div class="table-responsive resize-w">
        <table class="table table-hover table-bordered">
          @php
            $isRejectedLoan = ($status == ReportLoanStatus::REJECTED);
            $statusTitle = reportLoanStatuses($status);

            switch ($status) {
              case ReportLoanStatus::PENDING:
                $labelClass = 'badge badge-warning';
                break;
              case ReportLoanStatus::ACTIVE:
                $labelClass = 'badge badge-info';
                break;
              case ReportLoanStatus::PAID:
                $labelClass = 'badge badge-success';
                break;
              case ReportLoanStatus::REJECTED:
                $labelClass = 'badge badge-danger';
                break;
              default:
                $labelClass = '';
                break;
            }
          @endphp

          <thead>
            <tr>
              <th>{{ trans('app.no_sign') }}</th>
              <th>{{ trans('app.client') }}</th>
              <th>{{ trans('app.profile_photo') }}</th>

              <th>{{ trans('app.first_phone') }}</th>
              <th style="width: 10%">{{ trans('app.occupation_1') }}</th>
              <th>{{ trans('app.province') }}</th>
              <th>@sortablelink('account_number', trans('app.loan_code'))</th>
              <th>{{ trans('app.branch') }}</th>
              
              @if (isAdmin())
                <th>{{ trans('app.agent') }}</th>
              @endif

              <th>{{ trans('app.product') }}</th>
              <th>
                {{ __('app.loan_amount') }}
              </th>
              <th>{{ trans('app.request_date') }}</th>
              <th>{{ trans('app.status') }}</th>

              @if (isAdmin() && $isRejectedLoan)
                <th>{{ trans('app.action') }}</th>
              @endif
            </tr>
          </thead>
          <tbody>
            @foreach ($filteredLoans as $loan)
              <tr>
                <td>{{ $offset++ }}</td>
                <td>@include('partial.client-detail-link', ['client' => $loan->client])</td>
                <td>@include('partial.client-profile-photo', ['client' => $loan->client])</td>


                <td>{{ @$loan->client->first_phone }}{{ @$loan->client->second_phone ? ', '.@$loan->client->second_phone : '' }}</td>
                <td>{{ @$loan->client->occupation_1 }}</td>
                <td>{{ @$loan->client->province->khmer_name ?? @$loan->client->province->name }}</td>
                <td>
                  @if ($isRejectedLoan)
                    {{ $loan->client_code }}
                  @else
                    @include('partial.loan-detail-link')
                  @endif
                </td>
                <td>{{ @$loan->branch->location ?? trans('app.n/a') }}</td>
                

                @if (isAdmin())
                  <td>
                    @if (@$loan->staff)
                      	@include('partial.staff-detail-link', ['staff' => $loan->staff])   
                    @endif
              
                  </td>
                @endif

                <td>
                  @foreach ($loan->productDetails as $item)
                    @include('partial.product-detail-link', ['product' => $item->product, 'variantion' => $item->variantion->name])<br>
                  @endforeach
                </td>
                <td>
                  {{ num_f($loan->loan_amount) }}
                </td>
                <td>
                  {{ displayDate($loan->created_at) }}
                </td>
                <td><label class="{{ $labelClass }}">{{ $statusTitle }}</label></td>

                @if (isAdmin() && $isRejectedLoan)
                  <td class="text-center">
                    {{-- Revert rejected loan to pending --}}
                    <button type="button" class="btn btn-primary btn-sm btn-revert mb-1" data-redirect-url="{{ route('loan.show', $loan->id) }}" data-revert-url="{{ route('loan.change_status', [$loan->id, LoanStatus::PENDING]) }}">
                      {{ trans('app.revert') }}
                    </button>

                    {{-- Delete rejected loan --}}
                    <button type="button" class="btn btn-danger btn-sm btn-delete mb-1" data-url="{{ route('loan.destroy', $loan->id) }}">
                      {{ trans('app.delete') }}
                    </button>
                  </td>
                @endif
              </tr>
            @endforeach
          </tbody>
        </table>
        {!! $filteredLoans->appends(Request::except('page'))->render() !!}
      </div>
    </div>
  </main>
@endsection

@section('js')
  <script src="{{ asset('js/select2.min.js') }}"></script>
  <script src="{{ asset('js/select-box.js') }}"></script>
  <script src="{{ asset('js/rejected-loan.js') }}"></script>
  <script src="{{ asset('js/delete-item.js') }}"></script>
@endsection