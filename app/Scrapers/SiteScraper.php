<?php

namespace App\Scrapers;

use Exception;

abstract class SiteScraper {
  const SITE_NAME = self::SITE_NAME;

  protected function curl_get($url) {
	  try { 
	    $curl = curl_init();
	    
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	    $output = curl_exec($curl);
	    curl_close($curl);
	    
	    return $output;
	  } catch (Exception $e) {
	    trigger_error(sprintf(
		    'Curl failed with error #%d: %s',
		    $e->getCode(), $e->getMessage()),
			              E_USER_ERROR);
	  }
  }

  // Gets the URL of the specific song's lyrics page (may need to make a search request)
  abstract protected function getLyricsPage($title, $artist);

  // Parses the output of the lyrics page
  abstract protected function parseLyricsPage($output);

  // Tie it all together
  function scrape($title='', $artist='') {
    try {
	    $pageURL = $this->getLyricsPage($title, $artist);
	    if(is_array($pageURL)) {
		    $lyrics = [
		      "source" => static::SITE_NAME, 
		      "url" => $pageURL['url'],
		      "extra" => $pageURL['extra'], 
		    ];
		    $pageURL = $pageURL['url'];
	    } else {
		    $lyrics = [
		      "source" => static::SITE_NAME, 
		      "url" => $pageURL, 
		    ];
	    }

	    $output = $this->parseLyricsPage(
		    $this->curl_get(
		      $pageURL
		    )
	    );
	    
	    $output = trim($output);
	    $output = str_replace("\n", "", $output);
	    $output = str_replace("<br>", "\n", $output);
	    $output = str_replace("<br/>", "\n", $output);

	    $lyrics['lyrics'] = $output; 
	    
	    return $lyrics;
	  } catch(Exception $e) {
	    dump($e->getMessage(), $e->getTrace());
	    
	    return "ERROR SCRAPING PAGE: " . $this->getLyricsPage($title, $artist);
	  }
  }
  
}
