@extends('layouts/edit-form', [
    'createText' => trans('klusbib::admin/deliveries/form.create') ,
    'updateText' => trans('klusbib::admin/deliveries/form.update'),
    'helpTitle' => trans('klusbib::admin/deliveries/general.about_deliveries_title'),
    'helpText' => trans('klusbib::admin/deliveries/general.about_deliveries_text'),
    'formAction' => ($item) ? route('klusbib.deliveries.update', ['delivery' => $item->id]) : route('klusbib.deliveries.store'),
])

{{-- Page content --}}
@section('inputFields')
{{--@include ('partials.forms.edit.name', ['translated_name' => trans('klusbib::admin/deliveries/form.name')])--}}

{{--@include ('partials.forms.edit.asset-select', ['translated_name' => trans('general.select_asset'), 'fieldname' => 'tool_id', 'required'=>'true'])--}}
@include ('klusbib::partials.forms.edit.custom-user-select', ['translated_name' => trans('general.user'), 'fieldname' => 'user_id', 'required'=>'true'])

<!-- State-->
<div class="form-group {{ $errors->has('state') ? ' has-error' : '' }}">
    <label for="state" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.state') }}</label>
    <div class="col-md-7{{  (\App\Helpers\Helper::checkIfRequired($item, 'state')) ? ' required' : '' }}">
        <select class="form-control"  name="state" id="state_select">{{ Input::old('state', $item->state) }}
            <option value="REQUESTED" {{ (Input::old("state", $item->state) == "REQUESTED" ? "selected":"") }}>Aanvraag</option>
            <option value="CONFIRMED" {{ (Input::old("state", $item->state) == "CONFIRMED" ? "selected":"") }}>Bevestigd</option>
            <option value="DELIVERED" {{ (Input::old("state", $item->state) == "DELIVERED" ? "selected":"") }}>Geleverd</option>
            <option value="CANCELLED" {{ (Input::old("state", $item->state) == "CANCELLED" ? "selected":"") }}>Geannuleerd</option>
        </select>
        {!! $errors->first('state', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>

<!-- Type -->
<div class="form-group {{ $errors->has('type') ? ' has-error' : '' }}">
    <label for="state" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.type') }}</label>
    <div class="col-md-7{{  (\App\Helpers\Helper::checkIfRequired($item, 'type')) ? ' required' : '' }}">
        <select class="form-control"  name="type" id="type_select">{{ Input::old('type', $item->state) }}
            <option value="PICKUP" {{ (Input::old("type", $item->type) == "PICKUP" ? "selected":"") }}>Ophaling</option>
            <option value="DROPOFF" {{ (Input::old("type", $item->type) == "DROPOFF" ? "selected":"") }}>Levering</option>
        </select>
        {!! $errors->first('type', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>
<!-- Pick Up Date -->
<div class="form-group {{ $errors->has('pick_up_date') ? ' has-error' : '' }}">
    <label for="pick_up_date" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.pick_up_date') }}</label>

    <div class="input-group col-md-3">
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true">
            <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="pick_up_date"
                   id="pick_up_date" value="{{ Input::old('pick_up_date', $item->pick_up_date) }}">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
        {!! $errors->first('pick_up_date', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>

</div>

<!-- Pick Up Address -->
<div class="form-group {{ $errors->has('pick_up_address') ? ' has-error' : '' }}">
    <label for="pick_up_address" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.pick_up_address') }}</label>
    <div class="col-md-4">
        <input class="form-control" type="text" name="pick_up_address" id="pick_up_address" value="{{ Input::old('pick_up_address', $item->pick_up_address) }}" />
        {!! $errors->first('pick_up_address', '<span class="alert-msg">:message</span>') !!}
    </div>
</div>

<!-- Drop Off Date -->
<div class="form-group {{ $errors->has('drop_off_date') ? ' has-error' : '' }}">
    <label for="end_date" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.drop_off_date') }}</label>

    <div class="input-group col-md-3">
        <div class="input-group date" data-provide="datepicker" data-date-format="yyyy-mm-dd"  data-autoclose="true">
            <input type="text" class="form-control" placeholder="{{ trans('general.select_date') }}" name="drop_off_date"
                   id="drop_off_date" value="{{ Input::old('drop_off_date', $item->drop_off_date) }}">
            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
        </div>
        {!! $errors->first('drop_off_date', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>

<!-- Drop Off Address -->
<div class="form-group {{ $errors->has('drop_off_address') ? ' has-error' : '' }}">
    <label for="drop_off_address" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.drop_off_address') }}</label>
    <div class="col-md-4">
        <input class="form-control" type="text" name="drop_off_address" id="drop_off_address" value="{{ Input::old('drop_off_address', $item->drop_off_address) }}" />
        {!! $errors->first('drop_off_address', '<span class="alert-msg">:message</span>') !!}
    </div>
</div>

<!-- Price -->
<div class="form-group {{ $errors->has('price') ? ' has-error' : '' }}">
    <label for="price" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.price') }}</label>
    <div class="col-md-4">
        <input class="form-control" type="text" name="price" id="price" value="{{ Input::old('price', $item->price) }}" />
        {!! $errors->first('price', '<span class="alert-msg">:message</span>') !!}
    </div>
</div>

<!-- Consumers -->
<div class="form-group {{ $errors->has('consumers') ? ' has-error' : '' }}">
    <label for="consumers" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.consumers') }}</label>
    <div class="col-md-7 col-sm-12">
        <textarea class="col-md-6 form-control" id="consumers" name="consumers">{{ Input::old('consumers', $item->consumers) }}</textarea>
        {!! $errors->first('consumers', '<span class="alert-msg">:message</span>') !!}
    </div>
</div>


<!-- Notes -->
<div class="form-group {{ $errors->has('notes') ? ' has-error' : '' }}">
    <label for="notes" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.notes') }}</label>
    <div class="col-md-7 col-sm-12">
        <textarea class="col-md-6 form-control" id="notes" name="notes">{{ Input::old('notes', $item->comment) }}</textarea>
        {!! $errors->first('notes', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
    </div>
</div>
@stop
