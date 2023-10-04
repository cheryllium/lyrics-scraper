<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Request; 
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Scrapers\Sites\SongmeaningsLyricsScraper;
use App\Scrapers\Sites\GeniusLyricsScraper;
use App\Scrapers\LyricsScraper; 

class Controller extends BaseController
{
  use AuthorizesRequests, ValidatesRequests;

  function nowPlaying(Request $request) {
    /* Get the current track playing on Spotify */
    $currentTrack = $request->api->getMyCurrentTrack();
    if(!$currentTrack) {
      return view('empty');
    }
    
    $currentTrack = $currentTrack->item;
    $responseData = [
      'songtitle' => $currentTrack->name,
      'artist' => $currentTrack->artists[0]->name,
      'cover' => $currentTrack->album->images[0]->url,
      'album' => $currentTrack->album->name,
    ];

    /* Scrape the lyrics data */
    $scraper = new LyricsScraper();
    $lyricsData = $scraper->scrape($responseData['songtitle'], $responseData['artist']);

    if(array_key_exists('genius', $lyricsData) && array_key_exists('cover', $lyricsData['genius'])) {
      $responseData['cover'] = $lyricsData['genius']['cover'];
    }

    $currentPlayback = $request->api->getMyCurrentPlaybackInfo(); 
    $responseData['time_left_ms'] = $currentPlayback->item->duration_ms - $currentPlayback->progress_ms; 
    
    return view('index', [
      'songtitle' => $responseData['songtitle'],
      'artist' => $responseData['artist'],
      'cover' => $responseData['cover'],
      'album' => $responseData['album'],
      'time_left_ms' => $responseData['time_left_ms'],
      'lyrics' => $lyricsData, 
    ]);
  }

  function skip(Request $request) {
    try { 
      $api = $request->api;
      $api->next();
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
      /* Just wait a sec and redirect to the main route :( */
      sleep(1);

      return redirect()->route('main'); 
    }

    sleep(1); /* Let it catch up before grabbing the current track */
    return redirect()->route('main');
  }

  function back(Request $request) {
    try { 
      $api = $request->api;
      $api->previous();
    } catch (SpotifyWebAPI\SpotifyWebAPIException $e) {
      /* Just wait a sec and redirect to the main route :( */
      sleep(1);
      
      return redirect()->route('main'); 
    }

    sleep(1); /* Let it catch up before grabbing the current track */
    return redirect()->route('main');
  }
  
  function lookup(Request $request) {
    $title = $request->input('title', false);
    $artist = $request->input('artist', false);

    $scraper = new LyricsScraper();
    $lyrics = $scraper->scrape($title, $artist);

    return view('lookup', [
      'songtitle' => $title,
      'artist' => $artist,
      'lyrics' => $lyrics
    ]); 
  }
}
