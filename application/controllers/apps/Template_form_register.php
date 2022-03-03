<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template_form_register extends CI_Controller {

  function __construct()
  {
    parent::__construct();
    $this->load->model('template_form_registrasi_model');
    $this->load->model('tipe_form_registrasi_model');
    $this->load->model('template_tipe_input_form_register_model');
  }

  function index()
  {
    $data['list_status_publish'] = selectlist2(
            array(
            'table'=>'status_publish',
            'title'=>'Pilih status',
            'selected'=>$data['id_status_publish'])
        );

    render('apps/template_form_register/index', $data, 'apps');
  }

  function records()
    {
        $data = $this->template_form_registrasi_model->records();
        render('apps/template_form_register/records', $data, 'blank');

    }

    function add($id='')
    {
        if ($id)
        {
            $data = $this->template_form_registrasi_model->findById($id);

            if (!$data)
            {
                die('404');
            }

            $data           = quote_form($data);
            $data['judul']  = 'Edit';
            $data['proses'] = 'Update';
        }

        else
        {
            $data['judul']                = 'Add';
            $data['proses']               = 'Save';
            $data['id']                   = '';
            $data['name']                 = '';
            $data['uri_path']                 = '';
        }

        $data['list_tipe_input'] = $this->tipe_form_registrasi_model->findBy();
        foreach ($data['list_tipe_input'] as $key => $value) {
          $data['list_tipe_input'][$key]['id_tipe_input']    = $value['id'];
          $data['list_tipe_input'][$key]['name_tipe_input']  = $value['name'];
        }

        $data['list_status_publish']  = selectlist2(array('table'=>'status_publish','title'=>'Select Status','selected'=>$data['id_status_publish']));

        render('apps/template_form_register/add', $data, 'apps');
    }

    function proses($idedit='')
   {
       $this->layout   = 'none';
       $post           = $this->input->post();
       $ret['error']   = 1;

       $arr_id_tipe_input_form  = array_filter(explode(",", $post['id_tipe_input_form']));
       $arr_is_required         = explode(",", $post['is_required']);
       $arr_is_row              = explode(",", $post['is_row']);


      foreach ($arr_id_tipe_input_form as $key => $value) {
        $temp['id_tipe_input_form'] = ($arr_id_tipe_input_form[$key] == '' )? '':  $arr_id_tipe_input_form[$key];
        $temp['is_required']        = ($arr_is_required[$key] == '' )? '':  $arr_is_required[$key];
        $temp['is_row']             = ($arr_is_row[$key] == '' )? '':  $arr_is_row[$key];
        $datas['tipe_input_form'][] = $temp;
      }

       unset($post['id_tipe_input_form'],$post['is_required'],$post['is_row']);  

       $this->form_validation->set_rules('name', '"Template Name"', 'required');

           $this->db->trans_start();


           $post['name']            = $post['name'];

           if ($idedit)
           {
               auth_update();
               $ret['message']  = 'Update Success';
               $act             = "Update Template Form Register";
               $id_template          = $this->template_form_registrasi_model->update($post, $idedit);


           }
           else
           {
             auth_insert();
             $ret['message']  = 'Insert Success';
             $act             = "Insert Template Form Register";
             $id_template          = $this->template_form_registrasi_model->insert($post);
           }

          // $template = $this->template_form_registrasi_model->findById($id_template);

          $this->db->trans_complete();
          set_flash_session('message',$ret['message']);
          $ret['error'] = 0;


          #tipe_input_form

          $sort = 1;
          foreach ($datas['tipe_input_form'] as $key => $value) {
            if($value){
            $cek = $this->tipe_form_registrasi_model->fetchRow(array('id'=>$value['id_tipe_input_form']));//liat tags name di tabel ref

            if(!empty(array_filter($cek))){//kalo belom ada
              $id_tags = $cek['id']; //kalo udah ada, tinggal ambil idnya
            }

            $cekTagsNews = $this->template_tipe_input_form_register_model->fetchRow(array('id_tipe_input'=>$id_tags,'id_template'=>$id_template)); //liat di tabel news tags, (utk edit)


            if(!$cekTagsNews){//kalo blm ada ya di insert
              $tag['id_tipe_input'] = $value['id_tipe_input_form'];
              $tag['id_template']   = $id_template;
              $tag['is_required']   = $value['is_required'];
              $tag['is_row']        = $value['is_row'];
              $tag['sort']          = $sort++;
              $id_news_tags = $this->template_tipe_input_form_register_model->insert($tag);
            }
            else{//kalo udah ada, ambil id nya utk di simpen sbg array utk kebutuhan delete
              $tag['id_tipe_input'] = $value['id_tipe_input_form'];
              $tag['id_template']   = $id_template;
              $tag['is_required']   = $value['is_required'];
              $tag['is_row']        = $value['is_row'];
              $tag['sort']          = $sort++;
              $id_news_tags = $this->template_tipe_input_form_register_model->update($tag,$cekTagsNews['id']);
            }
         
          }
          // }



                    $this->db->where_not_in('a.id_tipe_input',$arr_id_tipe_input_form); 
          $delete = $this->template_tipe_input_form_register_model->findBy(array('a.id_template'=>$id_template)); //dapetin id news tags yg diapus  (where id not in insert / select and id_news = $id_template)

          // print_r($delete);exit;

          foreach ($delete as $key => $value) {
            $a['is_delete'] = 1;
            $b = $this->template_tipe_input_form_register_model->update($a,$value['id']);
          }
        }
    echo json_encode($ret);
  }
   
   function del()
    {
        auth_delete();
        $id     = $this->input->post('iddel');
        $data   = $this->template_form_registrasi_model->delete($id);
        detail_log();
        insert_log("Delete Pages");
    } 

    function getKategoriPertanyaan(){
      $post   = $this->input->post();
      $id_form = $post['id_form'];
      $data['list_kategori'] =  selectlist2(
                                array('table'=>'ref_kategori_pertanyaan_kuis_ipp',
                                      'title'=>'Pilih Kategori Pertanyaan',
                                      'id'=>'id',
                                      'name'=>'nama',
                                      'order'=>'urut',
                                      'where'=>array('id_ref_form_ipp'=>$id_form))
                                );
      echo json_encode($data);
    } 

    function getPertanyaanParent(){
      $post   = $this->input->post();
      $id_kategori = $post['id_kategori'];
      $data['list_parent'] =   selectlist2(
                                      array('table'=>'ref_pertanyaan_kuis_ipp',
                                            'title'=>'Pilih Pertanyaan Induk',
                                            'id'=>'id',
                                            'name'=>'nama',
                                            'order'=>'urut',
                                            'selected'=>$data['id'],
                                            'where'=>array('id_kategori'=>$id_kategori,
                                                            'id_parent_pertanyaan'=>0))
                                );
      echo json_encode($data);
    }

    function dataKategori(){
      $id_form = $this->input->post()['id_form'];

      $data['list_kategori'] = selectlist2(array(
        'table'=>'ref_kategori_pertanyaan_kuis_ipp',
        'title'=>'Pilih Kategori',
        'id'=>'id',
        'name'=>'nama',
        'where'=>array(
          'id_ref_form_ipp'=>$id_form)
        )
      );
      echo json_encode($data);
    }

  function get_template_tipe_input(){
    $id_template = purify($this->input->post('id'));
    $this->db->order_by('a.sort','asc');
    $this->db->select('b.name,b.parameter,b.id_ref_tipe_input,c.nama');
    $this->db->join('ref_tipe_form_register b',"b.id = a.id_tipe_input",'left');
    $this->db->join('ref_tipe_input_form_register c',"c.id = b.id_ref_tipe_input",'left');
    $data = $this->template_tipe_input_form_register_model->findBy(array('a.id_template'=>$id_template));
    echo json_encode($data);
  }
}