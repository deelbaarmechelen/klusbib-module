<?php

namespace Modules\Klusbib\Presenters;

use App\Helpers\Helper;
use App\Models\Setting;
use App\Presenters\Presenter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Class UserPresenter
 * @package App\Presenters
 */
class UserPresenter extends Presenter
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
                "sortable" => false,
                "switchable" => true,
                "title" => trans('klusbib::general.id'),
                "visible" => false
            ],
            [
                "field" => "user_id",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.user_id'),
                "visible" => true
            ],
            [
                "field" => "avatar",
                "searchable" => false,
                "sortable" => false,
                "switchable" => true,
                "title" => 'Avatar',
                "visible" => false,
                "formatter" => "imageFormatter"
            ],
            [
                "field" => "name",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('klusbib::admin/users/table.name'),
                "visible" => true,
                "formatter" => "usersLinkFormatter"
            ],
            [
                "field" => "firstname",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('klusbib::admin/users/table.firstname'),
                "visible" => false,
                "formatter" => "usersLinkFormatter"
            ],
            [
                "field" => "lastname",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('klusbib::admin/users/table.lastname'),
                "visible" => false,
                "formatter" => "usersLinkFormatter"
            ],
            [
                "field" => "state",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.membership_state'),
                "visible" => true,
            ],
            [
                "field" => "membership_start_date",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.membership_start_date'),
                "visible" => false,
                "formatter" => "dateDisplayFormatter"
            ],
            [
                "field" => "membership_end_date",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.membership_expiry'),
                "visible" => true,
                "formatter" => "dateDisplayFormatter"
            ],
            [
                "field" => "email",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.email'),
                "visible" => true,
                "formatter" => "emailFormatter"
            ],
            [
                "field" => "email_state",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.email_state'),
                "visible" => true,
//                "formatter" => "emailFormatter"
            ],
            [
                "field" => "phone",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.phone'),
                "visible" => true,
                "formatter"    => "phoneFormatter",
            ],
            [
                "field" => "address",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.address'),
                "visible" => false,
            ],
            [
                "field" => "postal_code",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.postal_code'),
                "visible" => false,
            ],
            [
                "field" => "city",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.city'),
                "visible" => false,
            ],
            [
                "field" => "role",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.role'),
                "visible" => false,
            ],
            [
                "field" => "payment_mode",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.payment_mode'),
                "visible" => false,
            ],
            [
                "field" => "accept_terms_date",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('klusbib::admin/users/table.accept_terms_date'),
                "visible" => false,
                "formatter" => "dateDisplayFormatter"
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
//            [
//                "field" => "assets_count",
//                "searchable" => false,
//                "sortable" => true,
//                "switchable" => true,
//                "title" => ' <span class="hidden-md hidden-lg">Assets</span>'
//                            .'<span class="hidden-xs"><i class="fa fa-barcode fa-lg"></i></span>',
//                "visible" => true,
//            ],
//            [
//                "field" => "consumables_count",
//                "searchable" => false,
//                "sortable" => true,
//                "switchable" => true,
//                "title" => ' <span class="hidden-md hidden-lg">Consumables</span>'
//                    .'<span class="hidden-xs"><i class="fa fa-tint fa-lg"></i></span>',
//                "visible" => true,
//            ],
//            [
//                "field" => "accessories_count",
//                "searchable" => false,
//                "sortable" => true,
//                "switchable" => true,
//                "title" => ' <span class="hidden-md hidden-lg">Accessories</span>'
//                    .'<span class="hidden-xs"><i class="fa fa-keyboard-o fa-lg"></i></span>',
//                "visible" => true,
//            ],
//            [
//                "field" => "notes",
//                "searchable" => true,
//                "sortable" => true,
//                "switchable" => true,
//                "title" => trans('general.notes'),
//                "visible" => true,
//            ],
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
                "formatter" => "apiUsersActionsFormatter",
            ]
        ];

        return json_encode($layout);
    }


    public function emailLink()
    {
        if ($this->email) {
            return '<a href="mailto:'.$this->email.'">'.$this->email.'</a>'
                .'<a href="mailto:'.$this->email.'" class="hidden-xs hidden-sm"><i class="fa fa-envelope"></i></a>';
        }
        return '';
    }
    /**
     * Returns the user full name, it simply concatenates
     * the user first and last name.
     *
     * @return string
     */
    public function fullName()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Standard accessor.
     * @TODO Remove presenter::fullName() entirely?
     * @return string
     */
    public function name()
    {
        return $this->fullName();
    }
    /**
     * Returns the user Gravatar image url.
     *
     * @return string
     */
    public function gravatar()
    {

        if ($this->avatar) {
            return config('app.url').'/uploads/avatars/'.$this->avatar;
        }

        if ((Setting::getSettings()->load_remote=='1') && ($this->email!='')) {
            $gravatar = md5(strtolower(trim($this->email)));
            return "//gravatar.com/avatar/".$gravatar;
        }

        // Set a fun, gender-neutral default icon
        return url('/').'/img/default-sm.png';

    }

    /**
     * Formatted url for use in tables.
     * @return string
     */
    public function nameUrl()
    {
        return (string) link_to_route('users.show', $this->fullName(), $this->id);
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('users.show', $this->id);
    }

    public function glyph()
    {
        return '<i class="fa fa-user"></i>';
    }
}
