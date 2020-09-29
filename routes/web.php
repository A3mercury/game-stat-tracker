<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// HOW TO GET THE ACHIEVEMENTS FROM STEAM
Route::get('/achievements', function () {
    $client = new \GuzzleHttp\Client();
    // $request = new \GuzzleHttp\Psr7\Request('GET', 'http://api.steampowered.com/ISteamUserStats/GetSchemaForGame/v2/?key=079704A32151CC2DE68731E8ABC5E71E&appid=1097150');
    $request = new \GuzzleHttp\Psr7\Request('GET', 'https://fallguysapi.tk/api/achievements');
    $promise = $client->sendAsync($request)->then(function ($response) use ($client) {
        $achievements = json_decode($response->getBody()->getContents());
        foreach ($achievements as $i => $a) {
            echo $a->displayName . '<br/>';
        }
    });
    $promise->wait();
});

Route::get('/cosmetics', function () {
    $client = new \GuzzleHttp\Client();
        $request = new \GuzzleHttp\Psr7\Request('GET', 'https://fallguysapi.tk/api/cosmetics');
        $promise = $client->sendAsync($request)->then(function ($response) use ($client) {
            $cosmetics = json_decode($response->getBody()->getContents());
            foreach ($cosmetics as $c) {
                if ($c->type === 'Pattern') {
                    ddd($c);
                }
            }
        });
        $promise->wait();
});

// HOW TO GET THE ROUNDS AND DETAILS
Route::get('/rounds', function () {
    $client = new \GuzzleHttp\Client();
    $request = new \GuzzleHttp\Psr7\Request('GET', 'https://fallguys.com/rounds/egg-scramble');
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
        foreach ($rounds as $r) {
            echo $r->fields->title . '<br/>';
        }
    });
    $promise->wait();
});