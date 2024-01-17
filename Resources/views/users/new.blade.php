@extends('layouts/default')

{{-- Page title --}}
@section('title')
    {{ trans('admin/users/table.createuser') }}
@parent
@stop

@push('custom-scripts')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v1.1.5/dist/alpine.js" defer></script>
@endpush

@section('header_right')
<a href="{{ URL::previous() }}" class="btn btn-primary pull-right">
  {{ trans('general.back') }}</a>
@stop

{{-- Page content --}}
@section('content')


<style>
    .form-horizontal .control-label {
      padding-top: 0px;
    }

    input[type='text'][disabled], input[disabled], textarea[disabled], input[readonly], textarea[readonly], .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
      background-color: white;
      color: #555555;
      cursor:text;
    }
    table.permissions {
      display:flex;
      flex-direction: column;
    }

    .permissions.table > thead, .permissions.table > tbody {
      margin: 15px;
      margin-top: 0px;
    }
    .permissions.table > tbody+tbody {

    }
    .header-row {
      border-bottom: 1px solid #ccc;
    }

    .header-row h3 {
      margin:0px;
    }
    .permissions-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }
    .table > tbody > tr > td.permissions-item {
      padding: 1px;
      padding-left: 8px;
    }

    .header-name {
      cursor: pointer;
    }

    [x-cloak] { display: none; }
</style>

<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <form class="form-horizontal"
          method="post"
          autocomplete="off"
          action="{{ ($item && $item->id) ? route('klusbib.users.update', ['user' => $item->id]) : route('klusbib.users.store') }}"
          id="userForm">
      {{csrf_field()}}

        {{--<!-- Custom Tabs -->--}}
      {{--<div class="nav-tabs-custom">--}}
        {{--<ul class="nav nav-tabs">--}}
          {{--<li class="active"><a href="#tab_1" data-toggle="tab">Information</a></li>--}}
          {{--<li><a href="#tab_2" data-toggle="tab">Permissions</a></li>--}}
        {{--</ul>--}}

        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
            <div class="row">
              <div class="col-md-12">

                <!-- First Name -->
                <div class="form-group {{ $errors->has('firstname') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="firstname">{{ trans('klusbib::admin/users/table.firstname') }}</label>
                  <div class="col-md-8 {{  (\App\Helpers\Helper::checkIfRequired($item, 'firstname')) ? ' required' : '' }}">
                    <input class="form-control" type="text" name="firstname" id="firstname" value="{{ Request::old('firstname', $item->firstname) }}" />
                    {!! $errors->first('firstname', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>

                <!-- Last Name -->
                <div class="form-group {{ $errors->has('lastname') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="lastname">{{ trans('klusbib::admin/users/table.lastname') }} </label>
                  <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'lastname')) ? ' required' : '' }}">
                    <input class="form-control" type="text" name="lastname" id="lastname" value="{{ Request::old('lastname', $item->lastname) }}" />
                    {!! $errors->first('lastname', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>

                <!-- Company -->
                <div class="form-group {{ $errors->has('company') ? 'has-error' : '' }}">
                    <label class="col-md-3 control-label" for="company">{{ trans('klusbib::admin/users/table.company') }} </label>
                    <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'company')) ? ' required' : '' }}">
                        <input class="form-control" type="text" name="company" id="company" value="{{ Request::old('company', $item->company) }}" />
                        {!! $errors->first('company', '<span class="alert-msg">:message</span>') !!}
                    </div>
                </div>

                <!-- Role -->
                <div class="form-group {{ $errors->has('role') ? 'has-error' : '' }}">
                    <label class="col-md-3 control-label" for="role">{{ trans('klusbib::admin/users/table.role') }} </label>
                    <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'role')) ? ' required' : '' }}">
                        <select class="form-control"  name="role" id="role_select">{{ Request::old('role', $item->role) }}
                            <option value="member" {{ ((Request::old("role", $item->role) == "member" || Request::old("role", $item->role) == "") ? "selected":"") }}>Lid</option>
                            <option value="supporter" {{ (Request::old("role", $item->role) == "supporter" ? "selected":"") }}>Steunlid</option>
                            <option value="admin" {{ (Request::old("role", $item->role) == "admin" ? "selected":"") }}>Admin</option>
                        </select>
                        {!! $errors->first('role', '<span class="alert-msg">:message</span>') !!}
                    </div>
                </div>


                {{--<!-- Password -->--}}
                {{--<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">--}}
                  {{--<label class="col-md-3 control-label" for="password">--}}
                    {{--{{ trans('admin/users/table.password') }}--}}
                  {{--</label>--}}
                  {{--<div class="col-md-5{{  (\App\Helpers\Helper::checkIfRequired($item, 'password')) ? ' required' : '' }}">--}}
                    {{--@if ($item->ldap_import!='1')--}}
                      {{--<input--}}
                        {{--type="password"--}}
                        {{--name="password"--}}
                        {{--class="form-control"--}}
                        {{--id="password"--}}
                        {{--value=""--}}
                        {{--autocomplete="off"--}}
                        {{--readonly--}}
                        {{--onfocus="this.removeAttribute('readonly');"--}}
                        {{--{{ ((config('app.lock_passwords') && ($item->id)) ? ' disabled' : '') }}>--}}
                    {{--@else--}}
                      {{--(Managed via LDAP)--}}
                    {{--@endif--}}
                    {{--<span id="generated-password"></span>--}}
                    {{--{!! $errors->first('password', '<span class="alert-msg">:message</span>') !!}--}}
                  {{--</div>--}}
                  {{--<div class="col-md-4">--}}
                    {{--@if ($item->ldap_import!='1')--}}
                      {{--<a href="#" class="left" id="genPassword">Generate</a>--}}
                    {{--@endif--}}
                  {{--</div>--}}
                {{--</div>--}}

                {{--@if ($item->ldap_import!='1')--}}
                {{--<!-- Password Confirm -->--}}
                {{--<div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">--}}
                  {{--<label class="col-md-3 control-label" for="password_confirmation">--}}
                    {{--{{ trans('admin/users/table.password_confirm') }}--}}
                  {{--</label>--}}
                  {{--<div class="col-md-5 {{  ((\App\Helpers\Helper::checkIfRequired($item, 'firstname')) && (!$item->id)) ? ' required' : '' }}">--}}
                    {{--<input--}}
                    {{--type="password"--}}
                    {{--name="password_confirmation"--}}
                    {{--id="password_confirm"--}}
                    {{--class="form-control"--}}
                    {{--value=""--}}
                    {{--autocomplete="off"--}}
                    {{--readonly--}}
                    {{--onfocus="this.removeAttribute('readonly');"--}}
                    {{--{{ ((config('app.lock_passwords') && ($item->id)) ? ' disabled' : '') }}--}}
                    {{-->--}}
                    {{--@if (config('app.lock_passwords') && ($item->id))--}}
                    {{--<p class="help-block">{{ trans('admin/users/table.lock_passwords') }}</p>--}}
                    {{--@endif--}}
                    {{--{!! $errors->first('password_confirmation', '<span class="alert-msg">:message</span>') !!}--}}
                  {{--</div>--}}
                {{--</div>--}}
                {{--@endif--}}

                <!-- Email -->
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="email">{{ trans('klusbib::admin/users/table.email') }} </label>
                  <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'email')) ? ' required' : '' }}">
                    <input
                      class="form-control"
                      type="text"
                      name="email"
                      id="email"
                      value="{{ Request::old('email', $item->email) }}"
                      {{ ((config('app.lock_passwords') && ($item->id)) ? ' disabled' : '') }}
                      autocomplete="off"
                      readonly
                      onfocus="this.removeAttribute('readonly');">
                    @if (config('app.lock_passwords') && ($item->id))
                    <p class="help-block">{{ trans('admin/users/table.lock_passwords') }}</p>
                    @endif
                    {!! $errors->first('email', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>

                <!-- Phone -->
                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="phone">{{ trans('klusbib::admin/users/table.phone') }}</label>
                  <div class="col-md-4">
                    <input class="form-control" type="text" name="phone" id="phone" value="{{ Request::old('phone', $item->phone) }}" />
                    {!! $errors->first('phone', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>

                  <!-- Address -->
                  <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                      <label class="col-md-3 control-label" for="address">{{ trans('klusbib::admin/users/table.address') }}</label>
                      <div class="col-md-4">
                          <input class="form-control" type="text" name="address" id="address" value="{{ Request::old('address', $item->address) }}" />
                          {!! $errors->first('address', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- City -->
                  <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                      <label class="col-md-3 control-label" for="city">{{ trans('klusbib::admin/users/table.city') }}</label>
                      <div class="col-md-4">
                          <input class="form-control" type="text" name="city" id="city" value="{{ Request::old('city', $item->city) }}" />
                          {!! $errors->first('city', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- Postal code -->
                  <div class="form-group{{ $errors->has('postal_code') ? ' has-error' : '' }}">
                      <label class="col-md-3 control-label" for="postal_code">{{ trans('klusbib::admin/users/table.postal_code') }}</label>
                      <div class="col-md-4">
                          <input class="form-control" type="text" name="postal_code" id="postal_code" value="{{ Request::old('postal_code', $item->postal_code) }}" maxlength="5" />
                          {!! $errors->first('postal_code', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- Comment -->
                  <div class="form-group{!! $errors->has('notes') ? ' has-error' : '' !!}">
                      <label for="comment" class="col-md-3 control-label">{{ trans('klusbib::admin/users/table.comment') }}</label>
                      <div class="col-md-8">
                          <textarea class="form-control" id="comment" name="comment">{{ Request::old('comment', $item->comment) }}</textarea>
                          {!! $errors->first('comment', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
                      </div>
                  </div>

                  <!-- Membership -->
                  <div id="membership" x-data="{membershiptype: 'NONE'}"  x-cloak>

                  <!-- Membership type -->
                  <div class="form-group {{ $errors->has('membership_type') ? 'has-error' : '' }}">
                      <label class="col-md-3 control-label" for="membership_type">{{ trans('klusbib::admin/users/general.membership_type') }} </label>
                      <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'membership_type')) ? ' required' : '' }}">
                          <select x-model="membershiptype" class="form-control"  name="membership_type" id="membership_type_select">{{ Request::old('membership_type', $item->membership_type) }}
                              @foreach ( $allowed_new_memberships as $value )
                                  <option value="{{$value}}" {{ (Request::old("membership_type", $item->membership_type) == $value ? "selected":"") }}>
                                      {{trans( 'klusbib::types/membershiptypes.'.$value)}}</option>
                              @endforeach
                          </select>
                          {!! $errors->first('membership_type', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- Membership start date -->
                  <div x-show="membershiptype === 'TEMPORARY' || membershiptype === 'REGULAR' || membershiptype === 'REGULARREDUCED' || membershiptype === 'REGULARORG'"
                       class="form-group {{ $errors->has('membership_start_date') ? 'has-error' : '' }}">
                      <label class="col-md-3 control-label" for="membership_start_date">{{ trans('klusbib::admin/users/table.membership_start_date') }} </label>
                      <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'membership_start_date')) ? ' required' : '' }}">
                          <input class="form-control" type="date" name="membership_start_date" id="membership_start_date" value="{{ Request::old('membership_start_date', $item->membership_start_date) }}" />
                          {!! $errors->first('membership_start_date', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- Membership end date -->
                  {{--<div x-show="membershiptype !== 'NONE'" class="form-group {{ $errors->has('membership_end_date') ? 'has-error' : '' }}">--}}
                      {{--<label class="col-md-3 control-label" for="membership_end_date">{{ trans('klusbib::admin/users/table.membership_end_date') }} </label>--}}
                      {{--<div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'membership_end_date')) ? ' required' : '' }}">--}}
                          {{--<input class="form-control" type="date" name="membership_end_date" id="membership_end_date" value="{{ Request::old('membership_end_date', $item->membership_end_date) }}" />--}}
                          {{--{!! $errors->first('membership_end_date', '<span class="alert-msg">:message</span>') !!}--}}
                      {{--</div>--}}
                  {{--</div>--}}

                  <!-- Payment mode -->
                  <div x-show="membershiptype === 'REGULAR' || membershiptype === 'REGULARREDUCED' || membershiptype === 'REGULARORG'" class="form-group{{ $errors->has('payment_mode') ? ' has-error' : '' }}">
                      <label class="col-md-3 control-label" for="payment_mode">{{ trans('klusbib::admin/users/table.payment_mode') }}</label>
                      <div class="col-md-4">
                          <select class="form-control"  name="payment_mode" id="payment_mode_select">{{ Request::old('payment_mode', $item->payment_mode) }}
                              @foreach ( $allowed_payment_modes as $value )
                                  <option value="{{$value}}" {{ (Request::old("payment_mode", $item->payment_mode) == $value ? "selected":"") }}>
                                      {{trans( 'klusbib::types/paymentmodes.'.$value)}}</option>
                              @endforeach
                          </select>
                          {!! $errors->first('payment_mode', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                <!-- Accept terms date -->
                <div x-show="membershiptype !== 'NONE'" class="form-group{!! $errors->has('accept_terms_date') ? ' has-error' : '' !!}">
                  <label for="accept_terms_date" class="col-md-3 control-label">{{ trans('klusbib::admin/users/table.accept_terms_date') }}</label>
                  <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'accept_terms_date')) ? ' required' : '' }}">
                    <input class="form-control" type="date" id="accept_terms_date" name="accept_terms_date" value="{{ Request::old('accept_terms_date', $item->accept_terms_date) }}" />
                    {!! $errors->first('accept_terms_date', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
                  </div>
                </div>
              </div> <!-- membership -->

              </div> <!--/col-md-12-->
            </div>
          </div><!-- /.tab-pane -->

        {{--</div><!-- /.tab-content -->--}}
        <div class="box-footer text-right">
          <button type="submit" class="btn btn-success"><i class="fa fa-check icon-white"></i> {{ trans('general.save') }}</button>
        </div>
      {{--</div><!-- nav-tabs-custom -->--}}
    </form>
  </div> <!--/col-md-8-->
</div><!--/row-->
@stop

{{--@section('moar_scripts')--}}
{{--<script src="{{ asset('js/pGenerator.jquery.js') }}"></script>--}}

{{--<script nonce="{{ csrf_token() }}">--}}
{{--$(document).ready(function() {--}}

	{{--$('#email').on('keyup',function(){--}}

	    {{--if(this.value.length > 0){--}}
	        {{--$("#email_user").prop("disabled",false);--}}
			{{--$("#email_user_warn").html("");--}}
	    {{--} else {--}}
	        {{--$("#email_user").prop("disabled",true);--}}
			{{--$("#email_user").prop("checked",false);--}}
	    {{--}--}}

	{{--});--}}

	{{--// Check/Uncheck all radio buttons in the group--}}
    {{--$('tr.header-row input:radio').on('ifClicked', function () {--}}
        {{--value = $(this).attr('value');--}}
        {{--area = $(this).data('checker-group');--}}
        {{--$('.radiochecker-'+area+'[value='+value+']').iCheck('check');--}}
    {{--});--}}

    {{--$('.header-name').click(function() {--}}
        {{--$(this).parent().nextUntil('tr.header-row').slideToggle(500);--}}
    {{--});--}}

    {{--$('.tooltip-base').tooltip({container: 'body'})--}}
    {{--$(".superuser").change(function() {--}}
        {{--var perms = $(this).val();--}}
        {{--if (perms =='1') {--}}
            {{--$("#nonadmin").hide();--}}
        {{--} else {--}}
            {{--$("#nonadmin").show();--}}
        {{--}--}}
    {{--});--}}

    {{--$('#genPassword').pGenerator({--}}
        {{--'bind': 'click',--}}
        {{--'passwordElement': '#password',--}}
        {{--'displayElement': '#generated-password',--}}
        {{--'passwordLength': 16,--}}
        {{--'uppercase': true,--}}
        {{--'lowercase': true,--}}
        {{--'numbers':   true,--}}
        {{--'specialChars': true,--}}
        {{--'onPasswordGenerated': function(generatedPassword) {--}}
            {{--$('#password_confirm').val($('#password').val());--}}
        {{--}--}}
    {{--});--}}

    {{--$("#two_factor_reset").click(function(){--}}
        {{--$("#two_factor_resetrow").removeClass('success');--}}
        {{--$("#two_factor_resetrow").removeClass('danger');--}}
        {{--$("#two_factor_resetstatus").html('');--}}
        {{--$("#two_factor_reseticon").html('<i class="fa fa-spinner spin"></i>');--}}
        {{--$.ajax({--}}
            {{--url: '{{ route('api.users.two_factor_reset', ['id'=> $item->id]) }}',--}}
            {{--type: 'POST',--}}
            {{--data: {},--}}
            {{--headers: {--}}
                {{--"X-Requested-With": 'XMLHttpRequest',--}}
                 {{--"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')--}}
            {{--},--}}
            {{--dataType: 'json',--}}

            {{--success: function (data) {--}}
                {{--$("#two_factor_reseticon").html('');--}}
                {{--$("#two_factor_resetstatus").html('<i class="fa fa-check text-success"></i>' + data.message);--}}
            {{--},--}}

            {{--error: function (data) {--}}
                {{--$("#two_factor_reseticon").html('');--}}
                {{--$("#two_factor_reseticon").html('<i class="fa fa-exclamation-triangle text-danger"></i>');--}}
                {{--$('#two_factor_resetstatus').text(data.message);--}}
            {{--}--}}


        {{--});--}}
    {{--});--}}


{{--});--}}
{{--</script>--}}


{{--@stop--}}
