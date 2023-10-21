@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('general.dashboard') }}
@parent
@stop


{{-- Page content --}}
@section('content')

@if ($snipeSettings->dashboard_message!='')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        {!!  Parsedown::instance()->text(e($snipeSettings->dashboard_message))  !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
  <!-- panel -->
  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-aqua">
      <div class="inner">
        <h3>{{ number_format($counts['user']) }}</h3>
        <p>{{ trans('klusbib::general.total_users') }}</p>
      </div>
      <div class="icon">
        <i class="fa fa-users"></i>
      </div>
      @can('index', \App\Models\Asset::class)
        <a href="{{ route('klusbib.users.index') }}" class="small-box-footer">{{ trans('general.moreinfo') }} <i class="fa fa-arrow-circle-right"></i></a>
      @endcan
    </div>
  </div>
  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-teal">
      <div class="inner">
        <h3>{{ number_format($counts['asset']) }}</h3>
        <p>{{ trans('general.total_assets') }}</p>
      </div>
      <div class="icon">
        <i class="fa fa-barcode"></i>
      </div>
      @can('index', \App\Models\Asset::class)
        <a href="{{ route('hardware.index') }}" class="small-box-footer">{{ trans('general.moreinfo') }} <i class="fa fa-arrow-circle-right"></i></a>
      @endcan
    </div>
  </div><!-- ./col -->

  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-orange">
      <div class="inner">
        <h3> {{ number_format($counts['accessory']) }}</h3>
          <p>{{ trans('general.total_accessories') }}</p>
      </div>
      <div class="icon">
        <i class="fa fa-keyboard-o"></i>
      </div>
      @can('index', \App\Models\Accessory::class)
          <a href="{{ route('accessories.index') }}" class="small-box-footer">{{ trans('general.moreinfo') }} <i class="fa fa-arrow-circle-right"></i></a>
      @endcan
    </div>
  </div><!-- ./col -->

  <div class="col-lg-3 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-purple">
      <div class="inner">
        <h3> {{ number_format($counts['consumable']) }}</h3>
          <p>{{ trans('general.total_consumables') }}</p>
      </div>
      <div class="icon">
        <i class="fa fa-tint"></i>
      </div>
      @can('index', \App\Models\Consumable::class)
        <a href="{{ route('consumables.index') }}" class="small-box-footer">{{ trans('general.moreinfo') }} <i class="fa fa-arrow-circle-right"></i></a>
      @endcan
    </div>
  </div><!-- ./col -->

    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-olive">
            <div class="inner">
                <h3> {{ number_format($counts['activity']) }}</h3>
                <p>{{ trans('klusbib::general.total_activity') }}</p>
            </div>
            <div class="icon">
                <i class="fa fa-tasks"></i>
            </div>
            {{--@can('index', \App\Models\Consumable::class)--}}
                {{--<a href="{{ route('consumables.index') }}" class="small-box-footer">{{ trans('general.moreinfo') }} <i class="fa fa-arrow-circle-right"></i></a>--}}
            {{--@endcan--}}
        </div>
    </div><!-- ./col -->
</div>

{{--@if ($counts['grand_total'] == 0)--}}

    {{--<div class="row">--}}
        {{--<div class="col-md-12">--}}
            {{--<div class="box">--}}
                {{--<div class="box-header with-border">--}}
                    {{--<h3 class="box-title">This is your dashboard. There are many like it, but this one is yours.</h3>--}}
                {{--</div>--}}
                {{--<!-- /.box-header -->--}}
                {{--<div class="box-body">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-md-12">--}}

                            {{--<div class="progress">--}}
                                {{--<div class="progress-bar progress-bar-yellow" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">--}}
                                    {{--<span class="sr-only">60% Complete (warning)</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}


                            {{--<p><strong>It looks like you haven't added anything yet, so we don't have anything awesome to display. Get started by adding some assets, accessories, consumables, or licenses now!</strong></p>--}}

                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-md-3">--}}
                            {{--@can('create', \App\Models\Asset::class)--}}
                            {{--<a class="btn bg-teal" style="width: 100%" href="{{ route('hardware.create') }}">New Asset</a>--}}
                            {{--@endcan--}}
                        {{--</div>--}}
                        {{--<div class="col-md-3">--}}
                            {{--@can('create', \App\Models\License::class)--}}
                                {{--<a class="btn bg-maroon" style="width: 100%" href="{{ route('licenses.create') }}">New License</a>--}}
                            {{--@endcan--}}
                        {{--</div>--}}
                        {{--<div class="col-md-3">--}}
                            {{--@can('create', \App\Models\Accessory::class)--}}
                                {{--<a class="btn bg-orange" style="width: 100%" href="{{ route('accessories.create') }}">New Accessory</a>--}}
                            {{--@endcan--}}
                        {{--</div>--}}
                        {{--<div class="col-md-3">--}}
                            {{--@can('create', \App\Models\Consumable::class)--}}
                                {{--<a class="btn bg-purple" style="width: 100%" href="{{ route('consumables.create') }}">New Consumable</a>--}}
                            {{--@endcan--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

{{--@else--}}

{{--<!-- recent activity -->--}}
{{--<div class="row">--}}
  {{--<div class="col-md-12">--}}
    {{--<div class="box">--}}
      {{--<div class="box-header with-border">--}}
        {{--<h3 class="box-title">{{ trans('general.recent_activity') }}</h3>--}}
        {{--<div class="box-tools pull-right">--}}
            {{--<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>--}}
            {{--</button>--}}
        {{--</div>--}}
      {{--</div><!-- /.box-header -->--}}
      {{--<div class="box-body">--}}
        {{--<div class="row">--}}
          {{--<div class="col-md-12">--}}
            {{--<div class="table-responsive">--}}

                {{--<table--}}
                    {{--data-cookie-id-table="dashActivityReport"--}}
                    {{--data-height="400"--}}
                    {{--data-side-pagination="server"--}}
                    {{--data-sort-order="desc"--}}
                    {{--data-sort-name="created_at"--}}
                    {{--id="dashActivityReport"--}}
                    {{--class="table table-striped snipe-table"--}}
                    {{--data-url="{{ route('api.activity.index', ['limit' => 25]) }}">--}}
                    {{--<thead>--}}
                    {{--<tr>--}}
                        {{--<th data-field="icon" data-visible="true" style="width: 40px;" class="hidden-xs" data-formatter="iconFormatter"></th>--}}
                        {{--<th class="col-sm-3" data-visible="true" data-field="created_at" data-formatter="dateDisplayFormatter">{{ trans('general.date') }}</th>--}}
                        {{--<th class="col-sm-2" data-visible="true" data-field="admin" data-formatter="usersLinkObjFormatter">{{ trans('general.admin') }}</th>--}}
                        {{--<th class="col-sm-2" data-visible="true" data-field="action_type">{{ trans('general.action') }}</th>--}}
                        {{--<th class="col-sm-3" data-visible="true" data-field="item" data-formatter="polymorphicItemFormatter">{{ trans('general.item') }}</th>--}}
                        {{--<th class="col-sm-2" data-visible="true" data-field="target" data-formatter="polymorphicItemFormatter">{{ trans('general.target') }}</th>--}}
                    {{--</tr>--}}
                    {{--</thead>--}}
                {{--</table>--}}



            {{--</div><!-- /.responsive -->--}}
          {{--</div><!-- /.col -->--}}
          {{--<div class="col-md-12 text-center" style="padding-top: 10px;">--}}
            {{--<a href="{{ route('reports.activity') }}" class="btn btn-primary btn-sm" style="width: 100%">View All</a>--}}
          {{--</div>--}}
        {{--</div><!-- /.row -->--}}
      {{--</div><!-- ./box-body -->--}}
    {{--</div><!-- /.box -->--}}
  {{--</div>--}}

{{--</div> <!--/row-->--}}
<!-- First line of charts -->
<div class="row">
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('general.users') }} per Membership</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="min-height: 120px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-responsive">
                            <canvas id="usersMembershipPieChart" height="120"></canvas>
                        </div> <!-- ./chart-responsive -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div><!-- /.box-body -->
        </div> <!-- /.box -->
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('general.users') }} per Status</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="min-height: 120px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-responsive">
                            <canvas id="usersStatusPieChart" height="120"></canvas>
                        </div> <!-- ./chart-responsive -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div><!-- /.box-body -->
        </div> <!-- /.box -->
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('klusbib::general.activity') }} per categorie</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="min-height: 120px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-responsive">
                            <canvas id="lendingsByCatChart" height="120"></canvas>
                            {{--<canvas id="activityProjectPieChart" height="120"></canvas>--}}
                        </div> <!-- ./chart-responsive -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div><!-- /.box-body -->
        </div> <!-- /.box -->
    </div>
</div>
<!-- Second line of charts -->
<div class="row">
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('klusbib::general.new_users') }}</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="min-height: 120px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-responsive">
                            <canvas id="newUsersBarChart" height="120"></canvas>
                        </div> <!-- ./chart-responsive -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div><!-- /.box-body -->
        </div> <!-- /.box -->
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('klusbib::general.checkout') }}</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="min-height: 120px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-responsive">
                            <canvas id="checkoutBarChart" height="120"></canvas>
                        </div> <!-- ./chart-responsive -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div><!-- /.box-body -->
        </div> <!-- /.box -->
    </div>
    <div class="col-md-4">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('klusbib::general.checkin') }} - Verwachte datum</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="min-height: 120px;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-responsive">
                            <canvas id="lendingsChart" height="120"></canvas>
                        </div> <!-- ./chart-responsive -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div><!-- /.box-body -->
        </div> <!-- /.box -->
    </div>
</div>

<!-- Lendings -->
<div class="row">

    <div class="col-md-12">

        <!-- Categories -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans('klusbib::general.lendings') }}</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                        <table
                                data-cookie-id-table="dashLendingsSummary"
                                data-height="400"
                                data-side-pagination="server"
                                data-sort-order="desc"
                                data-sort-field="due_date"
                                id="dashLendingsSummary"
                                class="table table-striped snipe-table"
                                data-url="{{ route('api.klusbib.lendings.byduedate', ['sort' => 'due_date', 'order' => 'desc']) }}">

                            <thead>
                            <tr>
                                <th class="col-sm-3" data-visible="true" data-field="id" data-sortable="false">
                                    {{ trans('klusbib::admin/dashboard/general.lending_id') }}</th>
                                <th class="col-sm-3" data-visible="true" data-field="start_date" data-sortable="false">{{ trans('klusbib::admin/dashboard/general.start_date') }}</th>
                                <th class="col-sm-1" data-visible="true" data-field="due_date" data-sortable="false">{{ trans('klusbib::admin/dashboard/general.due_date') }}</th>
                                <th class="col-sm-1" data-visible="true" data-field="username" data-sortable="false">{{ trans('klusbib::admin/dashboard/general.user') }}</th>
                                <th class="col-sm-1" data-visible="true" data-field="tool.code" data-sortable="false">{{ trans('klusbib::admin/dashboard/general.tool_code') }}</th>
                            </tr>
                            </thead>
                        </table>
                        </div>
                    </div> <!-- /.col -->
                    <div class="col-md-12 text-center" style="padding-top: 10px;">
                        <a href="{{ route('hardware.index', array("status" => "Deployed") ) }}" class="btn btn-primary btn-sm" style="width: 100%">View All</a>
                    </div>
                </div> <!-- /.row -->

            </div><!-- /.box-body -->
        </div> <!-- /.box -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                    <table
                            data-cookie-id-table="dashCategorySummary"
                            data-height="400"
                            data-side-pagination="server"
                            data-sort-order="desc"
                            data-sort-field="assets_count"
                            id="dashCategorySummary"
                            class="table table-striped snipe-table"
                            data-url="{{ route('api.categories.index', ['sort' => 'assets_count', 'order' => 'asc']) }}">

                        <thead>
                        <tr>
                            <th class="col-sm-3" data-visible="true" data-field="name" data-formatter="categoriesLinkFormatter" data-sortable="true">{{ trans('general.name') }}</th>
                            <th class="col-sm-3" data-visible="true" data-field="category_type" data-sortable="true">{{ trans('general.type') }}</th>
                            <th class="col-sm-1" data-visible="true" data-field="assets_count" data-sortable="true"><i class="fa fa-barcode"></i></th>
                            <th class="col-sm-1" data-visible="true" data-field="accessories_count" data-sortable="true"><i class="fa fa-keyboard-o"></i></th>
                            <th class="col-sm-1" data-visible="true" data-field="consumables_count" data-sortable="true"><i class="fa fa-tint"></i></th>
                            <th class="col-sm-1" data-visible="true" data-field="components_count" data-sortable="true"><i class="fa fa-hdd-o"></i></th>
                            <th class="col-sm-1" data-visible="true" data-field="licenses_count" data-sortable="true"><i class="fa fa-floppy-o"></i></th>
                        </tr>
                        </thead>
                    </table>
                    </div>
                </div> <!-- /.col -->
                <div class="col-md-12 text-center" style="padding-top: 10px;">
                    <a href="{{ route('categories.index') }}" class="btn btn-primary btn-sm" style="width: 100%">View All</a>
                </div>
            </div> <!-- /.row -->

        </div><!-- /.box-body -->
    </div> <!-- /.box -->
    </div>
</div>

{{--@endif--}}


@stop

@section('moar_scripts')
@include ('partials.bootstrap-table', ['simple_view' => true, 'nopages' => true])

@if ($snipeSettings->load_remote=='1')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
@else
    <script src="{{ asset('js/plugins/chartjs/Chart.min.js') }}"></script>
@endif


<script nonce="{{ csrf_token() }}">

    var pieChartUserMembershipCanvas = $("#usersMembershipPieChart").get(0).getContext("2d");
    var pieChartUserMembership = new Chart(pieChartUserMembershipCanvas);
    var ctxUsersMembership = document.getElementById("usersMembershipPieChart");

    var myPieChartUserMembership = new Chart(ctxUsersMembership,{

        type: 'pie',
        data: {
            datasets: [{
                data: [ {{number_format($counts['membership']['regular'])}}, 
                  {{number_format($counts['membership']['renewal'])}},
                  {{number_format($counts['membership']['reduced_regular'])}},
                  {{number_format($counts['membership']['reduced_renewal'])}},
                  {{number_format($counts['membership']['org_regular'])}},
                  {{number_format($counts['membership']['org_renewal'])}},
                  {{number_format($counts['membership']['temporary'])}},
                ],
                backgroundColor: [
                    '#f56954',
                    '#00a65a',
                    '#f39c12',
                    '#00c0ef',
                    '#3c8dbc',
                    '#d2d6de',
                    '#0005dc',
                ]
                // backgroundColor: [
                //     window.chartColors.red,
                //     window.chartColors.orange,
                //     window.chartColors.yellow,
                //     window.chartColors.green,
                //     window.chartColors.blue,
                // ]
            }],

            // These labels appear in the legend and in the tooltips when hovering different arcs
            labels: [
                'Standaard',
                'Hernieuwing',
                'Standaard (UIT-pas)',
                'Hernieuwing (UIT-pas)',
                'Standaard (Vereniging)',
                'Hernieuwing (Vereniging)',
                'Proef'
            ]
        },
        options: pieOptions
    });

    var pieChartUserStatusCanvas = $("#usersStatusPieChart").get(0).getContext("2d");
    var pieChartUserStatus = new Chart(pieChartUserStatusCanvas);
    var ctxUsersStatus = document.getElementById("usersStatusPieChart");

    var myPieChartUserStatus = new Chart(ctxUsersStatus,{

        type: 'pie',
        data: {
            datasets: [{
                data: [ {{number_format($counts['user_active'])}},{{number_format($counts['user_expired'])}}, {{number_format($counts['user_deleted'])}}],
                backgroundColor: [
                    '#f56954',
                    '#00a65a',
                    '#f39c12',
                    '#00c0ef',
                    '#3c8dbc',
                    '#d2d6de',
                    '#0005dc',
                ]
            }],

            // These labels appear in the legend and in the tooltips when hovering different arcs
            labels: [
                'Actief',
                'Vervallen',
                'Verwijderd'
            ]
        },
        options: pieOptions
    });


    let barOptions = {
        scales: {
            xAxes: [{
                gridLines: {
                    offsetGridLines: true,
                }
            }],
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }]
        }
    };

    var barChartNewUsersCanvas = $("#newUsersBarChart").get(0).getContext("2d");
    var barChartNewUsers = new Chart(barChartNewUsersCanvas);
    var ctxNewUsers = document.getElementById("newUsersBarChart");
    var myNewUsersBarChart = new Chart(ctxNewUsers, {
        type: 'bar',
        data: {
            labels: [
                'Vorige maand',
                'Huidige maand'
            ],
            datasets: [{
                label: 'Totaal',
                backgroundColor: '#f39c12',
                barPercentage: 0.5,
                barThickness: 6,
                maxBarThickness: 8,
                minBarLength: 2,
                borderWidth: 1,
                data: [{{number_format($counts['new_user_prev_month'])}},{{number_format($counts['new_user_curr_month'])}}]
            }],
            options: barOptions,
        }
    });


    var barChartCheckoutCanvas = $("#checkoutBarChart").get(0).getContext("2d");
    var barChartCheckout = new Chart(barChartCheckoutCanvas);
    var ctxCheckout = document.getElementById("checkoutBarChart");
    var myCheckoutBarChart = new Chart(ctxCheckout, {
        type: 'bar',
        data: {
            labels: [
                'Vorige maand',
                'Huidige maand'
            ],
            datasets: [{
                label: 'Totaal',
                backgroundColor: '#f39c12',
                barPercentage: 0.5,
                barThickness: 6,
                maxBarThickness: 8,
                minBarLength: 2,
                borderWidth: 1,
                data: [{{number_format($counts['activity_co_prev_month'])}},{{number_format($counts['activity_co_curr_month'])}}]
            }],
            options: barOptions,
        }
    });


    var ctxLendings = document.getElementById('lendingsChart').getContext("2d")
    var myChart = new Chart(ctxLendings, {
        type: 'bar',
        options: barOptions
    });


    $.ajax({
        type: 'GET',
        url: '{{  route('api.klusbib.lendings.byduedate') }}',
        headers: {
            "X-Requested-With": 'XMLHttpRequest',
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        },

        dataType: 'json',
        success: function (data) {
            var ctx = new Chart(ctxLendings,{
                type: 'bar',
                data: data.chart,
                options: barOptions
            });
        },
        error: function (error) {
            console.log('error in lendings.byduedate: ' + error);
            }
        });

    var ctxLendingsByCat = document.getElementById('lendingsByCatChart').getContext("2d")
    let barOptionsLendingsByCat = {
        scales: {
            xAxes: [{
                gridLines: {
                    offsetGridLines: true,
                }
            }],
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                }
            }]
        }
    };
    var myChart = new Chart(ctxLendingsByCat, {
        type: 'bar',
        options: barOptionsLendingsByCat
    });


    $.ajax({
        type: 'GET',
        url: '{{  route('api.klusbib.lendings.bycategory') }}',
        headers: {
            "X-Requested-With": 'XMLHttpRequest',
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        },

        dataType: 'json',
        success: function (data) {
            var ctx = new Chart(ctxLendingsByCat,{
                type: 'bar',
                data: data.chart,
                options: barOptions
            });
        },
        error: function (error) {
            console.log('error in lendings.bycategory: ' + error);
        }
    });


</script>


@stop
