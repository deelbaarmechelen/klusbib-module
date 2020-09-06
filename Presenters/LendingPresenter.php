<?php

namespace Modules\Klusbib\Presenters;

use App\Helpers\Helper;
use App\Models\Setting;
use App\Presenters\Presenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class LendingPresenter
 * @package App\Presenters
 */
class LendingPresenter extends Presenter
{

/*
 * {"total":3,"rows":[
 * {"id":3,"tool_id":"2","tool":{"id":2,"name":"KB-000-20-002","type":"TOOL"},"user":{"id":1,"name":"admin admin"},"user_id":"1","title":"",
 * "startsAt":{"date":"2020-07-05","formatted":"2020-07-05"},"dueAt":{"date":"2020-07-12","formatted":"2020-07-12"},"returnedAt":null,
 * "comment":null,"createdBy":null,"created_at":null,"updated_at":null,"available_actions":[]},
 * {"id":2,"tool_id":"2","tool":{"id":2,"name":"KB-000-20-002","type":"TOOL"},"user":{"id":1,"name":"admin admin"},"user_id":"1","title":"",
 * "startsAt":{"date":"2020-07-06","formatted":"2020-07-06"},"dueAt":{"date":"2020-07-07","formatted":"2020-07-07"},"returnedAt":{"date":"2020-07-06","formatted":"2020-07-06"},
 * "comment":null,"createdBy":null,"created_at":null,"updated_at":null,"available_actions":[]},
 * {"id":1,"tool_id":"1","tool":{"id":1,"name":"KB-000-20-001","type":"TOOL"},"user":{"id":3,"name":"Dummy Dummy"},"user_id":"3","title":"",
 * "startsAt":{"date":"2020-07-01","formatted":"2020-07-01"},"dueAt":{"date":"2020-07-07","formatted":"2020-07-07"},"returnedAt":null,
 * "comment":null,"createdBy":null,"created_at":null,"updated_at":null,"available_actions":[]}]}
 */
    /**
     * Json Column Layout for bootstrap table
     * @return string
     */
    public static function dataTableLayout()
    {
        $layout = [
//            [
//                "field" => "checkbox",
//                "checkbox" => true
//            ],
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
                "title" => trans('klusbib::admin/lendings/table.tool_id'),
                "visible" => false
            ],
            [
                "field" => "tool",
                "searchable" => false,
                "sortable" => false,
                "switchable" => true,
                "title" => trans('klusbib::admin/lendings/table.tool'),
                "visible" => true,
                "formatter" => "hardwareLinkObjFormatter"
            ],
            [
                "field" => "user_id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/lendings/table.user_id'),
                "visible" => false
            ],
            [
                "field" => "user",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/lendings/table.name'),
                "visible" => true,
                "formatter" => "usersLinkObjFormatter"
            ],
            [
                "field" => "startsAt",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/lendings/table.startsAt'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "dueAt",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/lendings/table.dueAt'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "returnedAt",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/lendings/table.returnedAt'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
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
                "field" => "updated_at",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.updated_at'),
                "visible" => false,
                'formatter' => 'dateDisplayFormatter'
            ],
//            [
//                "field" => "actions",
//                "searchable" => false,
//                "sortable" => false,
//                "switchable" => false,
//                "title" => trans('table.actions'),
//                "visible" => true,
//                "formatter" => "lendingsActionsFormatter",
//            ]
        ];

        return json_encode($layout);
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('lendings.show', $this->id);
    }

    public function glyph()
    {
        return '<i class="fa fa-calendar"></i>';
    }
}
