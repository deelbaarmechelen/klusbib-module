@extends('layouts/edit-form', [
    'createText' => trans('klusbib::admin/reservations/form.create') ,
    'updateText' => trans('klusbib::admin/reservations/form.update'),
    'helpTitle' => trans('klusbib::admin/reservations/general.about_reservations_title'),
    'helpText' => trans('klusbib::admin/reservations/general.about_reservations_text'),
    'formAction' => ($item) ? route('klusbib.reservations.update', ['reservation' => $item->id]) : route('klusbib.reservations.store'),
])

{{-- Page content --}}
@section('inputFields')
{{--@include ('partials.forms.edit.name', ['translated_name' => trans('klusbib::admin/reservations/form.name')])--}}

{{--TODO: translate asset_id to tool_id and vice-versa--}}
{{--TODO: translate assigned_user_id to user_id and vice-versa--}}
{{--@include ('partials.forms.edit.asset-select', ['translated_name' => trans('general.select_asset'), 'fieldname' => 'asset_id', 'required'=>'true'])--}}
{{--@include ('partials.forms.edit.user-select', ['translated_name' => trans('general.user'), 'fieldname' => 'user', 'required'=>'true'])--}}

<!-- Asset-->
<div class="form-group {{ $errors->has('asset') ? ' has-error' : '' }}">
    <label for="asset" class="col-md-3 control-label">{{ trans('klusbib::admin/reservations/form.asset') }}</label>
    <div class="col-md-7{{  (\App\Helpers\Helper::checkIfRequired($item, 'asset')) ? ' required' : '' }}">
        <textarea class="form-control" type="text" name="asset" id="asset">{{ Input::old('asset', $item->tool_id) }}</textarea>
        {!! $errors->first('asset', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>

<!-- User-->
<div class="form-group {{ $errors->has('user_id') ? ' has-error' : '' }}">
    <label for="user_id" class="col-md-3 control-label">{{ trans('klusbib::admin/reservations/form.user_id') }}</label>
    <div class="col-md-7{{  (\App\Helpers\Helper::checkIfRequired($item, 'user_id')) ? ' required' : '' }}">
        <textarea class="form-control" type="text" name="user_id" id="user_id">{{ Input::old('user_id', $item->user_id) }}</textarea>
        {!! $errors->first('user_id', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>

<!-- State-->
{{--<div class="form-group {{ $errors->has('state') ? ' has-error' : '' }}">--}}
    {{--<label for="state" class="col-md-3 control-label">{{ trans('klusbib::admin/reservations/form.state') }}</label>--}}
    {{--<div class="col-md-7{{  (\App\Helpers\Helper::checkIfRequired($item, 'state')) ? ' required' : '' }}">--}}
        {{--<textarea class="form-control" type="text" name="state" id="state">{{ Input::old('state', $item->state) }}</textarea>--}}
        {{--{!! $errors->first('state', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}--}}
    {{--</div>--}}
{{--</div>--}}
<div class="form-group {{ $errors->has('state') ? ' has-error' : '' }}">
    <label for="state" class="col-md-3 control-label">{{ trans('klusbib::admin/reservations/form.state') }}</label>
    <div class="col-md-7{{  (\App\Helpers\Helper::checkIfRequired($item, 'state')) ? ' required' : '' }}">
        <select class="form-control"  name="state" id="state_select">{{ Input::old('state', $item->state) }}
            <option value="REQUESTED" {{ (Input::old("state", $item->state) == "REQUESTED" ? "selected":"") }}>Aanvraag</option>
            <option value="CONFIRMED" {{ (Input::old("state", $item->state) == "CONFIRMED" ? "selected":"") }}>Bevestigd</option>
            <option value="CANCELLED" {{ (Input::old("state", $item->state) == "CANCELLED" ? "selected":"") }}>Geannuleerd</option>
        </select>
        {!! $errors->first('state', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>

<!-- Start Date -->
<div class="form-group {{ $errors->has('start_date') ? ' has-error' : '' }}">
    <label for="start_date" class="col-md-3 control-label">{{ trans('klusbib::admin/reservations/form.start_date') }}</label>

    <div class="input-group col-md-3">
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true">
            <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="start_date"
                   id="start_date" value="{{ Input::old('start_date', $item->startsAt) }}">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
        {!! $errors->first('start_date', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>

</div>

<!-- End Date -->
<div class="form-group {{ $errors->has('end_date') ? ' has-error' : '' }}">
    <label for="end_date" class="col-md-3 control-label">{{ trans('klusbib::admin/reservations/form.end_date') }}</label>

    <div class="input-group col-md-3">
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true">
            <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="end_date"
                   id="end_date" value="{{ Input::old('end_date', $item->endsAt) }}">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
        {!! $errors->first('end_date', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>

<!-- Notes -->
<div class="form-group {{ $errors->has('notes') ? ' has-error' : '' }}">
    <label for="notes" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
    <div class="col-md-7 col-sm-12">
        <textarea class="col-md-6 form-control" id="notes" name="notes">{{ Input::old('notes', $item->comment) }}</textarea>
        {!! $errors->first('notes', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>
<!-- Cancel reason -->
<div class="form-group {{ $errors->has('cancel_reason') ? ' has-error' : '' }}">
    <label for="cancel_reason" class="col-md-3 control-label">{{ trans('klusbib::admin/reservations/form.cancel_reason') }}</label>
    <div class="col-md-7 col-sm-12">
        <textarea class="col-md-6 form-control" id="cancel_reason" name="cancel_reason">{{ Input::old('cancel_reason', $item->cancel_reason) }}</textarea>
        {!! $errors->first('cancel_reason', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>
@stop
