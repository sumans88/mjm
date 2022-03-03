<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Event extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('eventModel');
        $this->load->model('pagesmodel');
    }

    function index()
    {

        $lang                         = $this->uri->segment(1);
        $uri_path                     = $this->uri->segment(4);
        $page                         = $this->uri->segment(5);
        $id_lang                      = id_lang();
        $today                        = date('Y-m-d');

        $view                         = 'index';
        switch ($uri_path) {

            case 'upcoming-events':
                $data['seo_title']              = 'Upcoming Event';
                $data['page_heading']           = 'Upcoming Event';
                $CI = &get_instance();
                $CI->data['dsp_event_list']    = 'hide';
                $CI->data['css_event_list']    = 'hide';

                $where['a.end_date    >=']      = $today;
                // $where['a.is_close ']        = 0;
                $this->db->order_by('start_date', 'asc');
                break;

            case 'past-events':
                $data['seo_title']          = 'Past Event';
                $data['page_heading']       = 'Past Event';

                $id_event_category = id_child_event(26, 1, 1);
                $this->db->where_in('a.id_event_category', $id_event_category);
                $where['a.end_date    <']      = $today;
                // $this->db->or_where('a.is_close', 1);
                $this->db->order_by('start_date', 'desc');
                break;

            case 'annual-golf-turnament':
                $data['pages_annual'] = $this->pagesmodel->findBy(array('uri_path' => 'annual-golf-tournament'), 1)['page_content'];
                $data['seo_title']          = 'Annual Golf Tournament';
                $data['page_heading']       = 'Annual Golf Tournament';
                // $where['a.is_close_1']      = '1';
                $id_annual                  = id_child_event(40, 1, 1);
                $view                       = 'annual_golf';
                $this->db->where_in('a.id_event_category', $id_annual);
                $this->db->order_by('publish_date', 'desc');
                break;

            default:
                not_found_page();
                break;
        }
        $this->db->limit(PAGING_PERPAGE);

        $where['a.id_lang']           = $id_lang;
        $where['a.id_status_publish'] = 2;
        $where['a.publish_date <= '] = $today;
        $data_events                  = $this->eventModel->findViewBy($where);

        $id_cat_amcham_event = array('26', '40');
        foreach ($data_events as $key => $value) {
            $data['events_list'][$key]['category'] = ($value['subcategory'] != '') ? $value['subcategory'] . ' ' : '';
            $data['events_list'][$key]['address']  = (!$value['location_name']) ? '-' : $value['location_name'];
            $data['events_list'][$key]['teaser']   = character_limiter($value['teaser'], 140, '...');
            $data['events_list'][$key]['color']    = in_array($value['id_event_category'], $id_cat_amcham_event) ? 'widget-blue-left' : ' widget-red-left';
            $data['events_list'][$key]['link']     = site_url('event/detail/' . $value['uri_path']);
            $data['events_list'][$key]['title']    = $value['name'];
            // golf
            if ($uri_path == 'annual-golf-turnament') {
                $data['events_list'][$key]['time'] = event_date($value['publish_date'], '', '', '-', 1);
                $data['events_list'][$key]['img']  = getImg('Golf Turkey Logo.jpg', 'large');
            } else {
                $data['events_list'][$key]['time'] = ($value['start_time'] == '00:00' || $value['start_time'] == '00:00:00' || $value['end_time'] == '00:00') ? event_date($value['start_date']) : event_date($value['start_date'], $value['start_time'], $value['end_time']);
                $data['events_list'][$key]['img']  = getImg($value['img'], 'large');
            }
            // past event
            if ($uri_path == 'past-events') {
                $data['hide_events_list'] = 'hidden';
            }
            // upcoming event
            if ($uri_path == 'upcoming-events') {
                $data['button_tab'] = '<li><span class="blue-back"></span> AmCham events</li>
                        <li><span class="red-back"></span> Non-AmCham events</li>';
            } else {
                $data['button_tab'] = '<li><span class="blue-back"></span> AmCham events</li>';
            }
        }

        if (!$data_events) {
            $data['no_events_list'] = 'hide';
        }


        // buat kalender
        $data_events_calendar       = $this->eventModel->data_amcham();

        foreach ($data_events_calendar as $key => $value) {

            if ($value['start_date'] != $value['end_date']) {
                $data_calendar['start']  = $value['start_date'] . ' ' . remove_whitespace($value['start_time']);
                $data_calendar['end']    = iso_date_custom_format($value['end_date'], 'Y-m-d') . ' ' . remove_whitespace($value['end_time']);
            } else {
                $data_calendar['start']  = $value['start_date'] . ' ' . remove_whitespace($value['start_time']);
                $data_calendar['end']    = iso_date_custom_format($value['end_date'] . '+ 1 day', 'Y-m-d') . ' ' . remove_whitespace($value['end_time']);
            }

            $data_calendar['title']      = $value['content_title'];
            $data_calendar['type_event'] = $value['id_event_category'];
            $data_calendar['color']      = (in_array($value['id_event_category'], array('26', '40')))
                ? '#132539!important' : '#be1e2d!important';
            $data_calendar['start_time'] = $value['start_time'];
            $data_calendar['allDay']     = false;
            $data_calendar['className']  = 'info';
            $data_calendar['url']        = site_url('event/detail/' . $value['uri_path']);
            $array_event_calendar[]      = $data_calendar;
        }

        $data['event_data']       = json_encode($array_event_calendar);

        $data['button_tab']       = empty($data['button_tab']) ? '<li><span class="blue-back"></span> AmCham events</li>
                                    <li><span class="red-back"></span> Non-AmCham events</li>' : $data['button_tab'];
        $data['events_list']      = empty($data['events_list'])  ? array() : $data['events_list'];
        $data['paging']           = PAGING_PERPAGE;
        $data['uri_path']         = ($uri_path == '' ? 'index' : $uri_path);
        $data['dsp_load_more']    = $this->more($uri_path, $data['paging'], 1) ? '' : 'hide';

        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar();
        // print_r($data['widget_sidebar']);exit;
        // print_r($data['widget_sidebar']);exit;
        $data['seo_title']        = ($data['seo_title'] == '') ? "MJM" : $data['seo_title'];
        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);
        // print_r($data);exit;
        render('event/' . $view, $data);
    }

    function more($uri_path, $page, $ret = 0, $year, $month)
    {
        $this->load->model('frontendmenumodel');

        $id_lang = id_lang();
        $today   = date('Y-m-d');
        $menu = $this->frontendmenumodel->fetchRow(array('a.id_language' => $id_lang, 'extra_param' => $uri_path));
        if ($uri_path == 'index') {
        } else {
            if ($uri_path == "upcoming-events") {
                $where['a.end_date >=']    = date('Y-m-d');
                $this->db->order_by('start_date', 'asc');
            } else if ($uri_path == "past-events") {
                $id_event_category = id_child_event(26, 1, 1);
                $this->db->where_in('a.id_event_category', $id_event_category);
                $where['a.end_date    <=']      = $today;
                // $where['a.is_close_1']   = '1';
                $this->db->order_by('start_date', 'desc');
            } else if ($uri_path == "annual-golf-turnament") {
                // $where['a.is_close_1']       = '1';
                $id_annual                  = id_child_event(40, 1, 1);
                $this->db->where_in('a.id_event_category', $id_annual);
                $this->db->order_by('publish_date', 'desc');
            }
        }

        $id_cat_amcham_event             = array('26', '40');
        $where['a.id_status_publish'] = 2;
        $where['a.id_lang']              = $id_lang;

        if ($year != 0 && $month != 0) {
            if ($month < 10) {
                $month  = zero_first($month, 2);
            }
            $this->db->like('start_date', $year . "-" . $month);
        } else if ($year != 0) {
            $this->db->like('start_date', $year);
        } else if ($month != 0) {
            if ($month < 10) {
                $month  = zero_first($month, 2);
            }
            $this->db->like('start_date', "-" . $month . "-");
        }
        $this->db->limit(PAGING_PERPAGE, ($page));
        $this->db->group_by('id');
        $this->db->order_by('start_date', 'desc');
        $data = $this->eventModel->findViewBy($where);
        if ($ret == 1) {
            return $data ? 1 : 0;
        }

        $bulan  = array(1 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
        foreach ($data as $key => $value) {
            if ($uri_path == 'annual-golf-turnament') {
                $img           = getImg('Golf Turkey Logo.jpg', 'large');
            } else {
                $img           = getImg($value['img'], 'large');
            }
            $event_year       = date("Y", strtotime($value['start_date']));
            $location_name    = $value['location_name'];
            $location_address = $value['location_name'] . '<br>' . $value['location_address'];
            if ($uri_path == 'annual-golf-turnament') {
                $time        = event_date($value['publish_date'], '', '', '-', 1);
            } else {
                $time       = event_date($value['start_date'], $value['start_time'], $value['end_time']);
            }

            $color         = in_array($value['id_event_category'], $id_cat_amcham_event) ? 'widget-blue-left' : ' widget-red-left';

            if ($uri_path == "annual-golf-turnament") {
?>
                <div class="media media-latest">
                    <div class="media-left">
                        <div class="thumbnail-amcham thumb-top-art"><?= $img ?></div>
                    </div>
                    <div class="media-body">
                        <div class="widget-white">
                            <div class="event-type"><?= $time ?></div>
                            <div class="title-event-list"><a href="<?= site_url("event/detail/$value[uri_path]") ?>"><?= $value['name'] ?></a></div>
                            <p><?= $value['teser'] ?></p>
                        </div>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="widget-white <?= $color ?> mb15">
                    <div class="event-type"><?= $value['category'] ?></div>
                    <div class="title-event-list mb20"><a href="<?= site_url("event/detail/$value[uri_path]") ?>"><?= $value['name'] ?></a></div>
                    <div class="row">
                        <div class="col-sm-5 col-md-5 col-xs-12">
                            <div class="media">
                                <div class="media-left media-middle"><span class="icon-time"></span></div>
                                <div class="media-body media-middle"><?= $time ?>
                                    <!-- 18:00 - 20:30 -->
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-7 col-md-7 col-xs-12">
                            <div class="media">
                                <div class="media-left media-middle"><span class="icon-maps"></span></div>
                                <div class="media-body media-middle"><?= $location_address ?>
                                    <!-- Ayana Hotel, Midplaza -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php
            }
        }

        $data = $this->more($uri_path, $page + PAGING_PERPAGE_MORE, 1, $year, $month);
        // $this->db->limit(PAGING_PERPAGE,($page+PAGING_PERPAGE_MORE));
        // $this->db->order_by('start_date','desc');
        // $data = $this/->eventModel->findBy($where);
        if ($data) {
            echo "<div class='text-center'><a href='" . site_url('event/more/' . $uri_path . '/' . (PAGING_PERPAGE_MORE + $page) . '/0/' . $year . '/' . $month) . "' class='load-more'>" . language('load_more') . "</a></div>";
        }
        // exit;
    }

    function detail($uri_path, $test_view)
    {
        $this->load->model('eventModel');
        $this->load->model('template_tipe_input_form_register_model');
        $this->load->model('NewsTagsModel');
        $this->load->model('eventFilesModel');
        $this->load->model('eventImagesModel');


        $lang                         = $this->uri->segment(1);
        $uri_path                     = $this->uri->segment(4);
        $page                         = $this->uri->segment(5);
        $user_sess_data               = $this->session->userdata('MEM_SESS');
        $id_lang                      = id_lang();
        $today                        = date('Y-m-d');
        $id_annual                    = id_child_event(40, 1, 1);

        $where['a.uri_path']          = $uri_path;
        $where['a.id_lang']           = $id_lang;
        if (!$test_view) {
            $where['a.id_status_publish'] = 2;
        }

        $data     = $this->eventModel->fetchRow($where);


        if (!$data) {
            not_found_page();
        }
        $data['MITRANS_SERVER_KEY']   = MIDTRANS_SERVER_KEY;
        $data['MITRANS_CLIENT_KEY']   = MIDTRANS_CLIENT_KEY;
        $data['MITRANS_MERCHANT_ID']  = MIDTRANS_MERCHANT_ID;

        $data['event']                = $data['id'];
        $data['user_publish'] = ($data['user_id_publisher'])
            ? ' | <span> by ' . db_get_one('auth_user', 'username', 'id_auth_user = ' . $data['user_id_publisher']) . '</span>'
            : '';
        //image detail
        $detail_img = $this->eventImagesModel->findBy(array('id_event' => $data['id'], 'filename !=' => ""));
        if (!empty(array_filter($detail_img))) {
            foreach ($detail_img as $key => $value) {
                $data['detail_image'][$key]['detail_image_img']         = getImg($value['filename'], 'large');
                $data['detail_image'][$key]['detail_image_description'] = $value['description'];
            }
        }

        $data['dsp_detail_image'] = (!empty(array_filter($data['detail_image'])) && $data['detail_image'] != "NULL") ? ''  : 'hide';

        /* start untuk material */
        $data['materials'] = array();
        $materials = $this->eventFilesModel->listFiles(array('id_event' => $data['id']));
        foreach ($materials as $key => $value) {
            $materials[$key]['materials_filename'] = $value['name_file'];
            $materials[$key]['mat_idx']       = md5plus($value['idFile']);
        }
        $data['materials']          = $materials;
        $data['dsp_materials']      = count($materials) > 0 ? '' : 'hidden';

        $data['event_id']         = $data['id'];
        $data['page_content']     = $data['content'];
        $data['news_title']       = $data['name'];;
        $data['news_date']        = event_date($data['start_date'], $data['start_time'], $data['end_time']);

        $data['event_date']       = periode(iso_date($data['start_date']), iso_date($data['end_date']));
        $data['event_time']       = event_time($data['start_date'], $data['start_time'], $data['end_time']);
        $data['location_name']    = $data['location_name'];
        $data['location_address'] = $data['location_address'];

        // untuk menampilkan price dan required form
        // kondisi nya bila ada price dan tidak ada price yang nominal =  0 maka akan tampil 
        // bila tidak price atau semua price amount > 0 maka tidak tampil
        $id_tags_price = db_get_one('event_price', 'group_concat(id_price)', 'id_event = ' . $data['id'] . ' and is_delete = 0');
        $id_tags_price = $id_tags_price ? $id_tags_price : "NULL";
        $price_list    = $this->db->get_where('price', 'id in(' . $id_tags_price . ')')->result_array();
        $is_free_event = false;
        foreach ($price_list as $value) {
            $is_free_event = ($value['amount'] == 0) ? true : $is_free_event;
            $price[] = ucfirst($value['name']);
        }
        $data['event_price']            = implode(',', $price);
        $data['dsp_price']              = $price_list ? "" : "hidden";

        if ($id_tags_price && !$is_free_event) {
            $data['list_event_price']  = selectlist2(array('table' => 'price', 'title' => 'Select Price', 'where' => 'id in(' . $id_tags_price . ') and is_delete = 0'));  //             
            $data['dsp_list_event_price']  = "";
            $data['req_list_event_price']  = "required";
        } else {
            $data['list_event_price']  = "";
            $data['dsp_list_event_price']  = "hidden";
            $data['req_list_event_price']  = "";
        }

        // untuk menghilangkan opsi payment di golf event
        $data['hdn_event_golf']        = in_array($data['id_event_category'], $id_annual) ? 'hidden' : '';
        // modal event register 
        $data['modal_event_register']  = register_now($data);
        // hidden bila past event
        $data['is_past_event']         = check_date_future($data['end_date']) ? '' : 'hidden';
        // hidden bila upcoming event
        $data['is_upcoming_event']     = !check_date_future($data['end_date']) || $data['is_close'] == 1 ? '' : 'hidden';

        // ambil gallery event lewat tags / dari id_gallery yang di event 
        $id_gallery = get_event_gallery_id($data['id']) ? get_event_gallery_id($data['id']) : 0;
        if ($data['id_gallery']) {
            $id_gallery_colomn = explodeable(',', $data['id_gallery']);
            $id_gallery        = array_filter(array_merge(array($id_gallery), $id_gallery_colomn));
        }

        if ($id_gallery) {
            $this->db->select('group_concat(id_tags) as group_id_tags');
            $tags       = $this->NewsTagsModel->findBy(array('id_news' => $data['id']), 1)['group_id_tags']; //ambil tag 
            $tags       = explodeable(',', $tags);

            $where_listphoto['backurl']     = 'event/index/past-events';
            $where_listphoto['hide_button'] = '1';
            $where_listphoto['id_tags']     = $tags;

            $imagelist = listphoto($id_gallery, $where_listphoto);
            if ($imagelist['status'] == 0) {
                $data['imagelist']       = $imagelist['imagelist'];
                $data['modal_imagelist'] = $imagelist['modal_imagelist'];
                $data['dsp_gallery']     = '';
            } else {
                $data['imagelist']       = '';
                $data['modal_imagelist'] = '';
                $data['dsp_gallery']     = 'hidden';
            }
        } else {
            $data['imagelist']       = '';
            $data['modal_imagelist'] = '';
            $data['dsp_gallery']     = 'hidden';
        }


        $data['seo_title']        = ($data['seo_title'] == '') ? "MJM" : $data['seo_title'];
        $data['share']            = share_widget();
        $data['banner_top']       = banner_top();
        $data['widget_sidebar']   = widget_sidebar();
        $data['full_url']         = current_url();

        $share_img                = $this->eventImagesModel->findBy(array('id_event' => $data['id'], 'is_share' => 1), 1);
        $data['meta_img']         = (!empty(array_filter($share_img))) ? getImgLink($share_img['filename'], 'large') : '';

        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);

        $this->db->select('a.is_required,a.is_row,b.name,b.parameter,b.id_ref_tipe_input,c.nama as tipe_input');
        $this->db->join('ref_tipe_form_register b', "b.id = a.id_tipe_input", 'left');
        $this->db->join('ref_tipe_input_form_register c', "c.id = b.id_ref_tipe_input", 'left');
        $this->db->order_by('a.sort', 'asc');
        $data['list_tipe_input_form'] = $this->template_tipe_input_form_register_model->findBy(array('a.id_template' => $data['id_template_form_register']));

        foreach ($data['list_tipe_input_form'] as $key => $value) {

            $is_row         = $value['is_row'] == 1 ? "col-sm-12 col-md-12 col-xs-12" : "col-sm-6 col-md-6 col-xs-12";
            $is_required    = $value['is_required'] == 1 ? "required" : "";
            $label          = $value['is_required'] == 1 ? '*' : '';

            $field = '<div class="' . $is_row . '"><label class="label-amcham text-left">' . $value['name'] . $label . '</label>';

            switch ($value['tipe_input']) {
                case 'text':
                    $field .=  '<input type="' . $value['tipe_input'] . '" name="' . $value['parameter'] . '" id="' . $value['parameter'] . '" class="form-control input-amcham" ' . $is_required . ' >';
                    break;

                case 'textarea':
                    $field .= ' <textarea class="form-control input-amcham" rows="5" name="' . $value['parameter'] . '" id="' . $value['parameter'] . '" ' . $is_required . '></textarea>';

                    switch ($value['parameter']) {
                        case 'participant_name':
                        case 'participant_email':
                        case 'participant_t_mobile':
                            // $field .= '<label>separated by comma</label>';
                            break;

                        default:
                            $field .= "";
                            break;
                    }

                    break;

                case 'email':
                    $field .=  '<input type="email" name="' . $value['parameter'] . '" id="' . $value['parameter'] . '" class="form-control input-amcham" ' . $is_required . ' >';
                    break;

                case 'dropdown':
                    switch ($value['parameter']) {
                        case 'id_ref_handicap':
                            $field  .=  '<select name="' . $value['parameter'] . '" id="' . $value['parameter'] . '" class="form-control input-amcham" ' . $is_required . ' >';
                            $field  .=  selectlist2(array('table' => 'ref_handicap', 'title' => 'Select Handicap'));
                            $field  .=  '</select>';
                            break;

                        case 'id_ref_gender':
                            $field  .=  '<select name="' . $value['parameter'] . '" id="' . $value['parameter'] . '" class="form-control input-amcham" ' . $is_required . ' >';;
                            $field  .=  selectlist2(array('table' => 'ref_gender', 'title' => 'Select Gender'));
                            $field  .=  '</select>';
                            break;

                        case 'id_ref_member':
                            $field  .=  '<select name="' . $value['parameter'] . '" id="' . $value['parameter'] . '" class="form-control input-amcham" ' . $is_required . ' >';
                            $field  .=  selectlist2(array('table' => 'ref_member', 'title' => 'Select Member'));
                            $field  .=  '</select>';

                            break;

                        case 'id_ref_meal':
                            $field  .=  '<select name="' . $value['parameter'] . '" id="' . $value['parameter'] . '" class="form-control input-amcham" ' . $is_required . ' >';
                            $field  .=  selectlist2(array('table' => 'ref_meal', 'title' => 'Select Meal'));
                            $field  .=  '</select>';

                            break;

                        default:
                            $field .= "";
                            break;
                    }
                    break;

                default:
                    $field .= "";
                    break;
            }

            $field  .= '</div>';
            $data['list_tipe_input_form'][$key]['field']                = $field;
        }

        // kalo ada template dan event end dibawah hari ini dan sudah di close
        if ($data['id_template_form_register'] != 0 && ($data['end_date'] > date('Y-m-d') || $data['is_close'] == 0)) {
            $data['join_event'] = '<a href="#" class="btn-red btn-link-price mt30" data-toggle="modal" data-target="#myModalEvents">Join Our Event</a>';
        } else {
            $data['join_event'] = '';
        }

        if (in_array($data['id_event_category'], $id_annual)) {
            render('event/detail_annual', $data);
        } else {
            render('event/detail', $data);
        }
    }

    function get_download()
    {
        $post = purify($this->input->post());

        $file = db_get_one('event', 'filename', array(md5field('id') => $post['idx']));
        if ($file) {
            $file = preg_replace('/\s+/', '_', $file);
            $data['path'] =  base_url() . 'file_upload/' . $file;
        } else {
            $data['path'] = 'error';
        }

        echo json_encode($data);
        exit();
    }

    function get_material_hits()
    {
        $this->load->model('eventFilesModel');
        $post        = purify($this->input->post());

        $g_data      = $this->db->get_where('event_files', array(md5field('id') => $post['idx']))->row_array();
        $ttl_hits    = intval($g_data['hits']);
        $upc['hits'] = $ttl_hits + 1;
        $this->eventFilesModel->updateAll($upc, $g_data['id']);

        $ret['modalname']  = 'myModalNewsletter';
        $file = db_get_one('event_files', 'filename', array(md5field('id') => $post['idx']));
        if ($file) {
            $file         = preg_replace('/\s+/', '_', $file);
            $data['path'] = base_url() . 'document/material/' . $file;
        } else {
            $data['path'] = 'error';
        }

        echo json_encode($data);
        exit();
    }

    function register($type_input)
    {
        $this->load->model('eventModel');
        $this->load->model('bank_account_model');
        $this->load->model('paymentconfirmation_model');
        $this->load->model('eventprice_model');

        $post              = purify($this->input->post());
        $full_url = $post['full_url'];
        $uri_path          = end(explode("/", $post['full_url']));
        $data_event        = $this->eventModel->findBy(array('a.uri_path' => $uri_path), 1);
        $online_payment = $post['online_payment'];
        unset($post['full_url'], $post['online_payment']);

        $event_id          = $data_event['id'];
        $event_name        = $data_event['name'];

        $user_sess_data    = $this->session->userdata('MEM_SESS');
        $data_form         = $post;
        $post['member_id'] = $user_sess_data['id'];
        $post['event_id']  = $post['event_id'];

        $ret['error']      = 1;
        // kirim email notif ke admin
        if ($type_input) {
            unset($data_form['event_id']);
            unset($data_form['full_url']);
            unset($data_form['bank_name']);
            unset($data_form['payment_method']);
            unset($data_form['success_invoice']);
            // content untuk email admin
            $content .= '<table border="1">';
            foreach ($data_form as $key => $value) {
                $content .= '<tr>';
                $content .= '<td>';
                if ($key == 'invoice_number') {
                    $content .= 'Invoice Number';
                } else if ($key == 'id_ref_event_price') {
                    $content .= "Price";
                } else {
                    $content .= db_get_one('ref_tipe_form_register', 'name', array('parameter' => $key));
                }
                $content .= '</td>';

                $content .= '<td>';

                $content .= template_event_value($key, $value);
                $content .= '</td>';
                $content .= '</tr>';
            }
            $content .= '</table>';

            $email = array(
                'event_name' => $event_name,
                "content" => $content,
                "link" => base_url() . 'apps/login'
            );

            // sent email admin

            if (db_get_one('event', 'id_event_category', array('id' =>  $event_id)) == '40') {
                $email_golf = db_get_one('contact_us_receive', 'group_concat(email)', array('id_email_category' => 20));
                $admin_email = $email_golf ? EMAIL_ADMIN_TO_SEND . ',' . $email_golf : EMAIL_ADMIN_TO_SEND;
            } else {
                $admin_email =  EMAIL_ADMIN_TO_SEND;
            }
            sent_email_by_category(8, $email, $admin_email);
            $ret['error']      = 0;
            $ret['clearform']  = 1;
            echo json_encode($ret);
            exit;
        } else {

            $price_data  = $this->eventprice_model->findBy(array('id' => $post['id_ref_event_price']), 1);

            $payment_method      = $post['payment_method'];
            $invoice_number = 'E-' . rand(10000000000, 99999999999);
            $participant_email = !empty($post['email_1']) ? $post['email_1'] : $post['email_2'];
            $participant_email = empty($participant_email) ? 'amar.bots2@gmail.com' : $participant_email;
            if ($payment_method == 'payment_online') {
                $id_ref_payment_type = '1';
            } else if ($payment_method == 'bank_transfer' || $payment_method == 'onsite_payment' || $price_data['amount'] == 0 || empty($price_data)) {
                // name 
                if (!empty($post['fullname'])) {
                    $participant_name = $post['fullname'];
                } else if (!empty($post['firstname'])) {
                    $participant_name = $post['firstname'];
                } else if (!empty($post['lastname'])) {
                    $participant_name = $post['lastname'];
                } else {
                    $participant_name = '';
                }

                // email




                if ($payment_method == 'bank_transfer') {
                    // jika bank transfer maka akan kirim emaili guide pembayaran

                    $bank_data                                      = $this->bank_account_model->bank_list();
                    $bank_page_content                              = bank_page_email($bank_data);

                    $data_email_event_register['name']              = $participant_name;
                    $data_email_event_register['event_name']        = $event_name;
                    $data_email_event_register['name_price']        = $price_data['alias'] == '' ? $price_data['name'] : $price_data['alias'];
                    $data_email_event_register['price']             = number_format($price_data['amount']);
                    $data_email_event_register['link']              = base_url_lang() . '/payment_confirmation/' . $invoice_number;
                    $data_email_event_register['invoice_number']    = $invoice_number;
                    $data_email_event_register['grand_total']       = number_format($price_data['amount']);
                    $data_email_event_register['bank_page_content'] = $bank_page_content;
                    $id_ref_payment_type                            = '2';
                    if (!empty($price_data)) {
                        sent_email_by_category(13, $data_email_event_register, $participant_email);
                    }
                } else if ($payment_method == 'onsite_payment' || $price_data['amount'] == 0 || empty($price_data)) {
                    // onsite or free
                    $data_email_event_register['name']              = $participant_name;
                    $data_email_event_register['event_name']          = $data_event['name'];
                    $data_email_event_register['event_date']          =
                        date('l j F Y', strtotime($data_event['start_date'])) . ' at ' .
                        $data_event['start_time'] . ' - ' . $data_event['end_time'];

                    $data_email_event_register['before_event_date']   =
                        date('l j F Y', strtotime('-1 days', strtotime($data_event['start_date'])));

                    $data_email_event_register['event_place']         = $data_event['location_name'];


                    $data_email_event_register['event_name_2']        = $data_email_event_register['event_name'];
                    $data_email_event_register['event_place_2']       = $data_email_event_register['event_place'];
                    $data_email_event_register['event_date_2']        =
                        indonesia_datetime($data_event['start_date'], 'l j F Y', '') . ' pukul ' .
                        $data_event['start_time'] . ' - ' . $data_event['end_time'];

                    $data_email_event_register['before_event_date_2'] = indonesia_datetime(
                        strtotime('-1 days', strtotime($data_event['start_date'])),
                        'l j F Y',
                        ''
                    );

                    $data_email_event_register['invoice_number']      = $invoice_number;
                    $data_email_event_register['event_place_2']       = $data_email_event_register['event_place'];

                    if (!empty($price_data)) {
                        //onsite
                        $id_ref_payment_type                              = '3';
                        sent_email_by_category(19, $data_email_event_register, $participant_email);
                    } else {
                        //free
                        $id_ref_payment_type                              = '4';
                        sent_email_by_category(23, $data_email_event_register, $participant_email);
                        $post['is_approve'] = 1;
                    }
                }

                $ret['modalname']     = 'myModalNewsletter';
            }


            $post['event_price']        = $price_data['name'];
            $post['event_price_amount'] = $price_data['amount'];
            unset($post['payment_method']);
            unset($post['bank_name']);
            unset($post['full_url']);
            unset($post['invoice_number']);
            unset($post['success_invoice']);
            $insert           = $this->eventModel->insertParticipant($post);
            // event_approve($event_id,$insert,'','nothing');


            if ($payment_method != 'payment_online') {
                $data_invoice['id_ref_payment_category'] = 1;
                $data_invoice['invoice_number']          = ($price_data['amount'] == 0) ? '-' : $invoice_number;
                $data_invoice['member_id']               = $insert;
                $data_invoice['id_ref_payment_type']     = $id_ref_payment_type;
                $data_invoice['event_id']                = $event_id;
                $data_invoice['is_paid']                 = ($payment_method == 'onsite_payment' || $price_data['amount'] == 0 || empty($price_data)) ? '1' : '0';
                $this->paymentconfirmation_model->insert_frontend($data_invoice);
            } else {
                //payment online
                $data_invoice['id_ref_payment_category'] = 1;
                $data_invoice['invoice_number']          = ($price_data['amount'] == 0) ? '-' : $invoice_number;
                $data_invoice['member_id']               = $insert;
                $data_invoice['id_ref_payment_type']     = 1;
                $data_invoice['event_id']                = $event_id;
                $data_invoice['is_paid']                 = 0;
                $this->paymentconfirmation_model->insert_frontend($data_invoice);

                $invoice = $this->create_Invoice($invoice_number, $participant_email, $price_data['amount'], 'Register Event ' . $event_name, ['url' => $full_url], $online_payment);
                $ret['error']      = 0;
                $ret['redirect'] = $invoice['invoice_url'];
                echo json_encode($ret);
                exit;
            }
            $ret['error']      = 0;
            $ret['modalclose'] = 'myModalEvents';
        }

        echo json_encode($ret);
    }
    private function create_Invoice($invoice_code, $invoice_email, $invoice_price, $invoice_desc, $invoice_option = [], $create_Invoice)
    {
        include APPPATH . 'third_party/xendit/XenditPHPClient.php';
        $options['secret_api_key'] = XENDIT_SECRET_KEY;
        $xendit = new XenditClient\XenditPHPClient($options);
        $url = $invoice_option['url'];
        unset($invoice_option['url']);
        $invoice_option = [
            'success_redirect_url' => $url . '?status=success',
            'failure_redirect_url' => $url . '?status=error',
            'should_send_email' => true,
        ];

        switch ($create_Invoice) {
            case 'virtual_account':
                $invoice_price =  $invoice_price + 5000;
                $invoice_option['payment_methods']  = [
                    "BRI", "MANDIRI", "BNI",
                    "PERMATA"
                ];

                break;

            case 'card_kredit':
                $invoice_price = ($invoice_price + 2000) + ($invoice_price * 0.026) + 52;
                $invoice_option['payment_methods']  = ["CREDIT_CARD"];
                break;
        }
        $createInvoice = $xendit->createInvoice($invoice_code, $invoice_price, $invoice_email, $invoice_desc, $invoice_option);

        return $createInvoice;
    }

    function search()
    {
        $this->load->model('eventModel');

        $post = $this->input->post();
        $uri_path                     = $this->uri->segment(4);
        $page                         = $this->uri->segment(5);

        if ($post['search_amchamEvent']) {
            $this->db->where_in('a.id_event_category', $post['search_amchamEvent']);
            $where['a.end_date    <=']      = $today;
            // $this->db->order_by('start_date','desc'); 

            $data_amcham_event = $this->eventModel->data_amcham_event();
            $i = 1;
            if ($data_amcham_event) {
                foreach ($data_amcham_event as $key => $value) {

                    $data['list_event'][$key]['no']      = $i;
                    $data['list_event'][$key]['id']      = $value['id'];
                    $data['list_event'][$key]['title']   = $value['name'];
                    $data['list_event'][$key]['time']    = ($value['start_time'] == '00:00' || $value['start_time'] == '00:00:00' || $value['end_time'] == '00:00') ? event_date($value['start_date']) : event_date($value['start_date'], $value['start_time'], $value['end_time']);
                    $data['list_event'][$key]['link']    = site_url('event/detail/' . $value['uri_path']);
                    $data['list_event'][$key]['address'] = (!$value['location_name']) ? '-' : $value['location_name'];
                    $data['list_event'][$key]['color']   = 'widget-blue-left';

                    $i++;
                }
            }
        } elseif ($post['search_nonamchamEvent']) {
            $this->db->where_in('a.id_event_category', $post['search_nonamchamEvent']);
            $where['a.end_date    <=']      = $today;
            // $this->db->order_by('start_date','desc'); 

            $data_nonamcham_event = $this->eventModel->data_nonamcham_event();
            $i = 1;
            if ($data_nonamcham_event) {
                foreach ($data_nonamcham_event as $key => $value) {
                    $data['list_event'][$key]['no']      = $i;
                    $data['list_event'][$key]['id']      = $value['id'];
                    $data['list_event'][$key]['title']   = $value['name'];
                    $data['list_event'][$key]['time']    = ($value['start_time'] == '00:00' || $value['start_time'] == '00:00:00' || $value['end_time'] == '00:00') ? event_date($value['start_date']) : event_date($value['start_date'], $value['start_time'], $value['end_time']);
                    $data['list_event'][$key]['link']    = site_url('event/detail/' . $value['uri_path']);
                    $data['list_event'][$key]['address'] = (!$value['location_name']) ? '-' : $value['location_name'];
                    $data['list_event'][$key]['color']   = 'widget-red-left';

                    $i++;
                }
            }
        }
        echo json_encode($data);
    }

    function check_price()
    {
        $this->load->model('eventprice_model');
        $post        = $this->input->post();
        $price_data  = $this->eventprice_model->findBy(array('id' => $post['id_price']), 1);
        if ($price_data['amount'] == '0') {
            echo 'true';
            exit;
        } else {
            echo 'false';
            exit;
        }
    }
} 

/*
Past Event Total 290 record dengan id_event_category=26 
- a.is_close dimatikan karena belum membuat query is_close di view_content_event
- sangat berpengaruh kepada id_status_publish 2

End Date Upcoming Events dimatikan

Annnual Golf 
IS Close tidak ada di query
*/
