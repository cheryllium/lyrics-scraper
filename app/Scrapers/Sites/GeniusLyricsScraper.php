<?php

namespace App\Scrapers\Sites;

use App\Scrapers\SiteScraper;

class GeniusLyricsScraper extends SiteScraper {
  const SITE_NAME = "Genius";

  private function cleanString($string) {
    return str_replace("?", "", utf8_decode(strtolower(trim($string))));
  }
  
  function getLyricsPage($title, $artist) {
    try {
      $query = [
        "access_token" => config('services.genius.token'),
        "q" => "{$title} {$artist}",
      ]; 
      $output = $this->curl_get("https://api.genius.com/search?" . http_build_query($query));
      $hits = json_decode($output)->response->hits;
      
      /* For songs with a long title with a hyphen in the title 
       * (often indicates extraneous info) */
      if(strlen($title) > 20) {
        $title_pieces = explode("-", $title, 2);
        if(count($title_pieces) > 1) {
          $title = trim($title_pieces[0]);
          $query = [
            "access_token" => config('services.genius.token'),
            "q" => "{$title} {$artist}",
          ]; 
          $output = $this->curl_get("https://api.genius.com/search?" . http_build_query($query));
          $hits = array_merge($hits, json_decode($output)->response->hits);
        }
      }
      
      /* If there are still no hits, then give up. */
      if(!$hits) {
        return false; 
      }

      $title = $this->cleanString($title);
      $artist = $this->cleanString($artist);
      
      /* Initial pass: filter out the good matches */
      $good_matches = [];
      foreach($hits as $hit) {
        /* Replace all non-ASCII characters with a space */
        $result_title = preg_replace(
          "/[^\\w\\s]/", " ",
          strtolower(trim($hit->result->full_title))
        );

        /* Replace all multiple spaces with single space*/
        $result_title = preg_replace('!\s+!', ' ', $result_title);

        similar_text($title, $this->cleanString($hit->result->title), $title_percent);
        
        similar_text($title, $this->cleanString($hit->result->title_with_featured), $title_with_featured_percent);
        similar_text($artist, $this->cleanString($hit->result->primary_artist->name), $artist_percent);

        $title_match = $title_percent > 95 || $title_with_featured_percent > 95;
        $artist_match = $artist_percent > 98;

        if($title_match || $artist_match) {
          $good_matches[] = [
            "hit" => $hit,
            "title_percent" => max($title_percent, $title_with_featured_percent),
            "artist_percent" => $artist_percent,
          ];
        }
      }

      if(count($good_matches) === 0) {
        return false;
      }
      
      /* If there is only one good match, just return that */
      if(count($good_matches) === 1) {
        $hit = $good_matches[0]['hit'];
        return [
          "url" => $hit->result->url,
          "extra" => $hit,
        ];
      }
      
      /* For keeping track of the top match as we go */
      $top_match = [
        "url" => "",
        "score" => 0,
      ];
      foreach($good_matches as $match) {
        $hit = $match['hit'];
        $score = (0.8 * $match['artist_percent']) + (0.2 * $match['title_percent']);
        if($score > $top_match['score']) {
          $top_match = [
            "url" => $hit->result->url,
            "score" => $score,
            "extra" => $hit,
          ];
        }

        /* If near-exact match, return this without processing the rest */
        if($score > 99) {
          return [
            'url' => $hit->result->url,
            'extra' => $hit,
          ];
        }
      }
      
      /* Take top match if it's above 95% confidence */
      if($top_match['score'] > 95) {
        return $top_match;
      } 

      /* Otherwise, the first match is a decent enough heuristic tbh */
      return [
        'url' => $hits[0]->result->url,
        'extra' => $hits[0]
      ];
      
    } catch (Exception $e) {
      return false;
    }
  }

  private function parseNode($lyrics, $node) {
    if(!$node) return $lyrics;
    if(is_string($node)) return $lyrics . $node;
    if(!isset($node->tag)) return $lyrics;

    switch($node->tag) {
      case "br": $lyrics .= "<br>"; break;
      case "b": 
      case "i": 
      case "a":
        if (count($node->children) > 1) {
          foreach($node->children as $child) {
            $lyrics = $this->parseNode($lyrics, $child);
          }
        } else {
          $content = $node->children[0];
          if(is_string($content)) {
            $lyrics .= $content;
          } else {
            $lyrics .= $content->children[0];
          } 
        }
    }

    return $lyrics;
  }

  function parseLyricsPage($output) {
    if(!$output) return false;

    $a = strpos($output, "__PRELOADED_STATE");
    $b = strpos($output, "__APP_CONFIG");
    if($a !== false && $b !== false) {
      $c = substr($output, $a, $b - $a);

      $c = substr($c, strpos($c, "JSON.parse('") + 12);
      $c = substr($c, 0, strpos($c, "');\n"));

      $json = json_decode(stripslashes($c));
      try {
        $json = $json->songPage->lyricsData->body->children[0]->children;
      } catch (\Exception $e) {
        return false;
      }
    } else {
      return false;
    }

    $lyrics = "";
    foreach($json as $elem) {
      if(is_string($elem)) {
        $lyrics .= $elem;
      } else if (isset($elem->tag)) {
        $lyrics = $this->parseNode($lyrics, $elem);
      }
    }

    return $lyrics;
  }
}
