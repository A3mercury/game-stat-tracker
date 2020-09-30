<?php

namespace App\Jobs;

use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Cosmetic;
use GuzzleHttp\Psr7\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateCosmeticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Retrieve Cosmetics JSON from https://fallguysapi.tk/api/cosmetics and
     * update Cosmetics in our database with any new data.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Processing Cosmetics from https://fallguysapi.tk/api/cosmetics');

        $client = new Client();
        $request = new Request('GET', 'https://fallguysapi.tk/api/cosmetics');
        $promise = $client->sendAsync($request)->then(function ($response) use ($client) {
            $cosmetics = json_decode($response->getBody()->getContents());
            $total_added = 0;
            foreach ($cosmetics as $c) {
                $cosmetic = Cosmetic::where('identifier', $c->id)->firstOr(function () use ($c, &$total_added) {
                    $start = isset($c->shop->dates[0]) ? $c->shop->dates[0] : null;
                    $end = isset($c->shop->dates[1]) ? $c->shop->dates[1] : null;
                    $new_cosmetic = Cosmetic::create([
                        'identifier' => $c->id,
                        'asset_id' => $c->associated_asset_id,
                        'rarity' => $c->rarity,
                        'display_name' => $c->displayName,
                        'local_id' => $c->locale_id,
                        'platform_id' => $c->platform_id,
                        'image' => $c->image,
                        'type' => $c->type,
                        'is_featured' => $c->shop->featured,
                        'price' => $c->shop->price,
                        'currency' => $c->shop->currency,
                        'start_date' => $start,
                        'end_date' => $end,
                    ]);

                    $total_added++;

                    Log::info('Cosmetic added: ' . $new_cosmetic->display_name);

                    return $new_cosmetic;
                });
            }

            Log::info($total_added . ' Cosmetics added.');
        });
        $promise->wait();
    }
}
