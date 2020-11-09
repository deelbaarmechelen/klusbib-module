@extends('layouts/edit-form', [
    'createText' => trans('klusbib::admin/deliveries/form.addItem') ,
    'updateText' => trans('klusbib::admin/deliveries/form.updateItem'),
    'helpTitle' => trans('klusbib::admin/deliveries/general.about_delivery_items_title'),
    'helpText' => trans('klusbib::admin/deliveries/general.about_delivery_items_text'),
    'formAction' => ($item) ? route('klusbib.deliveries.items.update', ['delivery' => $delivery->id, 'item' => $item->id])
                            : route('klusbib.deliveries.items.add', ['delivery' => $delivery->id]),
])

{{-- Page content --}}
@section('inputFields')
{{--@include ('partials.forms.edit.name', ['translated_name' => trans('klusbib::admin/deliveries/form.name')])--}}

@include ('partials.forms.edit.asset-select', ['translated_name' => trans('general.select_asset'), 'fieldname' => 'tool_id', 'required'=>'true'])


<!-- Notes -->
{{--<div class="form-group {{ $errors->has('notes') ? ' has-error' : '' }}">--}}
    {{--<label for="notes" class="col-md-3 control-label">{{ trans('klusbib::admin/deliveries/general.notes') }}</label>--}}
    {{--<div class="col-md-7 col-sm-12">--}}
        {{--<textarea class="col-md-6 form-control" id="notes" name="notes">{{ Input::old('notes', $item->comment) }}</textarea>--}}
        {{--{!! $errors->first('notes', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}--}}
    {{--</div>--}}
{{--</div>--}}
@stop
