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
                "visible" => true
            ],
            [
                "field" => "tool_id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.tool_id'),
                "visible" => false
            ],
            [
                "field" => "tool",
                "searchable" => false,
                "sortable" => false,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.tool'),
                "visible" => true,
                "formatter" => "hardwareLinkObjFormatter"
            ],
            [
                "field" => "user_id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.user_id'),
                "visible" => false
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
                "searchable" => false,
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
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.startsAt'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],            [
                "field" => "endsAt",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/reservations/table.endsAt'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],            [
                "field" => "comments",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.notes'),
                "visible" => true,
            ],
            [
                "field" => "created_at",
                "searchable" => false,
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
