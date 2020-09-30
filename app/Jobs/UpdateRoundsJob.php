<?php

namespace App\Jobs;

use App\Models\Round;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateRoundsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Processing rounds from https://fallguys.com/rounds');

        $client = new Client();
        $request = new Request('GET', 'https://fallguys.com/rounds');
        $promise = $client->sendAsync($request)->then(function ($response) use ($client) {
            $contents = $response->getBody()->getContents();
            preg_match_all('#<script(.*?)</script>#is', $contents, $matches);
            $props = null;
            foreach ($matches[1] as $i => $m) {
                if (stripos($m, '__NEXT_DATA__') !== false) {
                    $props = json_decode(substr($m, stripos($m, '{"props"')));
                    break;
                }
            }
            $rounds = $props->props->pageProps->rounds;
            $total_added = 0;
            foreach ($rounds as $r) {
                $round = Round::where('identifier', $r->fields->slug)->firstOr(function () use ($r, &$total_added) {
                    $new_round = Round::create([
                        'identifier' => $r->fields->slug,
                        'display_name' => $r->fields->title,
                        'description' => $r->fields->description->content[0]->content[0]->value,
                        'size' => $r->fields->size,
                        'archetype' => $r->fields->archetype,
                        'image' => $r->fields->videoUrl,
                    ]);

                    $total_added++;
    
                    Log::info('Round added: ' . $new_round->display_name);

                    return $new_round;
                });                
            }

            Log::info($total_added . ' Rounds added.');
        });
        $promise->wait();
    }
}
