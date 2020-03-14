<?php

namespace Modules\Klusbib\Console\Commands;

use App\Models\Accessory;
use App\Models\Actionlog;
use App\Models\Asset;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Modules\Klusbib\Models\Api\Lending;
use Modules\Klusbib\Notifications\Messages\LendingApiMessage;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Auth;
use Nwidart\Modules\Facades\Module;

class SyncLendings extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'klusbib:sync-lendings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync lendings from Snipe to API.';

    protected $client;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::debug('Handle SyncLendings');
        $settings = Setting::getSettings();
        $actionlogs = Actionlog::with('item', 'user', 'target','location');
        $actionlogs = $actionlogs->where('target_type','=',"App\\Models\\User");
        $actionlogs = $actionlogs->whereIn('item_type',["App\\Models\\Accessory", "App\\Models\\Asset"]);
        $actionlogs = $actionlogs->whereIn('action_type',['checkout', 'checkin from'])->orderBy('created_at', 'asc')->get();
//        $checkoutlogs = $actionlogs->where('action_type','=','checkout')->orderBy('created_at', 'desc')->get();
        $lendings = array();
        foreach ($actionlogs as $log) {
            // Loop through all logs and create list of lendings
            \Log::debug($log->action_type . ' log ' . $log->created_at);
            $target = $log->target;
            $item = $log->item;
            if (!isset($target->employee_num) || !isset($item->id)) {
                continue; // invalid entry
            }
            if ($log->action_type == 'checkout') {
                // Create new lending
                $lending = array();
                $lending["user_id"] = $target->employee_num;
                $lending["tool_id"] = $item->id;
                if ($log->item_type == "App\\Models\\Accessory") {
                    $lending["tool_type"] = LendingApiMessage::TOOL_TYPE_ACCESSORY;
                }
                if ($log->item_type == "App\\Models\\Asset") {
                    $lending["tool_type"] = LendingApiMessage::TOOL_TYPE_ASSET;
                }
                $lending["start_date"] = $log->created_at->format('Y-m-d');
                if (($log->expected_checkin) && ($log->expected_checkin!='')) {
                    $lending["due_date"] = $log->expected_checkin->format('Y-m-d');
                }
                $lending["comments"] = $log->note;
                $lendings[$target->employee_num . "|" . $item->id . "|" . $lending["tool_type"] . "|" . $log->created_at] = $lending;
                \Log::debug('Created lending with key ' . $target->employee_num . "|" . $item->id . "|" . $lending["tool_type"] . "|" . $log->created_at);

            }
            if ($log->action_type == 'checkin from') {
                // Update lending in array

                $matchingLendings = array_filter($lendings, function ($lending, $key) {
//                    \Log::debug('Searching lending with key ' . $key . ' and value ');
                    if (isset($lending["returned_date"] ) ){
                        return FALSE;
                    }
                    if (strpos($key, $lending["user_id"] . "|" . $lending["tool_id"] . "|" . $lending["tool_type"] . "|") === 0) {
                        return TRUE;
                    }
                    return FALSE;
                },ARRAY_FILTER_USE_BOTH);
                if (count($matchingLendings) == 1) {
                    $lendings[key($matchingLendings)]["returned_date"] = $log->created_at->format('Y-m-d');
                    \Log::debug('Updated lending with key ' . key($matchingLendings));
                } else if (count($matchingLendings) > 1) {
                    \Log::debug('Lending not uniquely found, updating last: ' . count($matchingLendings));
                    end($matchingLendings);
                    $lendings[key($matchingLendings)]["returned_date"] = $log->created_at->format('Y-m-d');
                    \Log::debug('Updated lending with key ' . key($matchingLendings));
                } else if (count($matchingLendings) == 0) {
                    \Log::debug('Lending not found: ' . count($matchingLendings));
                }

            }
        }
        $returnedLendings = array_filter($lendings, function ($v) {
            if (isset($v["returned_date"]) ) {
                return true;
            }
            return false;
        });
//        print_r($returnedLendings);
        $openLendings = array_filter($lendings, function ($v) {
            if (!isset($v["returned_date"]) ) {
                return true;
            }
            return false;
        });
        echo "Lendings count:" . count($lendings) . "\n";
        echo "Returned lendings count:" . count($returnedLendings) . "\n";
        echo "Open lendings count:" . count($openLendings) . "\n";
        echo "Open lendings:\n";
        print_r($openLendings);
//        foreach ($lendings as $lending) {
//            $this->pushLending($lending);
//        }

    }
    public function activeLendingForUserTool($key, $lending) {
        \Log::debug('Searching lending with key ' . $key / ' and value ' . $lending);
        if (isset($lending["returned_date"] ) ){
            return FALSE;
        }
        if (startsWith($key,$lending["user_id"] . "|" . $lending["tool_id"] . "|")) {
            return TRUE;
        }
        return FALSE;
    }

    private function pushLending($lending) {
        $existingLending = Lending::findByUserToolStart($lending["user_id"], $lending["tool_id"], $lending["tool_type"], $lending["start_date"]);
        if (!isset($existingLending)) {
            $this->createLending($lending);
        } else if (!isset($existingLending->returned_date) && isset($lending["returned_date"])) {
            $params = array(
                'returned_date' => $lending["returned_date"],
                'comments' => empty($existingLending->comments) ? $lending["comments"] : $existingLending->comments . " / On return: " . $lending["comments"]
            );
            \Log::debug('Klusbib Channel: update existing lending=' . \json_encode($params));
            try {
                $existingLending->update($params);
            } catch (\Exception $ex) {
                \Log::error("Unexpected error updating lending: " . $ex->getMessage());
            }
        } else {
            // nothing to do - lending already up to date
        }
    }

    private function createLending($lending) {
        $params = array(
            'user_id' => $lending["user_id"],
            'tool_id' => $lending["tool_id"],
            'tool_type' => $lending["tool_type"],
            'start_date' => $lending["start_date"],
            'comments' => $lending["comments"]
        );
        if (isset($lending["due_date"])) {
            $params["due_date"] = $lending["due_date"];
        }
        \Log::debug('Klusbib Channel: create lending=' . \json_encode($params));
        try {
            Lending::create($params);
        } catch (\Exception $ex) {
            \Log::error("Unexpected error creating lending: " . $ex->getMessage());
        }

    }
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
//            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
