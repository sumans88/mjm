<?php
class Model_log_updated extends  CI_Model{
	var $table_profile   = 'log_last_updated_member';
	var $table_company   = 'log_last_updated_company';
	var $table_committee = 'log_last_updated_member_committee';
	var $table_sector    = 'log_last_updated_member_sector';
    function __construct(){
       parent::__construct();
       $this->load->model('member_model');
       $this->load->model('company_model');
       $this->load->model('auth_member_committee_model');
       $this->load->model('auth_member_sector_model');
    }
    /*=================================
    =            Profile            =
    =================================*/
    function findBy_log_profile($where,$is_single_row=0){
		$this->db->select('a.*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->table_profile.' a',$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->table_profile.' a',$where)->result_array();
		}
	}

    function log_profile($id){
    	// insert data log
    	$data_db = $this->member_model->findById($id);
    	$data_member = $this->member_model->findViewById($id);

    	$data['member_id']   = $id ; 
    	$data['firstname']   = $data_db['firstname'];
    	$data['lastname']    = $data_db['lastname'];
    	$data['prefix_name'] = $data_db['prefix_name'];
    	$data['job']         = $data_db['job'];
    	$data['email']       = $data_db['email'];
    	$data['m_t_number']  = $data_db['m_t_number'];
    	$data['is_invis']    = $data_db['is_invis'];
    	$data['img']         = $data_db['img'];

    	$data['modify_date'] = datetime_today();

    	// cari no urut terakhir 
		$this->db->select('max(urut) as max_urut');
    	$max_urut            = $this->findBy_log_profile(['member_id'=>$id],1)['max_urut'];
    	$data['urut']        = $max_urut + 1;

    	$this->db->insert($this->table_profile,null_empty($data));

    	$id_member_log       = $this->db->insert_id();
    	$data_compare        = $this->compare_log_profile($id_member_log);

     	foreach ($data_compare as $key => $value) {
     		if ( strpos($key,'_is_different')  && $value == 'true') {
     			$key_real = str_replace("_is_different","",$key);
     			if ($key_real == 'img') {

     				if ($data_db['member_category_id'] == 3 || $data_db['member_category_id'] == 1) {
     					$type_path_image = 'representative';
     				}else{
     					$type_path_image = 'individu';
     				}
	     				$data_compare[$key_real.'_before'] = imageProfile($data_compare[$key_real.'_before'],$type_path_image,2) ;
	     				$data_compare[$key_real.'_after']  = imageProfile($data_compare[$key_real.'_after'],$type_path_image,2) ;
     			}
     			$data_email['content'][] = [
     				'label' => $this->label_log_profile($key_real),
     				'before' => $data_compare[$key_real.'_before'],
     				'after' => $data_compare[$key_real.'_after']
     			];
     		}
     	}
     	$data_email['content'] = empty($data_email['content']) ? [] : $data_email['content'];
    	// kalau ada yang beda sent email notif
    	if (!empty($data_email['content'])) {
	     	$sent_email['data_of_change'] = $this->parser->parse('apps/log/table_email.html',$data_email,TRUE);
	     	$sent_email['name']    = $data_member['member_name_without_prefix'];
	     	$sent_email['email']   = $data_member['member_email'];
	     	$sent_email['name_in'] = $data_member['company_in'];
			sent_email_by_category(22,$sent_email,'amar.ronaldo.m@gmail.com');
		}
    }
    function before_insert_log_profile($id){
    	//  check sudah ada belum di database kalau belum add sebagai first log 
    	$check = $this->db->select('max(urut) as max_urut')->get_where($this->table_profile,['member_id'=>$id])->row_array()['max_urut'];
    	if (empty($check)) {
	    	$data_db = $this->member_model->findById($id);

	    	$data['member_id']   = $id ; 
	    	$data['firstname']   = $data_db['firstname'];
	    	$data['lastname']    = $data_db['lastname'];
	    	$data['prefix_name'] = $data_db['prefix_name'];
	    	$data['job']         = $data_db['job'];
	    	$data['email']       = $data_db['email'];
	    	$data['m_t_number']  = $data_db['m_t_number'];
	    	$data['is_invis']    = $data_db['is_invis'];
	    	$data['img']         = $data_db['img'];

	    	$data['modify_date'] = datetime_today();

	    	$max_urut            = 1;
	    	$data['urut']        = $max_urut;
	    	$this->db->insert($this->table_profile,null_empty($data));
    	}
    }
    function compare_log_profile($id_log)
    {    
    	// ambil data sebelum nya dan di compare degan log sekarang
    	$data               = $this->findBy_log_profile(['id'=>$id_log],1);

    	$where['member_id'] = $data['member_id'];
    	$where['urut']      = $data['urut']-1;
    	$data_sebelumnya    = $this->findBy_log_profile($where,1);
    	// cari perbedaan kedua nya 
    	
    	$avoid_field = ['modify_date','member_id','id','urut'];
    	foreach ($data as $key => $value) {
    		if (!in_array($key, $avoid_field) ) {
				$data[$key.'_is_different'] = $data[$key] == $data_sebelumnya[$key] ? 'false' : 'true'; // status edited
				$data[$key.'_before'] = $data_sebelumnya[$key]; // data sebelum nya
				$data[$key.'_after'] = $data[$key] ; // data sesudah edit 
    		}
    	}
    	return $data;
    }
    function label_log_profile($field_table)
    {
    	// label untuk menganti field table 
    	switch ($field_table) {
    		case 'firstname'  : return 'First Name' ;                           break;
    		case 'lastname'   : return 'Last Name' ;                            break;
    		case 'prefix_name': return 'Mr/Ms/etc' ;                            break;
    		case 'job'        : return 'Job Position' ;                         break;
    		case 'email'      : return 'Email' ;                                break;
    		case 'm_t_number' : return 'Telephone' ;                            break;
    		case 'is_invis'   : return 'Information not available for Public' ; break;
    		case 'img'        : return 'Photo' ;                                break;
			default           : return $field_table;                            break;
    	}

    }
    /*=================================
    =            Company            =
    =================================*/
    function findBy_log_company($where,$is_single_row=0){
		$this->db->select('a.*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->table_company.' a',$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->table_company.' a',$where)->result_array();
		}
	}

    function log_company($id){
    	$user_sess_data = $this->session->userdata('MEM_SESS');
    	$sess_id_member = $user_sess_data['id'];

    	// insert data log
    	$data_db = $this->company_model->findById($id);
    	$data_member = $this->member_model->findViewById($sess_id_member);

    	$data['company_id']   	  = $id ; 
    	$data['member_id']   	  = $sess_id_member ; 
    	$data['name_in']          = $data_db['name_in'];
    	$data['address']          = $data_db['address'];
    	$data['city']             = $data_db['city'];
    	$data['postal_code']      = $data_db['postal_code'];
    	$data['t_number']         = $data_db['t_number'];
    	$data['headquarters']     = $data_db['headquarters'];
    	$data['website']          = $data_db['website'];
    	$data['description']      = $data_db['description'];
    	$data['is_invis_company'] = $data_db['is_invis_company'];
    	$data['img'] 			  = $data_db['img'];
    	$data['modify_date'] 	  = datetime_today();

    	// cari no urut terakhir 
		$this->db->select('max(urut) as max_urut');
    	$max_urut            = $this->findBy_log_company(['company_id'=>$id],1)['max_urut'];
    	$data['urut']        = $max_urut + 1;

    	$this->db->insert($this->table_company,null_empty($data));

    	$id_company_log      = $this->db->insert_id();
    	$data_compare        = $this->compare_log_company($id_company_log);

     	foreach ($data_compare as $key => $value) {
     		if ( strpos($key,'_is_different')  && $value == 'true') {
     			$key_real = str_replace("_is_different","",$key);
     			if ($key_real == 'img') {
     				$data_compare[$key_real.'_before'] = imageProfile($data_compare[$key_real.'_before'],'company',2) ;
     				$data_compare[$key_real.'_after']  = imageProfile($data_compare[$key_real.'_after'],'company',2) ;
     			}
     			$data_email['content'][] = [
     				'label' => $this->label_log_company($key_real),
     				'before' => $data_compare[$key_real.'_before'],
     				'after' => $data_compare[$key_real.'_after']
     			];
     		}
     	}
     	$data_email['content'] = empty($data_email['content']) ? [] : $data_email['content'];
    	// kalau ada yang beda sent email notif
    	if (!empty($data_email['content'])) {
	     	$sent_email['data_of_change'] = $this->parser->parse('apps/log/table_email.html',$data_email,TRUE);
	     	$sent_email['name']    = $data_member['member_name_without_prefix'];
	     	$sent_email['email']   = $data_member['member_email'];
	     	$sent_email['name_in'] = $data_member['company_in'];
			sent_email_by_category(22,$sent_email,'amar.ronaldo.m@gmail.com');
		}
    }
    function before_insert_log_company($id){
    	//  check sudah ada belum di database kalau belum add sebagai first log 
    	$check = $this->db->select('max(urut) as max_urut')->get_where($this->table_company,['company_id'=>$id])->row_array()['max_urut'];
    	if (empty($check)) {
	    	$data_db = $this->company_model->findById($id);

	    	$data['company_id']   	  = $id ; 
	    	$data['name_in']          = $data_db['name_in'];
	    	$data['address']          = $data_db['address'];
	    	$data['city']             = $data_db['city'];
	    	$data['postal_code']      = $data_db['postal_code'];
	    	$data['t_number']         = $data_db['t_number'];
	    	$data['headquarters']     = $data_db['headquarters'];
	    	$data['website']          = $data_db['website'];
	    	$data['description']      = $data_db['description'];
	    	$data['is_invis_company'] = $data_db['is_invis_company'];
	    	$data['img'] 			  = $data_db['img'];
	    	$data['modify_date'] 	  = datetime_today();

	    	$max_urut            = 1;
	    	$data['urut']        = $max_urut;
	    	$this->db->insert($this->table_company,null_empty($data));
    	}
    }
    function compare_log_company($id_log)
    {    
    	// ambil data sebelum nya dan di compare degan log sekarang
    	$data               = $this->findBy_log_company(['id'=>$id_log],1);

    	$where['company_id'] = $data['company_id'];
    	$where['urut']      = $data['urut']-1;
    	$data_sebelumnya    = $this->findBy_log_company($where,1);
    	// cari perbedaan kedua nya 
    	
    	$avoid_field = ['modify_date','company_id','id','urut','member_id'];
    	foreach ($data as $key => $value) {
    		if (!in_array($key, $avoid_field) ) {
				$data[$key.'_is_different'] = $data[$key] == $data_sebelumnya[$key] ? 'false' : 'true'; // status edited
				$data[$key.'_before'] = $data_sebelumnya[$key]; // data sebelum nya
				$data[$key.'_after'] = $data[$key] ; // data sesudah edit 
    		}
    	}
    	return $data;
    }
    function label_log_company($field_table)
    {
    	// label untuk menganti field table 
    	switch ($field_table) {
    		case 'name_in'          : return 'Company Name' ;                           break;
    		case 'address'          : return 'Company Address' ;                        break;
    		case 'city'             : return 'City' ;                                   break;
    		case 'postal_code'      : return 'Postal Code' ;                            break;
    		case 't_number'         : return 'Telephone number' ;                       break;
    		case 'headquarters'     : return 'Company Headquarters (country / state)' ; break;
    		case 'website'          : return 'Website' ;                                break;
    		case 'description'      : return 'Description' ;                            break;
    		case 'is_invis_company' : return 'Information not available for Public' ;   break;
    		case 'img'              : return 'Photo Company' ;                          break;
			default        		    : return $field_table;                              break;
    	}

    }

    /*=================================
    =            Committee            =
    =================================*/
    function findBy_log_committee($where,$is_single_row=0){
    	$this->db->select('a.*');
    	if($is_single_row==1){
    		return 	$this->db->get_where($this->table_committee.' a',$where)->row_array();
    	}
    	else{
    		return 	$this->db->get_where($this->table_committee.' a',$where)->result_array();
    	}
    }


    function log_committee(){	
    	$user_sess_data = $this->session->userdata('MEM_SESS');
    	$sess_id_member = $user_sess_data['id'];

	    $__modify_date = datetime_today();
	    $__member_id   = $sess_id_member ; 

    	// insert data log
		$this->db->select('max(urut) as max_urut');
    	$max_urut      = $this->findBy_log_committee(['member_id'=>$__member_id],1)['max_urut'];
	    $__urut   	   = $max_urut + 1 ; 

    	$data_db       = $this->auth_member_committee_model->findBy(['member_id'=>$__member_id]);
    	$data_member   = $this->member_model->findViewById($sess_id_member);


    	foreach ($data_db as $key => $value) {
    		$data_insert[] = [
    			'committee_id' => $value['committee_id'],
    			'member_id'    => $__member_id,
    			'modify_date'  => $__modify_date,
    			'urut'         => $__urut
    		] ;
    	}
    	$this->db->insert_batch('log_last_updated_member_committee',$data_insert);
    	
    	// cari no urut terakhir 
    	$data_email['content']  = $this->compare_log_committee($__urut);

     	$data_email['content'] = empty($data_email['content']) ? [] : $data_email['content'];
    	// kalau ada yang beda sent email notif
    	if (!empty($data_email['content'])) {
	     	$sent_email['data_of_change'] = $this->parser->parse('apps/log/table_email.html',$data_email,TRUE);
	     	$sent_email['name']    = $data_member['member_name_without_prefix'];
	     	$sent_email['email']   = $data_member['member_email'];
	     	$sent_email['name_in'] = $data_member['company_in'];
			sent_email_by_category(22,$sent_email,'amar.ronaldo.m@gmail.com');
		}
    }
    function before_insert_log_committee(){
    	$user_sess_data = $this->session->userdata('MEM_SESS');
    	$sess_id_member = $user_sess_data['id'];

		$__modify_date = datetime_today();
		$__member_id   = $sess_id_member ; 
		$__urut   	   = 1;     
    	//  check sudah ada belum di database kalau belum add sebagai first log 
    	$check = $this->db->select('max(urut) as max_urut')->get_where($this->table_committee,['member_id'=>$sess_id_member])->row_array()['max_urut'];

    	if (empty($check)) {
    		$data_db       = $this->auth_member_committee_model->findBy(['member_id'=>$sess_id_member]);	
    		if (empty($data_db)) {
    			$data_db[] = ['committee_id'=>0];
    		}
    		foreach ($data_db as $key => $value) {
    			$data_insert[] = [
    				'committee_id' => $value['committee_id'],
    				'member_id'    => $__member_id,
    				'modify_date'  => $__modify_date,
    				'urut'         => $__urut
    			] ;
    		}
    		$this->db->insert_batch('log_last_updated_member_committee',$data_insert);
    	}
    }
    function compare_log_committee($urut)
    {    
    	// ambil data sebelum nya dan di compare dengan log sekarang
    	$this->db->order_by('committee_id', 'asc');
    	$data               = $this->findBy_log_committee(['urut'=>$urut]);

    	$where['urut']      = $urut - 1;
    	$this->db->order_by('committee_id', 'asc');
    	$data_sebelumnya    = $this->findBy_log_committee($where);
    	// cari perbedaan kedua nya 
    	foreach ($data as $value) {
    		$data_compare_after[] = $value['committee_id'];
    	}
    	foreach ($data_sebelumnya as $value) {
    		$data_compare_before[] = $value['committee_id'];
    	}

    	$diff = 0 ; 
    	$check_diff = array_diff($data_compare_after, $data_compare_before);
    	$diff = $check_diff ? 1 : 0;

    	if ((count($data_sebelumnya) != count($data) || $diff)) {
    		$data_before_name = $this->db->select('group_concat(name) as concat_name')->where_in('id',$data_compare_before)->get('committee')->row_array()['concat_name'];
    		$data_after_name = $this->db->select('group_concat(name) as concat_name')->where_in('id',$data_compare_after)->get('committee')->row_array()['concat_name'];

    		$data_ret[] = [
    			'label' => 'Committee',
    			'before' => $data_before_name,
				'after' => $data_after_name
	    	];
    	}else{
    		$data_ret[] = [];
    	}
    	return $data_ret;
    }

     /*=================================
    =            Sector            =
    =================================*/
    function findBy_log_sector($where,$is_single_row=0){
    	$this->db->select('a.*');
    	if($is_single_row==1){
    		return 	$this->db->get_where($this->table_sector.' a',$where)->row_array();
    	}
    	else{
    		return 	$this->db->get_where($this->table_sector.' a',$where)->result_array();
    	}
    }


    function log_sector(){	
    	$user_sess_data = $this->session->userdata('MEM_SESS');
    	$sess_id_company = $user_sess_data['company_id'];
    	$sess_id_member = $user_sess_data['id'];



	    $__modify_date = datetime_today();
	    $__company_id   = $sess_id_company ; 

    	// insert data log
		$this->db->select('max(urut) as max_urut');
    	$max_urut      = $this->findBy_log_sector(['company_id'=>$__company_id],1)['max_urut'];
	    $__urut   	   = $max_urut + 1 ; 

    	$data_db       = $this->auth_member_sector_model->findBy(['company_id'=>$__company_id]);
    	$data_member   = $this->member_model->findViewById($sess_id_member);


    	foreach ($data_db as $key => $value) {
    		$data_insert[] = [
    			'sector_id' => $value['sector_id'],
    			'company_id'    => $__company_id,
    			'modify_date'  => $__modify_date,
    			'urut'         => $__urut
    		] ;
    	}
    	$this->db->insert_batch('log_last_updated_member_sector',$data_insert);
    	
    	// cari no urut terakhir 
    	$data_email['content']  = $this->compare_log_sector($__urut);

     	$data_email['content'] = empty($data_email['content']) ? [] : $data_email['content'];
    	// kalau ada yang beda sent email notif
    	if (!empty($data_email['content'])) {
	     	$sent_email['data_of_change'] = $this->parser->parse('apps/log/table_email.html',$data_email,TRUE);
	     	$sent_email['name']    = $data_member['member_name_without_prefix'];
	     	$sent_email['email']   = $data_member['member_email'];
	     	$sent_email['name_in'] = $data_member['company_in'];
			sent_email_by_category(22,$sent_email,'amar.ronaldo.m@gmail.com');
		}
    }
    function before_insert_log_sector(){
    	$user_sess_data = $this->session->userdata('MEM_SESS');
    	$sess_id_company = $user_sess_data['company_id'];
    	$sess_id_member = $user_sess_data['id'];


		$__modify_date = datetime_today();
		$__company_id   = $sess_id_company ; 
		$__urut   	   = 1;     
    	//  check sudah ada belum di database kalau belum add sebagai first log 
    	$check = $this->db->select('max(urut) as max_urut')->get_where($this->table_sector,['company_id'=>$__company_id])->row_array()['max_urut'];

    	if (empty($check)) {
    		$data_db       = $this->auth_member_sector_model->findBy(['company_id'=>$__company_id]);	
    		if (empty($data_db)) {
    			$data_db[] = ['sector_id'=>0];
    		}
    		foreach ($data_db as $key => $value) {
    			$data_insert[] = [
    				'sector_id' => $value['sector_id'],
    				'company_id'    => $__company_id,
    				'modify_date'  => $__modify_date,
    				'urut'         => $__urut
    			] ;
    		}
    		$this->db->insert_batch('log_last_updated_member_sector',$data_insert);
    	}
    }
    function compare_log_sector($urut)
    {    
    	// ambil data sebelum nya dan di compare dengan log sekarang
    	$this->db->order_by('sector_id', 'asc');
    	$data               = $this->findBy_log_sector(['urut'=>$urut]);

    	$where['urut']      = $urut - 1;
    	$this->db->order_by('sector_id', 'asc');
    	$data_sebelumnya    = $this->findBy_log_sector($where);
    	// cari perbedaan kedua nya 
    	foreach ($data as $value) {
    		$data_compare_after[] = $value['sector_id'];
    	}
    	foreach ($data_sebelumnya as $value) {
    		$data_compare_before[] = $value['sector_id'];
    	}

    	$diff = 0 ; 
    	$check_diff = array_diff($data_compare_after, $data_compare_before);
    	$diff = $check_diff ? 1 : 0;

    	if ((count($data_sebelumnya) != count($data) || $diff)) {
    		$data_before_name = $this->db->select('group_concat(name) as concat_name')->where_in('id',$data_compare_before)->get('sector')->row_array()['concat_name'];
    		$data_after_name = $this->db->select('group_concat(name) as concat_name')->where_in('id',$data_compare_after)->get('sector')->row_array()['concat_name'];

    		$data_ret[] = [
    			'label' => 'Sector',
    			'before' => $data_before_name,
				'after' => $data_after_name
	    	];
    	}else{
    		$data_ret[] = [];
    	}
    	return $data_ret;
    }

    function insert($data){
    	$data['user_id_create'] = id_user();
    	$this->db->insert($this->table,null_empty($data));
    	detail_log();
    	return $this->db->insert_id();
    }
    function update($data,$id){
    	$where['id_auth_user'] = $id;
    	$data['user_id_modify'] = id_user();
    	$data['modify_date'] 	= date('Y-m-d H:i:s');
    	$this->db->update($this->table,null_empty($data),$where);
    	detail_log();
    	return $id;
    }
    function delete($id){
    	$data['is_delete'] = 1;
    	$this->update($data,$id);
    }



	function findById($id){
		$where['a.id_auth_user'] = $id;
		$where['a.is_delete'] = 0;
		$this->db->select('a.*,b.approval_level');
		$this->db->join('auth_user_grup b','a.id_auth_user_grup = b.id_auth_user_grup');
		return 	$this->db->get_where($this->tableAs,$where)->row_array();
	}
	function findBy($where,$is_single_row=0){
		$where['is_delete'] = 0;
		$this->db->select('*');
		if($is_single_row==1){
			return 	$this->db->get_where($this->tableAs,$where)->row_array();
		}
		else{
			return 	$this->db->get_where($this->tableAs,$where)->result_array();
		}
	} 

	function fetchRow($where) {
		return $this->findBy($where,1);
	}

 }
