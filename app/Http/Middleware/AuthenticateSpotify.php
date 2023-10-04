<?php

namespace App\Http\Middleware;

use Closure;
use SpotifyWebAPI;
use Route;

class AuthenticateSpotify
{
    /**
     * Handle an incoming request.
     * 
     * This makes sure that the user is authenticated via Spotify before proceeding.
     * The Spotify API object is initialized and passed to the controller
     * as part of the request object; It can be accessed via $request->api 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
	      try { 
	          $uri = Route::getRoutes()->match($request)->uri;
            $protocol = config('app.ssl_enabled') ? "https" : "http";
 	          $current_uri = "https://lyrics.cheryllium.io/" . $uri;
	          if(strpos($current_uri, "{$protocol}://lyrics.cheryllium.io")) {
		            echo "Error! Invalid URI callback in AuthenticateSpotify Middleware";
		            die; 
	          }
	      } catch (Exception $e) {
	          dd($e->getMessage(), $e->getCode());
	      }

	      $session = new SpotifyWebAPI\Session(
	          config('services.spotify.id'),
	          config('services.spotify.secret'),
	          $current_uri, 
	      ); 
	      $api = new SpotifyWebAPI\SpotifyWebAPI();
	      $options = [
	          'scope' => [
		            /* Read access to a user's player state. */
		            'user-read-playback-state',

		            /* Read access to a user's currently playing track. */
		            'user-read-currently-playing',

		            /* Write access to a user's playback state. */
		            'user-modify-playback-state',

		            /* Read access to a user's recently played tracks. */
		            'user-read-recently-played',
	          ]
	      ];
	      
	      if(isset($_GET['code'])) {
	          try { 
		            $session->requestAccessToken($_GET['code']);

		            $accessToken = $session->getAccessToken();
		            $refreshToken = $session->getRefreshToken(); 

		            $api->setAccessToken($accessToken);

		            /* Quickly authenticate based on a whitelist */
		            $user = $api->me();
	          } catch (SpotifyWebAPI\SpotifyWebAPIAuthException $e) {
		            header('Location: ' . $session->getAuthorizeUrl($options));
		            die(); 
		            
		            switch($e->getMessage()) {
		                case "Invalid authorization code":
			                  echo "Invalid authorization code";
			                  break; 
		                case "Authorization code expired":
			                  echo "2";
			                  break;
		                default:
			                  echo $e->getMessage(); 
		            }
	          }

	          $request->api = $api; 
            return $next($request);
	      } else {
	          header('Location: ' . $session->getAuthorizeUrl($options));
	          die(); 
	      }
    }
}
