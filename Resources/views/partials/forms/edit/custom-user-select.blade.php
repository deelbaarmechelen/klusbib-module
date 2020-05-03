{{--Extra docs:--}}
{{--https://api.jquery.com/each/--}}
{{--https://select2.org/--}}
{{--Need to define a custom selectlist api route--}}
<div id="assigned_user" class="form-group{{ $errors->has($fieldname) ? ' has-error' : '' }}"{!!  (isset($style)) ? ' style="'.e($style).'"' : ''  !!}>

    {{ Form::label($fieldname, $translated_name, array('class' => 'col-md-3 control-label')) }}

    <div class="col-md-7{{  ((isset($required)) && ($required=='true')) ? ' required' : '' }}">
        <select class="js-data-ajax" data-endpoint="klusbib/users" data-placeholder="{{ trans('general.select_user') }}" name="{{ $fieldname }}" style="width: 100%" id="assigned_user_select">
            @if ($employee_num = Input::old($fieldname, (isset($item)) ? $item->{$fieldname} : ''))
                <option value="{{ $employee_num }}" selected="selected">
                    {{ (\App\Models\User::where('employee_num', '=', $employee_num)->first()) ? \App\Models\User::where('employee_num', '=', $employee_num)->first()->present()->fullName : '' }}
                </option>
            @else
                <option value="">{{ trans('general.select_user') }}</option>
            @endif
        </select>
    </div>

    {!! $errors->first($fieldname, '<div class="col-md-8 col-md-offset-3"><span class="alert-msg"><i class="fa fa-times"></i> :message</span></div>') !!}

</div>