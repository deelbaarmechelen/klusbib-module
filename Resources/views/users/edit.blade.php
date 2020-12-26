@extends('layouts/default')
{{-- TODO: add CSS for greyed out readonly elements: 'input:read-only { color:grey; }'--}}
{{-- Page title --}}
@section('title')
	@if ($item->id)
		{{ trans('admin/users/table.updateuser') }}
		{{ $item->firstname . ' ' . $item->lastname }}
	@else
		{{ trans('admin/users/table.createuser') }}
	@endif

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
      color:grey;
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

</style>
{{--<p>Edit view for Klusbib</p>--}}
<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <form class="form-horizontal" method="post" autocomplete="off" action="{{ ($item) ? route('klusbib.users.update', ['user' => $item->id]) : route('klusbib.users.store') }}" id="userForm">
      {{csrf_field()}}

      @if($item->id)
          {{ method_field('PUT') }}
      @endif
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
                  <!-- User id -->
                  {{--TODO: show as greyed out to visibily indicate value cannot be changed--}}
                  <div class="form-group">
                      <label class="col-md-3 control-label" for="user_id">{{ trans('klusbib::admin/users/table.user_id') }}</label>
                      <div class="col-md-8 {{  (\App\Helpers\Helper::checkIfRequired($item, 'user_id')) ? ' required' : '' }}">
                          <input class="form-control" type="text" name="user_id" id="user_id" value="{{ Input::old('user_id', $item->user_id) }}" readonly="readonly"/>
                      </div>
                  </div>

                  <!-- Employee Number -->
                  {{--TODO: show as greyed out to visibily indicate value cannot be changed--}}
                  <div class="form-group {{ $errors->has('employee_num') ? 'has-error' : '' }}">
                      <label class="col-md-3 control-label" for="employee_num">{{ trans('klusbib::admin/users/table.employee_num') }}</label>
                      <div class="col-md-8">
                          <input
                                  class="form-control"
                                  type="text"
                                  name="employee_num"
                                  id="employee_num"
                                  value="{{ Input::old('user_ext_id', $item->user_ext_id) }}"
                                  readonly="readonly"
                          />
                          {!! $errors->first('employee_num', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                <!-- First Name -->
                <div class="form-group {{ $errors->has('firstname') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="firstname">{{ trans('klusbib::admin/users/table.firstname') }}</label>
                  <div class="col-md-8 {{  (\App\Helpers\Helper::checkIfRequired($item, 'firstname')) ? ' required' : '' }}">
                    <input class="form-control" type="text" name="firstname" id="firstname" value="{{ Input::old('firstname', $item->firstname) }}" />
                    {!! $errors->first('firstname', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>

                <!-- Last Name -->
                <div class="form-group {{ $errors->has('lastname') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="lastname">{{ trans('klusbib::admin/users/table.lastname') }} </label>
                  <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'lastname')) ? ' required' : '' }}">
                    <input class="form-control" type="text" name="lastname" id="lastname" value="{{ Input::old('lastname', $item->lastname) }}" />
                    {!! $errors->first('lastname', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>

                  <!-- Role -->
                  <div class="form-group {{ $errors->has('role') ? 'has-error' : '' }}">
                      <label class="col-md-3 control-label" for="role">{{ trans('klusbib::admin/users/table.role') }} </label>
                      <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'role')) ? ' required' : '' }}">
                          <select class="form-control"  name="role" id="role_select">{{ Input::old('role', $item->role) }}
                              <option value="admin" {{ (Input::old("role", $item->role) == "admin" ? "selected":"") }}>Admin</option>
                              <option value="member" {{ (Input::old("role", $item->role) == "member" ? "selected":"") }}>Lid</option>
                              <option value="supporter" {{ (Input::old("role", $item->role) == "supporter" ? "selected":"") }}>Steunlid</option>
                          </select>
                          {!! $errors->first('role', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- State -->
                  <div class="form-group {{ $errors->has('state') ? 'has-error' : '' }}">
                      <label class="col-md-3 control-label" for="state">{{ trans('general.state') }} </label>
                      <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'state')) ? ' required' : '' }}">
                          <select class="form-control"  name="state" id="state_select">{{ Input::old('state', $item->state) }}
                              <option value="CHECK_PAYMENT" {{ (Input::old("state", $item->state) == "CHECK_PAYMENT" ? "selected":"") }}>Betaling nakijken</option>
                              <option value="ACTIVE" {{ (Input::old("state", $item->state) == "ACTIVE" ? "selected":"") }}>Actief</option>
                              <option value="EXPIRED" {{ (Input::old("state", $item->state) == "EXPIRED" ? "selected":"") }}>Vervallen</option>
                              <option value="DISABLED" {{ (Input::old("state", $item->state) == "DISABLED" ? "selected":"") }}>Inactief</option>
                              <option value="DELETED" {{ (Input::old("state", $item->state) == "DELETED" ? "selected":"") }}>Verwijderd</option>
                          </select>
                          {!! $errors->first('state', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                {{--<!-- Username -->--}}
                {{--<div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">--}}
                  {{--<label class="col-md-3 control-label" for="username">{{ trans('admin/users/table.username') }}</label>--}}
                  {{--<div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'username')) ? ' required' : '' }}">--}}
                    {{--@if ($item->ldap_import!='1')--}}
                      {{--<input--}}
                        {{--class="form-control"--}}
                        {{--type="text"--}}
                        {{--name="username"--}}
                        {{--id="username"--}}
                        {{--value="{{ Input::old('username', $item->username) }}"--}}
                        {{--autocomplete="off"--}}
                        {{--readonly--}}
                        {{--onfocus="this.removeAttribute('readonly');"--}}
                        {{--{{ ((config('app.lock_passwords') && ($item->id)) ? ' disabled' : '') }}--}}
                      {{-->--}}
                      {{--@if (config('app.lock_passwords') && ($item->id))--}}
                        {{--<p class="help-block">{{ trans('admin/users/table.lock_passwords') }}</p>--}}
                      {{--@endif--}}
                    {{--@else--}}
                      {{--(Managed via LDAP)--}}
                          {{--<input type="hidden" name="username" value="{{ Input::old('username', $item->username) }}">--}}

                    {{--@endif--}}

                    {{--{!! $errors->first('username', '<span class="alert-msg">:message</span>') !!}--}}
                  {{--</div>--}}
                {{--</div>--}}

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
                    <input class="form-control" type="text" name="email" id="email"
                      value="{{ Input::old('email', $item->email) }}" >
                    {!! $errors->first('email', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>

                <!-- Email State -->
                <div class="form-group {{ $errors->has('email_state') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="email_state">{{ trans('klusbib::admin/users/table.email_state') }} </label>
                  <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'email_state')) ? ' required' : '' }}">
                    <select class="form-control"  name="email_state" id="email_state_select" disabled>{{ Input::old('email_state', $item->email_state) }}
                      <option value="CONFIRM_EMAIL" {{ (Input::old("email_state", $item->email_state) == "CONFIRM_EMAIL" ? "selected":"") }}>Email verificatie</option>
                      <option value="CONFIRMED" {{ (Input::old("email_state", $item->email_state) == "CONFIRMED" ? "selected":"") }}>Email bevestigd</option>
                      <option value="BOUNCED" {{ (Input::old("email_state", $item->email_state) == "BOUNCED" ? "selected":"") }}>Email geweigerd (bounce)</option>
                    </select>
                    {!! $errors->first('email_state', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>


                <!-- Phone -->
                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="phone">{{ trans('klusbib::admin/users/table.phone') }}</label>
                  <div class="col-md-4">
                    <input class="form-control" type="text" name="phone" id="phone" value="{{ Input::old('phone', $item->phone) }}" />
                    {!! $errors->first('phone', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>

                <!-- Mobile -->
                <div class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="mobile">{{ trans('klusbib::admin/users/table.mobile') }}</label>
                  <div class="col-md-4">
                    <input class="form-control" type="text" name="mobile" id="mobile" value="{{ Input::old('mobile', $item->mobile) }}" />
                    {!! $errors->first('mobile', '<span class="alert-msg">:message</span>') !!}
                  </div>
                </div>

                  <!-- Address -->
                  <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                      <label class="col-md-3 control-label" for="address">{{ trans('klusbib::admin/users/table.address') }}</label>
                      <div class="col-md-4">
                          <input class="form-control" type="text" name="address" id="address" value="{{ Input::old('address', $item->address) }}" />
                          {!! $errors->first('address', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- City -->
                  <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                      <label class="col-md-3 control-label" for="city">{{ trans('klusbib::admin/users/table.city') }}</label>
                      <div class="col-md-4">
                          <input class="form-control" type="text" name="city" id="city" value="{{ Input::old('city', $item->city) }}" />
                          {!! $errors->first('city', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- Postal code -->
                  <div class="form-group{{ $errors->has('postal_code') ? ' has-error' : '' }}">
                      <label class="col-md-3 control-label" for="postal_code">{{ trans('klusbib::admin/users/table.postal_code') }}</label>
                      <div class="col-md-4">
                          <input class="form-control" type="text" name="postal_code" id="postal_code" value="{{ Input::old('postal_code', $item->postal_code) }}" maxlength="5" />
                          {!! $errors->first('postal_code', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- Registration code -->
                  <div class="form-group{{ $errors->has('registration_number') ? ' has-error' : '' }}">
                      <label class="col-md-3 control-label" for="registration_number">{{ trans('klusbib::admin/users/table.registration_number') }}</label>
                      <div class="col-md-4">
                          <input class="form-control" type="text" name="registration_number" id="registration_number" value="{{ Input::old('registration_number', $item->registration_number) }}" maxlength="11" />
                          {!! $errors->first('registration_number', '<span class="alert-msg">:message</span>') !!}
                      </div>
                  </div>

                  <!-- Comment -->
                  <div class="form-group{!! $errors->has('notes') ? ' has-error' : '' !!}">
                      <label for="comment" class="col-md-3 control-label">{{ trans('klusbib::admin/users/table.comment') }}</label>
                      <div class="col-md-8">
                          <textarea class="form-control" id="comment" name="comment">{{ Input::old('comment', $item->comment) }}</textarea>
                          {!! $errors->first('comment', '<span class="alert-msg"><i class="fa fa-times"></i> :message</span>') !!}
                      </div>
                  </div>

                  <!-- Membership -->
                  <div id="membership" x-data="{membershiptype: 'NONE'}" x-cloak x-init="membershiptype = $item->membership_type">

                      <!-- Membership type -->
                      <div class="form-group {{ $errors->has('membership_type') ? 'has-error' : '' }}">
                          <label class="col-md-3 control-label" for="membership_type">{{ trans('klusbib::admin/users/general.membership_type') }} </label>
                          <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'membership_type')) ? ' required' : '' }}">
                              <input class="form-control" type="hidden" name="membership_type" id="membership_type"
                                     value="{{ Input::old('membership_type', $item->membership_type) }}" readonly="readonly" />
                              {{trans( 'klusbib::types/membershiptypes.' .Input::old('membership_type', $item->membership_type)) }}

                              {!! $errors->first('membership_type', '<span class="alert-msg">:message</span>') !!}
                          </div>
                      </div>

                      <!-- New Membership type -->
                      <div class="form-group {{ $errors->has('new_membership_type') ? 'has-error' : '' }}">
                          <label class="col-md-3 control-label" for="new_membership_type">{{ trans('klusbib::admin/users/general.new_membership_type') }} </label>
                          <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'new_membership_type')) ? ' required' : '' }}">
                              <select x-model="membershiptype" class="form-control"  name="new_membership_type" id="new_membership_type_select">
                                  @foreach ( $allowed_new_memberships as $value )
                                      <option value="{{$value}}" {{ (Input::old("membership_type", $item->membership_type) == $value ? "selected":"") }}>
                                          {{trans( 'klusbib::types/membershiptypes.'.$value)}}</option>
                                  @endforeach
                              </select>
                              {!! $errors->first('new_membership_type', '<span class="alert-msg">:message</span>') !!}
                          </div>
                      </div>

                      <!-- Membership start date -->
                      <div x-show="membershiptype !== 'NONE' && membershiptype !== 'RENEWAL' && membershiptype !== 'STROOM'"
                           class="form-group {{ $errors->has('new_membership_start_date') ? 'has-error' : '' }}">
                          <label class="col-md-3 control-label" for="new_membership_start_date">{{ trans('klusbib::admin/users/table.membership_start_date') }} </label>
                          <div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'membership_start_date')) ? ' required' : '' }}">
                              <input class="form-control" type="date" name="new_membership_start_date" id="new_membership_start_date" value="{{ Input::old('new_membership_start_date', $item->new_membership_start_date) }}" />
                              {!! $errors->first('new_membership_start_date', '<span class="alert-msg">:message</span>') !!}
                          </div>
                      </div>

                      <!-- Membership end date: hidden as end date defined by membership type -->
                      {{--<div x-show="membershiptype !== 'NONE' && membershiptype !== 'RENEWAL'" class="form-group {{ $errors->has('membership_end_date') ? 'has-error' : '' }}">--}}
                          {{--<label class="col-md-3 control-label" for="membership_end_date">{{ trans('klusbib::admin/users/table.membership_end_date') }} </label>--}}
                          {{--<div class="col-md-8{{  (\App\Helpers\Helper::checkIfRequired($item, 'membership_end_date')) ? ' required' : '' }}">--}}
                              {{--<input class="form-control" type="date" name="membership_end_date" id="membership_end_date" value="{{ Input::old('membership_end_date', $item->membership_end_date) }}" />--}}
                              {{--{!! $errors->first('membership_end_date', '<span class="alert-msg">:message</span>') !!}--}}
                          {{--</div>--}}
                      {{--</div>--}}

                      <!-- Payment mode -->
                      <div x-show="membershiptype === 'REGULAR' || membershiptype === 'RENEWAL'" class="form-group{{ $errors->has('payment_mode') ? ' has-error' : '' }}">
                          <label class="col-md-3 control-label" for="payment_mode">{{ trans('klusbib::admin/users/table.payment_mode') }}</label>
                          <div class="col-md-4">
                              <select class="form-control"  name="payment_mode" id="payment_mode_select">{{ Input::old('payment_mode', $item->payment_mode) }}
                                  @foreach ( $allowed_payment_modes as $value )
                                      <option value="{{$value}}" {{ (Input::old("payment_mode", $item->payment_mode) == $value ? "selected":"") }}>
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
                        <input class="form-control" type="date" id="accept_terms_date" name="accept_terms_date" value="{{ Input::old('accept_terms_date', $item->accept_terms_date) }}" />
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
