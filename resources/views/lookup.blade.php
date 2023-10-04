@extends('layouts.master', ['pagetitle' => "lyrics lookup"])

@section('content')
  <div class='col-12 col-sm-4 col-md-3 text-center p-3'>
    <div class='col-7 col-sm-6 col-md-12 mx-auto'>
      <div class='mb-1'><strong>*manual lyrics lookup*</strong></div>
      <form method='get' action='/lookup'>
        <div class='form-group mb-1'>
          <label class='mb-0' id='title-label' for='title'>song name:</label>
          <input type='text' name='title' id='title'
                 aria-labelledby='title-label'
                 @if(isset($songtitle))
                 value='{{ $songtitle }}'
                 @endif
                 class='form-control form-control-sm bg-dark text-light'
          />
        </div>
        <div class='form-group'>
          <label class='mb-0' id='artist-label' for='artist'>artist:</label>
          <input type='text' name='artist' id='artist'
                 aria-labelledby='title-artist'
                 @if(isset($artist))
                 value='{{ $artist }}'
                 @endif
                 class='form-control form-control-sm bg-dark text-light'
          />
        </div>
        <button type='submit' class='btn btn-secondary btn-sm'>Lookup</button>
      </form>
      
      <button class='btn btn-secondary btn-sm mt-3' onClick="document.getElementById('artist').value='';document.getElementById('title').value='';">Clear</button>
    </div>

  </div>

  <div class='col-12 col-sm-8 col-md-9 p-3'>
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
      @if($songtitle)
        <p>No lyrics found. <a href="https://duckduckgo.com/?q={{ urlencode($songtitle) }}+{{ urlencode($artist) }}+lyrics" target="_blank">Click here</a> to try a web search.</p>
      @else
        <p>Type an artist and song name into the text box to search!</p>
      @endif
    @endif
    
    <hr />
    <p><em><a href='/'>lyrics.cheryllium</a></em></p>
  </div>
@stop
