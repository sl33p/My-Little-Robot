<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();
		$this->load->model('Tweets'); 
		$this->load->helper(array('form', 'url','cookie'));
	}
	
	function index()
	{
		//$this->Tweets->save_them_tweets();
		$data['tweeters'] = $this->Tweets->get_db_tweets();
		$this->load->view('welcome_message',$data);
	}
	
	function reload()
	{
	$this->Tweets->save_them_tweets();
echo 'done';
	}
	
	function quick_dirty_reply(){
	$this->Tweets->quick_dirty_reply();
	}
	
	function parse()
	{
	$this->Tweets->parse_tweets();
	}

	function followers()
	{
	
	$this->Tweets->save_them_followers();
	}
	
	function convert(){echo base_convert (13, 10, 32 );}

}


/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */