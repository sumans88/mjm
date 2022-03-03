<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('eventmodel');
        $this->load->model('languagemodel');
        $this->load->model('eventCategoryModel');
        $this->load->model('eventFilesModel');
        $this->load->model('eventImagesModel');
        $this->load->model('galleryModel');
        $this->load->model('galleryImagesModel');
        $this->load->model('galleryDetailModel');
        $this->load->model('languagemodel');
        $this->load->model('tagsModel');
        $this->load->model('newsTagsModel');
        $this->load->model('eventTagsModel');
        $this->load->model('newsTagsVersionModel');
        $this->load->model('Tipe_form_registrasi_model');
        $this->load->model('template_form_registrasi_model');
        $this->load->model('Template_tipe_input_form_register_model');
        $this->load->model('tipe_form_registrasi_model');
        $this->load->model('eventPriceModel');
        $this->load->model('galleryTagsModel');
        $this->load->model('newsModel');
        $this->load->model('authgroup_model','authGroupModel');
        $this->load->model('NewsTagsModel');
        $this->load->model('slideshowModel');
        $this->load->model('web_config_model');
        $this->load->model('eventprice_model');
    }
    function index(){
        // $data['list_cat'] = selectlist2(array('table'=>'ref_kategori_logframe','title'=>'All Category'));
        $lang = default_lang_id();
        $data['select_category'] = selectlist2(array('table'=>'event_category','title'=>'All Category','where'=> array('is_hide'=>0, 'id_lang'=>$lang,'is_delete'=>0 ,'id_parent_category'=>0)));  // event category // amcham/ / non-amcham
        $data['list_status_publish'] = selectlist2(array('table'=>'status_publish','title'=>'All Status'));
        
        $data['page_title'] = "Event";
        render('apps/event/index',$data,'apps');
    }
    public function add($id=''){
        if($id){
            $id_lang_default = $this->languagemodel->langId();
            $datas           = $this->eventmodel->selectData($id);
            if(!$datas){
                die('404');
            }

            $data                   = quote_form($datas);
            $data['judul']          = 'Edit';
            $data['proses']         = 'Update';
            $data['id']             = $id;
        }else{
            $this->eventFilesModel->updateByOther(array('is_delete' => 1), array('id_event' => 0));
            $data['judul']              = 'Add';
            $data['proses']             = 'Save';
            $data['name_data']          = '';
            $data['start_time']         = '00:00';
            $data['end_time']           = '00:00';
            $data['uri_path']           = '';
            $data['teaser']             = '';
            $data['location_name']      = '';
            $data['location_address']   = '';
            $data['content']            = '';
            $data['price']            = '';
            $data['start_date']         = '';
            $data['brochures']          = '';
            $data['sponsor']          = '';
            $data['end_date']           = '';
            $data['speaker']            = '';
            $data['id']                 = '';
            $data['id_event']           = '';
            $data['id_parent_lang']     = '';
            $data['seo_title']          = '';
            $data['meta_description']   = '';
            $data['meta_keywords']      = '';
            $data['is_not_available']   = '';
            $data['maps_location']      = '';
        }

        /*untuk tags*/
        $tags_data = $this->db->get_where('tags',array('is_delete'=>0))->result_array();
        $data['tags'] = generate_tags($tags_data,'name');
        /*end untuk tags*/

        /*untuk price*/
        $price_data = $this->db->get_where('price',array('is_delete'=>0))->result_array();
        $data['price'] = generate_tags($price_data,'name');
        /*end untuk tags*/

        /*untuk menu tags*/
        $this->db->group_by('extra_param');
        $this->db->order_by('extra_param','asc');
        $this->db->where("extra_param != '' AND extra_param != '-'");
        $tags_menu = $this->db->get_where('frontend_menu',array('is_delete'=>0, 'id_status_publish'=>2, 'name !='=>'', 'id_language'=>1))->result_array();
        $data['menu_tags'] = generate_tags($tags_menu,'extra_param');
        /*end untuk menu tags*/

        $data['list_lang']  = $this->languagemodel->langName();

        $n = 0;

        $detail_images = $this->eventImagesModel->findBy(array('id_event'=>$datas[0]['id'],'is_delete'=>0));
        if(!empty($detail_images)){

            // $img_detail = array_filter(explode(",", $datas[0]['detail_img']));
            $is_img_share = 1 ;
            foreach ($detail_images as $key_detail => $value_detail){
                $img_thumb          = ($value_detail['filename']=='') ? image('no_image.png','small') : image($value_detail['filename'],'small');
                $imagemanager       = imagemanager('detail_img',$img_thumb,750,186,'_'.$key_detail,'[]',$img_detail[$key_detail]);

                $is_checked   = ($value_detail['is_share'] == 1) ? 'checked' : '';
                $is_check     = ($value_detail['is_share'] == 1) ? 1 : 0;
                
                $is_img_share = ($is_check == 1) ? 0 :$is_img_share;
                
                $data['list_lang'][0]['images_detail'][$key_detail]['no_img_detail']            = $n++;
                $data['list_lang'][0]['images_detail'][$key_detail]['id_disclaim']              = $n;

                $data['list_lang'][0]['images_detail'][$key_detail]['id_images_detail']         = $value_detail['id'];
                $data['list_lang'][0]['images_detail'][$key_detail]['is_share_checked']         = $is_checked;
                $data['list_lang'][0]['images_detail'][$key_detail]['is_share']                 = $is_check;
                $data['list_lang'][0]['images_detail'][$key_detail]['form_images_detail']       = $imagemanager['browse'];
                $data['list_lang'][0]['images_detail'][$key_detail]['description_images_detail']    = $value_detail['description'];
                
                $data['list_lang'][0]['images_detail'][$key_detail]['form_images_detail_label'] = $key_detail == 0?'Image Detail':'';
                $data['list_lang'][0]['images_detail'][$key_detail]['invis_del_img']            = $key_detail == 0 ?'del-img-detail-first':'';

            }
            // $img_thumb               = image($data['img'],'small');
            $imagemanager               = imagemanager('detail_img',image('','small'),750,186,'','[]','');
            $data['form_images_detail'] = $imagemanager['browse'];
        }else{
            $data['list_lang'][0]['invis_del_img']                                 = 'del-img-detail-first';
            $data['list_lang'][0]['images_detail'][0]['id_images_detail']          = '';
            $data['list_lang'][0]['images_detail'][0]['form_images_detail_label']  = 'Image Detail';
            $data['list_lang'][0]['images_detail'][0]['no_img_detail']             = $n++;
            $data['list_lang'][0]['images_detail'][0]['description_images_detail'] = '';
            $data['list_lang'][0]['images_detail'][0]['is_share_checked']          = '';
            $data['list_lang'][0]['images_detail'][0]['is_share']                  = 0;
            $data['list_lang'][0]['images_detail'][0]['id_disclaim']               = '';

            $img_thumb                      = image('','small');
            $imagemanager                   = imagemanager('detail_img',$img_thumb,750,186,$key_detail,'[]','');
            $data['form_images_detail']     = $imagemanager['browse'];
        }

        foreach ($data['list_lang'] as $key => $value){
            $data['list_lang'][$key]['invis']                       = ($key==0 || ($key==1 && $datas[0]['is_not_available'] == 1)) ? '' : 'hide';
            $data['list_lang'][$key]['active']                      = ($key==0) ? 'active in' : '';
            $data['list_lang'][$key]['validation']                  = ($key==0) ? 'true' : 'false';
            $data['list_lang'][$key]['nomor']                       = $key;
                            
            
            
            $data['list_lang'][$key]['id_event']                    = $datas[$key]['id'];
            $data['list_lang'][$key]['name_data']                   = quote_form($datas[$key]['name']);
            $data['list_lang'][$key]['start_time']                  = ($datas[$key]['start_time']) ? $datas[$key]['start_time']: '00:00';
            $data['list_lang'][$key]['end_time']                    = ($datas[$key]['end_time']) ? $datas[$key]['end_time']: '00:00';
            $data['list_lang'][$key]['teaser']                      = quote_form($datas[$key]['teaser']);
            $data['list_lang'][$key]['uri_path']                    = $datas[$key]['uri_path'];
            $data['list_lang'][$key]['is_open']                     = ($datas[$key]['is_open']==1) ? 'checked' : '';
            $data['list_lang'][$key]['content']                     = $datas[$key]['content'];
            $data['list_lang'][$key]['price']                       = $datas[$key]['price'];
            $data['list_lang'][$key]['speaker']                     = $datas[$key]['speaker'];
            $data['list_lang'][$key]['location_name']               = str_ireplace(array("<br />"), "", $datas[$key]['location_name']);
            $data['list_lang'][$key]['location_address']            = str_ireplace(array("<br />"), "", $datas[$key]['location_address']);
            // $data['list_lang'][$key]['location_name']               = $datas[$key]['location_name'];
            // $data['list_lang'][$key]['location_address']            = $datas[$key]['location_address'];
            $data['list_lang'][$key]['start_date']                  = iso_date($datas[$key]['start_date']);
            $data['list_lang'][$key]['end_date']                    = iso_date($datas[$key]['end_date']);
            $data['list_lang'][$key]['sponsor']                     = $datas[$key]['sponsor'];
            $data['list_lang'][$key]['seo_title']                   = $datas[$key]['seo_title'];
            $data['list_lang'][$key]['meta_description']            = $datas[$key]['meta_description'];
            $data['list_lang'][$key]['meta_keywords']               = $datas[$key]['meta_keywords'];
            $data['list_lang'][$key]['is_not_available']            = $datas[$key]['is_not_available'] ? 'checked' : '';
            $data['list_lang'][$key]['maps_location']               = $datas[$key]['maps_location'];
            $data['list_lang'][$key]['event_files']                 = $this->show_files($datas[$key]['id'], $key);
            $data['list_lang'][$key]['bahasa']                      = ucwords($value['lang_name']);
            
            $data['list_lang2'][$key]['lang_name']                  = ucwords($value['lang_name']);
            
            /*new script (dwiki)*/
            $data['list_lang'][$key]['publish_date2']               = ($datas[$key]['publish_date']!='0000-00-00')?iso_date($datas[$key]['publish_date']):'';
            
            $data['list_lang'][$key]['list_template_form_register'] = selectlist2(array('table'=>'ref_template_form_register','title'=>'Select Template','where'=>array('id_status_publish' => 2,'is_delete' => 0),'selected'=>$datas[$key]['id_template_form_register']));
            
            $data['list_lang'][$key]['list_status_publish']         = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$datas[$key]['id_status_publish']));
            
            /*end new script (dwiki)*/
            
            $img_thumb                                              = image($datas[$key]['img'],'small');
            $imagemanager                                           = imagemanager('img',$img_thumb,320,180,$key,$datas[$key]['name'],'title'.$key);
            $data['list_lang'][$key]['img']                         = $imagemanager['browse'];
            $data['list_lang'][$key]['imagemanager_config']         = $imagemanager['config'];
            $data['list_lang'][$key]['id_event_category']           = selectlist2(array('table'=>'event_category','title'=>'All Category','where'=> array('is_hide'=>0, 'id_lang'=>1,'is_delete'=>0 ,'id_parent_category'=>0),'selected'=>$datas[$key]['id_event_category']));
            if ($datas[$key]['id_event_category']) {
                $data['list_lang'][$key]['id_event_subcategory']    = selectlist2(array('table'=>'event_category','title'=>'All Category','where'=> 'is_hide = 0 and id_lang = 1 and is_delete = 0 and id_parent_category <> 0 and id_parent_category = '.$datas[$key]['id_event_category'],'selected'=>$datas[$key]['id_event_subcategory']));
            }else{
                $data['list_lang'][$key]['id_event_subcategory']    = '';
            }

            /*untuk tag*/
                    $this->db->select('a.*,b.name as tags,b.uri_path');
                    $this->db->join('tags b','b.id = a.id_tags');
            $tags = $this->db->get_where('event_tags a',array('id_event'=>$datas[$key]['id']))->result_array();
            $data['list_lang'][$key]['tags_data']               = $tags ?generate_tags($tags,'tags') :"";
            /*end untuk tag*/

            /*untuk price*/
            $prices = $this->eventPriceModel->findBy(array('id_event'=>$datas[$key]['id']));
            $data['list_lang'][$key]['price_data'] = "'";
            foreach ($prices as $key1 => $value1) {
                $data['list_lang'][$key]['price_data']  .= ';'.$value1['tags'];
            }
            $data['list_lang'][$key]['price_data'] .= ";'";


            // $data['list_lang'][$key]['price_data'] = generate_tags($prices,'tags',';');

            // print_r($data['list_lang'][$key]['price_data']);exit;
            /*end untuk price*/

            /*untuk menu tag*/
                    $this->db->select('a.*,b.name as tags,b.extra_param as extra_param');
                    $this->db->join('frontend_menu b','b.id = a.id_menu');
            $menutags = $this->db->get_where('event_menu_tags a',array('id_event'=>$datas[$key]['id']))->result_array();
            
            $data['list_lang'][$key]['menutags_data']               = generate_tags($menutags,'extra_param');

            /*end untuk menu tag*/
        }

        /*$data['filemanager_config']   = $filemanager['config'];*/
        $data['list_lang2']     = $data['list_lang'];
        foreach ($data['list_lang2'] as $kx => $vx) {
            $data['list_lang2'][$kx]['lang_name'] = ucwords($vx['lang_name']);
        }

        $data['multiple_file_script']   = get_event_files("event/add_multiple_file.html", '', 0);
        $data['modal_add_price']        = $this->parser->parse("apps/event/modal_add_price.html", $this->data,true);
        render('apps/event/add',$data,'apps');
    }

    function show_files($idevent, $key){
        $allFiles = array();
        
        if($key == 0){
            $id_lang = 1;
        } else{
            $id_lang = 2;
        }
        $wh['is_delete']    = 0;
        $wh['id_event']     = $idevent;
        $wh['id_lang']      = $id_lang;
        $allFiles['nomorFile']          = $key;
        $allFiles['list_files'] = array();
        $listFiles = $this->eventFilesModel->listFiles($wh);
        foreach ($listFiles as $key => $value) {
            $ext = pathinfo($value['filename'], PATHINFO_EXTENSION);
            if($ext == "pdf"){
                $listFiles[$key]['imageFile'] = "file-pdf.jpg";
            }
            if($ext == "xls" | $ext == "xlsx"){
                $listFiles[$key]['imageFile'] = "file-excel.jpg";
            }
            if($ext == "doc" | $ext == "docx"){
                $listFiles[$key]['imageFile'] = "file-word.jpg";
            }
            if($ext == "ppt" | $ext == "pptx"){
                $listFiles[$key]['imageFile'] = "file-ppt.jpg";
            }
        }
        $allFiles['list_files'] = $listFiles;

        $allFiles['jmlFiles']       = count($allFiles['list_files']);
        $allFiles['showUploadAll']  = (count($allFiles['list_files']) != NULL ? 'block' : 'none');

        return get_event_files("event/list_files.html", $allFiles, 1);
    }

    function uploadFiles($idedit=''){
        $post = purify($this->input->post(NULL,TRUE));
        $data = array();

        $data_save['name']          = $post['nameFile'];
        $id_event                   = $post['id_event'];
        $id_lang                    = $post['id_lang'];
        if($post['id_lang'] == 1){
            $getID = $this->db->select('id')->get_where('event', array('id_parent_lang' => $post['id_event'], 'is_delete' => 0))->row_array();
            $id_event_new = $getID['id'];
            $id_lang_new = 2;
        } else{
            $getID = $this->db->select('id_parent_lang')->get_where('event', array('id' => $post['id_event'], 'is_delete' => 0))->row_array();
            $id_event_new = $getID['id_parent_lang'];
            $id_lang_new = 1;
        }
        if($post["statusFile"] == 0){
            foreach($_FILES as $index => $file){
                $uploadFile = multipleUpload($file, './document/material/', 20000000);
                
                if($uploadFile == true){
                    
                    $data_save['filename']  = $uploadFile['file_name'];

                    if($id_lang == 1){
                        auth_insert();
                        $data_save['id_event']  = $id_event;
                        $data_save['id_lang']   = $id_lang;
                        $ret['message'] = 'Insert Success';
                        $act            = "Insert Event File";
                        $iddata         = $this->eventFilesModel->insert($data_save);
                    } else{
                        auth_insert();
                        $data_save['id_event']  = $id_event_new;
                        $data_save['id_lang']   = $id_lang_new;
                        $ret['message'] = 'Insert Success';
                        $act            = "Insert Event File";
                        $iddata         = $this->eventFilesModel->insert($data_save);
                    }

                    $data_save['id_parent_lang']    = $iddata;

                    if($id_lang == 1){
                        auth_insert();
                        $data_save['id_event']  = $id_event_new;
                        $data_save['id_lang']   = $id_lang_new;
                        $ret['message'] = 'Insert Success';
                        $act            = "Insert Event File";
                        $iddata         = $this->eventFilesModel->insert($data_save);
                    } else{
                        auth_insert();
                        $data_save['id_event']  = $id_event;
                        $data_save['id_lang']   = $id_lang;
                        $ret['message'] = 'Insert Success';
                        $act            = "Insert Event File";
                        $iddata         = $this->eventFilesModel->insert($data_save);
                    }

                    $data["idFile"] = $post["imag"];
                    $data["statusFile"] = $post["statusFile"];
                }
            }
        } else{
            $idedit = $post["idSavedFile"];

            auth_update();
            $ret['message'] = 'Update Success';
            $act            = "Update Event File";
            $iddata         = $this->eventFilesModel->updateAll($data_save,$idedit);
            $data["idFile"] = $post["imag"];
            $data["statusFile"] = $post["statusFile"];
        }

        detail_log();
        insert_log($act);
        $this->db->trans_complete();
        set_flash_session('message', $ret['message']);

        if($iddata != 0){
            $data["idSavedFile"] = $iddata;
            $data["status"] = true;
        } else{
            $data["idSavedFile"] = 0;
            $data["status"] = false;
        }

        $data["reloadFiles"] = $this->show_files($post['id_event'], $post['key']);

        echo json_encode($data);
    }

    function deleteFile(){
        $post = purify($this->input->post(NULL,TRUE));

        if($key == 0){
            $getID = $this->db->select('id')->get_where('event_files', array('id_parent_lang' => $post['idSavedFile'], 'is_delete' => 0))->row_array();
            $id_event_new = $getID['id'];
        } else{
            $getID = $this->db->select('id_parent_lang')->get_where('event_files', array('id' => $post['idSavedFile'], 'is_delete' => 0))->row_array();
            $id_event_new = $getID['id_parent_lang'];
        }

        auth_delete();
        $this->eventFilesModel->delete($post['idSavedFile']);
        // unlink('./document/material/'.$post['filename']);
        detail_log();
        insert_log("Delete Event File");

        auth_delete();
        $this->eventFilesModel->delete($id_event_new);
        // unlink('./document/material/'.$post['filename']);
        detail_log();
        insert_log("Delete Event File");

        $data["idFile"] = $post["imag"];
        $data["status"] = true;
        $data["reloadFiles"] = $this->show_files($post['id_event'], $post['key']);

        echo json_encode($data);
    }

    public function view($id=''){
        if($id){
            $datas  = $this->eventmodel->findById($id);
            // $data = $this->eventmodel->findById($id);
            if(!$datas){
                die('404');
            } 
            else {
                foreach ($datas as $key => $value) {
                    $data['name'] =  $datas['name'];
                }

                if ($datas['id_template_form_register'] != 0){
                    $this->db->order_by('sort', 'asc');
                    $field_event = $this->Template_tipe_input_form_register_model->findBy(array('id_template'=>$datas['id_template_form_register']));

                    foreach ($field_event as $key2 => $value2) {
                        $data_field = $this->Tipe_form_registrasi_model->findById($value2['id_tipe_input']);
                        
                        $name  = $data_field['name'];
                        $param = $data_field['parameter'];

                        $data['header_title'] .= "<th class='center' title='Sort' id='".$param."'>".$name."<span></span></th>";                 
                        $data['header_search'] .= ($data_field['id_ref_tipe_input'] == 4) ? "<td></td>": "<td><input type='text' class='cari form-control' id='search_".$param."' placeholder='Search...'></td>";
                    }

                } else {
                    $data['header_search'] = '';
                    $data['header_title'] = '';
                }

            }
        }
        $data['id_event'] = $id;
        render('apps/event/view',$data,'apps');
    }

    public function hits_files($id=''){
        $wh['is_delete']    = 0;
        $wh['id_event']     = $id;
        $data['list_files'] = array();
        $listFiles = $this->eventFilesModel->listFiles($wh);
        foreach ($listFiles as $key => $value) {
            $ext = pathinfo($value['filename'], PATHINFO_EXTENSION);
            if($ext == "pdf"){
                $listFiles[$key]['imageFile'] = "file-pdf.jpg";
            }
            if($ext == "xls" | $ext == "xlsx"){
                $listFiles[$key]['imageFile'] = "file-excel.jpg";
            }
            if($ext == "doc" | $ext == "docx"){
                $listFiles[$key]['imageFile'] = "file-word.jpg";
            }
            if($ext == "ppt" | $ext == "pptx"){
                $listFiles[$key]['imageFile'] = "file-ppt.jpg";
            }
        }
        $data['list_files'] = $listFiles;
        render('apps/event/hits_files',$data,'apps');
    }

    function records(){
        $data = $this->eventmodel->records();
        foreach ($data['data'] as $key => $value) {
            /*if($data['data'][$key]['is_not_available'] == 1){
                $this->db->select('a.*, b.name as category');
                $this->db->join('event_category b',"b.id = a.id_event_category",'left');
                $getData = $this->db->get_where('event a', array('a.id_parent_lang'=>$data[$key]['id']))->row_array();
                $data['data'][$key]['name'] = quote_form($getData['name']);
                $data['data'][$key]['name_e'] = quote_form($getData['name']);
                $data['data'][$key]['start_date'] = iso_date($getData['start_date']);
                $data['data'][$key]['end_date'] = iso_date($getData['end_date']);
                $data['data'][$key]['category'] = $getData['category'];
                
                $data['data'][$key]['teaser']   = $getData['teaser'];
                $data['data'][$key]['speaker']  = $getData['speaker'];
            } else{*/
                $data['data'][$key]['name']       = ($value['name'] != 'NULL' ? quote_form($value['name']) : '');
                $data['data'][$key]['name_e']     = ($value['title_e'] != 'NULL' ? quote_form($value['title_e']) : '');

                // kalo ada start dan end
                if (!empty(iso_date($value['start_date'])) &&  !empty(iso_date($value['end_date']))) {
                    // kalo sama harinya
                    if ( iso_date($value['start_date']) == iso_date($value['end_date']) ) {
                        $data['data'][$key]['event_date'] = iso_date($value['start_date']);
                    }else{
                        $data['data'][$key]['event_date'] = iso_date($value['start_date']).' to '.iso_date($value['end_date']);
                    }
                }else{
                    $data['data'][$key]['event_date']     = " - ";
                }
                
                $data['data'][$key]['category']   = $value['category'];
                $data['data'][$key]['is_close']   = ($value['is_close'] == 1 ? 'hide' : '');
            /*}*/
            
        }
        render('apps/event/records',$data,'blank');
    }   
    function records_participant($id){
        $data  = $this->eventmodel->records_participant($id); //data partisipan
        $event = $this->eventmodel->findById($id); // data event

        foreach ($data['data'] as $key => $value) { 
                           $this->db->order_by('sort', 'asc');
            $field_event = $this->Template_tipe_input_form_register_model->findBy(array('id_template'=>$event['id_template_form_register']));

            foreach ($field_event as $key2 => $value2) {
                $field = $this->Tipe_form_registrasi_model->findById($value2['id_tipe_input'])['parameter'];
                $data['data'][$key][$field] = template_event_value($field,$data['data'][$key][$field]);

                $data['data'][$key]['field']    .= '<td>'.$data['data'][$key][$field].'</td>';
            }

            // $data['data'][$key]['id_ref_handicap']   ="awd";
            $this->load->model('paymentconfirmation_model');
            
            $data['data'][$key]['status']          = ($value['is_approve'] == 1 ? 'Paid' : '-');
            $data['data'][$key]['id']              = $value['id'];
            // $data['data'][$key]['event_id']        = (!empty($value['parent_lang']) ? $value['parent_lang'] :  $value['event_id']);
            $data['data'][$key]['event_id']        = $value['event_id'];
            $data['data'][$key]['st']              = ($value['is_approve'] == 1 ? 'invis' : '' );
        }
        render('apps/event/records_participant',$data,'blank');
    }   
    function prosesdocument($field){
        $file = $_FILES[$field];
        $fname = rand(100,999).'-'.$file['name'];
        move_uploaded_file($file['tmp_name'],"document/temp/$fname");
        $ret['fname'] = "$fname";
        echo json_encode($ret);
    }
    function proses($idedit=''){
        $id_user                = id_user();
        $this->layout           = 'none';
        $post                   = purify($this->input->post());
        
        $ret['error']           = 1;
        $id_parent_lang         = NULL;
        $this->db->trans_start(); 
        // $id_parent       = $this->languagemodel->langId();
        $this->form_validation->set_rules('name', '"Page Name"', 'required'); 
        $this->form_validation->set_rules('uri_path', '"Page URL"', 'required'); 
        $this->form_validation->set_rules('teaser', '"Teaser"', 'required'); 
        $this->form_validation->set_rules('content', '"Content"', 'required'); 
        $this->form_validation->set_rules('seo_title', '"SEO Title"', 'required'); 
        $this->form_validation->set_rules('meta_description', '"Meta Description"', 'required'); 
        /* cek uripath */
        if($idedit){
            $where['a.id !='] = $idedit;
        }
        $where['a.uri_path'] = $post['uri_path'][0];
        $unik                = $this->eventmodel->findBy($where);
            
        if($unik){
            $ret['message'] = "Page URL $value already taken";
            echo json_encode($ret);exit;
        }
        /* end cek uripath */

        if ($this->form_validation->run() == FALSE){
            $ret['message']     = validation_errors(' ',' ');
        }else{

            foreach ($post['teaser'] as $key => $value){
                $id_eventx = $post['id_event'][$key];
                if($key==0){
                    $id_event_category  = $post['id_event_category'][$key];

                    /*new script (dwiki)*/
                    if ($post['is_not_available'][$post['id_lang'][$key]]) {
                        $publish_date       = $post['publish_date'][1];
                        $id_status          = 1;
                    } else {
                        $publish_date       = $post['publish_date'][$key];
                        $id_status          = $post['id_status_publish'][$key];
                    }
                    /*end new script (dwiki)*/
                }
                else{
                    if($post['is_not_available'][$post['id_lang'][0]]){
                        $id_event_category  = $post['id_event_category'][$key];

                        /*new script (dwiki)*/
                        $publish_date       = $post['publish_date'][$key];
                        $id_status          = $post['id_status_publish'][$key];
                        /*end new script (dwiki)*/
                    } else{
                        /*new script (dwiki)*/
                        if ($post['is_not_available'][$post['id_lang'][$key]]) {
                            $publish_date       = $post['publish_date'][0];
                            $id_status          = 1;
                        }
                        /*end new script (dwiki)*/

                        $id_event_category  = db_get_one('event_category','id',array('id_parent_lang'=>$post['id_event_category'][0],'id_lang'=>$post['id_lang'][$key])); 
                    }
                }
                if($idedit){
                    $where['id !=']     = $idedit;
                }

                $data_save['publish_date']         = ($post['publish_date'][$key]) ? iso_date($post['publish_date'][$key]) : date('Y-m-d');
                $data_save['id_status_publish']    = $id_status;
                
                $data_save['id_event_category']    = $id_event_category;
                $data_save['id_event_subcategory'] = $post['id_event_subcategory'][0];
                $data_save['name']                 = $post['name'][$key];
                $data_save['location_name']        = nl2br($post['location_name'][0]);
                $data_save['location_address']     = nl2br($post['location_address'][0]);

                // $data_save['location_name'] = htmlspecialchars_decode(urldecode($post['location_name'.$key.'']));
                // $data_save['location_address'] = htmlspecialchars_decode(urldecode($post['location_address'.$key.'']));
                $data_save['maps_location']     = ($post['is_not_available'][$post['id_lang'][0]] ? $post['maps_location'][1] : $post['maps_location'][0]);
                $data_save['start_time']        = ($post['is_not_available'][$post['id_lang'][0]] ? $post['start_time'][1] : $post['start_time'][0]);
                $data_save['end_time']          = ($post['is_not_available'][$post['id_lang'][0]] ? $post['end_time'][1] : $post['end_time'][0]);
                $data_save['start_date']        = ($post['is_not_available'][$post['id_lang'][0]] ? iso_date($post['start_date'][1]) : iso_date($post['start_date'][0]));
                $data_save['end_date']          = ($post['is_not_available'][$post['id_lang'][0]] ? iso_date($post['end_date'][1]) : iso_date($post['end_date'][0]));
                $data_save['speaker']           = ($post['is_not_available'][$post['id_lang'][0]] ? $post['speaker'][$key] : $post['speaker'][0]);
                $data_save['is_open']           = ($post['is_not_available'][$post['id_lang'][0]] ? ($post['is_open'][$key]==1) ? 1 : $key : ($post['is_open'][0]==1) ? 1 : 0);
                $data_save['teaser']            = $post['teaser'][$key];
                $data_save['content']           = htmlspecialchars_decode(urldecode($post['content'.$key.'']));
                $data_save['sponsor']           = htmlspecialchars_decode(urldecode($post['sponsor'.$key.'']));
                $data_save['price']             = $post['price'][$key];
                $data_save['seo_title']         = $post['seo_title'][$key];
                $data_save['meta_description']  = htmlspecialchars_decode(urldecode($post['meta_description'.$key.'']));
                $data_save['meta_keywords']     = $post['meta_keywords'][$key];
                $data_save['uri_path']          = $post['uri_path'][$key];
                $data_save['id_lang']           = $post['id_lang'][$key];
                $data_save['is_not_available']  = $post['is_not_available'][$post['id_lang'][$key]] ? 1 : 0;
                $data_save['id_parent_lang']    = $id_parent_lang;
                $brochures                      = $post['brochures'][$key];
                $data_save['brochures']         = $brochures;
                if(is_file("document/temp/$brochures")){
                    rename("document/temp/$brochures","document/brochures/$brochures");
                }

                $data_save['id_template_form_register'] = $post['id_template_form_register'][0];

                if($post['imgDelete'][$key] == 0){
                    if($idedit && $post['img'][$key]){
                        $data_save['img']   = $post['img'][$key];
                    }elseif($idedit){
                        $datas              = $this->eventmodel->selectData($idedit);
                        $data_save['img']   = $datas[$key]['img'];
                    }else{
                        $data_save['img']   = $post['img'][$key];
                    }
                } else{
                    $data_save['img'] = NULL;
                }

                /* start check record */
                if ($key == 0) {
                    $cek_record = db_get_one('event', 'id', array('id'=>$idedit,'is_delete'=>0,'id_lang'=>$post['id_lang'][$key])); 
                } else{
                    $cek_record = db_get_one('event', 'id', array('id_parent_lang'=>$idedit,'is_delete'=>0,'id_lang'=>$post['id_lang'][$key])); 
                }
                /* end check record */
                foreach ($post['detail_img'] as $key => $value) {
                    $temp['filename']     = $post['detail_img'][$key];
                    $temp['description']  = $post['description_images_detail'][$key];
                    $temp['is_share']     = $post['is_share'][$key];
                    $temp['is_delete']    = $post['id_images_delete'][$key];
                    $data_save_img_detail[] = $temp;
                }

                if($idedit){
                    $this->db->where('id_event', $idedit);
                    $this->eventImagesModel->update(array('is_delete'=>1,'is_share'=>0));

                    foreach ($data_save_img_detail as $key3 => $value) {
                        $check_img = $this->eventImagesModel->findBydelete(array('id'=>$post['id_images_detail'][$key]));

                        if (!empty(array_filter($check_img))) {

                            if ($value['filename'] == '') {
                                $data_save_img_detail[$key3]['filename']  = $check_img[0]['filename'];
                            }
                            $this->db->where('id', $post['id_images_detail'][$key3]);
                            $this->eventImagesModel->update($data_save_img_detail[$key3]);
                        }else{
                            $data_save_img_detail[$key3]['id_event'] = $idedit;
                            $this->eventImagesModel->insert($data_save_img_detail[$key3]);
                        }
                    }
                    auth_update();
                    $ret['message'] = 'Update Success';
                    $act            = "Update Event";

                    if($cek_record != null){
                        if ($key == 0) {
                            $iddata         = $this->eventmodel->update($data_save,$idedit);
                        } else {
                            $iddata         = $this->eventmodel->updateKedua($data_save,$idedit);
                        }
                    } else{
                        auth_insert();
                        $ret['message'] = 'Insert Success';
                        $act            = "Insert Event";
                        $iddata         = $this->eventmodel->insert($data_save);
                        $id_eventx      = $iddata;
                    }

                }else{
                    auth_insert();
                    $ret['message'] = 'Insert Success';
                    $act            = "Insert Event";
                    $iddata         = $this->eventmodel->insert($data_save);

                    if(!empty($post['detail_img'])){
                        foreach ($data_save_img_detail as $key => $value) {
                            $data_save_img_detail[$key]['id_event'] = $iddata;
                            $this->eventImagesModel->insert($data_save_img_detail[$key]);
                        }
                    }

                    $this->eventFilesModel->updateByOther(array('id_event' => $iddata), array('id_event' => 0, 'id_lang' => $data_save['id_lang']));
                    $id_eventx      = $iddata;
                }
                if($key==0){
                    $id_parent_lang = $iddata;
                }
                detail_log();
                insert_log($act);
                $this->db->trans_complete();
                set_flash_session('message', $ret['message']);
                $ret['error'] = 0;

                /*untuk tags*/
                $tags = $post['tags'][$post['id_lang'][$key]];
                $idEventTags = array();
                foreach ($tags as $k => $v) {
                    $tag = strtolower($v);

                    /*untuk mengecek apakah tag sudah ada apa belum*/
                    $whr['name']      = $tag;
                    $whr['is_delete'] = 0;
                    $cek = $this->db->get_where('tags',$whr)->row_array();

                    if(!$cek){
                        $t['name']           = $tag;
                        $t['uri_path']       = url_title($tag);
                        $t['create_date']    = date('Y-m-d H:i:s');
                        $t['user_id_create'] = id_user();
                        $this->db->insert('tags',array_filter($t));
                        $idTags = $this->db->insert_id();
                    }
                    else{
                        $idTags = $cek['id'];
                    }
                    /*end untuk mengecek apakah tag sudah ada apa belum*/

                    /*untuk mengecek apakah tag sudah ada di event ini */
                    $whr2['a.id_event']   = $id_eventx;
                    $whr2['a.id_tags']   = $idTags;
                    $whr2['a.is_delete'] = 0;

                    $this->db->select('a.*,b.name as tags,b.uri_path');
                    $this->db->join('tags b','b.id = a.id_tags');
                    $cek2                = $this->db->get_where('event_tags a',$whr2)->row_array();

                    if(!$cek2){
                        $newsTags['id_event']       = $id_eventx;
                        $newsTags['id_tags']        = $idTags;
                        $newsTags['create_date']    = date('Y-m-d H:i:s');
                        $newsTags['user_id_create'] = id_user();
                        $this->db->insert('event_tags',array_filter($newsTags));
                    }
                    /*untuk mengecek apakah tag sudah ada di event ini */

                    /*mengambil id tags dalam event*/
                    $idEventTags[]   = $idTags;
                }

                if ($idEventTags && $id_eventx) {
                    $this->db->where('is_delete',0);
                    $this->db->where('id_event',$id_eventx);
                    $this->db->where_not_in('id_tags',$idEventTags);
                    $deleteEventTags = $this->db->get('event_tags')->result_array();
                    if ($deleteEventTags) {
                        foreach ($deleteEventTags as $eventTag) {
                            /*jika ingin langsung didelete*/
                            $this->db->delete('event_tags',array('id'=>$eventTag['id']));
                            // echo $this->db->last_query();exit();
                            /*jika ingin memakai is_delete*/
                            // $this->newsTagsModel->delete($newsTag['id']);
                        }
                    }
                }
                /*end untuk tags*/
                $prices = $post['event_price'];

                if (!$prices){
                    $whr5['a.id_event']   = $id_eventx;
                    $whr5['a.is_delete'] = 0;

                    $this->db->select('a.*,b.name as tags,b.uri_path');
                    $this->db->join('tags b','b.id = a.id_price');
                    $cek2                = $this->db->get_where('event_price a',$whr5)->row_array();
                    
                    $price_event['is_delete'] = 1;
                    $this->eventPriceModel->updatePrice($price_event,$id_eventx);
                }   
                /*untuk event price*/

                $idEventPrice = array();
                foreach ($prices as $k => $v) {
                    $price = strtolower($v);

                    /*untuk mengecek apakah tag sudah ada apa belum*/
                    $whr['name']      = $price;
                    $whr['is_delete'] = 0;
                    $cek = $this->db->get_where('price',$whr)->row_array();

                    
                    if(!$cek){
                        $t['name']           = $price;
                        $t['uri_path']       = url_title($price);
                        $t['create_date']    = date('Y-m-d H:i:s');
                        $t['user_id_create'] = id_user();
                        $this->db->insert('price',array_filter($t));
                        $idPrice = $this->db->insert_id();
                    }
                    else{
                        $idPrice = $cek['id'];
                    }
                    /*end untuk mengecek apakah tag sudah ada apa belum*/

                    /*untuk mengecek apakah tag sudah ada di event ini */
                    $whr5['a.id_event']  = $id_eventx;
                    $whr5['a.id_price']  = $idPrice;
                    $whr5['a.is_delete'] = 0;

                    $cek2                = $this->db->get_where('event_price a',$whr5)->row_array();
                    if(!$cek2){
                        unset($newsTags['id_tags']);
                        $newsTags['id_event']       = $id_eventx;
                        $newsTags['id_price']        = $idPrice;
                        $newsTags['create_date']    = date('Y-m-d H:i:s');
                        $newsTags['user_id_create'] = id_user();
                        $this->db->insert('event_price',array_filter($newsTags));
                    }
                    // print_r($this->db->last_query();exit;
                    /*untuk mengecek apakah tag sudah ada di event ini */

                    /*mengambil id tags dalam event*/
                    $idEventPrice[]  = $idPrice;
                }

                if ($idEventPrice && $id_eventx) {
                    $this->db->where('is_delete',0);
                    $this->db->where('id_event',$id_eventx);
                    $this->db->where_not_in('id_price',$idEventPrice);
                    $deleteEventTags = $this->db->get('event_price')->result_array();
                    if ($deleteEventTags) {
                        foreach ($deleteEventTags as $eventTag) {
                            /*jika ingin langsung didelete*/
                            $this->db->delete('event_price',array('id'=>$eventTag['id']));
                        }
                    }
                }
                /*end untuk event price*/

                /*untuk menu tags*/
                $menutags = $post['menu_tags'][$post['id_lang'][0]];
                $idEventMenuTags = array();
                foreach ($menutags as $k => $v) {
                    $tag = strtolower($v);

                    /*untuk mengecek apakah tag sudah ada apa belum*/
                    $whrMn['extra_param']      = $tag;
                    $whrMn['is_delete'] = 0;
                    $cek = $this->db->get_where('frontend_menu',$whrMn)->row_array();

                    if(!$cek){
                        $idMenuTags = 0;
                    }
                    else{
                        $idMenuTags = $cek['id'];
                    }
                    /*end untuk mengecek apakah tag sudah ada apa belum*/

                    /*untuk mengecek apakah tag sudah ada di event ini */
                    $whrMn2['a.id_event']   = $id_eventx;
                    $whrMn2['a.id_menu']   = $idMenuTags;
                    $whrMn2['a.is_delete'] = 0;

                    $this->db->select('a.*,b.name as tags,b.extra_param');
                    $this->db->join('frontend_menu b','b.id = a.id_menu');
                    $cek2                = $this->db->get_where('event_menu_tags a',$whrMn2)->row_array();
                    if(!$cek2 && $idMenuTags != 0 && $key == 0){
                        $newsTags['id_event']       = $id_eventx;
                        $newsTags['id_menu']        = $idMenuTags;
                        $newsTags['create_date']    = date('Y-m-d H:i:s');
                        $newsTags['user_id_create'] = id_user();
                        $this->db->insert('event_menu_tags',array_filter($newsTags));
                    }
                    /*untuk mengecek apakah tag sudah ada di event ini */

                    /*mengambil id tags dalam event*/
                    $idEventMenuTags[]   = $idMenuTags;
                }

                if ($idEventMenuTags && $id_eventx) {
                    $this->db->where('is_delete',0);
                    $this->db->where('id_event',$id_eventx);
                    $this->db->where_not_in('id_menu',$idEventMenuTags);
                    $deleteEventMenuTags = $this->db->get('event_menu_tags')->result_array();
                    if ($deleteEventMenuTags) {
                        foreach ($deleteEventMenuTags as $eventTag) {
                            /*jika ingin langsung didelete*/
                            $this->db->delete('event_menu_tags',array('id'=>$eventTag['id']));
                            // echo $this->db->last_query();exit();
                            /*jika ingin memakai is_delete*/
                            // $this->newsTagsModel->delete($newsTag['id']);
                        }
                    }
                }
                /*end untuk menu tags*/
            }
        }
        echo json_encode($ret);
    }
    function del(){
        auth_delete();
        $id = $this->input->post('iddel');
        $this->eventmodel->delete($id);
        $this->eventmodel->delete2($id);
        detail_log();
        insert_log("Delete Event");
    }
    function select_category(){
        render('apps/event/select_category',$data,'blank');
    }
    function record_select_category(){
        $data = $this->eventCategoryModel->records();
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['name'] = quote_form($value['name']);
        }
        render('apps/event/record_select_category',$data,'blank');
    }

    function record_select_page(){
        $data = $this->eventmodel->records();
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['name'] = quote_form($value['name']);
        }
        render('apps/event/record_select_page',$data,'blank');
    }
    public function approve($event_id='',$id=''){
        if($id){
            $datas  = $this->eventmodel->selectDataParticipant($id);
            $event = $this->eventmodel->findById($event_id);

            if(!$datas){
                die('404');
            } 
            else {

                foreach ($datas as $key => $value) {
                                   $this->db->order_by('sort', 'asc');
                    $field_event = $this->Template_tipe_input_form_register_model->findBy(array('id_template'=>$event['id_template_form_register']));
                
        
                    foreach ($field_event as $key2 => $value2) {
                        $data_field = $this->Tipe_form_registrasi_model->findById($value2['id_tipe_input']);
                        $name  = $data_field['name'];
                        $param = $data_field['parameter'];
                        $data['field']  .= '<tr>';
                        $data['field']  .= '<td>'.$name.'</td>';
                        $data['field']  .= '<td>'.template_event_value($data_field['parameter'],$datas[$key][$param]).'</td>';
                        $data['field']  .= '</tr>';
                    }
                    $data['id']         =  $id;
                }
            }
        }
        $data['event_id'] = $event_id;
        render('apps/event/view_participant',$data,'apps');
    }

    function proses_approve($event_id='',$id=''){
        event_approve($event_id,$id,base_url("apps/event/view/$event_id")); 
    }

    function close_event(){
        auth_update();
        $id = $this->input->post('id');
        $data_save['is_close'] = 1;
        $this->eventmodel->update_status($data_save,$id);
        detail_log();
        insert_log("Update status Event");
    }
    function listopt_subevent(){
        $post = $this->input->post();
        $id_event = $post['id'];
        $lang = default_lang_id();
        $data['listopt'] = selectlist2(array('table'=>'event_category','title'=>'All Category','where'=> 'is_hide = 0 and id_lang = '.$lang.' and is_delete = 0 and id_parent_category <> 0 and id_parent_category = '.$id_event));
        echo json_encode($data);
    }

    public function add_multiple_image($id ='')
    {
        delete_0_images_gallery();
        if($id){
            // echo $id;exit;
            // $data = $this->galleryModel->findById($id);
            // get_event_gallery_id($id);
            $datas_event           = $this->eventmodel->selectData($id);
            $id_gallery = get_event_gallery_id($id)?get_event_gallery_id($id):0; 
            $datas  = $this->galleryModel->selectData($id_gallery);
            if (!$datas) { // gallery sudah tidak ada
                $ins_data   = $this->eventmodel->selectData($id,1);
                
                $ins_datas['name']                = $ins_data['name'] ;
                $ins_datas['description']         = $ins_data['teaser'] ;
                $ins_datas['uri_path']            = $ins_data['uri_path'] ;
                $ins_datas['id_lang']             = $ins_data['id_lang'] ;
                $ins_datas['img']                 = $ins_data['img'] ;
                $ins_datas['filename']            = $ins_data['filename'] ;
                $ins_datas['id_gallery_category'] = 2 ;
                $ins_datas['id_category_item']    = $id ;
                $ins_datas['youtube_url']         = $ins_data['youtube_url'] ;
                $ins_datas['seo_title']           = $ins_data['seo_title'] ;
                $ins_datas['meta_description']    = $ins_data['meta_description'] ;
                $ins_datas['meta_keywords']       = $ins_data['meta_keywords']    ;
                $id_gallery                       = $this->galleryModel->insert($ins_datas);
                // print_r($this->db->last_query());exit;
                // print_r($datas);exit;

                $datas  = $this->galleryModel->selectData($id_gallery);

                if(!$datas){
                    die('404');
                }
            }else{
                $ins_data                         = $this->eventmodel->selectData($id,1);
                $ins_datas['name']                = $ins_data['name'] ;
                $ins_datas['description']         = $ins_data['teaser'] ;
                $ins_datas['uri_path']            = $ins_data['uri_path'] ;
                $ins_datas['id_lang']             = $ins_data['id_lang'] ;
                $ins_datas['img']                 = $ins_data['img'] ;
                $ins_datas['filename']            = $ins_data['filename'] ;
                $ins_datas['id_gallery_category'] = 2 ;
                $ins_datas['id_category_item']    = $id ;
                $ins_datas['youtube_url']         = $ins_data['youtube_url'] ;
                $ins_datas['seo_title']           = $ins_data['seo_title'] ;
                $ins_datas['meta_description']    = $ins_data['meta_description'] ;
                $ins_datas['meta_keywords']       = $ins_data['meta_keywords']    ;
                $this->galleryModel->update($ins_datas, $datas[0]['id']);
            }

            $data                 = quote_form($datas);
            $data['judul']        = 'Edit';
            $data['proses']       = 'Update';
            $data['publish_date'] = iso_date($data['publish_date']);
            $data['id']           = $id;
        }else{
            redirect(base_url("apps/event/"));

            $data['judul']            = 'Add';
            $data['proses']           = 'Save';
            $data['name_data']        = '';
            $data['tags']             = '';
            $data['uri_path']         = '';
            $data['description']      = '';
            $data['content']          = '';
            $data['price']          = '';
            $data['start_date']       = '';
            $data['end_date']         = '';
            $data['speaker']          = '';
            $data['id']               = '';
            $data['id_parent_lang']   = '';
            $data['youtube_url']      = '';
            $data['seo_title']        = '';
            $data['meta_description'] = '';
            $data['meta_keywords']    = '';
            $data['publish_date']     = '';
        }

        $data['list_lang']  = $this->languagemodel->langName();
        $tags_data = $this->newsTagsModel->records_tags_all();
        foreach ($tags_data as $key => $value_tags) {
            $tags_data_val .=  ",'".$value_tags['name']."'";
        }
        $data['tags'] = substr($tags_data_val,1);

        $this->db->group_by('filename');
        $allImages = $this->db->select("id, filename")->get_where('gallery_images', array('is_delete'=>0, 'filename !='=>''))->result_array();

        foreach ($data['list_lang'] as $key => $value){
            $data['list_lang'][$key]['invis']                 = ($key==0) ? '' : 'hide';
            $data['list_lang'][$key]['active']                = ($key==0) ? 'active in' : '';
            $data['list_lang'][$key]['validation']            = ($key==0) ? 'true' : 'false';
            $data['list_lang'][$key]['nomor']                 = $key;
            
            $data['list_lang'][$key]['id_gallery']            = $datas[$key]['id'];
            $data['list_lang'][$key]['name_data']             = quote_form($datas[$key]['name']);
            $data['list_lang'][$key]['description']           = quote_form($datas[$key]['description']);
            $data['list_lang'][$key]['uri_path']              = $datas[$key]['uri_path'];
            $data['list_lang'][$key]['content']               = $datas[$key]['content'];
            $data['list_lang'][$key]['price']                 = $datas[$key]['price'];
            $data['list_lang'][$key]['youtube_url']           = $datas[$key]['youtube_url'];
            $data['list_lang'][$key]['start_date']            = iso_date($datas[$key]['start_date']);
            $data['list_lang'][$key]['end_date']              = iso_date($datas[$key]['end_date']);
            $data['list_lang'][$key]['seo_title']             = $datas[$key]['seo_title'];
            $data['list_lang'][$key]['meta_description']      = $datas[$key]['meta_description'];
            $data['list_lang'][$key]['meta_keywords']         = $datas[$key]['meta_keywords'];
            $data['list_lang'][$key]['gallery_images']        = $this->show_images($datas[$key]['id'], $key);
            $data['list_lang'][$key]['gallery_album']         = $datas_event[$key]['id_gallery'] ?$datas_event[$key]['id_gallery'] : "";
            
            
            $data['list_lang'][$key]['publish_date2']         = iso_date($datas[$key]['publish_date']);
            
            
            $img_thumb                                        = image($datas[$key]['img'],'small');
            $imagemanager                                     = imagemanager('img',$img_thumb,320,180,$key,$datas[$key]['name'],'title'.$key);
            $data['list_lang'][$key]['img']                   = $imagemanager['browse'];
            $data['list_lang'][$key]['imagemanager_config']   = $imagemanager['config'];
            $data['list_lang'][$key]['list_gallery_category'] = selectlist2(array('table'=>'gallery_category','title'=>'Select Category','selected'=>$datas[$key]['id_gallery_category'],'where'=>array('is_delete'=>0)));

            /*untuk tag*/
                    $this->db->select('a.*,b.name as tags,b.uri_path');
                    $this->db->join('tags b','b.id = a.id_tags');
            $tags = $this->db->get_where('gallery_tags a',array('id_gallery'=>$datas[$key]['id']))->result_array();
            $tag = '';
            foreach ($tags as $k => $v){
                $tag .=  ','.$v['tags'];
            }
            $data['list_lang'][$key]['tags_data']               = substr($tag,1);
            /*end untuk tag*/

            /*Untuk File Upload*/
            $filemanager = filemanager($key+1,$datas[$key]['filename']);
            $data['list_lang'][$key]['file_upload']             = $filemanager['browse'];


        }
        $data['filemanager_config']     = $filemanager['config'];

        $data['id_gallery_category']  = $datas[0]['id_gallery_category'];

        $data['list_lang2']     = $data['list_lang'];
        foreach($data['list_lang2'] as $key => $value){
            $data['list_lang2'][$key]['lang_name'] = ucwords($value['lang_name']);
        }


        $data['gallery_images_modal']   = get_gallery_images("event/gallery_add_modal.html", $allImages, 0);
        $data['multiple_image_script']  = get_gallery_images("event/gallery_add_multiple_image.html", '', 0);

        render('apps/event/gallery_add',$data,'apps');
    }

    function show_images_AlbumGallery_add(){
        $post                   = purify($this->input->post(NULL,TRUE));
        $idalbum                = $post['id_album'];
        $array = array_filter(explode(",", $idalbum));

        $this->db->where_in('id_gallery',$array);
        $allImages['nomorGal']      = $key;

        $allImages['list_images']   = $this->galleryImagesModel->findBy();
    
        $i=1;
        foreach ($allImages['list_images'] as $key => $value) {

            $this->db->where('is_delete', 0);
            $this->db->select('group_concat(id_tags) as hasil');
            $tags = $this->db->get_where('gallery_tags',array('id_images'=>$value['id']))->row_array()['hasil'];
            $arr_tags = explode(',', $tags);


            $this->db->select('group_concat(name) as hasil');
            $this->db->where_in('id',$arr_tags);
            $tagsname = $this->db->get_where('tags')->row_array()['hasil'];
            $allImages['list_images'][$key]['tags']            = $allImagestags;
            $allImages['list_images'][$key]['tag_images']      = $tagsname;
            $allImages['list_images'][$key]['rowNum']          = $i++;
            $allImages['list_images'][$key]['name_img']        = $value['name'];
            $allImages['list_images'][$key]['description_img'] = $value['description'];
            $allImages['list_images'][$key]['idGallery']       = $value['id_gallery'];
        }
        render('apps/event/gallery_list_images_album_gallery',$allImages,'blank');
    }

    function show_images($idgallery, $key){
        $allImages = array();

        $wh['is_delete'] = 0;
        $this->db->where('id_gallery', $idgallery);
        $allImages['nomorGal']          = $key;
        $allImages['list_images']       = $this->galleryImagesModel->listImages($wh);

        $allImages['jmlImages']     = count($allImages['list_images']);
        $allImages['showUploadAll'] = (count($allImages['list_images']) != NULL ? 'block' : 'none');

        $tags_data = $this->newsTagsModel->records_tags_all();
        foreach ($tags_data as $key => $value_tags) {
            $tags_data_val .=  ",'".$value_tags['name']."'";
        }
        $allImagestags = substr($tags_data_val,1);

            // print_r($allImages['list_images']);exit;
        /*untuk tag*/
        foreach ($allImages['list_images'] as $key => $value) {
            $this->db->where('is_delete', 0);
            $this->db->select('group_concat(id_tags) as hasil');
            $tags = $this->db->get_where('gallery_tags',array('id_images'=>$value['idImag']))->row_array()['hasil'];
            $arr_tags = explode(',', $tags);


            $this->db->select('group_concat(name) as hasil');
            $this->db->where_in('id',$arr_tags);
            $tagsname = $this->db->get_where('tags')->row_array()['hasil'];
            // print_r($this->db->last_query());exit;
            $allImages['list_images'][$key]['tag_images'] = $tagsname;
            $allImages['list_images'][$key]['tags'] = $allImagestags;
        }

        return get_gallery_images("event/gallery_list_images.html", $allImages, 1);
    }
    function gallery_proses($idedit=''){
        // print_r('awawdawd');exit;
        $idedit = get_event_gallery_id($idedit);
        $id_user                = id_user();
        $this->layout           = 'none';
        $post                   = purify($this->input->post(NULL,TRUE));
        // print_r($post);exit();
        $ret['error']           = 1;
        $id_parent_lang         = NULL;
        $this->db->trans_start(); 
        // $id_parent       = $this->languagemodel->langId();

        foreach ($post['name'] as $key => $value){
        // print_r('awawdawd');exit;

            if(!$idedit){
                $where['uri_path']  = $post['uri_path'][$key];
                $unik               = $this->galleryModel->findBy($where);
                $this->form_validation->set_rules('name', '"page Name"', 'required'); 
                $this->form_validation->set_rules('description', '"Description"', 'required'); 
                $this->form_validation->set_rules('seo_title', '"SEO Title"', 'required'); 
                $this->form_validation->set_rules('meta_description', '"Meta Description"', 'required'); 
                $this->form_validation->set_rules('meta_keyword', '"Meta Keyword"', 'required'); 
                if ($this->form_validation->run() == FALSE){
                    $ret['message']     = validation_errors(' ',' ');
                }
                if($unik){
                    $ret['message']     = "Page URL $value already taken";
                }
            }
            
            if($idedit){
                $where['id !=']     = $idedit;
            }
            $idedit     = $post['id'][$key];
                
            $data_save['name']                = $post['name'][$key];
            $data_save['description']         = $post['description'][$key];
            $data_save['uri_path']            = $post['uri_path'][$key];
            $data_save['id_lang']             = $post['id_lang'][$key];
            $data_save['id_gallery_category'] = 2;
            $data_save['id_parent_lang']      = $id_parent_lang;
            $data_save['youtube_url']         = $post['youtube_url'][0];
            $data_save['publish_date']        = iso_date($post['publish_date'][0]);
            $data_save['seo_title']           = $post['seo_title'][$key];
            $data_save['meta_description']    = htmlspecialchars_decode(urldecode($post['meta_description'.$key.'']));
            $data_save['meta_keywords']       = $post['meta_keywords'][$key];
            if($post['imgDelete'][$key] == 0){
                if($post['img'][$key]){
                    $data_save['img']               = $post['img'][$key];
                }
            } else{
                $data_save['img'] = NULL;
            }

            /*masukan data file*/
            if ($_FILES['file']['name'][$key]) {
                    $data_save['filename']    = $_FILES['file']['name'][$key];
                    /*echo pathinfo($file, PATHINFO_EXTENSION); exit();*/
                    fileToUpload($_FILES['file'],$key);
            } else {
                unset($data_save['filename']);
            }
            /*masukan data file*/
            
            if($idedit){
                auth_update();
                $ret['message'] = 'Update Success';
                $act            = "Update Gallery";
                // if(!$post['img'][$key]){
                //  unset($post['img'][$key]);
                // }
                $iddata         = $this->galleryModel->update($data_save,$idedit);
            }else{
                auth_insert();
                $ret['message'] = 'Insert Success';
                $act            = "Insert Gallery";
                $iddata         = $this->galleryModel->insert($data_save);
                $this->galleryImagesModel->updateByOther(array('id_gallery' => $iddata), array('id_gallery' => 0, 'id_lang' => $data_save['id_lang']));
                $idedit = $iddata;
                // print_r($unik);
            }
            if($key==0){
                $id_parent_lang = $iddata;
            }
            detail_log();
            insert_log($act);
            $this->db->trans_complete();
            set_flash_session('message', $ret['message']);
            $ret['error'] = 0;


            /*untuk tags*/
            $tags = $post['tags'][$post['id_lang'][$key]];
            $idGalleryTag = array();
            foreach ($tags as $k => $v) {
                $tag = strtolower($v);

                /*untuk mengecek apakah tag sudah ada apa belum*/
                $whr['name']      = $tag;
                $whr['is_delete'] = 0;
                $cek = $this->db->get_where('tags',$whr)->row_array();

                if(!$cek){
                    $t['name']           = $tag;
                    $t['uri_path']       = url_title($tag);
                    $t['create_date']    = date('Y-m-d H:i:s');
                    $t['user_id_create'] = id_user();
                    $this->db->insert('tags',array_filter($t));
                    $idTags = $this->db->insert_id();
                }
                else{
                    $idTags = $cek['id'];
                }
                /*end untuk mengecek apakah tag sudah ada apa belum*/

                /*untuk mengecek apakah tag sudah ada di event ini */
                $whr2['a.id_gallery']   = $idedit;
                $whr2['a.id_tags']   = $idTags;
                $whr2['a.is_delete'] = 0;

                                       $this->db->select('a.*,b.name as tags,b.uri_path');
                                       $this->db->join('tags b','b.id = a.id_tags');
                $cek2                = $this->db->get_where('gallery_tags a',$whr2)->row_array();
                if(!$cek2){
                    $newsTags['id_gallery']       = $idedit;
                    $newsTags['id_tags']        = $idTags;
                    $newsTags['create_date']    = date('Y-m-d H:i:s');
                    $newsTags['user_id_create'] = id_user();
                    $this->db->insert('gallery_tags',array_filter($newsTags));
                }
                /*untuk mengecek apakah tag sudah ada di event ini */

                /*mengambil id tags dalam event*/
                $idGalleryTag[]  = $idTags;
            }

            if ($idGalleryTag && $idedit) {
                                   $this->db->where('is_delete',0);
                                   $this->db->where('id_gallery',$idedit);
                                   $this->db->where_not_in('id_tags',$idGalleryTag);
                $deleteEventTags = $this->db->get('gallery_tags')->result_array();
                if ($deleteEventTags) {
                    foreach ($deleteEventTags as $eventTag) {
                        /*jika ingin langsung didelete*/
                        $this->db->delete('gallery_tags',array('id'=>$eventTag['id']));
                        // echo $this->db->last_query();exit();
                        /*jika ingin memakai is_delete*/
                        // $this->newsTagsModel->delete($newsTag['id']);
                    }
                }
            }
            /*end untuk tags*/
        }
        echo json_encode($ret);
    }
    function uploadImages($idedit=''){
        $post = purify($this->input->post(NULL,TRUE));
        $data = array();

        $data_save['name']                  = $post['nameImag'];
        $data_save['description']           = $post['descImag'];
        $data_save['id_lang']               = $post['id_lang'];
        $data_save['id_gallery']            = $post['id_gallery'];
        $tag_image                          = array_filter(explode(",", $post['tag_image']));
    
        if($post["statusImag"] == 0){
            foreach($_FILES as $index => $file){
                $uploadImag = multipleUpload($file, './images/gallery/', 20000000);
                
                if($uploadImag == true){
                    
                    $data_save['filename']              = $uploadImag['file_name'];

                    auth_insert();
                    $ret['message'] = 'Insert Success';
                    $act            = "Insert Gallery Image";
                    $iddata         = $this->galleryImagesModel->insert($data_save);

                    $data["idImag"] = $post["imag"];
                    $data["statusImag"] = $post["statusImag"];

                    
                    $sort = 1;
                    foreach ($tag_image as $key => $value) {
                        $value = strtolower($tag_image[$key]);
                        $cek = $this->tagsModel->fetchRow(array('name'=>$value));//liat tags name di tabel ref
                        if(!$cek){//kalo belom ada
                            $id_tags = $this->tagsModel->insert(array('name'=>$value,'uri_path'=>url_title($value)));//insert ke tabel ref
                            detail_log();
                        }
                        else{
                            $id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
                        }

                        $cekTagsImage = $this->galleryTagsModel->fetchRow(array('id_tags'=>$id_tags,'id_images'=>$iddata)); //liat di tabel news tags, (utk edit)
                    
                        if(!$cekTagsNews){//kalo blm ada ya di insert
                        $tag['id_gallery']    = $data_save['id_gallery'];
                        $tag['id_images']     = $iddata;
                        $tag['id_tags']       = $id_tags;
                        $id_news_tags = $this->galleryTagsModel->insert($tag);
                        }
                        else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
                        $tag['id_gallery']    = $data_save['id_gallery'];
                        $tag['id_images']     = $cekTagsImage['id_images'];
                        $tag['id_tags']       = $id_tags;
                        $id_news_tags = $this->galleryTagsModel->update($tag,$cekTagsNews['id']);
                        }
                        $temp_id[]          =$id_tags;
                    }

                    $this->db->where_not_in('a.id_tags',$temp_id); 
                    $delete = $this->galleryTagsModel->findBy(array('a.id_images'=> $iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $id_template)
                    
                    foreach ($delete as $key => $value) {
                        $a['is_delete'] = 1;
                        $b = $this->galleryTagsModel->update($a,$value['id']);
                    }

                }
            }
        } else{
            $idedit = $post["idSavedImag"];
            
            auth_update();
            $ret['message'] = 'Update Success';
            $act            = "Update Gallery Image";
            $iddata         = $this->galleryImagesModel->update($data_save,$idedit);
            $data["idImag"] = $post["imag"];
            $data["statusImag"] = $post["statusImag"];

                    foreach ($tag_image as $key => $value) {
                        $value = strtolower($tag_image[$key]);
                        $cek = $this->tagsModel->fetchRow(array('name'=>$value));//liat tags name di tabel ref
                        if(!$cek){//kalo belom ada
                            $id_tags = $this->tagsModel->insert(array('name'=>$value,'uri_path'=>url_title($value)));//insert ke tabel ref
                            detail_log();
                        }
                        else{
                            $id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
                        }

                        $cekTagsImage = $this->galleryTagsModel->fetchRow(array('id_tags'=>$id_tags,'id_images'=>$iddata)); //liat di tabel news tags, (utk edit)


                    
                        if(!$cekTagsImage){//kalo blm ada ya di insert
                        $tag['id_gallery']    = $data_save['id_gallery'];
                        $tag['id_images']     = $iddata;
                        $tag['id_tags']       = $id_tags;
                        $id_news_tags = $this->galleryTagsModel->insert($tag);
                        }
                        else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
                        $tag['id_gallery']    = $data_save['id_gallery'];
                        $tag['id_images']     = $cekTagsImage['id_images'];
                        $tag['id_tags']       = $id_tags;
                        $id_news_tags = $this->galleryTagsModel->update($tag,$cekTagsNews['id']);
                        }
                        $temp_id[]          =$id_tags;

                    }

                    $this->db->where_not_in('a.id_tags',$temp_id); 
                    $delete = $this->galleryTagsModel->findBy(array('a.id_images'=> $iddata)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $id_template)

                    foreach ($delete as $key => $value) {
                        $a['is_delete'] = 1;
                        $b = $this->galleryTagsModel->update($a,$value['id']);
                    }
        }

        // detail_log();
        // insert_log($act);
        $this->db->trans_complete();
        set_flash_session('message', $ret['message']);

        if($iddata != 0){
            $data["idSavedImag"] = $iddata;
            $data["status"] = true;
        } else{
            $data["idSavedImag"] = 0;
            $data["status"] = false;
        }

        $data["reloadImages"] = $this->show_images($post['id_gallery'], $post['key']);

        echo json_encode($data);
    }

    function addImages(){
        $post = purify($this->input->post(NULL,TRUE));

        $img_name = explode(',', $post['selectIdImg']);

        $data_save['id_lang']       = $post['id_lang'];
        $data_save['id_gallery']    = $post['id_gallery'];
        for($i=0; $i<count($img_name); $i++) {
            $data_save['filename']      = $img_name[$i];
            auth_insert();
            $ret['message'] = 'Insert Success';
            $act            = "Insert Gallery Image";
            $iddata         = $this->galleryImagesModel->insert($data_save);
        }

        echo $this->show_images($post['id_gallery'], $post['key']);
    }

    function deleteImages(){
        $post = purify($this->input->post(NULL,TRUE));

        auth_delete();
        $this->galleryImagesModel->delete($post['idSavedImag']);
        // unlink('./images/gallery/'.$post['filename']);
        detail_log();
        insert_log("Delete Gallery Image");

        $data["idImag"] = $post["imag"];
        $data["status"] = true;
        $data["reloadImages"] = $this->show_images($post['id_gallery'], $post['key']);

        echo json_encode($data);
    }

    function loadInputTypeFormRegister(){


        $post = purify($this->input->post());


        $id = $post['id'];

        $template = $this->template_form_registrasi_model->findById($id);

        $cekTagsNews = $this->template_tipe_input_form_register_model->findBy(array('id_template'=>$template['id']));

        foreach ($cekTagsNews as $key => $value) {
          $a[] .= $value['id_tipe_input'];
          // $a[] .= $value['id_tipe_input'];
        }


        $data['list_tipe_input'] = $this->tipe_form_registrasi_model->findBy();
        foreach ($data['list_tipe_input'] as $key => $value) {

            if (in_array($value['id'], $a, true)) {
              $checked = 'checked';
            } else {
              $checked = '';
            }

            $ret_tipe_input .= '<div class="col-sm-12">
              <label class="checkbox-committee"><input type="checkbox" '.$checked.' value="'.$value['id'].'" id="'.$value['id'].'" class="tipe_form" name="id_tipe_input_form[]" >'.$value['name'].'<input type="checkbox" '.$checked.' value="'.$value['id'].'" id="'.$value['id'].'" class="tipe_form" name="is_required[]" ></label>
            </div>';
        }

        $data['list_tipe_input'] = $ret_tipe_input;

        echo json_encode($data);


    }

    function record_select_category_album(){
        $arr_id = $this->uri->segment_array();
        unset($arr_id[1]);
        unset($arr_id[2]);
        unset($arr_id[3]);
        $real_id = array_filter($arr_id);
        if (!empty(array_filter($arr_id))) {
            $this->db->where_not_in('a.id',$arr_id);
        }
        $this->db->where('a.id_gallery_category',1);
        $data['data']       = $this->galleryModel->findBy();
        
        $i=1;
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['name'] = $value['name'];
            $data['data'][$key]['nomor'] = $i++;
        }
        render('apps/event/record_select_category_album',$data,'blank');
    }

    function getAlbumGallery(){
        $post = purify($this->input->post(NULL,TRUE));

        if ($post['id_gallery']) {
            $arr_id = explode(",", $post['id_gallery']);
            if ($arr_id) {
                $data['idgallery'] = implode("/", $arr_id);
            }else{
                $data['idgallery'] = $post['id_gallery'];
            }
        }else{
            $data['idgallery'] = "";    
        }

        render('apps/event/select_category_album',$data,'blank');
    }

    function show_images_AlbumGallery(){

        $post = purify($this->input->post(NULL,TRUE));
        $idgallery =$post['id_gallery'];
        $idevent   =$post['id_event'];
        $arrayid   =$post['array_id'];
        

        $tags_data = $this->newsTagsModel->records_tags_all();
        foreach ($tags_data as $key => $value_tags) {
        $tags_data_val .=  ",'".$value_tags['name']."'";
        }
        $allImagestags = substr($tags_data_val,1);
    
        $this->db->where_in('id_gallery', $idgallery);
        $allImages['nomorGal']      = $key;
        $allImages['list_images']   = $this->galleryImagesModel->findBy();
        $i=1;
        foreach ($allImages['list_images'] as $key => $value) {

            $this->db->where('is_delete', 0);
            $this->db->select('group_concat(id_tags) as hasil');
            $tags = $this->db->get_where('gallery_tags',array('id_images'=>$value['id']))->row_array()['hasil'];
            $arr_tags = explode(',', $tags);


            $this->db->select('group_concat(name) as hasil');
            $this->db->where_in('id',$arr_tags);
            $tagsname = $this->db->get_where('tags')->row_array()['hasil'];
            $allImages['list_images'][$key]['tags']            = $allImagestags;
            $allImages['list_images'][$key]['tag_images']      = $tagsname;
            $allImages['list_images'][$key]['rowNum']          = $i++;
            $allImages['list_images'][$key]['name_img']        = $value['name'];
            $allImages['list_images'][$key]['description_img'] = $value['description'];
            $allImages['list_images'][$key]['idGallery']       = $value['id_gallery'];
        }

        $arrayid = implode(",", array_filter(explode(",", $arrayid)));
        if (empty($arrayid)) {
            $update_gallery['id_gallery'] = '';
        }else{
            $update_gallery['id_gallery'] = ','.$arrayid.',';
        }
        $this->eventmodel->update($update_gallery,$idevent);
        
        render('apps/event/gallery_list_images_album_gallery',$allImages,'blank');
    }

    function record_select_category_album_to_remove(){
        $arr_id = $this->uri->segment_array();
        unset($arr_id[1]);
        unset($arr_id[2]);
        unset($arr_id[3]);
        $real_id = array_filter($arr_id);
        // if (!empty(array_filter($arr_id))) {
            $this->db->where_in('a.id',$arr_id);
        // }
        $this->db->where('a.id_gallery_category',1);
        $data['data']       = $this->galleryModel->findBy();
        
        $i=1;
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['name'] = $value['name'];
            $data['data'][$key]['nomor'] = $i++;
        }
        render('apps/event/record_select_category_album_to_remove',$data,'blank');
    }

    function getAlbumGalleryToRemove(){
        $post = purify($this->input->post(NULL,TRUE));
        if ($post['id_gallery']) {
            $arr_id = explode(",", $post['id_gallery']);
            if ($arr_id) {
                $data['idgallery'] = implode("/", $arr_id);
            }else{
                $data['idgallery'] = $post['id_gallery'];
            }
        }else{
            $data['idgallery'] = "";    
        }

        render('apps/event/select_category_album_to_remove',$data,'blank');
    }

    function show_images_AlbumGallery_to_remove(){

        $post = purify($this->input->post(NULL,TRUE));
        $idgallery = $post['id_gallery'];

        $idevent   = $post['id_event'];

        $tags_data = $this->newsTagsModel->records_tags_all();
        foreach ($tags_data as $key => $value_tags) {
        $tags_data_val .=  ",'".$value_tags['name']."'";
        }
        $allImagestags = substr($tags_data_val,1);
    
        $this->db->where('id_gallery', $idgallery);
        $allImages['nomorGal']      = $key;
        $allImages['list_images']   = $this->galleryImagesModel->findBy();
        $i=1;
        foreach ($allImages['list_images'] as $key => $value) {

            $this->db->where('is_delete', 0);
            $this->db->select('group_concat(id_tags) as hasil');
            $tags = $this->db->get_where('gallery_tags',array('id_images'=>$value['id']))->row_array()['hasil'];
            $arr_tags = explode(',', $tags);


            $this->db->select('group_concat(name) as hasil');
            $this->db->where_in('id',$arr_tags);
            $tagsname = $this->db->get_where('tags')->row_array()['hasil'];
            $allImages['list_images'][$key]['tags']            = $allImagestags;
            $allImages['list_images'][$key]['tag_images']      = $tagsname;
            $allImages['list_images'][$key]['rowNum']          = $i++;
            $allImages['list_images'][$key]['name_img']        = $value['name'];
            $allImages['list_images'][$key]['description_img'] = $value['description'];
            $allImages['list_images'][$key]['idGallery']       = $value['id_gallery'];
        }

        $idgallery = implode(",", array_filter(explode(",", $idgallery)));
        if (empty($idgallery)) {
            $update_gallery['id_gallery'] = '';
        }else{
            $update_gallery['id_gallery'] = ','.$idgallery.',';
        }   

        $update_gallery['id_gallery'] = ','.$idgallery.',';
        $this->eventmodel->update($update_gallery,$idevent);

        render('apps/event/gallery_list_images_album_gallery',$allImages,'blank');
    }

    // public function download(){
 //        $post     = purify($this->input->post(NULL,TRUE));
      
 //        $id_event = $post['id_event'];
 //        $where    = '';

 //         $data  = $this->eventmodel->records_participant($id_event);
    //  $event = $this->eventmodel->findById($id_event);
    //  $nomor = 1;
    //  foreach ($data['data'] as $key => $value) {
    //                     $this->db->order_by('sort', 'asc');
    //      $field_event = $this->Template_tipe_input_form_register_model->findBy(array('id_template'=>$event['id_template_form_register']));

    //      foreach ($field_event as $key2 => $value2) {
    //          $field = $this->Tipe_form_registrasi_model->findById($value2['id_tipe_input'])['parameter'];
    //          $data['data'][$key]['field']    .= '<td>'.$data['data'][$key][$field].'</td>';
    //      }
            
    //      $data['data'][$key]['status']          = ($value['is_approve'] == 1 ? 'Paid' : '-');
    //      $data['data'][$key]['id']              = $value['id'];
    //      $data['data'][$key]['event_id']        = (!empty($value['parent_lang']) ? $value['parent_lang'] :  $value['event_id']);
    //      $data['data'][$key]['st']              = ($value['is_approve'] == 1 ? 'invis' : '' );
    //      $data['data'][$key]['nomor'] = $nomor;
    //      $nomor++;
    //  }

 //        render('apps/event/export',$data,'blank');
 //        $filename = 'list-event-participant.xls';
 //        header('Content-type: application/excel');
 //        header('Content-Disposition: attachment; filename='.$filename);
 //    }

       public function download(){
        $id_event = $this->uri->segment(4);
        $this->load->model('paymentconfirmation_model');
        $this->load->model('paymentconfirmationfiles_model');

        // $data  = $this->db->where('event_id', $id_event)->get('event_participant')->result_array();
        $this->db->select('b.*,c.name as event_name,(case when a.is_paid = 1 then "Paid" else "Not Paid" end) as status_payment, d.name as payment_type');
        $this->db->join('event_participant b', 'b.id = a.member_id', 'left');
        $this->db->join('event c', 'c.id = b.event_id', 'left');    
        $this->db->join('ref_payment_type d', 'd.id = a.id_ref_payment_type', 'left');  
        $this->db->where('b.event_id', $id_event);
        $data = $this->paymentconfirmation_model->findBy(array('a.id_ref_payment_category'=>1));

        $event = $this->eventmodel->findById($id_event);
                       $this->db->order_by('sort', 'asc');
        $field_event = $this->Template_tipe_input_form_register_model->findBy(array('id_template'=>$event['id_template_form_register']));

        foreach ($field_event as $key => $value) {
            $field                       = $this->Tipe_form_registrasi_model->findById($value['id_tipe_input']);
            $ret['field'] .= '<th style="vertical-align: top;" class="center" >'.$field['name'].'</th>';

            $parameter[]    .= $field['parameter'];
        }

        if (empty($data)) {
            $ret['content'] = [];
        }else{

        $no = 0;
        foreach ($data as $key2 => $value2) {
            $ret['content'][$key2]['no'] =  ++$no;
            $ret['content'][$key2]['status']           = $value2['status_payment'];
            $ret['content'][$key2]['payment_type']     = $value2['payment_type'];
            $ret['content'][$key2]['invoice_number']     = $value2['invoice_number'];
            $ret['content'][$key2]['event_price_amount']     = $value2['event_price_amount'];
            foreach ($parameter as $key3 => $value3) {

                // $data[$key2][$value3]
                switch ($value3) {
                    // case 'id_ref_handicap':
                    //  $where['id'] = $data[$key2][$value3];
                    //  $data[$key2][$value3] = db_get_one('ref_handicap',"name",$where);
                    //  break;

                    case 'id_ref_gender':
                        $where['id'] = $data[$key2][$value3];
                        $data[$key2][$value3] = db_get_one('ref_gender',"name",$where);
                        break;

                    case 'id_ref_meal':
                        $where['id'] = $data[$key2][$value3];
                        $data[$key2][$value3] = db_get_one('ref_meal',"name",$where);
                        break;

                    case 'id_ref_member':
                        $where['id'] = $data[$key2][$value3];
                        $data[$key2][$value3] = db_get_one('ref_member',"name",$where);
                        break;

                    case 'id_ref_event_price':
                        $where['id'] = $data[$key2][$value3];
                        $data[$key2][$value3] = db_get_one('price',"name",$where);
                        break;

                    case 'is_approve':
                        $data[$key2][$value3] = ($data[$key2][$value3] == 1 )? "Paid" :"";
                        break;
                    
                    default:
                        $data[$key2][$value3] = $data[$key2][$value3];
                        break;
                }

                $ret['content'][$key2]['td'] .= '<td style="vertical-align:top;" class="center">'.$data[$key2][$value3].'</td>';
            }
        }
        
        }
        render('apps/event/export',$ret,'blank');
        $filename = 'list-event-participant.xls';
        header('Content-type: application/excel');
        header('Content-Disposition: attachment; filename='.$filename);
        
        // html_to_excel($this->parser->parse('apps/event/export.html',$ret,true),'list-event-participant');

    }

    function preview($uri_path){
        $url = base_url().'en/event/detail/'.$uri_path.'/1';
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        
        $content = curl_exec($ch);
        curl_close($ch);

        print_r($content);exit;
    }

    function compare_id(){
        $id              = $this->input->post('id');

        // $is_amcham_event  = id_news_publication(1); // amcham report
        $is_amcham_event  = 28;
        $is_non_amcham_event = 28;
        $is_annual_golf_turnament = 40;

        $data['is_amcham_event'         ]   = '0';
        $data['is_non_amcham_event'     ]   = '0';
        $data['is_annual_golf_turnament']   = '0';

        
        if (in_array($id, $is_amcham_event)) {
            // check category report
            $data['is_amcham_event'] = '1';
        }else if ($id == $is_non_amcham_event){
            $data['is_non_amcham_event'] = '1';

        }else if ($id == $is_annual_golf_turnament){
            $data['is_annual_golf_turnament'] = '1';
        }
        
        echo json_encode($data);
    }
    function proses_add_price()
    {

        $post = $this->input->post();
        $this->db->trans_start();

        auth_insert();
        $ret['message'] = 'Insert Success';
        $act            = "Insert event_price";
        $this->eventprice_model->insert($post);

        detail_log();
        insert_log($act);
        $this->db->trans_complete();

        $ret['error']         = 0;
        $ret['price'] = $post['name'];
        
        $ret['msg']         = 'Success Add Price';
        $ret['no_reload']   = true;
        $ret['close_modal'] = 'modal-add-price';
        $ret['call_function'] = 'refresh_price';

        echo json_encode($ret);
    }
}
/* End of file event.php */
/* Location: ./application/controllers/apps/event.php */