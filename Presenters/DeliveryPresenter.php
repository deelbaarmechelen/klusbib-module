<?php

namespace Modules\Klusbib\Presenters;

use App\Helpers\Helper;
use App\Models\Setting;
use App\Presenters\Presenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class DeliveryPresenter
 * @package App\Presenters
 */
class DeliveryPresenter extends Presenter
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
                "title" => trans('klusbib::admin/deliveries/table.delivery_id'),
                "visible" => true
            ],
            [
                "field" => "tool_id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.tool_id'),
                "visible" => false
            ],
            [
                "field" => "tool",
                "searchable" => false,
                "sortable" => false,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.tool'),
                "visible" => true,
                "formatter" => "hardwareLinkObjFormatter"
            ],
            [
                "field" => "user_id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.user_id'),
                "visible" => false
            ],
            [
                "field" => "user",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.name'),
                "visible" => true,
                "formatter" => "usersLinkObjFormatter"
            ],
            [
                "field" => "type",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/general.type'),
                "visible" => true,
            ],
            [
                "field" => "state",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/general.state'),
                "visible" => true,
            ],
            [
                "field" => "pick_up_date",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/general.pick_up_date'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "pick_up_address",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/general.pick_up_address'),
                "visible" => false,
            ],
            [
                "field" => "drop_off_date",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/general.drop_off_date'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "drop_off_address",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/general.drop_off_address'),
                "visible" => false,
            ],
            [
                "field" => "price",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/general.price'),
                "visible" => true,
            ],
            [
                "field" => "consumers",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/general.consumers'),
                "visible" => true,
            ],
            [
                "field" => "comment",
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
                "formatter" => "deliveriesActionsFormatter",
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
        return route('deliveries.show', $this->id);
    }

    public function glyph()
    {
        return '<i class="fa fa-calendar"></i>';
    }
}
