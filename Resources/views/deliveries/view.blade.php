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
  @can('create', \Modules\Klusbib\Models\Api\Delivery::class)
      <a href="{{ route('klusbib.deliveries.items.new', ['delivery' => $delivery->id ]) }}"
         class="btn btn-primary pull-right" style="margin-right: 5px;">  {{ trans('klusbib::admin/deliveries/general.add_item') }}</a>
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


    <div> <!-- .table-->
        <table id="deliveryItemsTable"
               data-click-to-select="true"
               data-columns="{{ \Modules\Klusbib\Presenters\InventoryItemPresenter::dataTableLayout() }}"
               data-cookie-id-table="deliveryItemsTable"
               data-pagination="false"
               data-id-table="deliveryItemsTable"
               data-search="false"
               data-side-pagination="client"
               data-show-columns="true"
               data-show-export="true"
               data-show-refresh="false"
               data-sort-order="asc"
               data-toolbar="#toolbar"
               class="table table-striped"
            {{--array('deleted'=> (Input::get('status')=='deleted') ? 'true' : 'false','company_id'=>e(Input::get('company_id')))) }}"--}}
               data-export-options='{
                   "fileName": "export-delivery-items-{{ date('Y-m-d') }}",
                   "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
               }'>

        </table>
    </div> <!-- .table-->

</div> <!-- /.row -->



@stop


@section('moar_scripts')
  @include ('partials.bootstrap-table')
  @include ('klusbib::partials.custom-bootstrap-table', [ 'deliveryId' => $delivery->id ])

  <script>
      var $table = $('#deliveryItemsTable')

      $(function() {
          var stickyHeaderOffsetY = 0;

          if ( $('.navbar-fixed-top').css('height') ) {
              stickyHeaderOffsetY = +$('.navbar-fixed-top').css('height').replace('px','');
          }
          if ( $('.navbar-fixed-top').css('margin-bottom') ) {
              stickyHeaderOffsetY += +$('.navbar-fixed-top').css('margin-bottom').replace('px','');
          }

          var dataDelivery = {!! $delivery->items !!}
          $table.bootstrapTable({
              data: dataDelivery.rows,
              undefinedText: '',
              iconsPrefix: 'fa',
              cookie: true,
              cookieExpire: '2y',
              cookieIdTable: '{{ Route::currentRouteName() }}',
              mobileResponsive: true,
              maintainSelected: true,
              trimOnSearch: false,
              paginationFirstText: "{{ trans('general.first') }}",
              paginationLastText: "{{ trans('general.last') }}",
              paginationPreText: "{{ trans('general.previous') }}",
              paginationNextText: "{{ trans('general.next') }}",
              pageList: ['10','20', '30','50','100','150','200', '500'],
              pageSize: {{  (($snipeSettings->per_page!='') && ($snipeSettings->per_page > 0)) ? $snipeSettings->per_page : 20 }},
              paginationVAlign: 'both',
              formatLoadingMessage: function () {
              return '<h4><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> Loading... please wait.... </h4>';
              },

              icons: {
              advancedSearchIcon: 'fa fa-search-plus',
              paginationSwitchDown: 'fa-caret-square-o-down',
              paginationSwitchUp: 'fa-caret-square-o-up',
              columns: 'fa-columns',
              refresh: 'fa-refresh'
              },
              exportTypes: ['csv', 'excel', 'doc', 'txt','json', 'xml', 'pdf'],
          })
      })
      // Handle whether or not the edit button should be disabled
      $table.on('check.bs.table', function () {
          $('#bulkEdit').removeAttr('disabled');
      });

      $table.on('check-all.bs.table', function () {
          $('#bulkEdit').removeAttr('disabled');
      });

      $table.on('uncheck.bs.table', function () {
          if ($table.bootstrapTable('getSelections').length == 0) {
              $('#bulkEdit').attr('disabled', 'disabled');
          }
      });

      $table.on('uncheck-all.bs.table', function (e, row) {
          $('#bulkEdit').attr('disabled', 'disabled');
      });
  </script>
@stop

