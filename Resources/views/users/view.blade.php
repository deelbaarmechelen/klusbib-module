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
  @can('update', $user)
    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{ trans('button.actions') }}
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="{{ route('klusbib.users.edit', ['user' => $user->user_id]) }}">{{ trans('klusbib::admin/users/general.edit') }}</a></li>
{{--        <li><a href="{{ route('clone/user', $user->user_id) }}">{{ trans('admin/users/general.clone') }}</a></li>--}}
    </ul>
   @endcan
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
              <td>{{ trans('klusbib::admin/users/general.user_id') }}:</td>
              <td>{{ $user->user_id }}</td>
            </tr>
            @endif

            @if (isset($user->user_ext_id))
            <tr>
              <td>{{ trans('klusbib::admin/users/general.user_ext_id') }}:</td>
              <td>{{ $user->user_ext_id }}</td>
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

            @if ($user->comment)
            <tr>
              <td>{{ trans('klusbib::admin/users/general.notes') }}:</td>
              <td>
                {!! nl2br(e($user->comment)) !!}
              </td>
            </tr>
            @endif
          </tbody>
        </table>
      </div> <!-- .table-->
</div> <!-- /.row -->

@stop


@section('moar_scripts')
  @include ('partials.bootstrap-table')
@stop

