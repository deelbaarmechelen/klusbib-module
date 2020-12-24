<?php

namespace Modules\Klusbib\Presenters;

use App\Helpers\Helper;
use App\Models\Setting;
use App\Presenters\Presenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class MembershipPresenter
 * @package App\Presenters
 */
class MembershipPresenter extends Presenter
{

/*
 * {"total":2,"rows":[{"id":70,
 * "user":{"id":10,"name":"Test Lidmaatschap"},"user_id":"8","state":"ACTIVE","subscription_id":70,
 * "subscription":{"id":1,"name":"Regular","price":"30.00","duration":"365"},"start_at":{"date":"2020-12-11","formatted":"2020-12-11"},"expires_at":{"date":"2021-12-30","formatted":"2021-12-30"},
 * "last_payment_mode":"CASH","comment":null,"available_actions":[]},
 * {"id":72,"user":{"id":10,"name":"Test Lidmaatschap"},"user_id":"8","state":"PENDING","subscription_id":72,
 * "subscription":{"id":3,"name":"Renewal","price":"20.00","duration":"365"},"start_at":{"date":"2020-12-11","formatted":"2020-12-11"},"expires_at":{"date":"2022-12-30","formatted":"2022-12-30"},
 * "last_payment_mode":"TRANSFER","comment":null,"available_actions":[]}]}
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
                "field" => "user_id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/memberships/table.user_id'),
                "visible" => false
            ],
            [
                "field" => "user",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/memberships/table.name'),
                "visible" => true,
                "formatter" => "usersLinkObjFormatter"
            ],
            [
                "field" => "start_at",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/memberships/general.startAt'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "expires_at",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/memberships/general.expiresAt'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "state",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/memberships/general.state'),
                "visible" => true,
            ],
            [
                "field" => "subscription_id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/memberships/table.subscriptionId'),
                "visible" => true,
            ],
            [
                "field" => "subscription",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/memberships/table.subscription'),
                "visible" => true,
                "formatter" => "subscriptionNameObjFormatter"
            ],
            [
                "field" => "last_payment_mode",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/memberships/table.lastPaymentMode'),
                "visible" => true,
            ],
            [
                "field" => "comments",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.notes'),
                "visible" => true,
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
//            [
//                "field" => "updated_at",
//                "searchable" => false,
//                "sortable" => true,
//                "switchable" => true,
//                "title" => trans('general.updated_at'),
//                "visible" => false,
//                'formatter' => 'dateDisplayFormatter'
//            ],
//            [
//                "field" => "actions",
//                "searchable" => false,
//                "sortable" => false,
//                "switchable" => false,
//                "title" => trans('table.actions'),
//                "visible" => true,
//                "formatter" => "membershipsActionsFormatter",
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
        return route('memberships.show', $this->id);
    }

    public function glyph()
    {
        return '<i class="fa fa-calendar"></i>';
    }
}
