<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\Achievement;
use GuzzleHttp\Psr7\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateAchievementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Retrieve Achievement JSON from https://fallguysapi.tk/api/achievements and
     * update Achievements in our database with any new data.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Processing Achievements from https://fallguysapi.tk/api/achievements');

        $client = new Client();
        $request = new Request('GET', 'https://fallguysapi.tk/api/achievements');
        $promise = $client->sendAsync($request)->then(function ($response) use ($client) {
            $achievements = json_decode($response->getBody()->getContents());
            $total_added = 0;
            foreach ($achievements as $i => $a) {
                $achievement = Achievement::where('identifier', $a->name)->firstOr(function () use ($a, &$total_added) {
                    $new_achievement = Achievement::create([
                        'identifier' => $a->name,
                        'display_name' => $a->displayName,
                        'description' => $a->description,
                        'icon' => $a->icon,
                        'icon_gray' => $a->icongray,
                    ]);

                    Log::info('Achievement added: ' . $new_achievement->display_name);

                    return $new_achievement;
                });
            }

            Log::info($total_added . ' Achievements added.');
        });
        $promise->wait();
    }
}
