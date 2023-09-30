
{{-- Branch --}}
@if(empty(auth()->user()->staff))
  <div class="col-sm-6 col-lg-3 pl-1 pr-0">
    <select name="branch" id="branch" class="form-control select2">
      <option value="">{{ trans('app.branch') }}</option>
      @foreach (allBranches() as $branch)
        <option value="{{ $branch->id }}" {{ request('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->location }}</option>
      @endforeach
    </select>
  </div>

  {{-- @if (isAdmin()) --}}
    {{-- Agent --}}
    <div class="col-sm-6 col-lg-2 pl-1 pr-0">
      <select name="agent" id="agent" class="form-control select2">
        <option value="">{{ trans('app.agent') }}</option>
        @foreach ($agents as $agent)
          <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>
            {{ $agent->name }}
          </option>
        @endforeach
      </select>
    </div>

  {{-- @endif --}}
@endif
 {{-- End date --}}
 
<div class="form-group col-sm-3 col-lg-2">
  {{-- <label for="start_date" class="control-label">{{ trans('app.start_date') }}</label> --}}
  <input type="text" name="date" id="date" class="form-control date-picker" readonly placeholder="{{ trans('app.date_placeholder') }}" value="{{ request('date') ? displayDate(request('date')) : '' }}">
</div>

{{-- Text search --}}
<div class="col-lg-3 pl-1">
  @include('partial.search-input-group')
</div>
