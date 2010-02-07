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
  

  	//$timeline = "http://twitter.com/statuses/friends_timeline.xml?count=200";
  	
  	$login = "mylittlerobot:23ape56";
	$timeline = "http://twitter.com/statuses/mentions.xml";
	
	
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
# get_them_followers | return all mylittlerobot's followers (last 100)
 
function get_them_followers() {




	$timeline = "http://twitter.com/statuses/followers/mylittlerobot.xml";
	
	
	$c = curl_init();
	
	curl_setopt($c, CURLOPT_URL, $timeline);
	
	
	
	curl_setopt($c, CURLOPT_RETURNTRANSFER, TRUE);
	
	$cx = curl_exec($c);
	
	$followers = new SimpleXMLElement($cx);

	curl_close($c);
	
	return $followers;
	

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

// echo '<pre>';  print_r($tweet); exit;

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
 
# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# save_them_followers | save the followers (we might need to check this on each request?)
 
function save_them_followers() {

 
 $followers = $this->get_them_followers();
 
 

 foreach ( $followers as $follower )
 {


// check users and save if needed

$this->db->where('user_id', $follower->id);
$query = $this->db->get('followers', 1);
if(!$query->num_rows()) {

$userdata = array(	'id'=>null,
					'user_id'=>$follower->id,
					'name'=>''.$follower->name.'',
					'screen_name'=>''.$follower->screen_name.'',
 					'profile_image_url'=>''.$follower->profile_image_url.'', 					'profile_background_image_url'=>''.$follower->profile_background_image_url.'')
					;

$this->db->insert('followers', $userdata);

echo $this->db->last_query(); //exit;
} // insert users



 }
 
 
 }
 
# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# get_db_tweets | get tweets saved in db

function get_db_tweets() {

$this->db->orderby('tweet_id', 'desc');
$this->db->join('users', 'users.user_id = tweets.user_id', 'left');
return  $this->db->get('tweets');

} 

# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# parse_tweets | in progress

function parse_tweets() {

$this->db->orderby('tweet_id', 'desc');
$this->db->select('screen_name,text,users.user_id');
$this->db->join('users', 'tweets.user_id=users.user_id', 'inner');
$this->db->where('status_id', 0);
$query = $this->db->get('tweets');
foreach($query->result() as $row) {

   if(preg_match ("/-([frs]){1} -([srwhea]){1} (@[a-zA-z_1-9]*)$/",$row->text, $matches )) {  
      

  
   if($matches[1]=='f') {$this->fight($row,$matches);}
   if($matches[1]=='r') {$this->retaliate($row,$matches);}
   if($matches[1]=='s') {$this->surrender($row);}    
    
    
    }

}}

# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# quick_dirty_reply | reply structure.


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

# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# fight | 

function fight($row,$matches){
 // echo '<b>'.$row->screen_name.'</b> wants to <b>';
 
 
 // check if user follows
 
 $this->db->where('user_id', $row->user_id);
 $query = $this->db->get('followers', 1);
 if($query->num_rows()) {$following = true;}
 
 $powers = array('s'=>'speed','r'=>'reach','w'=>'weight','h'=>'height','e'=>'endurance','a'=>'affection');
 
 $actions = array('f'=>'fight','r'=>'retaliate','s'=>'surrender');
 

 $message =  ' Your little robot has been challenged to a fight using the power of '.$powers[$matches[2]].' – to retaliate use `@mylittlerobot  -r -[srwhea] @'.$row->screen_name.'` ';
 if($following) {$this->message($matches[3],$message);}

}
 
 
 function message($to, $message) {
 


	$message =  $to.' '.$message.''; 
	
	echo $message; exit;
	
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
    //
    // mark tweet as sorted - status 10
    
    print_r($buffer);
	}
	

}


 
# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# retaliate | 

function retaliate($row,$matches){
  echo '<b>'.$row->screen_name.'</b> wants to <b>';

  $powers = array('s'=>'speed','r'=>'reach','w'=>'weight','h'=>'height','e'=>'endurance','a'=>'affection');
  
  $actions = array('f'=>'fight','r'=>'retaliate','s'=>'surrender');

    echo $actions[$matches[1]].'</b> '.$matches[3].'\'s little robot using the power of <b>'.$powers[$matches[2]].'</b><hr>';

}

# . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .    
# surrender | 

function surrender($row){
  echo '<b>'.$row->screen_name.'</b> wants to <b>surrender</b><hr>';

}

}