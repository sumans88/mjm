<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Gallery extends CI_Controller {
    function __construct(){
        parent::__construct();
         $this->load->model('gallerymodel');
         $this->load->model('galleryImagesModel');
         $this->load->model('gallery_category_model');
         $this->load->model('Gallerytagsmodel');

    }
    function photo(){
        $data['page_heading'] = "Photo Gallery";
        $id_lang = id_lang();

       $this->db->select(' b.is_delete AS tags_is_del,
               a.name,
               a.description,
               a.is_delete AS gallery_is_del,
               a.id_gallery_category,
               a.id_status_publish,
               a.img,
               a.publish_date,
               a.uri_path,
               a.name,
               a.description,
               c.id AS id_tags,
               c.uri_path AS tags_uri_path,
               c.name AS tags_name
                            ');
        $this->db->limit(6,0);  
        $this->db->order_by('a.publish_date','desc');
        $this->db->group_by('a.id');
        $this->db->join('gallery_tags b', "a.id = b.id_gallery",'left');
        $this->db->join('tags c', "c.id = b.id_tags",'left');
        $this->db->where('a.id_status_publish','2');
        $this->db->where('a.is_delete','0');
        $this->db->where('a.publish_date <' ." '".date('Y-m-d H:i:s')."'");
        $this->db->where('id_gallery_category', 1);
        $query = $this->db->get('gallery a');
        // print_r($this->db->last_query());exit;
        if($query !== FALSE && $query->num_rows() > 0){
            $datalist = $query->result_array();
        }else{
            $datalist = '';
        }
        // print_r($this->db->last_query());exit;
        $count_datalist = count($datalist);
        foreach ($datalist as $key => $value) {
            //check image description
            // preg_match('!<img([\w\W]+?)/>!', $value['description'],$check_img);
            // if (!empty($check_img)) {
            //     //check gallery title
            //     $style = (strlen($value['name']) > 44) ? "teaser-gallery-2":"teaser-gallery";
            //     preg_match('!class!', $check_img[0],$check_style);
            //     //check class atribute
            //     if (!empty($check_style)) {
            //         $temp['teaser']  = str_replace('class="', 'class="'.$style.' ',$check_img[0]);
            //     }else{
            //         $temp['teaser']  = str_replace('img', 'img class="'.$style.'"',$check_img[0]);
            //     }
            // }else{
            //     // kalo gak ada imagenya 
            //     $temp['teaser']  = closetags(character_limiter($value['description'],121,'...'));
            // }
            $temp['teaser']  ='';

            $temp['img']    = ($value['img'] != '' ? getImg($value['img'],'small') : getImg('no-image-available.png','small'));
            $temp['date']   = ($value['publish_date'] == "0000-00-00") ? '' : event_date($value['publish_date'],'','','-',1) ;
            $temp['url']    = site_url('gallery/detailphoto/'.$value['uri_path']);
            $temp['title']  = $value['name'];
            // $temp['teaser'] = "";

            $data['gallery'][] = $temp;
        }

        $data['hidden_gallery'] =  $count_datalist != 0 ? '' : 'hidden';

        // $data['news'] = $this->gallerymodel->findBy(array('id_lang'=>$id_lang,'id_gallery_category'=>$id_cat));
        
        if($data['seo_title'] == ''){
            $data['seo_title'] = "MJM";
        }
        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);
        
        $data['paging']           = PAGING_PERPAGE;
        
        $data['dsp_load_more']    = $this->morephoto('-',$data['paging'],1) ? '' : 'hide';
        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar();

        $this->db->order_by('order', 'desc');
        $data['list_category_gallery']   = $this->gallery_category_model->findBy();
        render('gallery/photo',$data);
    }

    function morephoto($type,$page,$ret=0){
        $id_lang = id_lang();
        $type_uri_path  = db_get_one('gallery_category_frontend_tags','group_concat(id_tags)','id_category = "'.$type.'"');
        $this->db->select(' b.is_delete AS tags_is_del,
                a.name,
                a.description,
                a.is_delete AS gallery_is_del,
                a.id_gallery_category,
                a.id_status_publish,
                a.img,
                a.publish_date,
                a.uri_path,
                a.name,
                a.description,
                c.id AS id_tags,
                c.uri_path AS tags_uri_path,
                c.name AS tags_name
                             ');
         $this->db->limit(PAGING_PERPAGE_MORE,($page));
         $this->db->order_by('a.publish_date','desc');
         $this->db->group_by('a.id');
         $this->db->join('gallery_tags b', "a.id = b.id_gallery",'left');
         $this->db->join('tags c', "c.id = b.id_tags",'left');
         $this->db->where('a.id_status_publish','2');
         $this->db->where('a.is_delete','0');
         $this->db->where('a.publish_date <' ." '".date('Y-m-d H:i:s')."'");
         $this->db->where('id_gallery_category', 1);


        if ($type_uri_path) {
            $this->db->where('c.id in('.$type_uri_path.')');
        }

        $query = $this->db->get_where('gallery a',array('a.id_lang'=>$id_lang));

        if($query !== FALSE && $query->num_rows() > 0){
            $data = $query->result_array();
        }else{
            $data = '';
        }

        if($ret==1){
            return $data ? 1 : 0;
        }

        foreach ($data as $key => $value) { 
            $img    =  ($value['img'] != '' ? getImg($value['img'],'small') : getImg('no-image-available.png','small'));
            $date   = event_date($value['publish_date'],'','','-',1);
            $url    = site_url('gallery/detailphoto/'.$value['uri_path']);
            $title  = $value['name'];
            $teaser = character_limiter($value['description'],121,'...');

            ?>
            <div class="media media-latest">
                <div class="media-left">
                    <div class="thumbnail-amcham thumb-latest"><?=$img?></div>
                </div>
                <div class="media-body">
                    <div class="widget-white">
                      <div class="event-type"><?=$date?></div> <!-- Sep 29, 2017 -->
                      <div class="title-event-list"><a href="<?=$url?>"><?=$title?></a></div>
                      <p><?=$teaser?></p>
                    </div>
                </div>
            </div>
            <?php
        }

        $data   = $this->morephoto($type,$page+1,1);
        
        if($data){            
            // echo "<div class='text-center col-sm-12 col-md-12 col-xs-12 col-gallery'><a href='".site_url('gallery/morephoto/'.($page))."' class='load-more'>".language('load_more')."</a></div>";
            $type = $type?$type:'-';
            echo '<div class="text-center mt15"><span href="'.site_url('gallery/morephoto/'.$type.'/'.($page+PAGING_PERPAGE)).'" class="load-more tangan">'.language('load_more').'</span></div>';
        }
    }

    function detailphoto($uri_path){
        $this->load->model('gallerydetailmodel');
        $this->load->model('categorygallerymodel'); 
        $id_lang     = id_lang();  
        $lang        = $this->uri->segment(1);
        // $currentLang = id_lang();
        
        // $datas = $this->gallerymodel->fetchRow(array('a.uri_path'=>$uri_path)); 
        // if($currentLang != $datas['id_lang']){
        //     $id = $datas['id_parent_lang'] ? $datas['id_parent_lang'] : $datas['id'];
        //     $this->db->where("(a.id_parent_lang = '$id' or a.id = '$id')");
        //     $datas = $this->gallerymodel->findBy();
        //     foreach ($datas as $key => $value) {
        //         if($value['id_lang'] == $currentLang){
        //             redirect(base_url("$lang/gallery/detailphoto/$value[uri_path]"));
        //         }
        //     }
        // }

        $data   = $this->gallerymodel->fetchRow(array('id_lang'=>$id_lang,'uri_path'=>$uri_path,'id_gallery_category'=>1));
        // print_r($this->db->last_query());exit;
        if(!$data){ 
            $data = $this->gallerymodel->fetchRow(array('a.uri_path'=>$uri_path,'id_gallery_category'=>1));
        }

        if ($id_lang != $data['id_lang']) {
            $id = $data['id_parent_lang'] ? $data['id_parent_lang'] : $data['id'];
            $this->db->where("(a.id_parent_lang = '$id' or a.id = '$id')");
            $datas = $this->gallerymodel->findBy();
            foreach ($datas as $key => $value) {
                if($value['id_lang'] == $id_lang){
                    redirect(base_url("$lang/gallery/detailphoto/$value[uri_path]/$page"));
                }
            }
        }
        $data['date']         =  event_date($data['publish_date'],'','','-',1);
        $data['user_publish'] =  ($data['user_id_modify'])
                                 ? ' | <span> by '.db_get_one('auth_user','username','id_auth_user = '.$data['user_id_modify']).'</span>'  
                                 : ' | <span> by '.db_get_one('auth_user','username','id_auth_user = '.$data['user_id_create']).'</span>'  ;
    
        // $gallerymodel = $this->categorygallerymodel->fetchRow(array('id'=>$data['id_gallery_category']));  
       
        $detail       = $this->galleryImagesModel->listImagesGallery($data['id']);

        $count_detail = count($detail);   
        // debugvar($detail);exit;

        
        // $c_large = 0;
        foreach ($detail as $key => $value) {
            $detail[$key]['id_gallery']    =  $value['images_id'];
            $detail[$key]['inv_desc']      =  ($value['images_description']) ? '' : 'hide';
            $detail[$key]['l_description'] = $value['images_description'];
            $detail[$key]['l_name']        = $value['images_name'];
            $detail[$key]['l_img']         = ($value['images_filename'] != '' ? getImg($value['images_filename'],'large') : getImg('no-image-available.png','large'));
            // $detail[$key]['active']      =  $key==0 ? 'active' : '';
            // $detail[$key]['col']         = $key==0 || $key%5 == 0 ? 12 : 6;

            // if ($key==0 || $c_large == 4) {
            //     $detail[$key]['size']     = 'large';
            //     $detail[$key]['full']     = 'full';

            //     $c_large = 0;
            // }else{
            //     $detail[$key]['size']     = 'small';
            //     $detail[$key]['full']     = '';
            //     $c_large++;
            // }
            /*$detail[$key]['size']     = $key == 0 ? 'large' : 'small';
            $detail[$key]['full']     = $key == 0 ? 'full' : '';*/

        }
        // $data['detail_name'] = $gallerymodel['name'];
        $imagelist = $this->listphoto($data['id'],array('backurl' => 'gallery/photo') );

        if ($imagelist['status'] == 0) {
            $data['imagelist']       = $imagelist['imagelist'];
            $data['modal_imagelist'] = $imagelist['modal_imagelist'];
        } else {
            $data['imagelist']       = '';
            $data['modal_imagelist'] = '';
        }
        // $data['list_gallery2'] = $detail;

        $dataSEO = $this->gallerymodel->findById($detail[$key]['id_gallery']);
        if($data['seo_title'] == ''){
            $data['seo_title'] = "MJM";
        } else{
            $data['seo_title'] = $dataSEO['seo_title'];
        }
        $data['meta_keyword']     = $dataSEO['meta_keyword'];
        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $dataSEO['meta_description']);
        
        $data['share']            = share_widget();
        
        $dataCat                  = $this->gallerymodel->fetchRowCat(array('id_lang'=>$id_lang,'uri_path'=>$uri_path), 1);
        $data['category']         = $dataCat['category'];
        
        // $_SESSION['page_uri'] = "photo";

        // $this->db->limit(3);
        // $related = $this->gallerymodel->findBy(array('a.id_lang'=>$id_lang,'a.id_gallery_category'=>$data['id_gallery_category'],'a.id !='=>$data['id'],'a.is_delete'=>0));
        // foreach ($related as $key => $value) {
        //     $related[$key]['related_title']   = character_limiter($value['name'], 25);
        //     $related[$key]['related_teaser']   = character_limiter($value['teaser'], 50);
        //     $related[$key]['related_url']     = site_url("/gallery/detailphoto/$value[uri_path]");
        //     $related[$key]['related_img']     = (getImg($value['img'],'small')) ? getImg($value['img'],'small') : getImg('no-image-available.png','small');
        //     $related[$key]['isImgRelated']    = $related[$key]['related_img'] ? '' : 'hide';
        //     $related[$key]['styleHeight']     = ((strlen($related[$key]['related_title']) > 30 && $related[$key]['related_teaser'] == '') ? 'style="height:296px;"' : ($related[$key]['related_teaser'] ? 'style="height:390px;"' : ((strlen($related[$key]['related_title']) < 30 && $related[$key]['related_teaser'] == '') ? 'style="height:296px;"' : '')));
        // }
        // $data['related'] = $related;

        /*untuk list tags*/
        $data['url']        = site_url("tags");

                              $where['a.is_delete']  = 0;
                              $where['a.id_gallery']   = $data['id'];
                              $this->db->select('a.*,b.name as tags,b.uri_path as uri_path_tags');
                              $this->db->join('tags b','b.id = a.id_tags');
        $tags               = $this->db->get_where('gallery_tags a',$where)->result_array();
        // $tags               = $this->newstagsmodel->findBy(array('id_news'=>$data['id']));

        /*new script (dwiki)*/
        $count_tags = count($tags);
        foreach ($tags as $key => $value) {
            if ($key==($count_tags-1)) {
                $tags[$key]['comma_tags'] = '';
            } else {
                $tags[$key]['comma_tags'] = ', ';
            }
        }
        /*end new script (dwiki)*/

        $data['list_tags'] = $tags;
        $data['invis_tags'] = ($tags) ? '' : 'hide';
        /*end untuk list tags*/

        // $data['artikel_terkait'] = artikel_terkait($data['id'],'gallery');

        // if($currentLang == 1){
        //     $data['lang_related'] = ucwords(strtolower($data['category']))." Lainnya";
        // } else{
        //     $data['lang_related'] = "Other ".ucwords(strtolower($data['category']));
        // }

        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar();
        render('gallery/detailphoto',$data);
    }
    function listphoto($id,$conf = array()){
        $id = explodeable('-',$id);
        return listphoto($id,$conf);
    }

    function morelistphoto($id,$page,$backurl,$ret=0)
    {
        $id = explodeable('-',$id);
        return morelistphoto($id,$page,$backurl,$ret);
    }


    /*function video(){
        $id_lang = id_lang();
        $this->db->order_by('id','desc');
        $id_cat = 2;
        $data['video'] = $this->gallerymodel->findBy(array('id_lang'=>$id_lang,'id_gallery_category'=>$id_cat));
        foreach ($data['video'] as $key => $value) {
            $data['video'][$key]['youtube_url'] = getVideo($value['youtube_url']);
        }
        render('gallery/video',$data);
    }*/

    function video(){
        $data['page_heading'] = "Video Gallery";
        // $_SESSION['page_uri'] = "video";
        $id_lang = id_lang();
        $this->db->limit(9);
        $where['a.id_lang']                 = $id_lang;
        $where['a.id_status_publish']       = 2;
        $where['a.approval_level']          = 100;
        $where['a.is_not_available']        = 0;
        $where['a.link_youtube_video !=']   = '';
        $where['a.publish_date <=']         = date('Y-m-d H:i:s');        
        $this->db->order_by('a.publish_date','desc'); 
        $this->db->order_by('a.id','desc'); 

        $data['video'] = $this->gallerymodel->findBy(array('id_gallery_category'=>4,'id_status_publish'=>2));
        $count_datalist = count($data['video']);
        foreach ($data['video'] as $key => $value) {
            $data['video'][$key]['youtube_url'] = $value['youtube_url'];
            $data['video'][$key]['name']        = $value['name'];
            $data['video'][$key]['description'] = '';
            // $data['video'][$key]['description'] = closetags(character_limiter($value['description'],52,'...'));
            $data['video'][$key]['img_video']   = ($value['img']) ? getImg($value['img'],'small'):
            get_youtube_thumbnail($value['youtube_url']);
        }
        // print_r($data['video']);exit;
        $data['hidden_gallery'] =  $count_datalist != 0 ? '' : 'hidden';
        // $data['video'] = $this->gallerymodel->findByNews($where);
        // foreach ($data['video'] as $key => $value) {
        //     $data['video'][$key]['youtube_url'] = getVideo($value['link_youtube_video']);
        // }
        
        $data['paging'] = 9;
        $data['dsp_load_more'] = $this->morevideo($data['paging'],1) ? '' : 'hide';
        
        $data['url']            = site_url("news");
        if($data['seo_title'] == ''){
            $data['seo_title'] = "MJM";
        }
        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);
        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar();
        render('gallery/video',$data);
    }

    function morevideo($page,$ret=0){
        $id_lang = id_lang();
        $this->db->limit(9, $page);
        $this->db->order_by('a.publish_date','desc'); 

        $where['a.id_lang']                 = $id_lang;
        $where['a.id_status_publish']       = 2;
        $where['a.approval_level']          = 100;
        $where['a.is_not_available']        = 0;
        $where['a.link_youtube_video !=']   = '';
        $where['a.publish_date <=']         = date('Y-m-d H:i:s');        
        $this->db->order_by('a.publish_date','desc'); 
        $this->db->order_by('a.id','desc'); 

        $data = $this->gallerymodel->findBy(array('id_gallery_category'=>4,'id_status_publish'=>2));
        //echo var_dump($data);
        if($ret==1){
            return $data ? 1 : 0;
        }

        foreach ($data as $key => $value) { 
            $render['video'][$key]['youtube_url'] = $value['youtube_url'];
            $render['video'][$key]['name']        = $value['name'];
            $render['video'][$key]['description'] = '';
            // $render['video'][$key]['description'] = closetags(character_limiter($value['description'],52,'...'));
            $render['video'][$key]['img_video']   = ($value['img']) ? getImg($value['img'],'small'):
            get_youtube_thumbnail($value['youtube_url']);
            $render['video'][$key]['base_url'] = base_url();

            $return['images'] = $this->parser->parse('gallery/more_video.html',$render,true);
        }

        $datamore = $this->morevideo($page+PAGING_PERPAGE_GALLERY_MORE,1);

        if($datamore){
            $return['more'] = "<div class='text-center col-sm-12 col-md-12 col-xs-12 col-gallery'><a href='".site_url('gallery/morevideo/'.($page+PAGING_PERPAGE_GALLERY_MORE))."' class='load-more-video'>".language('load_more')."</a></div>";
        }else{
            $return['more'] = '';
        }
        echo json_encode($return);
    }
    function morelistvideo($id,$page,$backurl,$ret=0)
    {
        $id = explodeable('-',$id);
        return morelistphoto($id,$page,$backurl,$ret);
    }

    function pressrelease(){
        $id_lang = id_lang();
        $data['menu_name'] = "Press Release"; 
        $this->db->order_by('id','desc');
        $id_cat = 3;
        $data['gallery'] = $this->gallerymodel->findBy(array('id_lang'=>$id_lang,'id_gallery_category'=>$id_cat));
        if($data['seo_title'] == ''){
            $data['seo_title'] = "MJM";
        }
        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);
        render('gallery/photo',$data);
    }

    function factsheet(){
        $id_lang = id_lang();
        $data['menu_name'] = "Fact Sheet"; 
        $this->db->order_by('id','desc');
        $id_cat = 4;
        $data['gallery'] = $this->gallerymodel->findBy(array('id_lang'=>$id_lang,'id_gallery_category'=>$id_cat));
        if($data['seo_title'] == ''){
            $data['seo_title'] = "MJM";
        }
        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);
        render('gallery/photo',$data);
    }
    function infographic(){
        $id_lang = id_lang();
        $data['menu_name'] = "Infographic"; 
        $this->db->order_by('id','desc');
        $id_cat = 5;
        $data['gallery'] = $this->gallerymodel->findBy(array('id_lang'=>$id_lang,'id_gallery_category'=>$id_cat));
        // echo $this->db->last_query();
        if($data['seo_title'] == ''){
            $data['seo_title'] = "MJM";
        }
        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);
        render('gallery/infographic',$data);
    }
    function detailinfographic($uri_path){
        $id_lang = id_lang();
        $data = $this->gallerymodel->fetchRowCat(array('id_lang'=>$id_lang,'uri_path'=>$uri_path));
        $data['img2']        = image($data['img'],'large');
        $data['img']        = getImg($data['img'],'large');
        $data['isImg']      = $data['img'] ? '' : 'hide';

        $data['invis_download'] = ($data['filename']) ? '' : 'hide';
        $data['idx']            = ($data['filename']) ? md5plus($data['id']) : '';

        $data['page_cat'] = 2;

        $data['share'] = share_widget();

        $this->db->limit(3);
        $related = $this->gallerymodel->findBy(array('a.id_lang'=>$id_lang,'a.id_gallery_category'=>$data['id_gallery_category'],'a.id !='=>$data['id'],'a.is_delete'=>0));
        foreach ($related as $key => $value) {
            $related[$key]['related_title']   = character_limiter($value['name'], 63);
            $related[$key]['related_url']     = site_url("/gallery/detailinfographic/$value[uri_path]");
            $related[$key]['related_img']     = getImg($value['img'],'small');
            $related[$key]['isImgRelated']    = $related[$key]['related_img'] ? '' : 'hide';
        }
        $data['related'] = $related;
        $_SESSION['page_uri'] = "infographic";

        //echo var_dump($related);die;

        render('gallery/detailinfographic',$data);
    }

    function get_download(){
        $post = $this->input->post();
        
        $file = db_get_one('gallery','filename',array(md5field('id')=>$post['idx']));
        if ($file) {
            $file = preg_replace('/\s+/', '_', $file);
            $data['path'] =  base_url().'file_upload/'.$file;
        } else {
            $data['path'] = 'error';
        }

        echo json_encode($data);
        exit();
    }

    function otherspublications(){
        $id_lang = id_lang();
        $data['menu_name'] = "Others Publications"; 
        $this->db->order_by('id','desc');
        $id_cat = 6;
        $data['gallery'] = $this->gallerymodel->findBy(array('id_lang'=>$id_lang,'id_gallery_category'=>$id_cat));
        render('gallery/photo',$data);
    }

    function search() {

        $post           = $this->input->post();

        $type           = $post['search_photo'];
        $id_lang        = id_lang();
        $type_uri_path  = db_get_one('gallery_category_frontend_tags','group_concat(id_tags)','id_category = "'.$type.'"');
        $this->db->select(' b.is_delete AS tags_is_del,
                a.name,
                a.description,
                a.is_delete AS gallery_is_del,
                a.id_gallery_category,
                a.id_status_publish,
                a.img,
                a.publish_date,
                a.uri_path,
                a.name,
                a.description,
                c.id AS id_tags,
                c.uri_path AS tags_uri_path,
                c.name AS tags_name
                             ');
         $this->db->limit(6,0);  
         $this->db->order_by('a.publish_date','desc');
         $this->db->group_by('a.id');
         $this->db->join('gallery_tags b', "a.id = b.id_gallery",'left');
         $this->db->join('tags c', "c.id = b.id_tags",'left');
         $this->db->where('a.id_status_publish','2');
         $this->db->where('a.is_delete','0');
         $this->db->where('a.publish_date <' ." '".date('Y-m-d H:i:s')."'");
         $this->db->where('id_gallery_category', 1);


        if ($type_uri_path) {
            $this->db->where('c.id in('.$type_uri_path.')');
        }

        $data   = $this->db->get_where('gallery a',array('a.id_lang'=>$id_lang))->result_array();
        foreach ($data as $key => $value) { 
            $img    =  ($value['img'] != '' ? getImg($value['img'],'small') : getImg('no-image-available.png','small'));
            $date   = event_date($value['publish_date'],'','','-',1);
            $url    = site_url('gallery/detailphoto/'.$value['uri_path']);
            $title  = !empty($value['name']) ? $value['name'] :'';
            $teaser = !empty($value['description']) ? character_limiter($value['description'],121,'...') :'' ;

            $data['list_photo'][$key]['img']    = $img;
            $data['list_photo'][$key]['date']   = $date ;
            $data['list_photo'][$key]['url']    = $url;
            $data['list_photo'][$key]['title']  = $title;
            $data['list_photo'][$key]['teaser'] = $teaser;
        }

            $type = $type?$type:'-';
            $data['load_more']    = $this->morephoto($type,PAGING_PERPAGE+1,1) ? '
        <div class="text-center mt15"><span href="'.base_url_lang().'/gallery/morephoto/'.$type.'/'.PAGING_PERPAGE.'" class="load-more tangan">'.language('load_more').'</span></div>
        ' : '';

        echo json_encode($data);

    }   

    #Insert Gallery Tags
    function insertGalleryTags(){
        $data['data'] = $this->gallerymodel->findByGalleryTags();
        foreach ($data['data'] as $key => $value) {
            $name = $value['uri_path'];
            if (strpos($name, 'networking') !== false){
                $id_tags = '175';
            } else if (strpos($name, 'committee') !== false){
                $id_tags = '176';
            } else {    
                $id_tags = '177';
            }

            $insert_gallery_tags['id_gallery'] = $value['id'];
            $insert_gallery_tags['id_tags']    = $id_tags;
            $this->db->insert('gallery_tags',$insert_gallery_tags);
       }

    }

}