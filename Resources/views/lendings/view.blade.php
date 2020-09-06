@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('klusbib::admin/reservations/general.view') }}
{{-- - {{ $reservation->name }}--}}
 - {{ $reservation->reservation_id }}
@parent
@stop

{{-- Right header --}}
@section('header_right')
<div class="btn-group pull-right">
  @can('update', $reservation)
    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{ trans('button.actions') }}
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="{{ route('klusbib.reservations.edit', ['reservation' => $reservation->reservation_id]) }}">{{ trans('klusbib::admin/reservations/general.edit') }}</a></li>
{{--        <li><a href="{{ route('clone/reservation', $reservation->reservation_id) }}">{{ trans('admin/reservations/general.clone') }}</a></li>--}}
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


            @if (isset($reservation->user_id))
            <tr>
              <td>{{ trans('klusbib::admin/reservations/general.user_id') }}:</td>
              <td>{{ $reservation->user_id }}</td>
            </tr>
            @endif

            @if (isset($reservation->tool_id))
            <tr>
              <td>{{ trans('klusbib::admin/reservations/general.tool_id') }}:</td>
              <td>{{ $reservation->tool_id }}</td>
            </tr>
            @endif

            @if (isset($reservation->state))
            <tr>
              <td>{{ trans('klusbib::admin/reservations/general.state') }}:</td>
              <td>{{ $reservation->state }}</td>
            </tr>
            @endif

            @if (isset($reservation->startsAt))
            <tr>
              <td>{{ trans('klusbib::admin/reservations/general.start_date') }}:</td>
              <td>{{ $reservation->startsAt }}</td>
            </tr>
            @endif

            @if (isset($reservation->endsAt))
              <tr>
                <td>{{ trans('klusbib::admin/reservations/general.end_date') }}:</td>
                <td>{{ $reservation->endsAt }}</td>
              </tr>
            @endif

            @if ($reservation->comment)
            <tr>
              <td>{{ trans('klusbib::admin/reservations/general.notes') }}:</td>
              <td>
                {!! nl2br(e($reservation->comment)) !!}
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

