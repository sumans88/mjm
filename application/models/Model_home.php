<?php
class Model_home extends  CI_Model{
    function __construct(){
       parent::__construct(); 
    }
	 function list_data($id_profil_mitra){
	    
	    
		//$q = $this->db->query("(SELECT 'Naratif Report' as kat, id_jenis_laporan, name, tgl_jatuh_tempo
		//					FROM narative_report_schedule where tgl_jatuh_tempo >= now())
		//					UNION
		//					(SELECT 'Financial Report' as kat,id_jenis_laporan, name, tgl_jatuh_tempo
		//					FROM financial_report_schedule where tgl_jatuh_tempo >= now())
		//					ORDER BY tgl_jatuh_tempo desc LIMIT 5")->result_array();
		
		
		$q = $this->db->query("(SELECT 'Naratif Report' as kat
, id_jenis_laporan
, name
, tgl_jatuh_tempo
FROM narative_report_schedule n
left join data_program d on d.id = n.id_data_program
left join auth_user a on d.id_profil_mitra = a.profil_mitra_id 
where tgl_jatuh_tempo >= now() and a.profil_mitra_id=$id_profil_mitra)
UNION
(SELECT 'Financial Report' as kat
,id_jenis_laporan
, name
, tgl_jatuh_tempo
FROM financial_report_schedule f
left join data_program d on d.id = f.id_data_program
left join auth_user a on d.id_profil_mitra = a.profil_mitra_id 
where tgl_jatuh_tempo >= now() and a.profil_mitra_id=$id_profil_mitra)
ORDER BY tgl_jatuh_tempo desc LIMIT 5")->result_array();
		return $q;
    }
 }
