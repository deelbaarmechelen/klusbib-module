<?php

namespace Modules\Klusbib\Presenters;

use App\Helpers\Helper;
use App\Models\Setting;
use App\Presenters\Presenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class ReservationPresenter
 * @package App\Presenters
 */
class ReservationPresenter extends Presenter
{


    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
            [
                "field" => "checkbox",
                "checkbox" => true
            ],
            [
                "field" => "id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::general.id'),
                "visible" => false
            ],
//            [
//                "field" => "reservation",
//                "searchable" => true,
//                "sortable" => true,
//                "switchable" => true,
//                "title" => trans('klusbib::admin/reservations/table.reservation_id'),
//                "visible" => true
//            ],
            [
                "field" => "tool_id",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.tool_id'),
                "visible" => true,
                "formatter" => "hardwareLinkFormatter"
            ],
            [
                "field" => "tool",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.tool'),
                "visible" => true,
                "formatter" => "hardwareLinkObjFormatter"
            ],
            [
                "field" => "user_id",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.user_id'),
                "visible" => true,
                "formatter" => "usersLinkFormatter"
            ],
            [
                "field" => "user",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.name'),
                "visible" => true,
                "formatter" => "usersLinkObjFormatter"
            ],
            [
                "field" => "state",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.state'),
                "visible" => true,
            ],
//            [
//                "field" => "location",
//                "searchable" => true,
//                "sortable" => true,
//                "switchable" => true,
//                "title" => trans('admin/users/table.location'),
//                "visible" => true,
//                "formatter" => "locationsLinkObjFormatter"
//            ],
            [
                "field" => "startsAt",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.startsAt'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],            [
                "field" => "endsAt",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.endsAt'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],            [
                "field" => "comments",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.notes'),
                "visible" => true,
            ],
            [
                "field" => "created_at",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.created_at'),
                "visible" => false,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "actions",
                "searchable" => false,
                "sortable" => false,
                "switchable" => false,
                "title" => trans('table.actions'),
                "visible" => true,
                "formatter" => "reservationsActionsFormatter",
            ]
        ];

        return json_encode($layout);
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('reservations.show', $this->id);
    }

    public function glyph()
    {
        return '<i class="fa fa-calendar"></i>';
    }
}
