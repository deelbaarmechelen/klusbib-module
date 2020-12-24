@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('klusbib::admin/users/general.view') }}
{{-- - {{ $user->name }}--}}
 - {{ $user->user_id }}
@parent
@stop

{{-- Right header --}}
@section('header_right')
<div class="btn-group pull-right">
  {{--@can('update', $user)--}}
    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{ trans('button.actions') }}
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="{{ route('klusbib.users.edit', ['user' => $user->user_id]) }}">{{ trans('klusbib::admin/users/general.edit') }}</a></li>
{{--        <li><a href="{{ route('clone/user', $user->user_id) }}">{{ trans('admin/users/general.clone') }}</a></li>--}}
    </ul>
   {{--@endcan--}}
</div>
@stop

{{-- Page content --}}
@section('content')
<div class="row">

      <div class="table">
        <table class="table">
          <tbody>


            @if (isset($user->user_id))
            <tr>
              <td>{{ trans('klusbib::admin/users/general.user_id') }} (API):</td>
              <td>{{ $user->user_id }}</td>
            </tr>
            @endif

            @if (isset($user->user_ext_id))
            <tr>
              <td>{{ trans('klusbib::admin/users/general.user_ext_id') }} ({{ trans('klusbib::general.inventory') }}):</td>
              <td>{{ $user->user_ext_id }}</td>
            </tr>
            @endif

            @if ($user->firstname)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.firstname') }}:</td>
                <td>{{ $user->firstname }}</td>
              </tr>
            @endif

            @if ($user->lastname)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.lastname') }}:</td>
                <td>{{ $user->lastname }}</td>
              </tr>
            @endif

            @if ($user->company)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.company') }}:</td>
                <td>{{ $user->company }}</td>
              </tr>
            @endif

            @if ($user->role)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.role') }}:</td>
                <td>{{ $user->role }}</td>
              </tr>
            @endif

            @if (isset($user->state))
            <tr>
              <td>{{ trans('klusbib::admin/users/general.state') }}:</td>
              <td>{{ $user->state }}</td>
            </tr>
            @endif

            @if (isset($user->membershipStartsAt))
            <tr>
              <td>{{ trans('klusbib::admin/users/general.membership_start_date') }}:</td>
              <td>{{ $user->membershipStartsAt }}</td>
            </tr>
            @endif

            @if (isset($user->membershipEndsAt))
              <tr>
                <td>{{ trans('klusbib::admin/users/general.membership_end_date') }}:</td>
                <td>{{ $user->membershipEndsAt }}</td>
              </tr>
            @endif

            @if ($user->email)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.email') }}:</td>
                <td>{{ $user->email }}</td>
              </tr>
            @endif

            @if ($user->email_state)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.email_state') }}:</td>
                <td>{{  __('klusbib::types/emailstates.' . $user->email_state) }}</td>
              </tr>
            @endif

            @if ($user->phone)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.phone') }}:</td>
                <td>{{ $user->phone }}</td>
              </tr>
            @endif

            @if ($user->mobile)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.mobile') }}:</td>
                <td>{{ $user->mobile }}</td>
              </tr>
            @endif

            @if ($user->address)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.address') }}:</td>
                <td>{{ $user->address }}</td>
              </tr>
            @endif

            @if ($user->city)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.city') }}:</td>
                <td>{{ $user->city }}</td>
              </tr>
            @endif

            @if ($user->postal_code)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.postal_code') }}:</td>
                <td>{{ $user->postal_code }}</td>
              </tr>
            @endif

            @if ($user->registration_number)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.registration_number') }}:</td>
                <td>{{ $user->registration_number }}</td>
              </tr>
            @endif

            @if ($user->payment_mode)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.payment_mode') }}:</td>
                <td>{{  __('klusbib::types/paymentmodes.' . $user->payment_mode) }}</td>
              </tr>
            @endif

            @if ($user->accept_terms_date)
              <tr>
                <td>{{ trans('klusbib::admin/users/table.accept_terms_date') }}:</td>
                <td>{{ $user->accept_terms_date }}</td>
              </tr>
            @endif

            @if (isset($user->membership_type) )
              <tr>
                <td>{{ trans('klusbib::admin/users/table.membership_type') }}:</td>
                <td>{{ trans('klusbib::types/membershiptypes.' . $user->membership_type )}}</td>
              </tr>
            @endif

          </tbody>
        </table>
      </div> <!-- .table-->

    <table
            data-click-to-select="true"
            data-columns="{{ \Modules\Klusbib\Presenters\MembershipPresenter::dataTableLayout() }}"
            data-cookie-id-table="membershipsTable"
            data-pagination="false"
            data-id-table="membershipsTable"
            data-search="false"
            data-side-pagination="server"
            data-show-columns="true"
            data-show-export="false"
            data-show-refresh="false"
            data-sort-order="asc"
            data-toolbar="#toolbar"
            id="membershipsTable"
            class="table table-striped snipe-table"
            data-url="{{ route('api.klusbib.membership.index',
              array(
                'deleted' => (Input::get('status')=='deleted') ? 'true' : 'false',
                'user_id' => $user->user_id,
                'status'  => 'OPEN'
                ) ) }}">
    </table>

@stop


@section('moar_scripts')
  @include ('partials.bootstrap-table')
  @include ('klusbib::partials.custom-bootstrap-table')

@stop
