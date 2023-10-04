<?php

namespace App\Scrapers;

const SCRAPERS = [
  "Genius",
  "Songmeanings",
];

/* Import all the subclasses*/
foreach(SCRAPERS as $id) {
  require __DIR__ . "/Sites/{$id}LyricsScraper.php";
}

/* The actual LyricsScraper class! */
class LyricsScraper {
  function __construct () {
    $this->scrapers = [];

    try { 
      foreach(SCRAPERS as $id) {
        $scraperClassName = "App\\Scrapers\\Sites\\{$id}LyricsScraper";
        $this->scrapers[$id] = new $scraperClassName(); 
      }
    } catch (Exception $e) {
      dd($e);
    }
  }

  function scrape($title, $artist) {
    $results = [];
    
    foreach($this->scrapers as $name => $scraper) {
      $output = $scraper->scrape($title, $artist);
      if(array_key_exists('lyrics', $output) && $output['lyrics']) {
        $results[$name] = [
          'lyrics' => $output['lyrics'],
          'source' => $output['source'],
          'url' => $output['url']
        ];

        if(array_key_exists('extra', $output) && isset($output['extra']->result)) {
          $results[$name]['cover'] = $output['extra']->result->song_art_image_thumbnail_url;
        }
      }
    }

    return $results; 
  }

}
