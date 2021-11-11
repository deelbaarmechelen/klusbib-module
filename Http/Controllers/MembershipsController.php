<?php

namespace Modules\Klusbib\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\KlusbibApi;
use Modules\Klusbib\Models\Api\Membership;
use Modules\Klusbib\Api\Client;
use Illuminate\Support\Arr;

class MembershipsController extends Controller
{
    private $apiClient;

    /**
     * Create a new controller instance.
     *
     * @param  Client  $apiClient
     * @return void
     */
    public function __construct(Client $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the reservations listing, which is generated in getDatatable().
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->authorize('index', Membership::class);
//        $users = \Modules\Klusbib\Models\Api\User::all();
        return view('klusbib::memberships/index');
    }

    public function confirm($membershipId = null)
    {
        $membership = Membership::find($membershipId);
        $this->authorize('update', $membership);
        if (is_null($membership)) {
            return redirect()->route('klusbib.memberships.index')->with('error', trans('klusbib::admin/memberships/message.does_not_exist'));
        }

        $params = array(
            'membershipId'          => $membershipId,
            'paymentMode'           => $membership->last_payment_mode,
            'userId'                => $membership->contact_id
        );
        if ($this->apiClient->api('enrolment')->confirm($params)) {
            return redirect()->route("klusbib.users.show", ['user' => $membership->contact_id])
                ->with('success', trans('klusbib::admin/memberships/message.confirm.success'));
        } else {
            $errorMessage = trans('klusbib::admin/memberships/message.confirm.error');
            $errorMessage .= $this->formatApiErrorMessage($this->apiClient->errors());
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }
    }

    public function cancel($membershipId = null)
    {
        $membership = Membership::find($membershipId);
        $this->authorize('update', $membership);
        if (is_null($membership)) {
            return redirect()->route('klusbib.memberships.index')->with('error', trans('klusbib::admin/memberships/message.does_not_exist'));
        }
        $params = array(
            'membershipId'          => $membershipId,
            'paymentMode'           => $membership->last_payment_mode,
            'userId'                => $membership->contact_id
        );
        if ($this->apiClient->api('enrolment')->decline($params)) {
            return redirect()->route("klusbib.users.show", ['user' => $membership->contact_id])
                ->with('success', trans('klusbib::admin/memberships/message.decline.success'));
        } else {
            $errorMessage = trans('klusbib::admin/memberships/message.decline.error');
            $errorMessage .= $this->formatApiErrorMessage($this->apiClient->errors());
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

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