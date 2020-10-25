<?php
namespace Modules\Klusbib\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Group;
use Artisan;
use Auth;
use Config;
use Crypt;
use DB;
use Gate;
use HTML;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Input;
use Lang;
use Mail;
use Modules\Klusbib\Api\Client;
use Modules\Klusbib\Http\KlusbibApi;
use Modules\Klusbib\Models\Api\User;
use Str;
use Torann\RemoteModel\Model;
use URL;
use View;

/**
 * This controller handles all actions related to Users for
 * the Snipe-IT Asset Management application. (Klusbib extension module)
 *
 */


class UsersController extends Controller
{

    use AuthorizesRequests;

    private $apiClient;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(Client $apiClient)
    {
        Log::debug( "API client injected in UsersController - hash: " . \spl_object_hash($apiClient) );
        $this->apiClient = $apiClient;
    }

    /**
    * Returns a view that invokes the ajax tables which actually contains
    * the content for the users listing, which is generated in getDatatable().
    *
    * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
//        $this->authorize('index', User::class);
        return view('klusbib::users/index');
    }

    public function create()
    {
        Log::debug("Klusbib - Creating new user");

//        $this->authorize('create', User::class);
        $user = new User();

        return view('klusbib::users/new')->with('item', $user);
    }

    /**
     * Validates and stores the user form data submitted from the new
     * user form.
     *
     * @see UsersController::create() method that provides the form view
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Log::debug("Klusbib - Store new user");
        Model::getClient()->updateToken($request->session());
//        $this->authorize('create', User::class);
        $enrolmentType = $request->input('membership_type');

        // create a new model instance
        $user = new User();
        // Save the user data
        $user->firstname             = $request->input('firstname');
        $user->lastname              = $request->input('lastname');
        $user->role                  = $request->input('role');
        $user->state                 = User::STATE_DISABLED;
        $user->email                 = $request->input('email');
        $user->email_state           = $request->input('email_state');
        $user->phone                 = $request->input('phone');
        $user->mobile                = $request->input('mobile');
        $user->address               = $request->input('address');
        $user->city                  = $request->input('city');
        $user->postal_code           = $request->input('postal_code');
        $user->registration_number   = $request->input('registration_number');
        $user->comment               = $request->input('comment');
        Log::info('User: ' . \json_encode($user));

        // TODO: block possibility to create enrolment with payment mode MOLLIE
        if ($user->save()) {
            if ($enrolmentType === "NONE") {
                // no enrolment -> simply save user
                return redirect()->route("klusbib.users.index")->with('success', trans('klusbib::admin/users/message.create.success'));
            } else {
                $now = new \DateTime();
                $orderId = $user->employee_num . '-' . $now->format('YmdHis');

                // Send enrolment request to create membership
                $params = array(
                    'membership_start_date' => $request->input('membership_start_date'),
                    'membership_end_date'   => $request->input('membership_end_date'),
                    'paymentMode'           => $request->input('payment_mode'),
                    //paymentMean -> only used for Mollie payment mode, which is not available from Snipe
                    'accept_terms_date'     => $request->input('accept_terms_date'),
                    'enrolment_type'        => $enrolmentType,
                    'orderId'               => $orderId,
                    'userId'                => $user->employee_num
                );

                if ($this->apiClient->api('enrolment')->request($params)) {
                    return redirect()->route("klusbib.users.index")->with('success', trans('klusbib::admin/users/message.create.success'));
                } else {
                    $errorMessage = trans('klusbib::admin/users/message.enrolment.error');
                    return redirect()->back()->withInput()->with('error', $errorMessage);
                }
            }
        }
        $errorMessage = trans('klusbib::admin/users/message.create.error');
        $errors = Arr::get($user->getClientError(), 'errors');
        if (is_array($errors)) {
            $errorMessage .= " (API fout: ";
            foreach($errors as $key => $value) {
                if (\is_string($key)) {
                    $errorMessage .= $key . ": ";
                }
                $errorMessage .= $value;
            }
            $errorMessage .= ")";
        }
        // Show generic failure message
        return redirect()->back()->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Returns a view that displays the edit user form
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @param $permissions
     * @return View
     * @internal param int $id
     */
    public function edit($id = null)
    {
        if (is_null($item = User::find($id))) {
            return redirect()->route('klusbib.users.index')->with('error', trans('klusbib::admin/users/message.does_not_exist'));
        }
        Log::debug("User found: " . \json_encode($item));
        // {"user_id":2,"user_ext_id":"2","state":"DELETED","firstname":"Dummy","lastname":"Dummy","email":"bernard@klusbib.be",
        //"email_state":"CONFIRM_EMAIL","role":"member","membership_start_date":"2020-06-25","membership_end_date":"2021-06-25","birth_date":null,
        //"address":null,"postal_code":null,"city":null,"phone":null,"mobile":null,"registration_number":null,"payment_mode":"STROOM",
        //"accept_terms_date":"2020-06-25","created_at":"{\"date\":\"2020-06-25 00:25:19.000000\",\"timezone_type\":3,\"timezone\":\"Europe\\\/Berlin\"}"
        //,"updated_at":"{\"date\":\"2020-06-25 00:33:44.000000\",\"timezone_type\":3,\"timezone\":\"Europe\\\/Berlin\"}","reservations":"[]"}
        $item->id = $item->user_id; // view expects id to be set to distinguish between create (POST) and update (PUT)

        // TODO: set correct membership type
        $item->membership_type = "NONE";
        return view('klusbib::users/edit', compact('item'));

    }

    /**
     * Validates and stores the user form data submitted from the edit
     * user form.
     *
     * @see UsersController::getEdit() method that provides the form view
     * @param Request $request
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $userId = null)
    {
        Model::getClient()->updateToken($request->session());
        Log::debug("Klusbib - Update user");

        if (is_null($user = User::find($userId))) {
            return redirect()->route('klusbib.users.index')->with('error', trans('klusbib::admin/users/message.does_not_exist'));
        }
        Log::info('User exists: ' . $user->exists);
//        $this->authorize('update', $user);

        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->role = $request->input('role');
        $user->state             = $request->input('state');
        $user->email = $request->input('email');
        $user->email_state = $request->input('email_state');
        $user->phone = $request->input('phone');
        $user->mobile = $request->input('mobile');
        $user->address = $request->input('address');
        $user->city = $request->input('city');
        $user->postal_code = $request->input('postal_code');
        $user->registration_number = $request->input('registration_number');

        $membershipType = $request->input('membership_type');
        $user->membership_start_date   = $request->input('membership_start_date');
        $user->membership_end_date   = $request->input('membership_end_date');
        $user->payment_mode = $request->input('payment_mode');
        $user->accept_terms_date = $request->input('accept_terms_date');
//        $user->comment           = $request->input('notes');
        Log::info('User: ' . \json_encode($user));

        if ($user->save()) {
            $origMembershipType = "NONE";
            // FIXME: implement possible changes of membership type or completely disable possibility to modify membership
            //        would require a separate page for enrolment...
            // allowed transitions:
            // NONE -> REGULAR (regular enrolment), NONE -> STROOM (Stroom enrolment), NONE -> TEMPORARY (trial enrolment),
            // TEMPORARY -> REGULAR (regular enrolment), TEMPORARY -> STROOM (Stroom enrolment), TEMPORARY -> NONE (expiration)
            // REGULAR -> NONE (expiration >1 jaar), REGULAR -> RENEWAL (Renewal), REGULAR -> STROOM (Stroom enrolment)
            // STROOM -> NONE (expiration >1 jaar), STROOM -> RENEWAL (Renewal), STROOM -> REGULAR (regular enrolment, expired > approx 6 months)
            // RENEWAL -> NONE (expiration >1 jaar), RENEWAL -> STROOM (Stroom enrolment), RENEWAL -> RENEWAL (Renewal)

            if ($origMembershipType == $membershipType) {
                return redirect()->route('klusbib.users.show', ['user' => $userId])
                    ->with('success', trans('klusbib::admin/users/message.update.success'));
            }



            if ($membershipType === "NONE") {
                // no enrolment -> simply save user
                return redirect()->route("klusbib.users.index")->with('success', trans('klusbib::admin/users/message.create.success'));
            } else {
                $now = new \DateTime();
                $orderId = $user->employee_num . '-' . $now->format('YmdHis');

                // Send enrolment request to create membership
                $params = array(
                    'membership_start_date' => $request->input('membership_start_date'),
                    'membership_end_date'   => $request->input('membership_end_date'),
                    'paymentMode'           => $request->input('payment_mode'),
                    //paymentMean -> only used for Mollie payment mode, which is not available from Snipe
                    'accept_terms_date'     => $request->input('accept_terms_date'),
                    'enrolment_type'        => $membershipType,
                    'orderId'               => $orderId,
                    'userId'                => $user->employee_num
                );

                if ($this->apiClient->api('enrolment')->request($params)) {
                    return redirect()->route("klusbib.users.index")->with('success', trans('klusbib::admin/users/message.create.success'));
                } else {
                    $errorMessage = trans('klusbib::admin/users/message.enrolment.error');
                    return redirect()->back()->withInput()->with('error', $errorMessage);
                }
            }
        }
    }

    /**
     * Makes the user detail page.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id = null)
    {

        $user = User::find($id);

        if ($user) {
//            $this->authorize('view', $user);
            $user->id = $user->user_id;
            Log::info(\json_encode(compact('user')));
            return view('klusbib::users/view', compact('user'));
        }
        return redirect()->route('klusbib.users.index')
            ->with('error', trans('klusbib::admin/users/message.does_not_exist', compact('id')));
    }

    private function filterDisplayable($permissions)
    {
        $output = null;
        foreach ($permissions as $key => $permission) {
            $output[$key] = array_filter($permission, function ($p) {
                return $p['display'] === true;
            });
        }
        return $output;
    }

}
