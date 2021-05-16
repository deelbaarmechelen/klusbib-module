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
        $this->authorize('index', User::class);
        return view('klusbib::users/index');
    }

    public function create()
    {
        Log::debug("Klusbib - Creating new user");

        $this->authorize('create', User::class);
        $user = new User();

        return view('klusbib::users/new')
            ->with('item', $user)
            ->with('allowed_new_memberships', $this->getAllowedNewMembershipTypes("NONE", "DISABLED"))
            ->with('allowed_payment_modes', $this->getAllowedPaymentModes());
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
        $this->authorize('create', User::class);
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
        $user->company               = $request->input('company');
        $user->comment               = $request->input('comment');
        Log::info('User: ' . \json_encode($user));

        if ($user->save()) {
            $paymentMode = $request->input('payment_mode');
            $paymentCompleted = false;
            if ($paymentMode == "TRANSFER_DONE") {
                $paymentMode = "TRANSFER";
                $paymentCompleted = true;
            }
            if ($paymentMode == "TRANSFER_STARTED") {
                $paymentMode = "TRANSFER";
                $paymentCompleted = false;
            }
            if ($enrolmentType === "NONE") {
                // no enrolment -> simply save user
                return redirect()->route("klusbib.users.index")->with('success', trans('klusbib::admin/users/message.create.success'));
            } else if ($enrolmentType === "RENEWAL") {
                $errorMessage = trans('klusbib::admin/users/message.renewal_new_user.error');
                return redirect()->back()->withInput()->with('error', $errorMessage);
            } else if ($request->input("payment_mode") === "MOLLIE") {
                // block possibility to create enrolment with payment mode MOLLIE
                $errorMessage = trans('klusbib::admin/users/message.unsupported.payment_mode');
                return redirect()->back()->withInput()->with('error', $errorMessage);
            } else {
                if ($enrolmentType === "STROOM") {
                    $paymentMode = "STROOM";
                } elseif ($enrolmentType === "TEMPORARY") {
                    $paymentMode = "OTHER";
                }
                $now = new \DateTime();
                $orderId = $user->user_id . '-' . $now->format('YmdHis');

                // Send enrolment request to create membership
                $params = array(
                    'startMembershipDate' => $request->input('membership_start_date'),
//                    'membership_end_date'   => $request->input('membership_end_date'),
                    'paymentMode'           => $paymentMode,
                    'paymentCompleted'      => $paymentCompleted,
                    //paymentMean -> only used for Mollie payment mode, which is not available from Snipe
                    'acceptTermsDate'       => $request->input('accept_terms_date'),
                    'membershipType'        => $enrolmentType,
                    'orderId'               => $orderId,
                    'userId'                => $user->user_id
                );

                if ($this->apiClient->api('enrolment')->request($params)) {
                    return redirect()->route("klusbib.users.index")->with('success', trans('klusbib::admin/users/message.create.success'));
                } else {
                    $errorMessage = trans('klusbib::admin/users/message.enrolment.error');
                    $errorMessage .= $this->formatApiErrorMessage($this->apiClient->errors());
                    return redirect()->back()->withInput()->with('error', $errorMessage);
                }
            }
        }
        $errorMessage = trans('klusbib::admin/users/message.create.error');
        $errorMessage .= $this->formatApiErrorMessage($user->getClientError());

        // Show generic failure message
        return redirect()->back()->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Returns a view that displays the edit user form
     *
     * @return View
     * @internal param int $id
     */
    public function edit($id = null)
    {
        if (is_null($item = User::find($id))) {
            return redirect()->route('klusbib.users.index')->with('error', trans('klusbib::admin/users/message.does_not_exist'));
        }
        $this->authorize('update', $item);
        Log::debug("User found: " . \json_encode($item));
        $item->id = $item->user_id; // view expects id to be set to distinguish between create (POST) and update (PUT)

        // set membership type
        if (empty($item->active_membership) || empty(\json_decode($item->active_membership))) {
            $item->membership_type = "NONE";
            $item->membership_start_date = null;
            $item->membership_end_date = null;
            $item->new_membership_start_date = null;
            $item->payment_mode = "UNKNOWN";
        } else {
            Log::debug(json_encode($item->active_membership) );
            $membership = \json_decode($item->active_membership);
            // {"id":2,"status":"ACTIVE","start_at":"2020-06-25T22:00:00.000000Z","expires_at":"2021-06-25T22:00:00.000000Z","subscription_id":1,"contact_id":2,"last_payment_mode":"PAYCONIQ","comment":null,"created_at":"2020-06-24T22:25:19.000000Z","updated_at":"2020-07-21T21:59:54.000000Z","deleted_at":null}
            $item->membership_type = $this->convertToMembershipType($membership->subscription_id);
            $item->membership_start_date = $membership->start_at;
            $item->membership_end_date = $membership->expires_at;
            $newMembershipStartDate = new \DateTime($membership->expires_at);
            $newMembershipStartDate = $newMembershipStartDate->add(new \DateInterval('P1D'));
            $item->new_membership_start_date = $newMembershipStartDate->format('Y-m-d');
            $item->payment_mode = $membership->last_payment_mode;
        }
        $data = compact('item');
//        $data["allowed_new_memberships"] = $this->getAllowedNewMembershipTypes($item->membership_type, $item->state);
//        $data["allowed_payment_modes"] = $this->getAllowedPaymentModes();
        Log::debug("data: " . \json_encode($data));
        return view('klusbib::users/edit', $data)
            ->with('allowed_new_memberships', $this->getAllowedNewMembershipTypes($item->membership_type, $item->state))
            ->with('allowed_payment_modes', $this->getAllowedPaymentModes());
    }

    private function convertToMembershipType($subscriptionId) {
        if ($subscriptionId == 1) {
            return "REGULAR";
        } elseif ($subscriptionId == 2) {
            return "TEMPORARY";
        } elseif ($subscriptionId == 3) {
            return "RENEWAL";
        } elseif ($subscriptionId == 4) {
            return "STROOM";
        } elseif ($subscriptionId == 5) {
            return "REGULARORG";
        } elseif ($subscriptionId == 6) {
            return "RENEWALORG";
        } else {
            return "NONE"; // or throw exception??
        }
    }
    /**
     * @param $currentMembershipType current user membership type
     * @param $currentState current user state
     * @return array
     */
    private function getAllowedNewMembershipTypes($currentMembershipType, $currentState): array
    {
        $allowed_new_memberships = array();
        array_push($allowed_new_memberships, "NONE"); // geen aanpassing aan lidmaatschap
        if ($currentMembershipType == "NONE" || $currentMembershipType == "TEMPORARY"
            || ($currentMembershipType == "STROOM" && $currentState == "EXPIRED")) {
            array_push($allowed_new_memberships, "REGULAR");
        }
        if ($currentMembershipType == "NONE") {
            array_push($allowed_new_memberships, "TEMPORARY");
        }
//        if ($currentMembershipType != "NONE" && $currentMembershipType != "TEMPORARY") {
        if ($currentMembershipType == "REGULAR" || $currentMembershipType == "STROOM"
            || $currentMembershipType == "RENEWAL") {
            array_push($allowed_new_memberships, "RENEWAL");
        }
        if ($currentMembershipType == "NONE" || $currentMembershipType == "TEMPORARY"
            || $currentMembershipType == "REGULAR" || $currentMembershipType == "STROOM"
            || $currentMembershipType == "RENEWAL") {
            array_push($allowed_new_memberships, "STROOM");
        }
        // Company memberships
        if ($currentMembershipType == "NONE") {
            array_push($allowed_new_memberships, "REGULARORG");
        }
        if ($currentMembershipType == "REGULARORG"
         || $currentMembershipType == "RENEWALORG") {
            array_push($allowed_new_memberships, "RENEWALORG");
        }

        return $allowed_new_memberships;
    }

    /**
     * @return array of allowed payment modes
     */
    private function getAllowedPaymentModes(): array
    {
        $allowed_payment_modes = array(
//            "MOLLIE", -> not allowed on inventory, only to be used for online payment from public website
            "PAYCONIQ",
            "TRANSFER_STARTED",
            "TRANSFER_DONE",
            "CASH",
            "STROOM",
            "MBON",
            "KDOBON",
            "LETS",
            "OVAM",
            "SPONSORING",
            "OTHER",
//            "UNKNOWN",
//            "NONE" -> TODO: to check if NONE should be a valid payment mode for free subscriptions
        );
        return $allowed_payment_modes;
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
        $this->authorize('update', $user);
        Log::debug('Request data: ' . \json_encode($request->all()));
        $user->firstname = $request->input('firstname');
        $user->lastname = $request->input('lastname');
        $user->role = $request->input('role');
//        $user->state = $request->input('state'); // no longer allow update of state, as it is dependent of membership status
        $user->email = $request->input('email');
        $user->email_state = $request->input('email_state');
        $user->phone = $request->input('phone');
        $user->mobile = $request->input('mobile');
        $user->address = $request->input('address');
        $user->city = $request->input('city');
        $user->postal_code = $request->input('postal_code');
        $user->registration_number = $request->input('registration_number');

        $origMembershipType = $request->input('membership_type');
        $newMembershipType = $request->input('new_membership_type');
        $user->payment_mode = $request->input('payment_mode');
        $user->accept_terms_date = $request->input('accept_terms_date');
        $user->company = $request->input('company');
        $user->comment = $request->input('comment');
        Log::info('User: ' . \json_encode($user));

        if ($user->save()) {
            // FIXME: implement possible changes of membership type or completely disable possibility to modify membership
            //        would require a separate page for enrolment...
            // allowed transitions:
            // NONE -> REGULAR (regular enrolment), NONE -> STROOM (Stroom enrolment), NONE -> TEMPORARY (trial enrolment),
            // TEMPORARY -> REGULAR (regular enrolment), TEMPORARY -> STROOM (Stroom enrolment), TEMPORARY -> NONE (expiration)
            // REGULAR -> NONE (expiration >1 jaar), REGULAR -> RENEWAL (Renewal), REGULAR -> STROOM (Stroom enrolment)
            // STROOM -> NONE (expiration >1 jaar), STROOM -> RENEWAL (Renewal), STROOM -> REGULAR (regular enrolment, expired > approx 6 months)
            // RENEWAL -> NONE (expiration >1 jaar), RENEWAL -> STROOM (Stroom enrolment), RENEWAL -> RENEWAL (Renewal)

            if ($newMembershipType == "NONE") {
                Log::info('No membership type update: from ' . $origMembershipType . ' to ' . $newMembershipType);
                return redirect()->route('klusbib.users.show', ['user' => $userId])
                    ->with('success', trans('klusbib::admin/users/message.update.success'));
            }
            Log::info('Membership type to be updated from ' . $origMembershipType . ' to ' . $newMembershipType);
            $paymentMode = $request->input('payment_mode');
            if ($newMembershipType === "STROOM") {
                $paymentMode = "STROOM";
            } elseif ($newMembershipType === "TEMPORARY") {
                $paymentMode = "OTHER";
            }
            $paymentCompleted = false;
            if ($paymentMode == "TRANSFER_DONE") {
                $paymentMode = "TRANSFER";
                $paymentCompleted = true;
            }
            if ($paymentMode == "TRANSFER_STARTED") {
                $paymentMode = "TRANSFER";
                $paymentCompleted = false;
            }
            if ($paymentMode == "PAYCONIQ"
                || $paymentMode == "CASH"
                || $paymentMode == "STROOM"
                || $paymentMode == "MBON"
                || $paymentMode == "KDOBON"
                || $paymentMode == "LETS"
                || $paymentMode == "OVAM"
                || $paymentMode == "SPONSORING"
                || $paymentMode == "OTHER"
            ) {
                $paymentCompleted = true;
            }

            if ( ($origMembershipType === "NONE"
                && ($newMembershipType === "REGULAR" || $newMembershipType === "REGULARORG" || $newMembershipType === "TEMPORARY") )
                // FIXME: should be considered as a special case of renewal? -> currently fails with "Enrolment not supported for user state ACTIVE"
                || ($origMembershipType === "TEMPORARY"
                        && ($newMembershipType === "REGULAR" || $newMembershipType === "STROOM" ) )
            ){
                $now = new \DateTime();
                $orderId = $user->user_id . '-' . $now->format('YmdHis');

                // Send enrolment request to create membership
                $params = array(
                    'membershipStart'       => $request->input('new_membership_start_date'),
//                    'membershipExpiration'  => $request->input('membership_end_date'),
                    'paymentMode'           => $paymentMode,
                    'paymentCompleted'      => $paymentCompleted,
                    //paymentMean -> only used for Mollie payment mode, which is not available from Snipe
                    'acceptTermsDate'       => $request->input('accept_terms_date'),
                    'membershipType'        => $newMembershipType,
                    'orderId'               => $orderId,
                    'userId'                => $user->user_id
                );

                $response = $this->apiClient->api('enrolment')->request($params);
                if ($response) {
                    // In case of response 208 (already enrolled), new membership is not created -> considered as error
                    // -> check if response contains the created enrolment <-> contains warning/error message
                    if (is_array($response) && isset($response["message"]) ) {
                        Log::info("API enrolment response: " . \json_encode($response) . " - " . $response["message"] );
                        return redirect()->back()->withInput()->with('error', $response["message"]);
                    } else {
                        return redirect()->route("klusbib.users.show", ['user' => $userId])
                            ->with('success', trans('klusbib::admin/users/message.update.success'));
                    }
                } else {
                    $errorMessage = trans('klusbib::admin/users/message.enrolment.error');
                    $errorMessage .= $this->formatApiErrorMessage($this->apiClient->errors());
                    return redirect()->back()->withInput()->with('error', $errorMessage);
                }
            } elseif (  ( ( $origMembershipType === "REGULAR" || $origMembershipType === "STROOM" || $origMembershipType === "RENEWAL")
                            && $newMembershipType === "RENEWAL")
                     || (   $origMembershipType === "REGULARORG"
                            && $newMembershipType === "RENEWALORG")
                     )
            { // renewal: send request without providing start membership date
                $now = new \DateTime();
                $orderId = $user->user_id . '-' . $now->format('YmdHis');

                // Send enrolment request to renew membership
                $params = array(
                    'paymentMode'           => $paymentMode,
                    'paymentCompleted'      => $paymentCompleted,
                    //paymentMean -> only used for Mollie payment mode, which is not available from Snipe
                    'acceptTermsDate'       => $request->input('accept_terms_date'),
                    'membershipType'        => $newMembershipType,
                    'orderId'               => $orderId,
                    'userId'                => $user->user_id
                );
                if ($this->apiClient->api('enrolment')->request($params)) {
                    return redirect()->route("klusbib.users.show", ['user' => $userId])
                        ->with('success', trans('klusbib::admin/users/message.update.success'));
                } else {
                    $errorMessage = trans('klusbib::admin/users/message.enrolment.error');
                    $errorMessage .= $this->formatApiErrorMessage($this->apiClient->errors());
                    return redirect()->back()->withInput()->with('error', $errorMessage);
                }
            } elseif ( ($origMembershipType === "NONE" || $origMembershipType === "REGULAR" || $origMembershipType === "TEMPORARY" || $origMembershipType === "RENEWAL")
                && $newMembershipType === "STROOM"){
                $now = new \DateTime();
                $orderId = $user->user_id . '-' . $now->format('YmdHis');

                // Send enrolment request to renew membership
                $params = array(
                    'paymentMode'           => $paymentMode,
                    'paymentCompleted'      => $paymentCompleted,
                    //paymentMean -> only used for Mollie payment mode, which is not available from Snipe
                    'acceptTermsDate'       => $request->input('accept_terms_date'),
                    'membershipType'        => $newMembershipType,
                    'orderId'               => $orderId,
                    'userId'                => $user->user_id
                );
                if ($this->apiClient->api('enrolment')->request($params)) {
                    return redirect()->route("klusbib.users.show", ['user' => $userId])
                        ->with('success', trans('klusbib::admin/users/message.update.success'));
                } else {
                    $errorMessage = trans('klusbib::admin/users/message.enrolment.error');
                    $errorMessage .= $this->formatApiErrorMessage($this->apiClient->errors());
                    return redirect()->back()->withInput()->with('error', $errorMessage);
                }
            } else {
                return redirect()->back()->withInput()
                    ->with('error', trans('klusbib::admin/users/message.update.error') . " (Not supported)");
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
            $this->authorize('view', $user);
            $user->id = $user->user_id;
            Log::info(\json_encode(compact('user')));
            // set membership type
            if (!$user->active_membership || empty(\json_decode($user->active_membership))) {
                $user->membership_type = "NONE";
            } else {
//            Log::debug($item->active_membership);
                $membership = \json_decode($user->active_membership);
                $user->active_membership = $membership;
                $user->membership_type = $this->convertToMembershipType($membership->subscription_id);
            }
            // TODO: also return an array of memberships

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
    /**
     * @param $clientError MessageBag containing API client error(s)
     * @return string formatted error message to be appended to a general error message
     */
    private function formatApiErrorMessage($clientError): string
    {
        $errorMessage = "";
        $errors = Arr::get($clientError, 'errors');
        if (is_array($errors)) {
            $errorMessage .= " (API fout: ";
            foreach ($errors as $key => $value) {
                if (\is_string($key)) {
                    $errorMessage .= $key . ": ";
                }
                $errorMessage .= $value;
            }
            $errorMessage .= ")";
        }
        return $errorMessage;
    }
}
