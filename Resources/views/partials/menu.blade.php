
@section('klusbib-menu')

@if ( \Illuminate\Support\Facades\Route::has('klusbib.home')
        && !is_null(Auth::user()) )
<li class="treeview active }}">
    <a href="#"><i class="fa fa-wrench"></i>
        <span>{{ trans('klusbib::general.name') }}</span>
        <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu">
        {{--<li>--}}
        {{--<a href="{{ url('klusbib') }}">--}}
        {{--{{ trans('general.list_all') }}--}}
        {{--</a>--}}
        {{--</li>--}}
        <li>
            <a href="{{ route('klusbib.home') }}">
                <i class="fa fa-dashboard"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @if (\Illuminate\Support\Facades\Route::has('klusbib.users.index')
        && Auth::user()->can('index',\Modules\Klusbib\Models\Api\User::class))
            <li>
                <a href="{{ route('klusbib.users.index') }}">
                    <i class="fa fa-users"></i>
                    {{ trans('general.people') }}
                </a>
            </li>
        @endif
        @if (\Illuminate\Support\Facades\Route::has('klusbib.reservations.index')
        && Auth::user()->can('index',\Modules\Klusbib\Models\Api\Reservation::class))
            <li>
                <a href="{{ route('klusbib.reservations.index') }}">
                    <i class="fa fa-calendar"></i>
                    <span>Reservaties</span>
                </a>
            </li>
        @endif
    </ul>
</li>
@endif
@endsection