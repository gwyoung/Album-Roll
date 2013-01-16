<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('SITE_OWNER', 'greg@albumroll.com');
define('NO_REPLY', 'noreply@albumroll.com');
class Email extends MY_Controller
{
	const Contact = 0;
	const Reset = 1;
	const Bug = 2;
	const Invite = 3;
	
	function Email()
	{
		parent::MY_Controller();
		
		$config = array();
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'mail.albumroll.com';
		$config['smtp_user'] = 'noreply@albumroll.com';
		$config['smtp_pass'] = '4veyTare';
		
		$this->email->initialize($config);
	}
	
	function reset_password()
	{
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email', 'email', 
			'trim|required|valid_email|callback__email_exists');
		
		if($this->form_validation->run() == FALSE)
		{
			$data['view'] = 'reset_password';
			$this->_load($data);
		}
		else
		{
			//send email, reset password
			$email = $this->input->post('email');
			$password = $this->user_model->reset_password($email);
			$user_name = $this->user_model->name_from_email($email);
			
			$this->email->from(NO_REPLY);
			$this->email->to($email);
			$this->email->subject('Your album roll password');
			$this->email->message(
				'Your password has been reset. Here\'s your new login information and temporary password:
				
	Name: '.$user_name.'
	Password: '.$password.'
				
Please change your password once you log in. Thanks for using album roll!
				
Note: This email account is unmonitored, so please do not reply to this address. If you have questions, contact the site owner at '.SITE_OWNER.'. Thanks!'
			);
			$this->email->send();
			
			redirect('email/success/'.Email::Reset);
		}
	}
	
	public function _email_exists($email)
	{
		if (!$this->user_model->email_exists($email))
		{
			$this->form_validation->set_message('_email_exists', 'Invalid email address.');
		  	return FALSE;
		}
		else
		{
		    return TRUE;
		}
	}
	
	function invite()
	{
		$user_id = $this->session->userdata('user_id');
		if(!$user_id)
		{
			$data['view'] = 'invite';
			$this->_load($data);
			return;
		}
		
		$user = $this->user_model->get_user($user_id);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('emails', 'email addresses', 
			'trim|required|callback__emails_valid');
		$this->form_validation->set_rules('message', 'message', 'trim|required');
		
		if($this->form_validation->run() == FALSE)
		{
			$data['message'] = set_value('message');
			if(empty($data['message']))
			{
				$data['message'] = 
"Hey there!
					
I think you might enjoy using this new site I found, http://www.albumroll.com, to share your favorite albums and find new music. Take a few minutes and check it out! I'm sure you won't regret it.";
			}
			
			$data['view'] = 'invite';
			$this->_load($data);
		}
		else
		{
			$emails = preg_split( "/(,|;)/", str_replace(' ', '', $this->input->post('emails')));
			$message = $this->input->post('message');
			
			$this->email->from(NO_REPLY);
			$this->email->to($emails);
			$this->email->subject('Your friend '.$user->name.' has invited you to use Album Roll!');
			$this->email->message($user->name." (".$user->email.") says:
			
			\"".$message."\"
			
This address is unmonitored. Respond to your friend directly, or check out the site at http://www.albumroll.com! Thanks!");
			$this->email->send();
			
			redirect('email/success/'.Email::Invite);
		}
	}
	
	//checks a collection of emails
	public function _emails_valid($emails)
	{
		if(empty($emails))
		{
			$this->form_validation->set_message('_emails_valid', 
				'Please enter at least one address.');
			return FALSE;
		}
		$emails = preg_split( "/(,|;)/", $emails);
		$invalid_emails = array();
		foreach($emails as $email)
		{
			if(!valid_email(trim($email)) || $this->user_model->email_exists($email))
			{
				$invalid_emails[] = trim($email);
			}
		}
		if(!empty($invalid_emails))
		{
			$this->form_validation->set_message('_emails_valid', 
				'The following are invalid or are already members: '.implode(', ', $invalid_emails));
			return FALSE;
		}
		return TRUE;
	}
	
	function contact()
	{
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
		$this->form_validation->set_rules('description', 'description', 'trim|required');
		
		if($this->form_validation->run() == FALSE)
		{
			$data['view'] = 'contact';
			$this->_load($data);
		}
		else
		{
			//send contact email
			$email = $this->input->post('email');
			$description = $this->input->post('description');
			
			$this->email->from($email);
			$this->email->to(SITE_OWNER);
			$this->email->subject('Contact');
			$this->email->message($description);
			$this->email->send();
			
			redirect('email/success/'.Email::Contact);
		}
	}
	
	function feedback()
	{
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email', 'email', 'trim|required|valid_email');
		$this->form_validation->set_rules('description', 'description', 'trim|required');
		
		if($this->form_validation->run() == FALSE)
		{
			$data['view'] = 'feedback';
			$this->_load($data);
		}
		else
		{
			//send email with bug
			$email = $this->input->post('email');
			$description = $this->input->post('description');
			
			$this->email->from($email);
			$this->email->to(SITE_OWNER);
			$this->email->subject('Feedback');
			$this->email->message($description);
			$this->email->send();
			
			redirect('email/success/'.Email::Bug);
		}
	}
	
	function success($type)
	{
		$data['error_title'] = 'success!';
		
		switch($type)
		{
			case Email::Contact:
				$data['error_message'] = 'Your email has been received and you should get a response within a day or so. Thanks for the feedback!';
				break;
			case Email::Reset:
				$data['error_message'] = 'Your password has been reset. You will receive mail with your user name and a new passord shortly!';
				break;
			case Email::Bug:
				$data['error_message'] = 'Your feedback has been submitted. We\'ll be in touch through the address you gave if we have any questions. Thanks for helping to improve the site!';
				break;
			case Email::Invite:
				$data['error_message'] = 'Your friends have been successfully invited. Now it\'s up to them whether or not they join!';
				break;
			default:
				$data['error_message'] = 'Thank you for the feedback.';
				break;
		}
		
		$data['view'] = 'error';
		$this->_load($data);
	}
}
?>