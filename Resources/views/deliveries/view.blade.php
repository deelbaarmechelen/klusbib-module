@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('klusbib::admin/deliveries/general.view') }}
{{-- - {{ $delivery->name }}--}}
 - {{ $delivery->id }}
@parent
@stop

{{-- Right header --}}
@section('header_right')
<div class="btn-group pull-right">
  @can('update', $delivery)
    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">{{ trans('button.actions') }}
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li><a href="{{ route('klusbib.deliveries.edit', ['delivery' => $delivery->id]) }}">{{ trans('klusbib::admin/deliveries/general.edit') }}</a></li>
{{--        <li><a href="{{ route('clone/delivery', $delivery->id) }}">{{ trans('admin/deliveries/general.clone') }}</a></li>--}}
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


            @if (isset($delivery->user_id))
            <tr>
              <td>{{ trans('klusbib::admin/deliveries/general.user_id') }}:</td>
              <td>{{ $delivery->user_id }}</td>
            </tr>
            @endif

            {{--@if (isset($delivery->tool_id))--}}
            {{--<tr>--}}
              {{--<td>{{ trans('klusbib::admin/deliveries/general.tool_id') }}:</td>--}}
              {{--<td>{{ $delivery->tool_id }}</td>--}}
            {{--</tr>--}}
            {{--@endif--}}

            @if (isset($delivery->state))
            <tr>
              <td>{{ trans('klusbib::admin/deliveries/general.state') }}:</td>
              <td>{{ $delivery->state }}</td>
            </tr>
            @endif

            @if (isset($delivery->pick_up_date))
            <tr>
              <td>{{ trans('klusbib::admin/deliveries/general.pick_up_date') }}:</td>
              <td>{{ $delivery->pick_up_date }}</td>
            </tr>
            @endif

            @if (isset($delivery->drop_off_date))
              <tr>
                <td>{{ trans('klusbib::admin/deliveries/general.drop_off_date') }}:</td>
                <td>{{ $delivery->drop_off_date }}</td>
              </tr>
            @endif

            @if (isset($delivery->pick_up_address))
            <tr>
              <td>{{ trans('klusbib::admin/deliveries/general.pick_up_address') }}:</td>
              <td>{{ $delivery->pick_up_address }}</td>
            </tr>
            @endif

            @if (isset($delivery->drop_off_address))
              <tr>
                <td>{{ trans('klusbib::admin/deliveries/general.drop_off_address') }}:</td>
                <td>{{ $delivery->drop_off_address }}</td>
              </tr>
            @endif

            @if ($delivery->comment)
            <tr>
              <td>{{ trans('klusbib::admin/deliveries/general.notes') }}:</td>
              <td>
                {!! nl2br(e($delivery->comment)) !!}
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

