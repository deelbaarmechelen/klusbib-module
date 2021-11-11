@include ('partials.bootstrap-table')

<script>


    // Make the show/edit/delete buttons
    function customApiUsersActionsFormatter(destination) {
        return function (value,row) {

            var actions = '<nobr>';

            // Add some overrides for any funny urls we have
            var dest = 'klusbib/' + destination;

            if ((row.available_actions) && (row.available_actions.clone === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.user_id + '/clone" class="btn btn-sm btn-info" data-tooltip="true" title="Clone"><i class="fa fa-copy"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.show === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.user_id + '" class="btn btn-sm btn-primary" data-tooltip="true" title="Show"><i class="fa fa-eye"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.update === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.user_id + '/edit" class="btn btn-sm btn-warning" data-tooltip="true" title="Update"><i class="fa fa-pencil"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.delete === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.user_id + '" '
                    + ' class="btn btn-danger btn-sm delete-asset"  data-tooltip="true"  '
                    + ' data-toggle="modal" '
                    + ' data-content="{{ trans('general.sure_to_delete') }} ' + row.name + '?" '
                    + ' data-title="{{  trans('general.delete') }}" onClick="return false;">'
                    + '<i class="fa fa-trash"></i></a>&nbsp;';
            } else {
                actions += '<a class="btn btn-danger btn-sm delete-asset disabled" onClick="return false;"><i class="fa fa-trash"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.restore === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.user_id + '/restore" class="btn btn-sm btn-warning" data-tooltip="true" title="Restore"><i class="fa fa-retweet"></i></a>&nbsp;';
            }

            actions +='</nobr>';
            return actions;

        };
    }
    window['apiUsersActionsFormatter'] = customApiUsersActionsFormatter('users');

    // Make the edit/delete buttons
    function customKlusbibActionsFormatter(destination) {
        return function (value,row) {

            var actions = '<nobr>';

            // Add some overrides for any funny urls we have
            var dest = 'klusbib/' + destination;

            if ((row.available_actions) && (row.available_actions.clone === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/clone" class="btn btn-sm btn-info" data-tooltip="true" title="Clone"><i class="fa fa-copy"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.show === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '" class="btn btn-sm btn-primary" data-tooltip="true" title="Show"><i class="fa fa-eye"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.update === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/edit" class="btn btn-sm btn-warning" data-tooltip="true" title="Update"><i class="fa fa-pencil"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.confirm === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/confirm" class="btn btn-sm btn-success" data-tooltip="true" title="Confirm"><i class="fa fa-check"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.cancel === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/cancel" class="btn btn-sm btn-danger" data-tooltip="true" title="Cancel"><i class="fa fa-close"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.delete === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '" '
                    + ' class="btn btn-danger btn-sm delete-asset"  data-tooltip="true"  '
                    + ' data-toggle="modal" '
                    + ' data-content="{{ trans('general.sure_to_delete') }} ?" '
                    + ' data-title="{{  trans('general.delete') }}" onClick="return false;">'
                    + '<i class="fa fa-trash"></i></a>&nbsp;';
            } else {
                actions += '<a class="btn btn-danger btn-sm delete-asset disabled" onClick="return false;"><i class="fa fa-trash"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.restore === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/restore" class="btn btn-sm btn-warning" data-tooltip="true" title="Restore"><i class="fa fa-retweet"></i></a>&nbsp;';
            }
            console.log(actions);
            actions +='</nobr>';
            return actions;

        };
    }
    function customHardwareCheckinCheckoutFormatter(destination) {
        return function (value,row) {

            // The user is allowed to check items out, AND the item is deployable
            if ((row.available_actions.checkout == true) && (row.user_can_checkout == true) && ((!row.asset_id) && (!row.assigned_to))) {
                return '<a href="{{ url('/') }}/' + destination + '/' + row.id + '/checkout" class="btn btn-sm bg-maroon" data-tooltip="true" title="Check this item out">{{ trans('general.checkout') }}</a>';

                // The user is allowed to check items out, but the item is not deployable
            } else if (((row.user_can_checkout == false)) && (row.available_actions.checkout == true) && (!row.assigned_to)) {
                return '<div  data-tooltip="true" title="This item has a status label that is undeployable and cannot be checked out at this time."><a class="btn btn-sm bg-maroon disabled">{{ trans('general.checkout') }}</a></div>';

                // The user is allowed to check items in
            } else if (row.available_actions.checkin == true)  {
                if (row.assigned_to) {
                    return '<a href="{{ url('/') }}/' + destination + '/' + row.id + '/checkin" class="btn btn-sm bg-purple" data-tooltip="true" title="Check this item in so it is available for re-imaging, re-issue, etc.">{{ trans('general.checkin') }}</a>'
                         + '<a href="{{ url('/') }}/' + destination + '/' + row.id + '/extend" class="btn btn-sm bg-teal" data-tooltip="true" title="Extend expected checkin date">{{ trans('klusbib::general.extend') }}</a>';
                } else if (row.assigned_pivot_id) {
                    return '<a href="{{ url('/') }}/' + destination + '/' + row.assigned_pivot_id + '/checkin" class="btn btn-sm bg-purple" data-tooltip="true" title="Check this item in so it is available for re-imaging, re-issue, etc.">{{ trans('general.checkin') }}</a>' +
                           '<a href="{{ url('/') }}/' + destination + '/' + row.id + '/extend" class="btn btn-sm bg-teal" data-tooltip="true" title="Extend expected checkin date">{{ trans('klusbib::general.extend') }}</a>';
                }
            }
        }
    }

    function customMembershipsActionsFormatter(destination) {
        return function (value,row) {

            var actions = '<nobr>';
            var dest = 'klusbib/' + destination;

            if ((row.available_actions) && (row.available_actions.confirm === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/confirm" class="btn btn-sm btn-success" data-tooltip="true" title="Confirm"><i class="fa fa-check"></i></a>&nbsp;';
            }
            if ((row.available_actions) && (row.available_actions.cancel === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/cancel" '
                    + ' class="btn btn-danger btn-sm delete-asset"  data-tooltip="true"  '
                    + ' data-toggle="modal" '
                    + ' data-content="{{ trans('klusbib::admin/memberships/general.sure_to_decline') }} ?" '
                    + ' data-title="{{  trans('klusbib::admin/memberships/general.decline') }}" onClick="return false;">'
                    + '<i class="fa fa-close"></i></a>&nbsp;';
            }
            actions +='</nobr>';
            return actions;
        }
    }

    var extraFormatters = [
        'reservations',
        'deliveries',
        'memberships',
        'payments'
    ];
    for (var i in extraFormatters) {
        window[extraFormatters[i] + 'LinkFormatter'] = genericRowLinkFormatter(extraFormatters[i]);
        window[extraFormatters[i] + 'LinkObjFormatter'] = genericColumnObjLinkFormatter(extraFormatters[i]);
        window[extraFormatters[i] + 'ActionsFormatter'] = genericActionsFormatter(extraFormatters[i]);
        window[extraFormatters[i] + 'InOutFormatter'] = genericCheckinCheckoutFormatter(extraFormatters[i]);
    }

    window['hardwareInOutFormatter'] = customHardwareCheckinCheckoutFormatter('hardware');
    window['membershipsActionsFormatter'] = customMembershipsActionsFormatter('memberships');
    window['reservationsActionsFormatter'] = customKlusbibActionsFormatter('reservations');
    window['deliveriesActionsFormatter'] = customKlusbibActionsFormatter('deliveries');
    @isset($deliveryId)
        window['deliveryItemsActionsFormatter'] = customKlusbibActionsFormatter('deliveries/{{$deliveryId}}/items');
    @endisset

    function subscriptionNameObjFormatter() {
        return function (value,row) {

            if ((value) && (value.name)) {
                var name = value.name;
                // var name = trans('klusbib::types/membershiptypes.' . value.name );
                if ((value.price) && (value.duration)) {
                    return '<span>' + name + ' (' + value.price + ' eur - ' + value.duration + 'd)</span>';
                }
                return '<span>' + name +'</span>';
            }
            return '';
        }
    }
    window['subscriptionNameObjFormatter'] = subscriptionNameObjFormatter();


</script>
