<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class News extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('newsModel');
        $this->load->model('newsImagesModel');
        $this->load->model('newsCategoryModel');
        $this->load->model('newsTagsModel');
        $this->load->model('frontendmenumodel');
        $this->load->model('gallerymodel');
        $this->load->model('pagesmodel');

        $uri_path       = $this->uri->segment(4);
        $limit_content  = 0;
    }

    function index()
    {
        $lang               = $this->uri->segment(1);
        $uri_path           = $this->uri->segment(4);

        $page               = $this->uri->segment(5);
        $id_lang            = id_lang();
        $post               = $this->input->post();
        $keyword            = $post['keyword'];
        $m                  = $post['month'];
        $y                  = $post['year'];

        $is_about_report = $uri_path == "us-indonesia-investment-initiative";
        $uri_path = $is_about_report ? 'investment-report' : $uri_path;

        if ($post) {
            $lang     = $post['lang'];
            $uri_path = $post['uri_path'];
            $page     = $post['page'];
        }

        $data = $this->newsCategoryModel->fetchRow(array('uri_path' => $uri_path, 'a.id_lang' => $id_lang));

        if (!$data) {
            $data = $this->newsCategoryModel->fetchRow(array('uri_path' => $uri_path));
            if (!$data) {
                not_found_page();
            }
        }

        if ($id_lang != $data['id_lang']) {
            $id = $data['id_parent_lang'] ? $data['id_parent_lang'] : $data['id'];
            $this->db->where("(id_parent_lang = '$id' or id = '$id')");
            $datas = $this->newsCategoryModel->findBy();
            foreach ($datas as $key => $value) {
                if ($value['id_lang'] == $id_lang) {
                    redirect(base_url("$lang/news/index/$value[uri_path]/$page"));
                }
            }
        }
        $bulan  = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        $this->db->order_by('publish_date', 'desc');

        $data['uri']                    = $uri_path;
        $where['a.id_lang']             = $id_lang;
        $where['a.id_status_publish']   = 2;
        $where['a.approval_level']      = 100;
        $where['a.is_not_available']    = 0;
        $where['a.publish_date  <=']    = date('Y-m-d H:i:s');

        $menu = $this->frontendmenumodel->fetchRow(array('a.id_language' => $id_lang, 'extra_param' => $uri_path)); // ambil id menu 
        if ($menu && $menu['id_parent'] == 0) { // kalo menu sebagai parent category
            $this->db->where('id_parent', $menu['id']);
            $this->db->or_where('a.id', $menu['id']);
            $sub = $this->frontendmenumodel->findBy();
            foreach ($sub as $k => $v) { // cari uripath bawahaanya dan dirinya sendiri
                $param[] = $v['extra_param'];
            }
            unset($where['uri_path_cat']);

            /*untuk load more*/
            if ($uri_path == 'us-indonesia-investment-initiative' || $is_about_report) {
                $this->load->model('pagesmodel');
                $data['page_us'] = $this->pagesmodel->fetchRow(array('uri_path' => 'us-indonesia-investment-initiative'))['page_content']; // check bahasa ini
                $render_us_indonesia['year_us_indonesia']    = date('Y');
                $render_us_indonesia['content_us_indonesia'] = $this->pagesmodel->fetchRow(array('uri_path' => 'join-us-indonesia-investment-initiative'))['page_content'];
                $data['modal_us_indonesia']                  = $this->parser->parse('news/modal_us_indonesia.html', $render_us_indonesia, true);
                $view                                        = 'us_indonesia';
            } else if ($uri_path == "report-and-publications") {
                $view = 'report';
            }
            $data['uri_path'] = $uri_path;
        } else { // kalo menu id_parentnya !=0

            // $this->limit_content  = 10;
            if ($uri_path == 'us-indonesia-investment-initiative' || $is_about_report) {
                $this->load->model('pagesmodel');
                $data['page_us'] = $this->pagesmodel->fetchRow(array('uri_path' => 'us-indonesia-investment-initiative'))['page_content']; // check bahasa ini
                $render_us_indonesia['year_us_indonesia']    = date('Y');
                $render_us_indonesia['content_us_indonesia'] = $this->pagesmodel->fetchRow(array('uri_path' => 'join-us-indonesia-investment-initiative'))['page_content'];
                $data['modal_us_indonesia']                  = $this->parser->parse('news/modal_us_indonesia.html', $render_us_indonesia, true);
                $view                                        = 'us_indonesia';
            } else
            if ($uri_path == 'amcham-reports' || $uri_path == 'other-reports' || $uri_path == 'investment-report') { // custom uri path
                $view = 'report';
            } else if ($uri_path == 'newsletter') {
                $view              = 'newsletter';
                $data['seo_title'] = 'Newsletter';
                $CI = &get_instance();

                $data['newsletter_modal'] = $CI->parser->parse("news/newsletter_modal.html", $CI->data, true);
            }
        }

        if (!$menu) {
            $getCatChild = $this->newsCategoryModel->fetchRow(array('id_parent_lang' => $data['id'], 'is_delete' => 0));
            $menu = $this->frontendmenumodel->fetchRow(array('a.id_language' => $id_lang, 'extra_param' => $getCatChild['uri_path']));
        }
        $data['page_heading'] = $menu['name'];
        if ($uri_path == 'news') {
            $data['page_heading'] = "Hightlights";
        }
        if ($is_about_report) {
            $data['page_heading'] = "About the Investment Initiative";
        }

        $id_menu     = db_get_one('news_category', 'id', 'uri_path ="' . $uri_path . '"');
        $arr_id_menu = db_get_one('news_category', 'group_concat(id)', 'id = ' . $id_menu . ' or id_parent_category = ' . $id_menu);
        if ($uri_path == 'newsletter' && $post) {
            $ar_news_category = id_child_news(53, 1);
            $arr_id_menu = implode(',', $ar_news_category);
            // $ar_news_category = id_child_news(53,1);
            // $arr_id_menu = implode(',', $ar_news_category);
        } else if ($uri_path == 'articles') {
            if (strpos($arr_id_menu, ',13')) {
                $arr_id_menu = str_replace(',13', '', $arr_id_menu);
            } else if (strpos($arr_id_menu, '13')) {
                $arr_id_menu = str_replace('13', '', $arr_id_menu);
            }
        }
        if ($menu && $menu['id_parent'] == 0) {
            // $ar_news_category = id_child_news($data['id'],1);
            $this->db->start_cache();
            // if ($ar_news_category) {
            //     $this->db->where_in("id_news_category",$ar_news_category);
            // }else{
            // $this->db->where("( id_menu = ".$menu['id']." OR uri_path_cat = '".$uri_path."' )");
            // }
            if ($param) {
                foreach ($param as $key => $value) {
                    $param[] = '"' . $value . '"';
                    unset($param[$key]);
                }
                $param = implode(',', $param);
                $this->db->where("( id_menu = " . $menu['id'] . " OR uri_path_cat = '" . $uri_path . "' or uri_path_cat in (" . $param . "))");
            } else {
                $this->db->where("( id_menu = " . $menu['id'] . " OR uri_path_cat = '" . $uri_path . "' )");
            }
        } else {
            // if ($ar_news_category) {

            //     if ($uri_path != 'newsletter'){
            //         $this->db->where_in("id_news_category",$ar_news_category);
            //     } else if (!$post){
            //         $this->db->where_in("id_news_category",$ar_news_category);
            //     }

            // }else{
            // $this->db->where("( id_menu = ".$menu['id']." OR uri_path_cat = '".$uri_path."' )");
            // }
            $this->db->start_cache();
            if (empty($menu['id'])) {
                $this->db->where("id_news_category in(" . $arr_id_menu . ")");
            } else {

                $this->db->where("( id_menu = " . $menu['id'] . " OR uri_path_cat = '" . $uri_path . "' or id_news_category in(" . $arr_id_menu . "))");
            }
        }


        $this->limit_content = PAGING_PERPAGE;


        if ($uri_path == 'us-indonesia-investment-initiative') {
            // $this->db->where('id_news_category',56 );
            $this->db->order_by('year', 'desc');
        }

        $this->db->group_by('id');
        $this->db->order_by('publish_date', 'desc');
        $this->db->order_by('id', 'desc');
        $this->db->stop_cache();
        $news_rows = count($this->newsModel->findViewBy($where, 0, $post));


        $this->db->limit($this->limit_content, $offset);
        $news = $this->newsModel->findViewBy($where, 0, $post);
        // print_r($this->db->last_query());exit;
        $this->db->flush_cache();

        $monthName = date('F', mktime(0, 0, 0, $m, 10)); // March

        $find_k    = ($keyword) ? ' Keywords ' . $keyword . ' ' : "";
        $find_m    = ($m) ? " " . $monthName . ' ' : "";
        $find_y    = ($y) ? ' ' . $y . ' ' : "";

        if ($keyword || $m || $keyword || $y) {
            if ($keyword && $m && $y) {
                $find = $find_k . 'in' . $find_m . '' . $find_y;
            } else if ($keyword && $m && !$y) {
                $find = $find_k . 'in' . $find_m;
            } else if ($keyword && !$m && $y) {
                $find = $find_k . 'in' . $find_y;
            } else if ($keyword && !$m && !$y) {
                $find = $find_k;
            } else if (!$keyword && $m && $y) {
                $find = $find_m . 'in' . $find_y;
            } else if (!$keyword && $m && !$y) {
                $find = $find_m;
            } else if (!$keyword && !$m && $y) {
                $find = $find_y;
            }

            echo '<div class="mb15">Find ' . $news_rows . ' ' . $menu['name'] . ' with' . $find . '</div>';
        }

        foreach ($news as $key => $value) {
            $news[$key]['news_img']      = ($value['img'] != '' ? getImg($value['img'], 'small') : getImg('no-image-available.png', 'small'));
            $news[$key]['news_category'] = ($uri_path == 'us-indonesia-investment-initiative') ? $value['year'] : $value['category'];
            $news[$key]['news_date']     = event_date($value['publish_date']);
            $news[$key]['news_subol']    = $value['teaser'];
            $news[$key]['news_writer']    = ($value['writer'] != "") ? $value['writer'] : "";

            if ($value['id_news_category'] == 54 && $value['mailchimp'] != '') {
                $url = $value['mailchimp'];
                $mailchimp = 'target="_BLANK"';
            } else {
                $url = site_url('news/detail/' . $value['uri_path']);
                $mailchimp = '';
            }

            $news[$key]['news_url']      = $url;
            $news[$key]['mailchimp']     = $mailchimp;
            $news[$key]['news_title']    = $value['news_title'];

            $news[$key]['news_teaser']   = closetags(character_limiter($value['teaser'], 121, '...'));
            $news[$key]['news_teaser']   = ($uri_path == 'other-reports' || $uri_path == 'amcham-reports' || $uri_path == 'investment-report' || $uri_path == 'report-and-publications') ? $value['teaser'] : $news[$key]['news_teaser']; //kalo public
            
            $value['filename'] = ($value['filename']) ? $value['filename'] :$this->db->get_where('news_files a',['a.id_news' => $value['id']])->row_array()['filename'];
            $news[$key]['idx']            = ($value['filename']) ? md5plus($value['id']) : '';
            $news[$key]['invis_download'] = ($value['filename']) ? '' : 'hide';
            $news[$key]['button']         = ($value['filename']) ?
                '<a class="tangan btn-red link-reports ' . $news[$key]['invis_download'] . '" id="' . $news[$key]['idx'] . '" d-title="news" onclick="download_file(this)">  Read the report</a>' : '<a href="' . $news[$key]['news_url'] . '" class="btn-red link-reports">Read and Download Report</a>';

            $news[$key]['news_year']     = $value['year'];
        }
        // print_r($news);exit;
        $data['news']          = $news;

        $data['slideshow']     = slideshow();
        $view                  = $view ? $view : 'index';
        $data['paging']        = PAGING_PERPAGE;
        $data['dsp_load_more'] = $this->more($uri_path, $data['paging'], 1, $keyword, $m, $y) && !$is_about_report  ? '' : 'hide';


        if ($data['seo_title'] == '') {
            $data['seo_title'] = "MJM";
        }

        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);
        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar();

        $data['list_year']  = list_year($y, 10);
        $data['list_month'] = list_month($m);
        $data['dsp_search'] = $uri_path == 'interview' ? '' : 'hide';
        // $data['list_day']   = $list_day;
        // print_r($data['news']);exit;

        if ($post) {
            $data['keyword'] = empty($post['keyword']) ? 0 : $post['keyword'];
            $data['month']   = empty($post['month']) ? 0 : $post['month'];
            $data['year']    = empty($post['year']) ? 0 : $post['year'];

            if ($uri_path == 'newsletter') {
                $data_json       = render("news/search_newsletter", $data, 'blank');
            } else {
                $data_json       = render("news/search_news", $data, 'blank');
            }
            return json_encode($data_json);
            exit;
        } else {
            render("news/$view", $data);
        }
    }

    function detail()
    {
        $this->load->model('newsFilesModel');

        $lang = $this->uri->segment(1);
        $uri_path = $this->uri->segment(4);
        if (!$uri_path) {
            redirect(site_url('news'));
        }
        $this->load->model('newsModel');
        $this->load->model('NewsTagsModel');
        $currentLang = id_lang();
        $data = $this->newsModel->fetchRow(array('a.uri_path' => $uri_path, 'a.id_lang' => $currentLang));
        // $data['category_news'] = db_get_one('news_category','name',array('id'=>$data['id_news_category']));
        if (!$data) {
            $data = $this->newsModel->fetchRow(array('a.uri_path' => $uri_path));

            if ($uri_path == "contribute") {
                $data = $this->pagesmodel->findBy(array('uri_path' => 'contribute'), 1);
                // print_r($data);exit;
            }
            if (!$data && $uri_path != 'contribute') {
                not_found_page();
            }
        }
        // ambil gambar untuk news category Networking  / Us Indonesia Investment-initiave / amcham-in-the-news
        $data['dsp_photo_gallery'] = 'hidden';
        $data['dsp_video_gallery'] = 'hidden';
        $data['dsp_year']          = "hidden";
        $data['dsp_download_us']   = "hidden";
        $data['modal_imagelist']   = '';
        $data['style']             = '';
        $this->db->select('group_concat(id_tags) as group_id_tags');
        $tags       = $this->NewsTagsModel->findBy(array('id_news' => $data['id']), 1)['group_id_tags']; //ambil tag 
        $tags       = explodeable(',', $tags);
        $id_cat_us  = id_child_news(56, 1); // us investment-initiave
        $id_cat_net = id_child_news(52, 1); // networking
        $id_cat_in_news = id_child_news(60, 1); // amcham-in-the-news
        array_push($id_cat_net, 60);
        if (in_array($data['id_news_category'], $id_cat_net) || in_array($data['id_news_category'], $id_cat_us) || in_array($data['id_news_category'], $id_cat_in_news)) { // networking / us investment-initiave
            // cari gallery by id news
            $id_gallery = get_news_gallery_id($data['id']) ? get_news_gallery_id($data['id']) : 0;
            if ($data['id_gallery']) {
                // ambil gallery by id album gallery 
                $id_gallery_colomn = explodeable(',', $data['id_gallery']);
                $id_gallery        = array_filter(array_merge(array($id_gallery), $id_gallery_colomn));
            }
            if ($id_gallery) {
                if (in_array($data['id_news_category'], $id_cat_net)) {
                    $where_listphoto['backurl'] = 'news/index/networking';
                } else if (in_array($data['id_news_category'], $id_cat_us)) {
                    $where_listphoto['backurl'] = 'news/index/us-indonesia-investment-initiative';
                } else if (in_array($data['id_news_category'], $id_cat_in_news)) {
                    $where_listphoto['backurl'] = 'news/index/amcham-in-the-news';
                }
                $where_listphoto['id_tags'] = $tags;
                $imagelist                  = listphoto($id_gallery, $where_listphoto);
                if ($imagelist['status'] == 0) {
                    $data['imagelist']         = $imagelist['imagelist'];
                    $data['modal_imagelist']   = $imagelist['modal_imagelist'];
                    $data['dsp_photo_gallery'] = '';
                } else {
                    $data['imagelist']         = '';
                    $data['modal_imagelist']   = '';
                    $data['dsp_photo_gallery'] = 'hidden';
                }
            } else {
                $data['imagelist']         = '';
                $data['modal_imagelist']   = '';
                $data['dsp_photo_gallery'] = 'hidden';
            }
            if (in_array($data['id_news_category'], $id_cat_us)) {
                $data['style'] = '<style>
                                  ol { counter-reset: item; }
                                ol li { display: block; }
                                ol li:before {
                                    content: counter(item) ". ";
                                    counter-increment: item;
                                    color: #337ab7;
                                }
                            </style>';
            }

            // for video 
            if (in_array($data['id_news_category'], $id_cat_us) && !empty($tags)) { // Us Indonesia Investment-initiave

                #videoUS
                // $data['page_heading'] = "Video Gallery";
                // $_SESSION['page_uri'] = "video";
                $id_lang = id_lang();
                // $this->db->limit(9, 0);
                // $this->db->order_by('a.publish_date','desc'); 
                $where['id_gallery_category'] = 4;
                $where['id_status_publish']   = 2;

                $this->db->where_in('b.id_tags', $tags);
                $this->db->join('gallery_tags b ', 'b.id_gallery = a.id and b.id_images is null', 'left');
                $this->db->group_by('a.id');
                $this->db->select('b.*');
                $data['list_video'] = $this->gallerymodel->findBy($where);

                $count_datalist     = count($data['list_video']);

                foreach ($data['list_video'] as $key => $value) {
                    $data['list_video'][$key]['youtube_url_video'] = getVideo($value['youtube_url']);
                    $data['list_video'][$key]['img_video']         = ($value['img']) ? getImg($value['img'], 'small') : get_youtube_thumbnail($value['youtube_url']);
                }
                $data['dsp_video_gallery'] =  $count_datalist != 0 ? '' : 'hidden';
            } else {
                $data['list_video'] = array();
            }
        } else {
            $data['hidden_gallery'] = 'hidden';
            $data['list_video']     = array();
        }
        if (in_array($data['id_news_category'], $id_cat_us)) {
            $data['dsp_year']        = "";
            $data['dsp_download_us'] = ($data['filename']) ? '' : 'hidden';
            $data['news_img']        = ($data['img'] != '' ? getImg($data['img'], 'small') : getImg('no-image-available.png', 'small'));
            $data['news_category']   = $data['year'];
            $data['news_date']       = event_date($data['publish_date']);
            $data['news_url']        = site_url('news/detail/' . $data['uri_path']);
            $data['news_title']      = $data['news_title'];
            $data['news_teaser']     = $data['teaser'];
            $data['news_year']       = $data['year'];
        }

        $data['news_date']    = event_date($data['publish_date']);
        $data['news_teaser']  = $data['teaser'] != "" ? $data['teaser'] . "<br/>" : "";
        $data['dsp_news_teaser']  = $data['news_teaser'] == "" ? "hidden" : "";
        $data['dsp_news_teaser']  = in_array($data['id_news_category'], $id_cat_us) ? "hidden" : $data['dsp_news_teaser'];
        $data['news_writer'] = ($data['writer'])
            ? ' | <span>' . $data['writer'] . '</span>'
            : '';
        /*($data['user_id_publisher'])
                                 ? ' | <span> by '.db_get_one('auth_user','username','id_auth_user = '.$data['user_id_publisher']).'</span>'  
                                 : '';*/

        /*untuk menambahkan counter hits setiap kali user membuka halaman articel*/
        $whr['id']   = $data['id'];
        $ttl_hits    = intval($data['hits']);
        $upc['hits'] = $ttl_hits + 1;
        $this->db->update('news', $upc, $whr);
        /*end untuk menambahkan counter hits setiap kali user membuka halaman articel*/

        // unset($data['page_content'],$data['teaser']);
        // print_r($data['page_content']);exit; 
        $data['page_content'] = closetags($data['page_content']);
        $data['lang_not_available'] = '';
        if ($currentLang != $data['id_lang']) {
            $id = $data['id_parent_lang'] ? $data['id_parent_lang'] : $data['id'];
            $this->db->where("(a.id_parent_lang = '$id' or a.id = '$id')");
            $datas = $this->newsModel->findBy();
            foreach ($datas as $key => $value) {
                if ($value['id_lang'] == $currentLang && $value['is_not_available'] == 0) {
                    redirect(base_url("$lang/news/detail/$value[uri_path]"));
                }
            }
            $data['is_not_available'] = 1;
        }
        if (!$data) {
            $data['page_name'] = 'page not found';
            $data['page_content'] = 'error 404. page not found';
        }
        if ($data['is_not_available'] == 1) {
            $bahasa = db_get_one('language', 'name', array('id' => $currentLang));
            // $data['lang_not_available'] = '<div style="font-weight:bold"><hr>'.language('not_available_language').' ' .$bahasa.'. '.language('will_be_available_soon'). ' '.$bahasa.'</div><hr>';
            $data['lang_not_available'] = '<div>' . language('not_available_language') . ' ' . ucwords($bahasa) . '.</div><hr>';
            $this->db->select("name");
            $this->db->join("news b", "b.id_news_category = a.id");
            $fieldID = ($currentLang == 1 ? "id_parent_lang" : "id");
            $fieldSelect = ($currentLang == 1 ? "b.id" : "b.id_parent_lang");
            $newCat = $this->db->get_where("news_category a", array($fieldSelect => $data[$fieldID]))->row_array();
            $data['category'] = $newCat['name'];
        }

        $data['dsp_detail_image'] = '';
        $data['is_video']         = '';

        $data['video']      = ($data['link_youtube_video']) ? '<iframe width="840" height="477" src="' . getVideo($data['link_youtube_video']) . '?autoplay=1" frameborder="0" allowfullscreen></iframe>' : '';
        if ($data['video'] != '') {
            $data['dsp_detail_image'] = 'hide';
        } else {
            $data['is_video'] = 'hide';
        }
        /* start untuk material */
        $data['materials'] = array();
        $materials = $this->newsFilesModel->listFiles(array('id_news' => $data['id']));
        foreach ($materials as $key => $value) {
            $materials[$key]['materials_filename'] = $value['name_file'];
            $materials[$key]['mat_idx']       = md5plus($value['idFile']);
        }
        $data['materials']          = $materials;
        $data['dsp_materials']      = count($materials) > 0 ? '' : 'hidden';

        // image image image image image image image image image
        $this->db->where('filename !=""');
        $detail_img = $this->newsImagesModel->findBy(array('id_news' => $data['id']));
        if (!empty(array_filter($detail_img))) {
            foreach ($detail_img as $key => $value) {
                unset($temp);
                $temp['detail_image_img']         = getImg($value['filename'], 'large');
                $temp['detail_image_description'] = $value['description'];
                $temp['dsp_caption_detail_img']   = ($value['description'] == "") ? "hide" : "";
                $data['detail_image'][]           = $temp;
            }

            $data['dsp_detail_image'] = '';
        } else {
            //kalo gak set detail img
            // $temp['detail_image_img']         = getImg($data['img'],'large');
            $temp['detail_image_img']         = "";
            $temp['detail_image_description'] = "";
            $temp['dsp_caption_detail_img']   = "hide";
            $data['detail_image'][]           = $temp;
            $data['dsp_detail_image'] = 'hide';
        }
        // array_filter($data['detail_image']);

        // print_r($data['detail_image']);exit;

        $share_img                = $this->newsImagesModel->findBy(array('id_news' => $data['id'], 'is_share' => 1), 1);
        //kalo gak set detail img
        if (empty(array_filter($share_img))) {
            $share_img['filename'] = $data['img'];
        }


        /*new script (dwiki)*/
        $count_tags = count($tags);
        foreach ($tags as $key => $value) {
            if ($key == ($count_tags - 1)) {
                $tags[$key]['comma_tags'] = '';
            } else {
                $tags[$key]['comma_tags'] = ',';
            }
        }
        /*end new script (dwiki)*/

        $data['share'] = share_widget();

        $data['idx']            = ($data['filename']) ? md5plus($data['id']) : '';
        $data['invis_download'] = ($data['filename']) ? '' : 'hide';
        $data['invis_download'] = in_array($data['id_news_category'], $id_cat_us) ? 'hidden' : $data['invis_download'];

        $get_uri_cat = $this->db->get_where('news_category', array('id' => $data['id_news_category']))->row_array();
        if ($data['link_youtube_video'] != "") {
            $_SESSION['page_uri'] = "video";
        } else {
            $_SESSION['page_uri'] = $get_uri_cat['uri_path'];
        }
        // BREADCRUMB
        $data['news_breadcrumb'] = '';
        $data['page_name'] = in_array($get_uri_cat['id'], id_child_news('56', 1)) ? 'Initiative Indonesia' : $get_uri_cat['name'];

        if ($data['seo_title'] == '') {
            $data['seo_title'] = "MJM";
        }
        $data['meta_img']   = (!empty(array_filter($share_img))) ? getImgLink($share_img['filename'], 'large') : '';
        $data['head_title'] = $data['news_title'];
        $data['full_url']   = current_url();
        $data['head_title'] = $data['seo_title'];


        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);

        // $data['invis_infographic'] = "";
        // if($data['id_news_category'] == 26 || $data['id_news_category'] == 27){
        //     $data['invis_infographic'] = "thumb-dtl-infographic";
        // }
        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar();

        $data['publish_date'] = date("d-m-Y", strtotime($data['publish_date']));
        $group_id_publication  = id_news_publication();

        // if( in_array($data['id_news_category'],$arr_id) ){ //publication beda view
        //     $data['page_cat'] = 1;
        //     render('news/detail_publications',$data);

        // }else 
        if (in_array($data['id_news_category'], $id_cat_net)) { // networking
            render('news/networking', $data);
        } else if ($uri_path == 'contribute') { // contribute newsletter
            render('news/detail_contribute', $data);
        } else {
            render('news/detail', $data);
        }
    }
    function get_download()
    {
        $post = $this->input->post();

        /*untuk menambahkan counter hits setiap kali user membuka halaman articel*/
        // $g_data      = db_get_one('news','filename',array(md5field('id')=>$post['idx']));
        $g_data      = $this->db->get_where('news', array(md5field('id') => $post['idx']))->row_array();
        $whr['id']   = $g_data['id'];
        $ttl_hits    = intval($g_data['hits_download']);
        $upc['hits_download'] = $ttl_hits + 1;
        $this->db->update('news', $upc, $whr);
        /*end untuk menambahkan counter hits setiap kali user membuka halaman articel*/

        $file = $g_data;
        if ($file) {
            // $url_file = base_url().'/file_upload/'.$file;
            $base_url   = base_url();
            $file_path = !$file['filename'] ?  'document/material/':'file_upload/';
            $file['filename'] =  !$file['filename'] ? $this->db->get_where('news_files a',['a.id_news' => $file['id']])->row_array()['filename'] : $file['filename'];
            
            $data['path'] =  $base_url . 'viewer/web/viewer.html?file=' . $base_url . $file_path . $file['filename'];
        } else {
            $data['path'] = 'error';
        }

        echo json_encode($data);
        exit();
    }

    function tags()
    {
        $lang           = $this->uri->segment(1);
        $uri_path       = $this->uri->segment(4);
        $page           = $this->uri->segment(5);
        $id_lang        = id_lang();
        $data           = $this->newsTagsModel->fetchRow(array('b.uri_path' => $uri_path));

        if (!$data) {
            die('404');
        }

        $total = count($this->newsModel->getNewsByTags($data['id_tags'], $page, $id_lang));
        $this->db->order_by('publish_date', 'desc');
        $this->db->limit(PAGING_PERPAGE, $page);
        $news = $this->newsModel->getNewsByTags($data['id_tags'], $page, $id_lang);
        foreach ($news as $key => $value) {
            $news[$key]['start_date']       = iso_date($value['start_date']);
            $news[$key]['uri_path_detail']  = $value['uri_path'];
            $news[$key]['show_img']         = getImg($vaflue['img'], 'small');
        }
        $data['url']            = site_url("news");
        $data['list_news']      = $news;
        $config['base_url']     = site_url("news/tags/$uri_path/");
        $config['total_rows']   = $total;
        $config['per_page']     = PAGING_PERPAGE;
        $config['uri_segment']  = 5;
        $this->load->library('pagination');
        $this->pagination->initialize($config);
        $data['paging'] = $this->pagination->create_links();
        render('news/tags', $data);
    }

    function more($uri_path, $page, $ret = 0, $keyword, $month, $year)
    {
        $id_lang = id_lang();

        $dataCat = $this->newsCategoryModel->fetchRow(array('uri_path' => $uri_path, 'a.id_lang' => $id_lang));

        $menu = $this->frontendmenumodel->fetchRow(array('a.id_language' => $id_lang, 'extra_param' => $uri_path));

        if ($menu && $menu['id_parent'] == 0) {
            $this->db->where('id_parent', $menu['id']);
            $this->db->or_where('a.id', $menu['id']);
            $sub = $this->frontendmenumodel->findBy();
            foreach ($sub as $k => $v) {
                $param[] = $v['extra_param'];
            }
        }
        $where['id_status_publish']   = 2;
        $where['id_lang']             = $id_lang;
        $where['a.approval_level'] = 100;
        $id_menu     = db_get_one('news_category', 'id', 'uri_path ="' . $uri_path . '"');
        $arr_id_menu = db_get_one('news_category', 'group_concat(id)', 'id = ' . $id_menu . ' or id_parent_category = ' . $id_menu);
        // print_r($arr_id_menu );exit;
        if ($uri_path == 'newsletter' && (!empty($keyword) || !empty($month) || !empty($year))) {
            $ar_news_category = id_child_news(53, 1);
            $arr_id_menu = implode(',', $ar_news_category);
        } else if ($uri_path == 'articles') {
            if (strpos($arr_id_menu, ',13')) {
                $arr_id_menu = str_replace(',13', '', $arr_id_menu);
            } else if (strpos($arr_id_menu, '13')) {
                $arr_id_menu = str_replace('13', '', $arr_id_menu);
            }
        }

        if (!$menu) {
            $getCatChild = $this->newsCategoryModel->fetchRow(array('id_parent_lang' => $dataCat['id'], 'is_delete' => 0));
            $menu = $this->frontendmenumodel->fetchRow(array('a.id_language' => $id_lang, 'extra_param' => $getCatChild['uri_path']));
        }
        if ($menu && $menu['id_parent'] == 0) {
            // if($param) $this->db->where_in('uri_path_cat',$param);
            // $this->db->where_in('uri_path_cat',$param);
            // $this->db->where('uri_path_cat IN ( SELECT uri_path FROM news_category WHERE id_lang = '.$id_lang.' AND is_delete = 0)');
            if ($param) {
                foreach ($param as $key => $value) {
                    $param[] = '"' . $value . '"';
                    unset($param[$key]);
                }
                $param = implode(',', $param);
                $this->db->where("( id_menu = " . $menu['id'] . " OR uri_path_cat = '" . $uri_path . "' or uri_path_cat in (" . $param . "))");
            } else {
                $this->db->where("( id_menu = " . $menu['id'] . " OR uri_path_cat = '" . $uri_path . "' )");
            }
        } else {
            if (empty($menu['id'])) {
                $this->db->where("id_news_category in(" . $arr_id_menu . ")");
            } else {
                $this->db->or_where("( id_menu = " . $menu['id'] . " OR uri_path_cat = '" . $uri_path . "' or id_news_category in(" . $arr_id_menu . "))");
            }
        }
        // print_r($arr_id_menu);exit;

        /*$where['b.uri_path']            = $uri_path;*/
        $where['is_not_available'] = 0;
        $where['publish_date <=']     = date('Y-m-d H:i:s');
        $this->db->group_by('id');
        $this->db->limit(PAGING_PERPAGE, ($page));
        $this->db->order_by('publish_date', 'desc');
        $this->db->order_by('id', 'desc');


        if (!empty($keyword)) {
            $post['keyword'] = $keyword;
        }
        if (!empty($month)) {
            $post['month'] = $month;
        }
        if (!empty($year)) {
            $post['year'] = $year;
        }

        $data   = $this->newsModel->findViewBy($where, 0, $post);
        // print_r($this->db->last_query());exit;
        $bulan  = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        if ($ret == 1) {
            return $data ? 1 : 0;
        }

        foreach ($data as $key => $value) {
            $news_img      = ($value['img'] != '' ? getImg($value['img'], 'small') : getImg('no-image-available.png', 'small'));
            $news_category = ($uri_path == 'us-indonesia-investment-initiative') ? $value['year'] : $value['category'];

            $news_date     = event_date($value['publish_date']);
            $news_url      = $value['uri_path'];
            $news_title    = $value['news_title'];
            $news_teaser   = closetags(character_limiter($value['teaser'], 121, '...'));
            $value['filename'] = ($value['filename']) ? $value['filename'] :$this->db->get_where('news_files a',['a.id_news' => $value['id']])->row_array()['filename'];
            $idx            = ($value['filename']) ? md5plus($value['id']) : '';
            $invis_download = ($value['filename']) ? '' : 'hide';
            $button = ($value['filename']) ?
                '<a class="tangan btn-red link-reports ' . $invis_download . '" id="'.$idx.'" d-title="news" onclick="download_file(this)">  Read the report</a>' : '<a href="' . site_url('news/detail/' . $news_url) . '" class="btn-red link-reports">Read and Download Report</a>';

            $news_writer   = $value['writer'];
            if ($uri_path == 'other-reports' || $uri_path == 'amcham-reports' || $uri_path == 'investment-report' || $uri_path == 'report-and-publications') {
                ?>
                <div class="list-reports">
                    <div class="media media-latest">
                        <div class="media-left">
                            <div class="thumbnail-amcham thumb-reports"><?= $news_img ?></div>
                        </div>
                        <div class="media-body">
                            <div class="widget-white">
                                <div class="event-type "><?= $news_category ?>
                                    <!-- |  <?= $news_date ?> -->
                                </div> <!-- Sep 29, 2017 -->
                                <?= $button ?>

                                <hr class="line-content mt15">
                                <div class="title-event-list"><?= $news_title ?></a></div>
                                <!-- <div class="title-event-list"><a href="<?= site_url('news/detail/' . $news_url) ?>"><?= $news_title ?></a></div>  -->
                                <!-- <div class="title-event-list"><a href="<?= $news_url ?>"><?= $news_title ?></a></div> -->
                                <p><?= $news_teaser ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                        } else if ($uri_path == 'newsletter') {
                            if ($value['id_news_category'] == 54 && $value['mailchimp'] != '') {
                                $url = $value['mailchimp'];
                                $mailchimp = 'target="_BLANK"';
                            } else {
                                $url = site_url('news/detail/' . $value['uri_path']);
                                $mailchimp = '';
                            }
                            ?>

                <div id="media-news">
                    <div class="widget-white widget-blue-left media-latest mb15">
                        <div class="event-type"><?= $news_category ?> | <?= $news_date ?></div>
                        <div class="title-event-list mb20"><a href="<?= $url ?>" <?= $mailchimp ?>><?= $news_title ?></a></div>
                        <p><?= $news_teaser ?></p>
                    </div>
                </div>

            <?php } else { ?>
                <div class="media media-latest">
                    <div class="media-left">
                        <div class="thumbnail-amcham thumb-latest"><?= $news_img ?></div>
                    </div>
                    <div class="media-body">
                        <div class="widget-white">
                            <div class="event-type"><?= $news_category ?> | <?= $news_date ?> | <?= $news_writer ?></div> <!-- Sep 29, 2017 -->
                            <div class="title-event-list"><a href="<?= site_url('news/detail/' . $news_url) ?>"><?= $news_title ?></a></div>
                            <p><?= $news_teaser ?></p>
                        </div>
                    </div>
                </div>

<?php
            }
        }
        $data = $this->more($uri_path, $page, 1, $keyword, $month, $year);


        if ((!empty($keyword) || !empty($month) || !empty($year)) && $data) {
            $month = empty($month) ? 0 : $month;
            $year = empty($year) ? 0 : $year;
            echo $this->more($uri_path, ($page + PAGING_PERPAGE), 1, $keyword, $month, $year) ? "<div class='text-center mt10'><a href='" . site_url('news/more/' . $uri_path . '/' . (PAGING_PERPAGE + $page) . '/0/' . $keyword . '/' . $month . '/' . $year) . "' class='load-more'>" . language('load_more') . "</a></div>" : "";
        } else if ($data) {
            echo $this->more($uri_path, ($page + PAGING_PERPAGE), 1) ? "<div class='text-center mt10'><a href='" . site_url('news/more/' . $uri_path . '/' . (PAGING_PERPAGE + $page)) . "' class='load-more'>" . language('load_more') . "</a></div>" : "";
        }
    }

    public function send()
    {
        $post = purify($this->input->post());
        if ($post) {
            $this->form_validation->set_rules('fullname', '"Name"', 'required');
            $this->form_validation->set_rules('email', '"Email"', 'required');
            $this->form_validation->set_rules('email', '"Email"', 'valid_email');
            $this->form_validation->set_rules('commentar', '"Comment"', 'required');
            $this->form_validation->set_rules('id_parent', '"ID Parent"', 'required');
            $ret['error'] = 1;
            $ret['message'] = 'error..';
            if ($this->form_validation->run() == TRUE) {
                $post['create_date']    = date('Y-m-d H:i:s');
                $insert = $this->db->insert("comment_list", $post);

                $this->load->helper('mail');
                $get_event = $this->db->select("name")->get_where("event", array("id" => $post['id_parent']))->row_array();
                $get_email = $this->db->select("smtp_user")->get("email_config")->row_array();
                $email['subject']   = "Comment from " . $post['fullname'];
                $email['content']   = "
                    Content Title : " . $get_event['name'] . "
                    Fullname : " . $post['fullname'] . "
                    Email : " . $post['email'] . "
                    Comment : " . $post['commentar'] . "
                ";
                $email['to']        = $get_email['smtp_user'];
                sent_mail($email);

                if ($insert) {
                    $ret['error'] = 0;
                    $ret['message'] = 'Success..';
                }

                echo json_encode($ret);
                exit();
            } else {
                $ret['error'] = 1;
                $ret['message'] = validation_errors();


                echo json_encode($ret);
                exit();
            }
        } else {
            die('error');
        }
    }

    function searchByName()
    {
        $post = purify($this->input->post(NULL, TRUE));
        $data['news_title'] = $post['news_title'];

        $where['a.id_status_publish'] = 2;
        $where['a.approval_level']    = 100;
        $where['a.is_not_available']  = 0;
        $where['a.id_news_category'] = 55;
        if ($post['news_title'] != '') {
            $this->db->like('a.news_title', $data['news_title']);
        }

        $total = $this->newsModel->findBy($where, 'all');
        $this->db->limit(PAGING_PERPAGE, $page);

        if ($post['news_title'] != '') {
            $this->db->like('a.news_title', $data['news_title']);
        }
        $data['news'] = $this->newsModel->findBy($where);
        $data['keyword_search'] = count($data['news']) == 0 ? ': "' . $post['news_title'] . '" Not Found' : ': "' . $post['news_title'] . '" ';

        foreach ($data['news'] as $key => $value) {
            $data['news'][$key]['news_img']      = ($value['img'] != '' ? getImg($value['img'], 'small') : getImg('no-image-available.png', 'small'));
            $data['news'][$key]['news_category'] = $value['category'];

            $data['news'][$key]['news_date']     = event_date($value['publish_date']);
            $data['news'][$key]['news_url']      = site_url('news/detail/' . $value['uri_path']);
            $data['news'][$key]['news_titles']   = 'sdsdsd';
            $data['news'][$key]['news_teaser']   = character_limiter($value['teaser'], 121, '...');
        }

        render('news/load_more', $data, 'blank');
    }
    public function registNewsletter()
    {
        $this->load->model('newsletterModel');
        $post           =  purify($this->input->post());

        $ret['error']   = 1;

        $where['email'] = $post['email'];
        $checkemail     = $this->newsletterModel->findBy($where);
        if ($checkemail) {
            $ret['msg']   = "sorry, email has registered befored";
        } else {
            $this->newsletterModel->insert($post);

            sent_email_by_category(1, $check_email, $post['email']);

            $ret['error'] = 0;
            $ret['modalname'] = 'myModalThanks';
            $ret['close_modal'] = 'myModalEventsNews';
        }
        echo json_encode($ret);
    }
    function get_material_hits()
    {
        $this->load->model('newsFilesModel');
        $post        = purify($this->input->post());

        $g_data      = $this->db->get_where('news_files', array(md5field('id') => $post['idx']))->row_array();
        $ttl_hits    = intval($g_data['hits']);
        $upc['hits'] = $ttl_hits + 1;
        $this->newsFilesModel->updateAll($upc, $g_data['id']);

        // $ret['modalname']  = 'myModalNewsletter';
        $file = db_get_one('news_files', 'filename', array(md5field('id') => $post['idx']));
        if ($file) {
            $file         = preg_replace('/\s+/', '_', $file);
            $data['path'] = base_url() . 'document/material/' . $file;
        } else {
            $data['path'] = 'error';
        }

        echo json_encode($data);
        exit();
    }
}
