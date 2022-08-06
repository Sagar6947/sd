	<?php
	defined('BASEPATH') or exit('No direct script access allowed');

	class Home extends CI_Controller
	{

		public function index()
		{
			$data['title'] = "Home |  SaharDirectory - Get a Personal Visiting Card";

			$data['companycate'] = $this->CommonModal->getRowByIdWithLimit('company_category', 'premium', '0', 7);
			$data['category'] = $this->CommonModal->getAllRowsWithLimit('company_category', '12', 'cate_id');
			$data['blogs'] = $this->CommonModal->getAllRows('blog');

			$this->load->view('home', $data);
		}

		public function search()
		{
			$searchname = $this->input->get('listing-name');
			$searchlocation = $this->input->get('listing-location');

			$serch_sql = "SELECT website_subservice.id, website_subservice.name, website_subservice.description, website_subservice.featured FROM website_subservice
            LEFT JOIN company_subcategory ON website_subservice.service_id = company_subcategory.category_id
            WHERE website_subservice.name LIKE '%{$searchname}%' OR website_subservice.description LIKE '%{$searchname}%'";

			$data['search_data'] = $this->CommonModal->runQuery($serch_sql);
			$data['category'] = $this->CommonModal->getAllRowsWithLimit('company_category', '12', 'cate_id');

			$data['title'] = "Search |  SaharDirectory - Get a Personal Visiting Card";

			$this->load->view('search', $data);
		}



		public function login()
		{
			if ($this->session->has_userdata('login_user_id')) {
				redirect(base_url('dashboard'));
			}
			$data['logo'] = 'assets/logo.png';

			$data['title'] = "Login | SaharDirectory - Get a Personal Visiting Card";
			if (count($_POST) > 0) {
				extract($this->input->post());
				$table = "tbl_registration";
				$login_data = $this->CommonModal->getRowByOr($table, array('email' => $email), array('mobile' => $mobile));

				if (!empty($login_data)) {
					if ($login_data[0]['password'] == $password) {
						$session = $this->session->set_userdata(array('login_user_id' => $login_data[0]['rgid'], 'login_user_name' => $login_data[0]['name'], 'login_user_emailid' => $login_data[0]['email'], 'login_user_contact' => $login_data[0]['mobile']));
						redirect(base_url('dashboard'));
					} else {
						$this->session->set_userdata('msg', 'Wrong Password');
						redirect(base_url('login'));
					}
				} else {
					$this->session->set_flashdata('loginError', 'Username or password doesnt match');
					redirect(base_url('login'));
				}
			} else {
				if ($this->session->has_userdata('login_user_id')) {
					redirect(base_url('dashboard'));
				}
			}

			$this->load->view('login', $data);
		}

		public function register()
		{
			if ($this->session->has_userdata('login_user_id')) {
				redirect(base_url('dashboard'));
			}

			$data['title'] = 'Register | SaharDirectory - Get a Personal Visiting Card';
			if (count($_POST) > 0) {
				$count = $this->CommonModal->getNumRows('tbl_registration', array('mobile' => $this->input->post('mobile'), 'email' => $this->input->post('email')));


				if ($count > 0) {
					$this->session->set_userdata('msg', '<h6 class="alert alert-warning">You have already registered with this email id or contact no.</h6>');
				} else {
					$post = $this->input->post();
					if ($post['password'] !=  $post['cpassword']) {
						$this->session->set_userdata('msg', '<h6 class="alert alert-warning">Your Password and Confirm Password are not match .</h6>');
					} else {
						$rgid = $this->CommonModal->insertRowReturnId('tbl_registration', $post);
						// print_r($rgid);
						// exit();
						$lastid = $this->CommonModal->runQuery("SELECT * FROM tbl_registration ORDER BY rgid DESC LIMIT 1");
						// print_R($lastid);
						// exit();
						$session = $this->session->set_userdata(array('login_user_id' => $lastid[0]['rgid'], 'login_user_name' => $lastid[0]['name'], 'login_user_emailid' => $lastid[0]['email'], 'login_user_contact' => $lastid[0]['mobile']));

						if (!empty($rgid)) {
							$this->session->set_userdata('msg', '<h6 class="alert alert-success">You have Registered Successfully. Login to continue.</h6>');
							redirect(base_url('dashboard-add-profile'));
						} else {
							$this->session->set_userdata('msg', '<h6 class="alert alert-danger">Server error</h6>');
						}
					}
				}
			} else {
			}
			$this->load->view('signup', $data);
		}



		public function add_profile()
		{
			if (!$this->session->has_userdata('login_user_id')) {
				redirect(base_url('login'));
			}
			$data['login_user'] = $this->session->userdata();
			$data['profiledata'] = $this->CommonModal->getRowById('tbl_registration', 'rgid', $this->session->userdata('login_user_id'));
			$this->load->view('dashboard-add-profile', $data);
		}

		public function dashboard()
		{
			if (!$this->session->has_userdata('login_user_id')) {
				redirect(base_url('login'));
			}
			$data['login_user'] = $this->session->userdata();
			$data['profiledata'] = $this->CommonModal->getRowById('tbl_registration', 'rgid', $this->session->userdata('login_user_id'));
			$this->load->view('dashboard', $data);
		}

		public function logout()
		{
			$this->session->unset_userdata('login_user_id');
			$this->session->unset_userdata('login_user_name');
			$this->session->unset_userdata('login_user_emailid');
			$this->session->unset_userdata('login_user_contact');
			redirect(base_url());
		}
	}
