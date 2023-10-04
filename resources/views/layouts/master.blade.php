<!doctype html>
<html>
    <head>
	      <title>lyrics.cheryllium</title>
	      <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

	      <style type='text/css'>
	       @-webkit-keyframes gradientBG {
	           0%{background-position:0% 50%}
	           50%{background-position:100% 50%}
	           100%{background-position:0% 50%}
	       }
	       @-moz-keyframes gradientBG {
	           0%{background-position:0% 50%}
	           50%{background-position:100% 50%}
	           100%{background-position:0% 50%}
	       }
	       @keyframes gradientBG { 
	       0%{background-position:0% 50%}
	       50%{background-position:100% 50%}
	       100%{background-position:0% 50%}
	       }

	       html, body { height: 100%; color: #888; }
	       html {
	           background-color: #111;
	           
	           -webkit-animation: gradientBG 11s ease infinite;
	           -moz-animation: gradientBG 11s ease infinite;
	           animation: gradientBG 11s ease infinite;
	       }
	       body { background: transparent; }

	       .main { background-color: #111111ee; min-height: 100%; }
	       .header { background-color: #1d1d1d; font-size: 0.8em; border-bottom: 1px solid #0a0a0a; }
         .container { min-width: 85%; }
         .lyrics-container { max-width: 1000px; margin-right: auto; margin-left: auto; }

	       a { color: #dcdcdc; }
	       a:hover { color: #efefef;}

	       #art:hover { cursor: pointer; }

         .list-group-horizontal { justify-content: flex-end; }
         .list-group-item { background: #222; border: 1px solid #333; font-size: 0.9em; color: #aaa; width: unset; padding: 5px; }
         .list-group-item.list-group-item-action { background: #222; border: 1px solid #333; font-size: 0.9em; color: #aaa; width: unset; padding: 5px; }
         .list-group-item.list-group-item-action.active { color: #dcdcdc; }

         #lyrics-list { border-bottom: 1px solid #222; border-top: 1px solid #222; }
         #lyrics-list > .title { padding-top: 5px; font-size: 0.9em; background: none; border: 0; }
	      </style>
	      
    </head>
    <body>
        <div class="container-fluid">
            <div class='row'>
		            <div class='col-12 p-1 header'>
		                <a href='/'>lyrics.cheryllium</a> - {{ $pagetitle }}
		                <span class='d-none d-md-inline'>&nbsp;|&nbsp;</span>
		                @if($pagetitle === "now playing")
			                  <span class='d-block d-md-inline'><a href='/back'>&lt;&lt; back</a> ... <a href='/skip'>skip &gt;&gt;</a></span>
			                  <span class='d-none d-md-inline'>&nbsp;|&nbsp;</span>			
			                  <span class='d-block d-md-inline'><a href='/lookup'><i><u>manual lookup</u></i></a></span>
		                @else
			                  this section is still under construction
		                @endif
		            </div>
	          </div>
        </div>

	      <div class='container main'>
	          <!--
	               <div class='row'>
		             <div class='col-12 p-2 m-2 border rounded'>
		             <p><b>MAINTENANCE ONGOING - Please ignore any weirdness</b></p>
		             <p><i>"Everybody has a testing environment. Some people are lucky enough to have a totally separate environment to run production in."</i></p>
		             </div>
	               </div>
	             -->
	          
	          <div class='row'>
		            @yield('content')
	          </div>
	      </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
	      
	      <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.0/color-thief.umd.js"></script>

	      <script type="text/javascript">
	       const colorThief = new ColorThief();
	       const img = document.getElementById("art");

	       function changeBackgroundColors() {
	           let palette = colorThief.getPalette(img);
	           
	           let backgroundCSS = "#111 linear-gradient(15deg, ";
	           let backgroundColors = [];

	           palette = palette.slice(0, 4);
	           
	           for(let color of palette) {
		             backgroundColors.push("rgba(" + color[0] + ", " + color[1] + ", " + color[2] + ", 0.6)"); 
	           }

	           backgroundCSS += backgroundColors.join(",") + ") fixed";

	           let htmlElem = document.querySelector("html"); 
	           htmlElem.style.background = backgroundCSS;
	       }
	       
	       if(img.complete) {
	           changeBackgroundColors(); 
	       } else {
	           img.addEventListener('load', function() {
		             changeBackgroundColors(); 
	           });
	       }

	       $(document).ready(function ($) {
	           $('#art').on('click', function () {
		             let $this = $(this);
		             let url = $this.attr('src');

		             if(url.includes("300x300")) {
		                 $this.attr('src', url.replace("300x300", "1000x1000"));
		                 $('.w-toggle').removeClass('col-sm-4 col-md-3');
		                 $('.w2-toggle').removeClass('col-7 col-sm-6 col-md-12');
                     $('.w3-toggle').removeClass('col-sm-8 col-md-9');
                     $('.w3-toggle').addClass('lyrics-container');
		             } else {
		                 $this.attr('src', url.replace("1000x1000", "300x300"));
		                 $('.w-toggle').addClass('col-sm-4 col-md-3');
		                 $('.w2-toggle').addClass('col-7 col-sm-6 col-md-12');
                     $('.w3-toggle').addClass('col-sm-8 col-md-9');
                     $('.w3-toggle').removeClass('lyrics-container');
		             }
	           });
	       });
	      </script>
	      
    </body>
</html>
