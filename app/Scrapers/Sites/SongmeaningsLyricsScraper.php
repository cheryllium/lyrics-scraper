<?php

namespace App\Scrapers\Sites; 

use App\Scrapers\SiteScraper;

use DOMDocument;
use DOMXPath; 

class SongmeaningsLyricsScraper extends SiteScraper {
  const SITE_NAME = "SongMeanings";

  function getLyricsPage($title, $artist) {
    $search_url = "https://songmeanings.com/query/?query=".urlencode($title).urlencode(" ".$artist)."&type=all";
    $search_page_html = $this->curl_get($search_url);

    $dom = new DOMDocument;
    $internalErrors = libxml_use_internal_errors(true);
    $dom->loadHTML($search_page_html);

    /* Select all anchors that are descendants of tr class=item*/
    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query("//tr[@class='item']//a");
    
    $next_flag = false;
    $song_url = "";

    foreach($nodes as $node) {
      if(strtolower($node->textContent) === strtolower($title)) {
        return $node->getAttribute('href'); 
      }
    }
    
    return "";
  }

  function parseLyricsPage($output) {
    if(!$output) return false;
    
    $start_string = "lyric-box\">";
    $finish_string = "<div style=";
    
    $startsAt = strpos($output, $start_string) + strlen($start_string);
    $endsAt = strpos($output, $finish_string, $startsAt);
    $result = substr($output, $startsAt, $endsAt - $startsAt);

    return $result;
  }
}
