<?php

namespace App\Console\Commands;

use App\Models\Sale;
use App\Models\spotplayer;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Webinar;


class ImportSpotPlayer extends Command
{

    protected $signature = 'import:spotplayer';
    protected $description = 'Import spotplayer from a JSON file';

    public function handle()
    {
        $jsonFile = storage_path('app/spotplayer.json');

        if (!File::exists($jsonFile)) {
            $this->error("File not found: $jsonFile");
            return;
        }
        $jsonData = File::get($jsonFile);
        $data = json_decode($jsonData, true);
        foreach ($data as $entry) {
            $userName = trim($entry['name'], "\" ");
            $userKey = $entry['key'];
            $courses = $entry['course'];
            if (isset($userName)) {
                $user = User::where('full_name', $userName)->first();
            }
            if (!$user) {
                continue;
            }

            foreach ($courses as $course) {

                $webinar = Webinar::whereTranslationLike('title', "%$course%")
                    ->first();
                if (!$webinar) {
                    continue;
                }
                $Sale =  Sale::create([
                    'buyer_id' => $user->id,
                    'type' => 'webinar',
                    'webinar_id' => $webinar->id,
                    'amount' => $webinar->price,
                    'created_at' => time(),
                ]);
                spotplayer::create([
                    'user_id' => $user->id,
                    'sale_id' =>  $Sale->id,
                    'webinar_id' => $webinar->id,
                    'key' =>  $userKey
                ]);
            }
        }

        // return response()->json(['message' => 'Keys assigned successfully']);
    }
}
