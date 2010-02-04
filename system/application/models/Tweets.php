<?php  
class Tweets extends Model { 

  
    function Tweets()
    {
        // Call the Model constructor
        parent::Model();
    }
    	
# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# get_them_tweets | returns all tweets from timeline

  function get_them_tweets() {
  
  	//$login = "takete:b3y0nd";
  	$timeline = "http://twitter.com/statuses/friends_timeline.xml?count=200";
  	
  	$login = "mylittlerobot:23ape56";
	$timeline = "http://twitter.com/statuses/user_timeline.xml";
	
	
	$c = curl_init();
	
	curl_setopt($c, CURLOPT_URL, $timeline);
	
	curl_setopt($c, CURLOPT_USERPWD, $login);
	
	curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
	
	$cx = curl_exec($c);
	
	$tweets = new SimpleXMLElement($cx);

	curl_close($c);
	
	return $tweets;
	
	// need some fail shizzle here
  
  }
  
# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# highest_tweet_id | return id of last saved tweet
 
 function highest_tweet_id() {
 $this->db->orderby('tweet_id', 'desc');
 $query = $this->db->get('tweets', 1);
 $row = $query->row(); 
 return $row->tweet_id;
 }
 
# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# save_them_tweets | save the timeline to the db

 function save_them_tweets() {
 $last_saved = $this->highest_tweet_id();
 
 $tweets = $this->get_them_tweets();
 
 

 foreach ( $tweets as $tweet )
 {

# . . . . . . . . . . .    
# 	testing

# echo '<pre>';  print_r($tweet); //exit;

# . . . . . . . . . . .    


 $id = $tweet->id;

	$testdata =  '<hr>'.
		$tweet->text.
		'<br>'.
		'tweet has id '.$tweet->id . ' which is ';
		
if($id > $last_saved) {

	$testdata .= 'higher *'.$tweet->id;

// save tweets
	$tweetdata = array('id'=>null,'user_id'=>$tweet->user->id,'tweet_id'=>$tweet->id,'text'=>"$tweet->text");

$this->db->insert('tweets', $tweetdata);

// check users and save if needed

$this->db->where('user_id', $tweet->user->id);
$query = $this->db->get('users', 1);
if(!$query->num_rows()) {

$userdata = array(	'id'=>null,
					'user_id'=>$tweet->user->id,
					'name'=>''.$tweet->user->name.'',
					'screen_name'=>''.$tweet->user->screen_name.'',
 					'profile_image_url'=>''.$tweet->user->profile_image_url.'',
 					'profile_background_image_url'=>''.$tweet->user->profile_background_image_url.'')
					;

$this->db->insert('users', $userdata);

#echo $this->db->last_query(); //exit;
} // insert users


} else { $testdata .= 'lower'; }

	$testdata .= '* than ('.$last_saved.') the last saved tweet.<br>';


# . . . . . . . . . . .    
# 	testing

#echo $testdata; exit;

# . . . . . . . . . . .    

 }
 
 
 }
 

function get_db_tweets() {

$this->db->orderby('tweet_id', 'desc');
$this->db->join('users', 'users.user_id = tweets.user_id', 'left');
return  $this->db->get('tweets');

} 

function quick_dirty_reply() {

	$message =  "Type \":help\" to get started";
	
	//"Your robot has been assigned the machine code x86" ;

	$login = "mylittlerobot:23ape56";

	$update = "http://twitter.com/statuses/update.xml";
	
	
	$curl_handle = curl_init();
	
	curl_setopt($curl_handle, CURLOPT_URL, $update);
	
	curl_setopt($curl_handle, CURLOPT_USERPWD, $login);
	
	curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "status=$message");

	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
	
		$buffer = curl_exec($curl_handle);
	curl_close($curl_handle);
	// check for success or failure
	if (empty($buffer)) {
    echo 'shit';
	} else {
    echo 'success<hr><pre>';
    print_r($buffer);
	}
	

}

  
}  
  

	// take'm out of the db