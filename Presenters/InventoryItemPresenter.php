<?php

namespace Modules\Klusbib\Presenters;

use App\Helpers\Helper;
use App\Models\Setting;
use App\Presenters\Presenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class InventoryItemPresenter
 * @package App\Presenters
 */
class InventoryItemPresenter extends Presenter
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
                "title" => trans('klusbib::admin/deliveries/table.item_id'),
                "visible" => true
            ],
            [
                "field" => "item_type",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.item_type'),
                "visible" => true,
            ],
            [
                "field" => "sku",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.sku'),
                "visible" => true,
            ],
            [
                "field" => "name",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.name'),
                "visible" => true,
            ],
            [
                "field" => "brand",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.brand'),
                "visible" => true,
            ],
            [
                "field" => "description",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.description'),
                "visible" => true,
            ],
            [
                "field" => "keywords",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.keywords'),
                "visible" => true,
            ],
            [
                "field" => "is_active",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/deliveries/table.is_active'),
                "visible" => true,
                'formatter' => 'trueFalseFormatter'
            ],
//            [
//                "field" => "created_at",
//                "searchable" => false,
//                "sortable" => true,
//                "switchable" => true,
//                "title" => trans('general.created_at'),
//                "visible" => false,
//                'formatter' => 'dateDisplayFormatter'
//            ],
            [
                "field" => "actions",
                "searchable" => false,
                "sortable" => false,
                "switchable" => false,
                "title" => trans('table.actions'),
                "visible" => true,
                "formatter" => "deliveryItemsActionsFormatter",
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
