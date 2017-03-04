<?php

class Api extends Controller {

	function __construct() {
		
		parent::__construct();
	}

	public function videos() { 
		
		$json = file_get_contents('https://www.googleapis.com/youtube/v3/search?key=AIzaSyDxMfSijPwaJ6_JxdJLWOPbxZF0hqhhYx8&channelId=UCeTxaLmSmRuS0xeB3DAl76Q&part=snippet,id&order=date&maxResults=24');
		$json = json_decode($json, true);
		$html = '<div class="container">';
		$n 	  = 0;

		foreach($json['items'] as $item) {

			$html .= '<div class="responsive">' . 
					  '<div class="img">' .
						  '<a target="_blank" href="//i.ytimg.com/vi_webp/' . $item['id']['videoId'] . '/mqdefault.webp">' .
						    '<img src="//i.ytimg.com/vi_webp/' . $item['id']['videoId'] . '/mqdefault.webp" alt="Fjords" width="300" height="200">' .
						  '</a>' .
						  '<div class="desc">' . $item['snippet']['title'] . '</div>' .
					  '</div>' .
					 '</div>';
		}
		$html .= "</div>";
		echo $html;
	}
	
	private function time_elapsed_string($datetime, $full = false) {
	    $now = new DateTime;
	    $ago = new DateTime($datetime);
	    $diff = $now->diff($ago);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(
	        'y' => 'year',
	        'm' => 'month',
	        'w' => 'week',
	        'd' => 'day',
	        'h' => 'hour',
	        'i' => 'minute',
	        's' => 'second',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	private function youtubeTime($youtube_time) {
	    preg_match_all('/(\d+)/',$youtube_time,$parts);

	    // Put in zeros if we have less than 3 numbers.
	    if (count($parts[0]) == 1) {
	        array_unshift($parts[0], "0", "0");
	    } elseif (count($parts[0]) == 2) {
	        array_unshift($parts[0], "0");
	    }

	    $sec_init = $parts[0][2];
	    $seconds = $sec_init % 60;
	    $seconds_overflow = floor($sec_init/60);

	    $min_init = $parts[0][1] + $seconds_overflow;
	    $minutes = ($min_init) % 60;
	    $minutes_overflow = floor(($min_init)/60);

	    $hours = $parts[0][0] + $minutes_overflow;

	    if($hours != 0)
	        return $hours.':'.$minutes.':'.$seconds;
	    else
	        return $minutes.':'.$seconds;
	}


    

	

	public function highlight($text, $words) {
	    $highlighted = preg_filter('/' . preg_quote($words) . '/i', '<strong>$0</span></strong>', $text);
	    if (!empty($highlighted)) {
	        $text = $highlighted;
	    }
	    return $text;
	}

	public function news() {

		$latest = file_get_contents('https://www.reddit.com/r/soccer/search.json?q=site%3Astreamable.com&sort=new&restrict_sr=on&t=year');

		echo $latest;
	}

	
}