@extends('layouts.master', ['pagetitle' => 'now playing'])

@section('content')
  <div class='col-12 col-sm-4 col-md-3 text-center p-0 pt-3 w-toggle'>
    <div class='col-7 col-sm-6 col-md-12 mx-auto w2-toggle'>
      <img src='{{ $cover }}' class='img-fluid d-sm-block mb-2 mx-auto' id='art' crossorigin='anonymous'>
    </div>

    <div class='p-3'>
      <div class='font-weight-bold mb-1'>{{ $songtitle }}</div>
      <div class='font-italic'>{{ $artist }}</div>
      <div>&ndash;<span class='font-weight-light'>{{ $album }}</span></div>
    </div>
  </div>
  <div class='col-12 col-sm-8 col-md-9 p-0 p-md-3 w3-toggle'>
    @if(isset($lyrics) && count($lyrics) > 0)
      <div class="list-group list-group-horizontal" id="lyrics-list" role="tablist">
        <div class='mr-auto pl-3 list-group-item title'><i>Lyrics</i></div>
        @foreach ($lyrics as $result)
          <a class="list-group-item list-group-item-action {{ $loop->first ? 'active' : '' }}" data-toggle="list" href="#tab-{{ $result['source'] }}" role="tab">{{ $result['source'] }}</a>
        @endforeach
      </div>

      <div class="tab-content">
        @foreach ($lyrics as $result)
          <div class="tab-pane {{ $loop->first ? 'active' : '' }}" id="tab-{{ $result['source'] }}" role="tabpanel">
            <div class='p-3'>
              <p>{!! nl2br(e($result['lyrics'])) !!}</p>
              <p class="text-muted"><small>Lyrics from: <a href="{{ $result['url'] }}" target="_blank">{{ $result['source'] }}</a> | Not accurate? <a href="https://duckduckgo.com/?q={{ urlencode($songtitle) }}+{{ urlencode($artist) }}+lyrics" target="_blank">Click here</a> to try a web search. </small></p>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p class='p-3'>No lyrics found. <a href="https://duckduckgo.com/?q={{ urlencode($songtitle) }}+{{ urlencode($artist) }}+lyrics" target="_blank">Click here</a> to try a web search.</p>
    @endif
    
    <hr />
    <p class='p-3'><em><a href='/'>lyrics.cheryllium</a></em></p>
  </div>

  <script type='text/javascript'>
   document.body.scrollTop = document.documentElement.scrollTop = 0;
   setTimeout(function () {
     location.reload(true); 
   }, {{ $time_left_ms }} + 500);
  </script>
@stop
