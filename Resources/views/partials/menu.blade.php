
@section('klusbib-menu')

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
        @if (\Illuminate\Support\Facades\Route::has('klusbib.home'))
            <li>
                <a href="{{ route('klusbib.home') }}">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        @endif
        @if (\Illuminate\Support\Facades\Route::has('klusbib.users.index'))
            <li>
                <a href="{{ route('klusbib.users.index') }}">
                    <i class="fa fa-users"></i>
                    {{ trans('general.people') }}
                </a>
            </li>
        @endif
        @if (\Illuminate\Support\Facades\Route::has('klusbib.reservations.index'))
            <li>
                <a href="{{ route('klusbib.reservations.index') }}">
                    <i class="fa fa-calendar"></i>
                    <span>Reservaties</span>
                </a>
            </li>
        @endif
        @if (\Illuminate\Support\Facades\Route::has('klusbib.deliveries.index'))
            <li>
                <a href="{{ route('klusbib.deliveries.index') }}">
                    <i class="fa fa-bicycle"></i>
                    <span>Deliveries</span>
                </a>
            </li>
        @endif
        @if (\Illuminate\Support\Facades\Route::has('klusbib.memberships.index'))
            <li>
                <a href="{{ route('klusbib.memberships.index') }}">
                    <i class="fa fa-id-card"></i>
                    <span>Lidmaatschappen</span>
                </a>
            </li>
        @endif
        @if (\Illuminate\Support\Facades\Route::has('klusbib.payments.index'))
            <li>
                <a href="{{ route('klusbib.payments.index') }}">
                    <i class="fa fa-money"></i>
                    <span>Betalingen</span>
                </a>
            </li>
        @endif
        @if (\Illuminate\Support\Facades\Route::has('klusbib.lendings.index'))
            <li>
                <a href="{{ route('klusbib.lendings.index') }}">
                    <i class="fa"></i>
                    <span>Ontleningen</span>
                </a>
            </li>
        @endif
    </ul>
</li>
@endsection