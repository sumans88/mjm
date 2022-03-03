<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tipe_form_register extends CI_Controller {

  function __construct()
  {
    parent::__construct();
    $this->load->model('tipe_form_registrasi_model');

  }

  function index()
  {
    $data['list_status_publish'] = selectlist2(
            array(
            'table'=>'status_publish',
            'title'=>'Pilih status',
            'selected'=>$data['id_status_publish'])
        );

    render('apps/tipe_form_register/index', $data, 'apps');
  }

  function records()
    {
        $data = $this->tipe_form_registrasi_model->records();
        render('apps/tipe_form_register/records', $data, 'blank');

    }

    function add($id='')
    {
        if ($id)
        {
            $data = $this->tipe_form_registrasi_model->findById($id);

            if (!$data)
            {
                die('404');
            }

            $data           = quote_form($data);
            $data['judul']  = 'Sunting';
            $data['proses'] = 'Update';
        }

        else
        {
            die('404');
            // $data['judul']                = 'Tambah';
            // $data['proses']               = 'Simpan';
            // $data['id']                   = '';
            // $data['name']                 = '';
            // $data['parameter']                 = '';
            // $data['uri_path']                 = '';
        }

         $data['list_tipe_input'] = selectlist2(
            array(
              'table'=>'ref_tipe_input_form_register',
              'title'=>'Pilih tipe Input Form Register',
              'id'=>'id',
              'name'=>'nama',
              'selected'=>$data['id_ref_tipe_input']
              )
          );

        render('apps/tipe_form_register/add', $data, 'apps');
    }

    function proses($idedit='')
   {
       $this->layout   = 'none';
       $post           = $this->input->post();
       $ret['error']   = 1;  

       $this->form_validation->set_rules('name', '"Template Name"', 'required');

       if ($this->form_validation->run() == FALSE)
       {
           $ret['message']  = validation_errors(' ',' ');
       }
       else
       {
           $this->db->trans_start();
           if ($idedit)
           {
               auth_update();
               $ret['message']  = 'Update Success';
               $act             = "Update Tipe Form Register";
               $idedit          = $this->tipe_form_registrasi_model->update($post, $idedit);

           }
           else
           {
             auth_insert();
             $ret['message']  = 'Insert Success';
             $act             = "Insert Tipe Form Register";
             $idedit          = $this->tipe_form_registrasi_model->insert($post);
           }

           $ret['error']   = 0;
           $this->db->trans_complete();
       }

       echo json_encode($ret);
   }
   
   function del()
    {
        auth_delete();
        $id     = $this->input->post('iddel');
        $data   = $this->tipe_form_registrasi_model->delete($id);
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
}