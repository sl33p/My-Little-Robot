<?
/*


   o       o       o       o       o       o      <o      <o>
  ^|\     ^|^     v|^     v|v     |/v     |X|      \|      |
   /\      >\     /<       >\     /<       >\     /<       >\

  o>      o       o       o       o       o       o       o
  \       x      </      <|>     </>     <\>     <)>      |\
 /<       >\     /<       >\     /<       >\      >>      L


 copied some shit from http://papermashup.com/using-the-twitter-api/ rather than writing it myself

 http://flixpulse.com/ and http://www.twittercritic.com/ are two services that polls the public timeline and tries to find movie titles. They both seem to be unmaintained.


*/
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>My Little Robot - test page</title>
<!--[if IE]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link href="/css/main.css" rel="stylesheet">

</head>
<body>
<?










	echo '<div id="container"><header><hgroup><h1>My Little Robot</h1><h2>A small robot army</h2></hgroup>
	<p>Type ":help" to get started.</p></header>';
	echo anchor('/welcome/reload/', 'Reload', array('title' => 'Reload the tweets from the interwebs'));
//echo '<pre>';

//print_r($tweeters); exit;
foreach ( $tweeters->result() as $tweet )
{
//print_r($tweet);    
//echo '<hr>';






$img = $tweet->profile_image_url ? $tweet->profile_image_url : '/img/spacer.gif';
echo "<section class='user'><a href=\"http://www.twitter.com/", $tweet->screen_name,"\"><img height=\"48\" width=\"48\" class=\"twitter_followers\" src=\"", $img, "\" alt=\"", $tweet->name, "\" /></a>\n";
 echo "<div class='name'>", $tweet->name,"</div>";


echo "<div class='description'>".$tweet->text."</div></section>";
}

echo '</div>';


?>
</body>
</html>