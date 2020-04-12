@include ('partials.bootstrap-table')

<script>
    var extraFormatters = [
        'reservations',
        'deliveries'
    ];
    for (var i in extraFormatters) {
        window[extraFormatters[i] + 'LinkFormatter'] = genericRowLinkFormatter(extraFormatters[i]);
        window[extraFormatters[i] + 'LinkObjFormatter'] = genericColumnObjLinkFormatter(extraFormatters[i]);
        window[extraFormatters[i] + 'ActionsFormatter'] = genericActionsFormatter(extraFormatters[i]);
        window[extraFormatters[i] + 'InOutFormatter'] = genericCheckinCheckoutFormatter(extraFormatters[i]);
    }
    // Make the edit/delete buttons
    function reservationActionsFormatter(destination) {
        return function (value,row) {

            var actions = '<nobr>';

            // Add some overrides for any funny urls we have
            var dest = destination;

            if ((row.available_actions) && (row.available_actions.clone === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/clone" class="btn btn-sm btn-info" data-tooltip="true" title="Clone"><i class="fa fa-copy"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.update === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/edit" class="btn btn-sm btn-warning" data-tooltip="true" title="Update"><i class="fa fa-pencil"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.cancel === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/cancel" class="btn btn-sm btn-danger    " data-tooltip="true" title="Cancel"><i class="fa fa-close"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.delete === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '" '
                    + ' class="btn btn-danger btn-sm delete-asset"  data-tooltip="true"  '
                    + ' data-toggle="modal" '
                    + ' data-content="{{ trans('general.sure_to_delete') }} ' + row.name + '?" '
                    + ' data-title="{{  trans('general.delete') }}" onClick="return false;">'
                    + '<i class="fa fa-trash"></i></a>&nbsp;';
            } else {
                actions += '<a class="btn btn-danger btn-sm delete-asset disabled" onClick="return false;"><i class="fa fa-trash"></i></a>&nbsp;';
            }

            if ((row.available_actions) && (row.available_actions.restore === true)) {
                actions += '<a href="{{ url('/') }}/' + dest + '/' + row.id + '/restore" class="btn btn-sm btn-warning" data-tooltip="true" title="Restore"><i class="fa fa-retweet"></i></a>&nbsp;';
            }

            actions +='</nobr>';
            return actions;

        };
    }
    window['reservationsActionsFormatter'] = reservationActionsFormatter('reservations');
</script>
