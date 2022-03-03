<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Home extends CI_Controller {



    function __construct(){



        parent::__construct();



        // $this->load->library('../controllers/article');



        



    }

 /*       function update_gallery_tags()

    {

      $this->db->like('name','Networking');

      $this->db->or_like('name','Evening');

      $data_net = $this->db->get('gallery')->result_array();



      foreach ($data_net as $value) {

        $data_insert[] = ['id_gallery'=>$value['id'],

                          'id_tags'=>175,

                          'user_id_create'=> 1,

                          'create_date'=> '2018-12-10 10:10:10'

        ];

      }

      $this->db->like('name','Committee');

      $this->db->or_like('name','Meeting');

      $data_com = $this->db->get('gallery')->result_array();



      foreach ($data_com as $value) {

        $data_insert[] = ['id_gallery'=>$value['id'],

                          'id_tags'=>176,

                          'user_id_create'=> 1,

                          'create_date'=> '2018-12-10 10:10:10'

        ];

      }



      $this->db->like('name','Initiative');

      $data_ini = $this->db->get('gallery')->result_array();



      foreach ($data_ini as $value) {

        $data_insert[] = ['id_gallery'=>$value['id'],

                          'id_tags'=>178,

                          'user_id_create'=> 1,

                          'create_date'=> '2018-12-10 10:10:10'

        ];

      }



      $this->db->not_like('name','Networking');

      $this->db->not_like('name','Evening');

      $this->db->not_like('name','Committee');

      $this->db->not_like('name','Meeting');

      $this->db->not_like('name','Initiative');

      $data_other = $this->db->get('gallery')->result_array();



       foreach ($data_other as $value) {

        $data_insert[] = ['id_gallery'=>$value['id'],

                          'id_tags'=>177,

                          'user_id_create'=> 1,

                          'create_date'=> '2018-12-10 10:10:10'

        ];

      }

      $this->db->insert_batch('gallery_tags', $data_insert);

      print_r($data_insert);





      // Networking Evenings 175

      // Committee Meetings 176

      // Initiative Indonesia 177

      // Other Special Events 178







    }*/



/*    function test()



     {



      print_r(



        db_get_one('contact_us_receive','group_concat(email)',array('id_email_category' => 15))



      );exit;



       # code...



     } 



    function test_function($as){



          // Open the image to draw a watermark



          $image = new Imagick();



          $image->readImage(getcwd(). "./test_watermark/1.jpg");







          // Open the watermark image



          // Important: the image should be obviously transparent with .png format



          $watermark = new Imagick();



          $watermark->readImage(getcwd(). "/test_watermark/AMCHAM-logo-04.png");







          // Retrieve size of the Images to verify how to print the watermark on the image



          $img_Width = $image->getImageWidth();



          $img_Height = $image->getImageHeight();



          $watermark_Width = $watermark->getImageWidth();



          $watermark_Height = $watermark->getImageHeight();







          // // Check if the dimensions of the image are less than the dimensions of the watermark



          // // In case it is, then proceed to 



          // if ($img_Height < $watermark_Height || $img_Width < $watermark_Width) {



          //     // Resize the watermark to be of the same size of the image



          //     $watermark->scaleImage($img_Width, $img_Height);







          //     // Update size of the watermark



          //     $watermark_Width = $watermark->getImageWidth();



          //     $watermark_Height = $watermark->getImageHeight();



          // }







          // Calculate the position



          $x = ($img_Width - $watermark_Width) / 2;



          $y = ($img_Height - $watermark_Height) / 2;







          // Draw the watermark on your image



          $image->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);











          // From now on depends on you what you want to do with the image



          // for example save it in some directory etc.



          // In this example we'll Send the img data to the browser as response



          // with Plain PHP



          // $image->writeImage("/test_watermark/<kambing class=""></kambing>" . $image->getImageFormat()); 



          // header("Content-Type: image/" . $image->getImageFormat());



          // echo $image;



          echo 'sudah';







          // Or if you prefer to save the image on some directory



          // Take care of the extension and the path !



          $image->writeImage(getcwd(). "/test_watermark/hasil-04.jpg"); 



    }



    function test_function_2($id_invoice)



    {



      $ch = curl_init("http://google.com");



      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);



      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);



      curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);



      $content = curl_exec($ch);



      curl_close($ch);



      print_r($content);exit;



    }



    function clear_html($value){



      $clear = strip_tags($value);



        // Clean up things like &



        $clear = html_entity_decode($clear);



        // Strip out any url-encoded stuff



        $clear = urldecode($clear);



        // Replace non-AlNum characters with space



        $clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);



        // Replace Multiple spaces with single space



        $clear = preg_replace('/ +/', ' ', $clear);



        // Trim the string of leading/trailing space



        $clear = trim($clear);







        return $clear;



    }



    function check_amcham($value='')



    {



      $this->db->like('content', 'amcham');



      $data = $this->db->get('event')->result_array();



      foreach ($data as $key => $value) {



        // Strip html Tags



        $clear = $this->clear_html($value['content']);







        preg_match('/amcham/',  $clear,$amcham_ada);







        if ($amcham_ada) {



          $check[$value['id']][] = array('content'=>$value['content']); 



        }







        // $clear2 = $this->clear_html($value['news_title']);







        // preg_match('/amcham/',$clear2,$amcham_ada2);



        // if ($amcham_ada2) {



        //   $check[$value['id']][] = array('is_news_title' => 1); 



        // }







      }



      print_r($check);



    }*/



   /* public function networking_thumbnail_update()



    {



      $CI =& get_instance();



      // $CI->db->limit(1); 



      // $CI->db->where('id',1);



      // $CI->db->where('id',32);



      $data_news = $CI->db->get_where('news', array('id_news_category' => 52))->result_array();



      // $data_news = $CI->db->get_where('news', array('id_news_category' => 52c,'img '=>''))->result_array();



      // print_r($data_news);exit; 



       foreach ($data_news as $key => $value) {



          // print_r($id_gallery);exit;



        $arr_gallery = explode(',', $value['id_gallery']);



        $awal = 0;



        do {



          $id_gallery = $arr_gallery[$awal];



          // print_r($id_gallery);exit;



          $awal++;



          if ($awal == 3) {



             $id_gallery = '0' ;



           } 



        } while ($id_gallery == '' );



      // print_r($id_gallery);exit; 



        if ($id_gallery == '0') {



          $img = '';



        }else{



          $img = $CI->db->get_where('gallery', array('id'=>$id_gallery))->row_array()['img'];   



          



        }



      // print_r($img);exit; 



        $update_batch[] = array(



          'id'=>$value['id'],



          'img'=>$img



        );







       }



       $this->db->update_batch('news',$update_batch,'id');



       print_r($this->db->last_query());exit;



    }



*/    // function update_writer_html_char(){



    //   // replace "\u2018" to ‘ && "\u2019" to ’



    //   $newsdata  = $this->db->get_where('news','teaser like "%\u2018%" or teaser like "%\u2019%" ')->result_array() ;



    //   foreach ($newsdata as $key => $value) {







    //     $teaser  = str_replace("\u2018", "‘", $value['teaser']);



    //     $teaser  = str_replace("\u2019", "’", $teaser);



    //     $data_update[]  = array('id'=>$value['id'],



    //                           'teaser'=>$teaser);



    //   }



    //   // print_r($data_update);exit;



    //   // $this->db->update_batch('news', $data_update,'id');







    //   // $newsdata_writer  = $this->db->get_where('news','writer like "%\u2018%" or writer like "%\u2019%" ')->result_array() ;



    //   // foreach ($newsdata_writer as $key => $value) {







    //   //   $writer  = str_replace("\u2018", "‘", $value['writer']);



    //   //   $writer  = str_replace("\u2019", "’", $writer);







    //   //   $data_update_writer[]  = array('id'=>$value['id'],



    //   //                         'teaser'=>$teaser);



    //   // }



    //   // print_r($data_update_writer);exit;



    //   // $this->db->update_batch('news', $data_update_writer,'id');



    //   // print_r($this->db->last_query());exit;



    // }



    // function update_content_direct_image  (){



    //   // replace "\u2018" to ‘ && "\u2019" to ’



    // $newsdata  = $this->db->get_where('news','page_content like "%/amcham/assets/kcfinder%" ')->result_array() ;



    // foreach ($newsdata as $key => $value) {



    //   $page_content  = str_replace("/amcham/assets/kcfinder", "/assets/kcfinder", $value['page_content']);



    //   $page_content  = str_replace("\u2019", "’", $page_content);



    //   $data_update[]  = array('id'=>$value['id'],



    //                         'page_content'=>$page_content);



    // }



    // $this->db->update_batch('news', $data_update,'id');



    // print_r($data_update);exit;



    // }



    /*function update_thumbnail_gallery(){



     $CI =& get_instance();



     $array_img  = array('5b17cef536eba-img_7224-web.jpg','netwoking-1.jpg','networking.jpg');



     $CI->db->where_no_in('img', $array_img);



     $CI->db->limit('1');



     $CI->db->order('id','desc');



     $data = $CI->db->get('gallery')->result_array();







     foreach ($data as $key => $value) {



      $where['id_gallery']  = $value['id'];



      $data_image  = $CI->db->get_where('gallery_images',$where)->row_array();



      



     }



    }*/



    // function preview_file()



    // { 



    //   $uri = $this->uri->segment(3);



    //   // print_r($this->uri->segment_array());exit;



    //   $data['path']  = base_url()."viewerjs/#../file_upload/".$uri;



    //   return $this->parser->parse("layout/view_file.html",$data);



    // }



   /* function resize_thumbanil_gallery()



    {



      $defaultpath = './images/gallery/';



      $thumbnail_path = './images/gallery/thumb/';



      $treepath = dirToArray($defaultpath);







      if (!file_exists($thumbnail_path)) {



          mkdir($thumbnail_path, 0755, true);



      }



      foreach ($treepath as $key => $value) {



        // menghindari file .html



        if (!strpos($value , '.html')) {



          if (!file_exists($thumbnail_path.$value)) {



            resize_image($defaultpath.$value,



            $thumbnail_path.$value,260,170,



            100);



          }



        }



      }



      print_r($treepath);exit;



      print_r("sudah jalan");



    }*/



    // function update_member_id_migrasi()



    // {



    //  $CI =& get_instance();



    //  $CI->migrasi   = $CI->load->database('migrasi', TRUE);







    //  $CI->db->where('migrasi_id != "0"');



    //  $CI->db->select('group_concat(migrasi_id)');



    //  $data_member = $CI->db->get('auth_member')->row_array();



    //  print_r($CI->db->last_query());exit;



    //  print_r($data_member);exit;



    //  $no = 0;



    //  foreach ($data_member as $key => $value) {



    //     // unset($name_member);



    //     // unset($name_member_2);



    //     //



    //     // $name_member = $value['lastname'].', '.$value['firstname'];



    //     // $name_member_2 = $value['lastname'].' , '.$value['firstname'];



    //     unset($data_migrasi);



    //     $data_migrasi = $CI->migrasi->where('email',$value['email'])



    //                                 ->get('jos_users')->row_array();







    //     if ($data_migrasi) {



    //       $CI->db->where('id', $value['id']);



    //       $CI->db->update('auth_member', array('migrasi_id'=>$data_migrasi['id']));



    //     }







    //  }



    //     print_r($no);exit;











    //  # member update profile > 50



    //    /*



    //    $CI->migrasi->where("LENGTH(a.profile_value) >50 and (profile_key != 'rating.texture' and profile_key != 'rating.temperature'")



    //    $CI->migrasi->group_by('user_id')



    //    $user_id = $CI->migrasi->query("jos_user_profiles")->result_array();







    //    $CI->migrasi->where("LENGTH(a.profile_value) >50 and (profile_key != 'rating.texture' and profile_key != 'rating.temperature'")



    //    $data_migrasi = $CI->migrasi->query("jos_user_profiles")->result_array();



    //    */







    //  $CI->db->get('Table', limit, offset);



    // }



    // function update_uri_path_member(){



    //   $CI =& get_instance();



    //   $data = $CI->db->get('auth_member')->result_array();



    //   foreach ($data as $key => $value) {



    //     $uri_path = generate_url_member(full_name($value));



    //     $CI->db->where('id',$value['id']);



    //     $CI->db->update('auth_member', array('uri_path' =>$uri_path));



    //   }



    //   print_r('sudah selesai :)')    ;  



    // }



    // function update_uri_path_company(){



    //   $CI =& get_instance();



    //   $data = $CI->db->get('company')->result_array();



    //   foreach ($data as $key => $value) {



    //     $uri_path = generate_url_company($value['name_in']);



    //     $CI->db->where('id',$value['id']);



    //     $CI->db->update('company', array('uri_path_name_out' =>$uri_path));



    //   }



    //   print_r('sudah selesai :)')     ; 



    // }



    // function update_amcham_networking_uri()



    // {   



    //     $this->db->where_in('name', array("AmCham Networking Evening","Services Committee Breakfast Meeting","Mass Open Online Courses : Beyond Silicon Valley: Growing Entrepreneurship in Transitioning Economies","Education & Workforce Development Committee Meeting","Mass Open Online Courses : Academic and Business Writing","AmCham Thanksgiving Networking Evening","Power Committee Breakfast Meeting","The Education & Workforce Development Committee Luncheon ","UPH Executive Education program, Open Enrollment Short Courses","Indonesian Red Cross Induction Training March 2016 ","AmCham Indonesia Young Professionals Social Evening","AmCham Networking Cocktail","AmCham-OSAC Security Breakfast Meeting","Education and Workforce Development Committee Luncheon","EFFECTIVE PAs & EXECUTIVE ASSISTANTS WORKSHOP","Eurocham Indonesia Quarterly Briefing","NULL","OSAC Security Committee Meeting","Red Nose in Concert","SelectUSA Investor Summit","Services Committee Meeting","Young Professionals Social Evening","Services Committee Breakfast Meeting","3rd Sankalp Southeast Asia Summit","AmCham Annual General Meeting","AmCham Indonesia Services Committee Breakfast","AmCham Services Committee Meeting","AmCham Services Committee Meeting","Automotive & Moving Vehicle Committee Meeting","Breakfast Discussion with  Christine Brown  ","BTI Consultants - Society for Human Resource Management (SHRM) SCP Certification Preparation Programme","COFFEE CONNECTION ","Coorporate Volunteers Agenda in Jakarta","Ghouls’ Night Out","HR Committee Meeting ","International Joint Gathering in Surabaya","IT & Teleconectivity Committee Meeting ","IT & Teleconnectivity Committee Meeting","JFCC Social Event : Pool Party","Joint Chambers Networking Evening","Live Show Superbowl at The American Club","MOOC Courses @america",'MVB Event - International Seminar About Corporate Sustainability And Best Business Practises "Business 360',"OSAC Security Committee meeting: Piracy and Election Risks","Services Breakfast Meeting","Services Committee Breakfast","The 3rd Annual Responsible Business Forum on Food and Agriculture ",'The Launch of the Book "Journeys to The Heart"',"The South-East Asia Summit on 27 August 2014 in Jakarta, Indonesia","USINDO event - 2016 U.S. Presidential Election: The Electoral Process and Candidates' Prospects"));



    //     $data = $this->db->get('event')->result_array();



    //     $jml = count($data);



    //     for ($i=0; $i < $jml; $i++) { 



    //         $update[$i]['uri_path']  = generate_url($data[$i]['name']).'-'.$i;



    //         $update[$i]['id']  = $data[$i]['id'];







    //         // $this->db->update('event', array('id'=>$value['id']));



    //     }



    //     $this->db->update_batch('event', $update,'id');



    //     // print_r($this->db->last_query());exit;



    // }



    // function migrasi_writer_teaser()



    // {



    //     $CI=& get_instance();







    //     // $CI->db->limit(1);



    //     // $CI->db->where('DATE(create_date)  > DATE("2018-05-23 08:19:00")');



    //     // id_migrasi != ""  and DATE(create_date)  > DATE("2018-05-23 08:19:00")







    //     // $CI->db->where('id_migrasi != "" ');



    //     $CI->db->where('id  >',3984);



    //     $data_news = $CI->db->get('news')->result_array();











    //     // print_r($CI->db->last_query());



    //     // print_r($data_news);exit;



    //     $CI->migrasi   = $CI->load->database('migrasi', TRUE);



    //     foreach ($data_news as $key => $value) {



    //         $CI->migrasi->where('user_id', $value['id_migrasi']);



    //         $CI->migrasi->where('profile_key', 'rating.temperature');



    //         $writer = $CI->migrasi->get('jos_user_profiles')->row_array()['profile_value'];



    //         if ($writer !='""' && !empty($writer)) {



    //             $writer = str_replace('"', '', $writer);



    //             $update_news[] = array('id'=>$value['id'],'writer'=>$writer);



    //         }



    //     }







    //     foreach ($data_news as $key => $value) {



    //         $CI->migrasi->where('user_id', $value['id_migrasi']);



    //         $CI->migrasi->where('profile_key', 'rating.texture');



    //         $writer = $CI->migrasi->get('jos_user_profiles')->row_array()['profile_value'];



    //         if ($writer !='""' && !empty($writer)) {



    //             $writer = str_replace('"', '', $writer);



    //             $update_news[] = array('id'=>$value['id'],'teaser'=>$writer);



    //         }



    //     }



    //     // print_r($update_news);exit;



    //     $this->db->update_batch('news', $update_news,'id');



    //     // print_r($update_news);exit;



    //     // print_r($this->db->last_query());exit;



    // }



    // function migrasi_ulang()



    //     {



    //         // exit;



    //         $CI=& get_instance();



    //         $CI->migrasi   = $CI->load->database('migrasi_terbaru', TRUE);



    //         // $CI->migrasi->where('DATE(created)  > DATE("2018-03-13 07:11:36") and catid !=",2,"');



    //         // $CI->migrasi->limit(,1);







    //         // $CI->migrasi->where('catid REGEXP "(,[0-9]{0,3},[0-9]{0,3},)"');



    //         $CI->migrasi->where('(catid = ",161," or 



    //                               catid = ",181," or 



    //                               catid = ",401," or 



    //                               catid = ",72," or 



    //                               catid = ",757," or 



    //                               catid = ",281," or 



    //                               catid = ",421," or 



    //                               catid = ",762," or 



    //                               catid = ",763," or 



    //                               catid = ",74," or 



    //                               catid = ",221," or 



    //                               catid = ",231," or 



    //                               catid = ",241," or 



    //                               catid = ",411," or 



    //                               catid = ",8," or 



    //                               catid = ",351," or 



    //                               catid = ",79," or 



    //                               catid = ",749," or 



    //                               catid = ",301," or 



    //                               catid = ",311," or 



    //                               catid = ",321," or 



    //                               catid = ",331," or 



    //                               catid = ",341," or 



    //                               catid = ",737," or 



    //                               catid = ",742," or 



    //                               catid = ",77," or 



    //                               catid = ",78," or 



    //                               catid = ",738," or 



    //                               catid = ",748," or 



    //                               catid = ",750," or 



    //                               catid = ",751," or 



    //                               catid = ",752," or 



    //                               catid = ",754," or 



    //                               catid = ",753," or 



    //                               catid = ",84," or 



    //                               catid = ",291," or 



    //                               catid = ",759," or 



    //                               catid = ",73," or 



    //                               catid = ",381," or 



    //                               catid = ",271," or 



    //                               catid = ",211," or 



    //                               catid = ",171," or 



    //                               catid = ",151," or 



    //                               catid = ",111," or 



    //                               catid = ",86,")'); 



    //                               // and is_migrasi = 0');



    //         $CI->migrasi->where('id > 5727');



    //         // $CI->migrasi->where('id = 5726');



    //         // $CI->migrasi->limit(1);



    //         $array_id_category = array(",161,"=> "52",



    //                                    ",181,"=> "52",



    //                                    ",401,"=> "52",



    //                                    ",72,"=> "53",



    //                                    ",757,"=> "53",



    //                                    ",281,"=> "54",



    //                                    ",421,"=> "54",



    //                                    ",762,"=> "56",



    //                                    ",763,"=> "56",



    //                                    ",74,"=> "59",



    //                                    ",221,"=> "63",



    //                                    ",231,"=> "64",



    //                                    ",241,"=> "65",



    //                                    ",411,"=> "66",



    //                                    ",8,"=> "67",



    //                                    ",351,"=> "67",



    //                                    ",79,"=> "68",



    //                                    ",749,"=> "68",



    //                                    ",301,"=> "69",



    //                                    ",311,"=> "70",



    //                                    ",321,"=> "71",



    //                                    ",331,"=> "72",



    //                                    ",341,"=> "73",



    //                                    ",737,"=> "74",



    //                                    ",742,"=> "75",



    //                                    ",77,"=> "77",



    //                                    ",78,"=> "78",



    //                                    ",738,"=> "79",



    //                                    ",748,"=> "80",



    //                                    ",750,"=> "81",



    //                                    ",751,"=> "82",



    //                                    ",752,"=> "83",



    //                                    ",754,"=> "84",



    //                                    ",753,"=> "85",



    //                                    ",84,"=> "86",



    //                                    ",291,"=> "86",



    //                                    ",759,"=> "88",



    //                                    ",73,"=> "90",



    //                                    ",381,"=> "91",



    //                                    ",271,"=> "93",



    //                                    ",211,"=> "94",



    //                                    ",171,"=> "95",



    //                                    ",151,"=> "96",



    //                                    ",111,"=> "97",



    //                                    ",86,"=> "98");







    //         $data_content  = $CI->migrasi->get('jos_content')->result_array();



    //         print_r($CI->migrasi->last_query());exit;



    //         // print_r($data_content);exit;



    //         $image_status  = array();







    //         foreach ($data_content as $key => $value) {



    //             /*image migrasi*/



    //             preg_match_all('!<a href="(.*?)".*>!i', $value['introtext'], $full_content);



    //             $string = $value['introtext'];







    //             unset($image);







    //             preg_match_all('/<img[^>]+src="([^">]+)"/i', $string, $image); // get all image



    //             $string_db = $string;



    //             if ($image[1]) {



    //                 foreach ($image[1] as $key2 => $value2) {



    //                     $img[$key2]         = $value2; // path asli



    //                     $img_name[$key2]    = str_replace("%20"," ",end(explode("/", $img[$key2]))); // nama img saja 



    //                     $img_uniqename      = uniqid();



    //                     // $img_replace[$key2] = "images/article/large/".str_replace("%20"," ",$img_name[$key2]); // path update image large



    //                     if ($key2 == 0 ) {



    //                          // image pertama







    //                         /*upload image ke article small*/



    //                         $target_path    = UPLOAD_DIR.'../../images/article/small/';



    //                         $target_path_large    = UPLOAD_DIR.'../../images/article/large/';



    //                         $sourcefile     = UPLOAD_DIR.'../../'.$img[$key2];



    //                         //hilangin %20



                            



    //                         // image save                            



    //                         $thumb          = $img_uniqename .'_'.pathinfo($sourcefile, PATHINFO_BASENAME);   



    //                         $sourcefile     = str_replace('%20', ' ', $sourcefile);



    //                         $upload_image_small[] = array(



    //                             'awal' => $sourcefile,



    //                             'akhir' =>  $target_path . $thumb,



    //                             'large' =>  $target_path_large . $thumb,



    //                             'id' => $value['id']



    //                         );



    //                     }



    //                 }



    //             }



    //             /*end image migrasi*/







    //             // remove {} di content



    //             preg_match_all('/{phocagallery.*categoryid=(.*)(\|.*)}/i', $string, $image_bracet);



    //             if ($image_bracet) {



                    



    //                 if ($value['catid'] == ",191,") {



    //                     // kalo annual golf







    //                     preg_match("/(.*){/is",$string,$var_top_gallery);



    //                     $string                  = str_replace( $var_top_gallery[1], "", $string);



    //                     $sponsor_gallery_content = $var_top_gallery[1]; // top img_gallery for annual report



    //                 }else{



    //                     $top_gallery_content = "";



    //                 }







    //                 if (count($image_bracet[1]) == 1) {



    //                     # preg_match("/[0-9]{0,}/i", $image_bracet[1][0], $images_id);



    //                 }else{



    //                     // preg_match_all("/[0-9]{0,}/i", $image_bracet[1][], $images_id);



    //                     $images_id = implode(',',$images_id);







    //                 }



    //                 $img_gallery_id = $images_id; // get img_gallery_id



    //                 $introtext      = str_replace($image_bracet[0], "", $string); // remove {}



    //             }else{



    //                 $introtext = $value['introtext'];



    //             }



    //             // end remove {} di content







    //             // dapetin category



    //             $arr_id_category =  array_values(array_filter(explode(',',$value['catid'])));







    //             if (count($arr_id_category)  == 1) {



    //                 $id_category = $arr_id_category[0];



    //             }else{



    //                 if (in_array("401",$arr_id_category)) {



    //                     $id_category = '52';// id category yang di simpen







    //                 }else if (in_array("221",$arr_id_category)) { // kalo 221 (featured)



    //                     $id_category = '63';// id category yang di simpen



    //                     if (in_array("421",$arr_id_category)) { // interview



                            



    //                         $id_category = '13';// id category yang di simpen



    //                         $menu_news[] = array(



    //                             'id_news'=>$value['id'],



    //                             'id_menu' => 179,



    //                             'create_date'=> date('Y-m-d')



    //                         );



    //                     }







    //                 }else{



    //                      $id_category = $arr_id_category[0];                



    //                 }



    //             }



    //             // end dapetin category



    //                 $insert[]    = array(



    //                          "publish_date"        => $value["publish_up"],



    //                          "user_id_create"      => $value["created_by"],



    //                          "create_date"         => $value["created"],



    //                          "modify_date"         => $value["modified"],



    //                          "user_id_modify"      => $value["modified_by"],



    //                          "id_news_category"    => $id_category,



    //                          "id_migrasi_category" => $value["catid"],



    //                          "id_migrasi"          => $value["id"],



    //                          "img"                 => $thumb,



    //                          "sponsor"             => $sponsor_gallery_content,



    //                          // "teaser"              => $value["introtext"],



    //                          "page_content"        => $introtext,



    //                          "id_gallery"          => $img_gallery_id,



    //                          "uri_path"            => $value["alias"],



    //                          "news_title"          => $value["title"],



    //                          "id_lang"             => 1,



    //                          "approval_level"      => 100,



    //                          "id_status_publish"   => 2



    //                         );



    //         }



    //         /*upload image thumbnail*/







    //         // foreach ($upload_image_small as $key => $value) {



    //         //     copy($value['awal'], $value['large']);



    //         //     if(copy($value['awal'], $value['akhir'])) {



    //         //         // $image_status[] = array('status'=>1,'id'=>$value['id']);



    //         //     } else{



    //         //         // $image_status[] = array('status'=>0,'id'=>$value['id']);



    //         //         print_r(array('id'=>$value['id'],'status'=>'gagal'));



    //         //     }



    //         // }



    //         print_r($insert);exit;



    //         $CI->db->insert_batch('news',$insert);



    //         print_r('<br/> query news </br>');



    //         print_r($CI->db->last_query());







    //         // print_r($menu_news);



    //         /*foreach ($menu_news as $key => $value) {



    //             $id_news = $this->db->get_where('news',array('id_migrasi' =>$value['id_news']))->row_array()['id'];



    //             $menu_news[$key]['id_news'] = $id_news;



    //         }*/



    //         // print_r();



    //         // iso_date_custom_format()



    //         /*$this->db->insert_batch('news_menu_tags',$menu_news);



    //         print_r('<br/> query menu </br>');



    //         print_r($this->db->last_query());*/



    //         // print_r($image_status);            







    //         // print_r($upload_image_small);



    //         // print_r($menu_news);



    //         // print_r($insert);exit;   



    //         // $this->migrasi->where('id', $value['id']);



    //         // $this->migrasi->update('jos_content', $data);



    //     }







        // function migrasi_normalisasi_data()



        // {



        //     exit;



        //     $CI=& get_instance();



        //     $CI->migrasi   = $CI->load->database('migrasi', TRUE);







        //     $this->db->where('id_migrasi != "" ');



        //     $data_news = $this->db->get('news')->result_array();



        //     foreach ($data_news as $key => $value) {



        //         $CI->migrasi->where('id', $value['id_migrasi']);



        //         $data_migrasi = $CI->migrasi->get('jos_content')->row_array();



        //         if (!empty($data_migrasi)) {



        //             $string = $data_migrasi['introtext_old'];



        //             preg_match_all('/{phocagallery.*categoryid=(.*)(\|.*)}/i', $string, $image_bracet);



        //             if ($image_bracet) {



        //                 if ($data_migrasi['catid'] == ",191,") {



        //                     // kalo annual golf







        //                     preg_match("/(.*){/is",$string,$var_top_gallery);



        //                     $string                  = str_replace( $var_top_gallery[1], "", $string);



        //                     $sponsor_gallery_content = $var_top_gallery[1]; // top img_gallery for annual report



        //                 }else{



        //                     $top_gallery_content = "";



        //                 }







        //                 if (count($image_bracet[1]) == 1) {



        //                     # preg_match("/[0-9]{0,}/i", $image_bracet[1][0], $images_id);



        //                 }else{



        //                     // preg_match_all("/[0-9]{0,}/i", $image_bracet[1][], $images_id);



        //                     $images_id = implode(',',$images_id);







        //                 }



        //                 $img_gallery_id = $images_id; // get img_gallery_id



        //                 $introtext      = str_replace($image_bracet[0], "", $string); // remove {}



        //             }else{



        //                 $introtext = $data['introtext'];



        //             }



        //             $update_news[]    = array(



        //                      "img"                 => $thumb,



        //                      "sponsor"             => $sponsor_gallery_content,



        //                      "page_content"        => $introtext,



        //                      "id_gallery"          => $img_gallery_id,



        //                      "id"                 => $value['id']



        //                     );



        //         }



        //     }



        //     print_r($update_news);exit;



        //     $this->db->update_batch('news', $update_news,'id');



        // }



    // function migrasi_kita_newletter_kita_terbaru()



    //     {



    //         $CI=& get_instance();



    //         $CI->migrasi = $CI->load->database('migrasi_terbaru', TRUE);



    //         $CI->migrasi->where('DATE(created)  > DATE("2018-05-23 08:19:00") and catid !=",2,"');



    //         $data_content  = $CI->migrasi->get('jos_content')->result_array();



    //         // print_r($data_content);exit;     







    //         foreach ($data_content as $key => $value) {



    //             // dapetin image



    //             preg_match_all('!<a href="(.*?)".*>!i', $value['introtext'], $full_content);



    //             $string = $value['introtext'];



    //             unset($image);



    //             preg_match_all('/<img[^>]+src="([^">]+)"/i', $string, $image); // get all image



    //             $string_db = $string;



    //             if ($image[1]) {



    //                 foreach ($image[1] as $key2 => $value2) {



    //                     $img[$key2]         = $value2;



    //                     $img_name[$key2]    = str_replace("%20"," ",end(explode("/", $img[$key2])));



    //                     $img_replace[$key2] = "images/article/large/".str_replace("%20"," ",$img_name[$key2]);                



    //                     if ($key2 == 0 ) {



    //                          // image pertama



    //                         $thumb  = $img_name[$key2];                    



    //                     }



    //                 }



    //                 $data = array(



    //                             'thumb' => $thumb, // first img



    //                             'img' => implode("|",$img)."|" // implode all image



    //                         );



    //             }



    //             // end dapetin image







    //             // remove {} di content                







    //             /*source update ke content migrasi*/



    //             /*preg_match('/({phocagallery.*categoryid=(.*)(\|.*)})/i', $string, $image_bracet);



    //             if ($image_bracet) {



    //                 $last = strpos($image_bracet[2],"|");



    //                 $var  = substr($image_bracet[2],0,$last);



    //                 if ($value['catid'] == ",191,") {



    //                     // kalo annual golf







    //                     preg_match("/(.*){/is",$string,$var_top_gallery);



    //                     $string  = str_replace( $var_top_gallery[1], "", $string);



    //                     $data2['sponsor_gallery_content'] = $var_top_gallery[1]; // top img_gallery for annual report



    //                 }else{



    //                     $data2['top_gallery_content'] = "";



    //                 }



    //                 $data2['img_gallery_id'] = $var; // get img_gallery_id



    //                 $data2['introtext']      = str_replace($image_bracet[0], "", $string); // remove {}







    //                 $this->migrasi->where('id', $value['id']);



    //                 $this->migrasi->update('jos_content', $data2);



    //             }*/







    //             /*custom langsung ke table news */



    //             preg_match('/({phocagallery.*categoryid=(.*)(\|.*)})/i', $string, $image_bracet);



    //             if ($image_bracet) {



    //                 $last = strpos($image_bracet[2],"|");



    //                 $var  = substr($image_bracet[2],0,$last);



    //                 if ($value['catid'] == ",191,") {



    //                     // kalo annual golf







    //                     preg_match("/(.*){/is",$string,$var_top_gallery);



    //                     $string                  = str_replace( $var_top_gallery[1], "", $string);



    //                     $sponsor_gallery_content = $var_top_gallery[1]; // top img_gallery for annual report



    //                 }else{



    //                     $top_gallery_content = "";



    //                 }



    //                 $img_gallery_id = $var; // get img_gallery_id



    //                 $introtext      = str_replace($image_bracet[0], "", $string); // remove {}



    //             }else{



    //                 $introtext = $data['introtext'];



    //             }



    //             //end remove {} di content







    //             // dapetin category



    //             $arr_id_category =  array_values(array_filter(explode(',',",221,421,749,")));







    //             if (count($arr_id_category)  == 1) {



    //                 $id_category = $arr_id_category[0];



    //             }else{



    //                 if (in_array("221",$arr_id_category)) {







    //                     foreach($arr_id_category as $key3 =>$value3)



    //                     {



    //                         if ($value == '221') {



    //                             $mykey = $key3;



    //                         }



    //                     }



    //                     unset($arr_id_category[$mykey]);



    //                     $arr_id_category = array_values($arr_id_category);



    //                 }



    //                 $id_category = $arr_id_category[0];



    //             }



    //             // end dapetin category



    //                 $insert[]    = array(



    //                          "publish_date"        => $value["publish_up"],



    //                          "user_id_create"      => $value["created_by"],



    //                          "create_date"         => $value["created"],



    //                          "modify_date"         => $value["modified"],



    //                          "user_id_modify"      => $value["modified_by"],



    //                          "id_news_category"    => $id_category,



    //                          "id_migrasi_category" => $value["catid"],



    //                          "id_migrasi"          => $value["id"],



    //                          "img"                 => $thumb,



    //                          "sponsor"             => $sponsor_gallery_content,



    //                          // "teaser"              => $value["introtext"],



    //                          "page_content"        => $introtext,



    //                          "id_gallery"          => $img_gallery_id,



    //                          "uri_path"            => $value["alias"],



    //                          "news_title"          => $value["title"],



    //                          "id_lang"             => 1,



    //                          "approval_level"      => 100,



    //                          "id_status_publish"   => 2



    //                         );



    //         }



    //         // print_r($insert);exit;



    //         // $this->migrasi->where('id', $value['id']);



    //         // $this->migrasi->update('jos_content', $data);



    //         $this->db->insert_batch('news', $insert);



    //         print_r($this->db->last_query());exit;



    //     }







    // function remove_jos_content_phocagallery_tags(){



    //     //     exit;



    //     $CI=& get_instance();



    //     $CI->migrasi  = $CI->load->database('migrasi', TRUE);



    //     $CI->migrasi->where('introtext_old like "%{%"');



    //     $dataaa = $CI->migrasi->get('jos_content')->result_array();



    //     // print_r($dataaa);exit;



    //     foreach ($dataaa as $key => $value) {



    //         $string = $value['introtext_old'];



    //         $data2[$key]['into'] = $value['introtext_old'];



    //         $data2[$key]['into_baru'] = $value['introtext'];



    //         preg_match('/({phocagallery.*categoryid=(.*)(\|.*)})/i', $string, $image_bracet);



    //         // print_r($value);exit;



    //         if ($image_bracet) {



    //             $last = strpos($image_bracet[2],"|");



    //             $var  = substr($image_bracet[2],0,$last);



    //             if ($value['catid'] == ",191,") {



    //                 preg_match("/(.*){/is",$string,$var_top_gallery);



    //                 $string  = str_replace( $var_top_gallery[1], "", $string);







    //                 $data2[$key]['sponsor_gallery_content'] = $var_top_gallery[1]; // top img_gallery for annual report



    //             }else{



    //                 $data2[$key]['top_gallery_content'] = "";



    //             }



    //             $data2[$key]['img_gallery_id'] = $var; // get img_gallery_id



    //             $data2[$key]['introtext']      = str_replace($image_bracet[0], "", $string); // remove {}



    //             $data2[$key]['check'] = strpos($data2[$key]['introtext'], '{');



    //             $data2[$key]['id'] = $value['id'];



    //             // print_r(1);exit;   



    //         }



    //     }



    //         print_r($data2);exit;



    //         $CI->migrasi->update_batch('jos_content', $data2, 'id');



    // }



    /*function migrasi_other_report()



    {



        exit;



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi_terbaru', TRUE);



        $this->migrasi->where('id !=',5727);



        $data = $this->migrasi->get_where('jos_content',array('catid' => ',74,'))->result_array();



        foreach ($data as $key => $value) {



            $insert[]    = array(



                             "publish_date"        => $value["publish_up"],



                             "user_id_create"      => $value["created_by"],



                             "create_date"         => $value["created"],



                             "modify_date"         => $value["modified"],



                             "user_id_modify"      => $value["modified_by"],



                             "id_news_category"    => '59',



                             "id_migrasi_category" => $value["catid"],



                             "id_migrasi"          => $value["id"],



                             "img"                 => $value["thumb"],



                             "id_gallery"          => $value["img_gallery_id"],



                             "sponsor"             => $value["sponsor_gallery_content"],



                             "teaser"              => $value["introtext"],



                             "page_content"        => $value["fulltext"],



                             "uri_path"            => $value["alias"],



                             "news_title"          => $value["title"],



                             "id_lang"             => 1,



                             "approval_level"      => 100,



                             "id_status_publish"   => 2



                            );



        }



        // $this->db->update_batch('news',$insert,'id_migrasi');



        $this->db->insert_batch('news',$insert);







        print_r($this->db->last_query());



    }*/



/*    function update_content_remove_img_duplicate(){    



        // remove gambar di content yang duplicate dengan



        $this->db->where('img !="" ');



        $data  = $this->db->get('news')->result_array();



        foreach ($data as $key => $value) {



            preg_match_all("!<p>.*!", $value['page_content'], $tag_p);



            // print_r($tag_p);exit;



            if (preg_match("!(<img.*\/>)!", $tag_p[0][0], $check_image)) {



                // kalo ada image awal



                $new_p = preg_replace("(<img.*\/>)", "", $tag_p[0][0]);



                // print_r($value['page_content']);



                // print_r($tag_p[0][0]);



                // print_r($new_p);



                // exit;



                // str_replace(search, replace, subject)



                $new_paragraf = str_replace($tag_p[0][0],$new_p,$value['page_content']);   



                $new_content[] = array('page_content'=>$new_paragraf,'id'=>$value['id']);



            }



            // print_r($check_image);exit;



        }



        // print_r($new_content);exit;



        $this->db->update_batch('news', $new_content,'id');



        print('a');exit;



    }*/



    // function migrasi_newsletter(){



    //     exit;



    //     $CI=& get_instance();



    //     $CI->migrasi = $CI->load->database('migrasi_terbaru', TRUE);



    //     $CI->migrasi->where('tempid !=', 0);



    //     $CI->migrasi->where('tempid !=', 1);



    //     $CI->migrasi->where('mailid >', 254);



    //     $data = $CI->migrasi->get('jos_acymailing_mail')->result_array();



    //     foreach ($data as $key => $value) {



    //         $check_mail = strpos($value['body'],  'Read more this Newsletter in your browser');



    //         if ($check_mail) {



    //             unset($mailchip);



    //             preg_match('!href=["\']?([^"\'>]+)["\']?!', $value['body'], $mailchip);



    //             $mailchip = $mailchip[1];;



    //         }else{



    //             $mailchip = "";



    //         }



    //         preg_match('/([0-9]{0,}\/[0-9]{0,}\/[0-9]{0,}):?+/', $value['subject'], $publish_date);



    //         if (!empty($publish_date)) {



    //             $value['subject'] = str_replace($publish_date[0],'',$value['subject']);



    //             $myDateTime = DateTime::createFromFormat('d/m/Y', $publish_date[1]);



    //             $date_create = $myDateTime->format('Y-m-d');



    //         }else{



    //             $date_create =  date('Y-m-d', $value['created']);



    //         }



    //         // unset($publish_date);







    //         $value['published'] = $value['published'] ==0 ? 1:2;







    //         $insert[]    = array('news_title'      => $value['subject'],



    //                             'page_content'     => $value['body'],



    //                             'uri_path'         => $value['alias'],



    //                             'create_date'      => $date_create,



    //                             'id_news_category' => 54,



    //                             'id_status_publish'=> $value['published'],



    //                             'publish_date'     => $date_create,



    //                             'approval_level'   => 100,



    //                             'mailchimp'        => $mailchip,



    //                             'id_lang'          => 1,



    //                             'id_migrasi'       => 'newsletter_'.$value['mailid']



    //                         );



    //     }



    //     // print_r($insert);exit;



    //     $CI->db->insert_batch('news',$insert);







    //     // print_r('ok');



    //     print_r($CI->db->last_query());



    // }



/*    function migrasi_check_user(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        $data = $this->migrasi->get('migrasi_user_individual')->result_array();



        foreach ($data as $key => $value) {



            $this->migrasi->like('name', $value['name'], 'BOTH');



            $this->migrasi->where('id !='.$value['id']);



            $check = $this->migrasi->get('migrasi_user_individual')->result_array();   



            if (!empty($check)) {



                $nama_duplicate[] = $value['name'];



            }



        }



        print_r($nama_duplicate);



    }*/



    // function migrasi_user_individual(){



    //     exit;



    //     $CI=& get_instance();



    //     $CI->migrasi = $CI->load->database('migrasi', TRUE);



    //     // $this->migrasi->where('member_code not like "%cam%" ');



        



    //     /* corporate */



    //     // $this->migrasi->where('member_code like',"COR%");



    //     /* corporate child */



    //     // $this->migrasi->where("company_name != 'BLACK & VEATCH INTERNATIONAL COMPANY'");



    //     // $this->migrasi->where("company_name != 'BTI CONSULTANTS '");



    //     // $this->migrasi->where("company_name != 'CARGILL TRADING INDONESIA, PT.'");



    //     // $this->migrasi->where("company_name != 'CATERPILLAR FINANCE INDONESIA, PT'");



    //     // $this->migrasi->where("company_name != 'DANA PENSIUN FREEPORT INDONESIA'");



    //     // $this->migrasi->where("company_name != 'JW MARRIOTT HOTEL JAKARTA'");



    //     // $this->migrasi->where("company_name != 'KPMG ADVISORY INDONESIA'");



    //     // $this->migrasi->where("company_name != 'MARSH INDONESIA, PT.'");



    //     // $this->migrasi->where("company_name != 'SHERATON GRAND JAKARTA GANDARIA CITY HOTEL'");



    //     // $this->migrasi->where("company_name != 'SOLAR SERVICES INDONESIA, PT.'");



    //     // $this->migrasi->where("company_name != 'SORINI AGRO ASIA, PT.'");



    //     // $this->migrasi->where("company_name != 'CATERPILLAR INDONESIA - BATAM, PT.'");



    //     // $this->migrasi->where("member_code != 'CAM0016'");



    //     // $this->migrasi->where("member_code != 'CAM0006'");



    //     // $this->migrasi->where("member_code != 'CAM0068'");



    //     $data_awal = $this->db->get('auth_member')->result_array();



    //     foreach ($data_awal as $key => $value) {



    //         $id_corporate_representatif[] = $value['id'];



    //     }



            



    //     $this->migrasi->where_not_in('id', $id_corporate_representatif);



    //     $data = $this->migrasi->get('migrasi_user_individual')->result_array();   



    //     // // print_r(  $data);



    //     // exit;







    //     foreach ($data as $key => $value) {



            



    //             // /* company */



    //             $data_company['name_in']       = $value['company_name'] ;



    //             $data_company['name_out']      = $value['company_name'] ;



    //             $data_company['address']       = $value['alamat'] ;



    //             $data_company['website']       = $value['website'] ;







    //             $data_company_old              = $this->db->get_where('company_old',array('name_in'=>$value['company_name']) )->row_array();







    //             // ambil_dari datacompany migrasi lama 



    //             $data_company['t_number']      = $data_company_old['t_number'] ? $data_company_old['t_number'] : "";



    //             $data_company['m_number']      = $data_company_old['m_number'] ? $data_company_old['m_number'] : "";



    //             $data_company['description']   = $data_company_old['description'] ? $data_company_old['description'] : "";



    //             $data_company['img']           = $data_company_old['img'] ? $data_company_old['img'] : "";



    //             $data_company['create_date']   = $data_company_old['create_date'] ? $data_company_old['create_date'] : "";



    //             $data_company['date_modified'] = $data_company_old['date_modified'] ? $data_company_old['date_modified'] : "";



    //             $data_company['modify_date']   = $data_company_old['modify_date'] ? $data_company_old['modify_date'] : "";







    //             $this->db->insert('company', $data_company);



    //             $id_company               = $this->db->insert_id();



                







    //             // /* Sector */



    //             $data_sector                 = array_filter(explode(";", $value['sector']));



    //             $id_sector                   = array();







    //             foreach ($data_sector as $value2) {



    //                 $id_sector_db = db_get_one('sector','id',array('name'=>$value2));



    //                 $id_sector[]  = array('sector_id'=>$id_sector_db,'company_id'=>$id_company);



    //             }







    //             if (!empty($id_sector)) {



    //             // insert per company



    //                 $this->db->insert_batch('auth_member_sector', $id_sector);



    //             }



    //             /* End Sector */







    //             /* end company */







    //             /* user */



    //             $data_user['id']                        = $value['id'] ;



    //             $user_name                              = explode(', ', $value['name']);



    //             $data_user['firstname']                 = $user_name[0] ;



    //             $data_user['lastname']                  = $user_name[1] ?$user_name[1] :"";



    //             $data_user['job']                       = $value['job'] ?$value['job'] :"";



    //             $data_user['citizenship']               = $value['country'] ?$value['country'] :"";



    //             $data_user['company_id']                = $id_company ;



    //             $data_user['email']                     = $value['email'] ?$value['email']:"";



    //             $data_user['password']                  = md5($value['member_code']) ;



    //             $data_user['membership_information_id'] = $value['id'];



    //             $data_user_old                          = $this->db->get_where('auth_member_old',array('firstname'=>$user_name[0],'lastname'=>$user_name[1]))->row_array();







    //             $data_user['m_m_number']                = empty($data_user_old['m_m_number']) ? "":$data_user_old['m_m_number'];



    //             $data_user['m_t_number']                = empty($data_user_old['m_t_number'])? "": $data_user_old['m_t_number'] ;



    //             $data_user['create_date']               = empty($data_user_old['create_date'])?"":$data_user_old['create_date'] ;



    //             $data_user['modify_date']               = empty($data_user_old['modify_date'])?"":$data_user_old['modify_date'] ;



    //             $data_user['member_category_id']        = 2 ; // company 1 individu 2 representatif 3



    //             // $data_user['member_category_id']        = 3 ; // company 1 individu 2 representatif 3



    //             $data_user['status_payment_id']         = 1 ;







    //             $this->db->insert('auth_member', $data_user);



    //             $id_user               = $this->db->insert_id();



    //             /* end user */



            



    //             /* membership information */



    //             $data_membership["id"]                    = $id_user;



    //             $data_membership["member_id"]             = $id_user;



    //             $data_membership["company_id"]            = $id_company ;



    //             $data_membership["membership_code"]       = $value['member_code'] ;



    //             // ambil dari lama                        



    //             $data_membership_old                      = $this->db->get_where('membership_information_old',array('member_id'=>$data_user_old['id']))->row_array();



    //             $data_membership["expired_date"]          = date('Y-12-31') ;



    //             $data_membership["registered_date"]       = empty($data_membership_old['registered_date'])?date('Y-m-d'):$data_membership_old['registered_date'] ;



    //             $data_membership["last_visited_date"]     = $data_membership_old['last_visited_date'] ;



    //             $data_membership["first_registered_date"] = $data_membership_old['first_registered_date'] ;



    //             $this->db->insert('membership_information', $data_membership);







    //             /* end membership information */







    //                 /*[company_name] => AIG INSURANCE INDONESIA, PT.



    //                 [alamat] => Indonesia Stock Exchange Bldg., Tower 2, 3A Floor



    //             Jl. Jend. Sudirman Kav 52-53



    //             Jakarta - 12190



    //             [website] => www.aig.co.id



                



    //             [name] => Morris, Michael J.



    //             [job] => Finance Director



    //             [email] => Mick.morris@aig.com



    //             [country] => Ireland







    //             [member_code] => CAM0053







    //             [sector] => Banking, Investment, Financial Services;Insurance, Insurance Brokers*/



    //         }



        



    //         print_r("selesai");



    // }







    // function migrasi_user_company_representatif(){



    //     exit;



    //     $CI=& get_instance();



    //     $CI->migrasi = $CI->load->database('migrasi', TRUE);



    //     $this->migrasi->like('member_code',"cam", 'BOTH');



        



    //     /* corporate */



    //     // $this->migrasi->where('member_code like',"COR%");



    //     /* corporate child */



    //     $this->migrasi->where("company_name != 'BLACK & VEATCH INTERNATIONAL COMPANY'");



    //     $this->migrasi->where("company_name != 'BTI CONSULTANTS '");



    //     $this->migrasi->where("company_name != 'CARGILL TRADING INDONESIA, PT.'");



    //     $this->migrasi->where("company_name != 'CATERPILLAR FINANCE INDONESIA, PT'");



    //     $this->migrasi->where("company_name != 'DANA PENSIUN FREEPORT INDONESIA'");



    //     $this->migrasi->where("company_name != 'JW MARRIOTT HOTEL JAKARTA'");



    //     $this->migrasi->where("company_name != 'KPMG ADVISORY INDONESIA'");



    //     $this->migrasi->where("company_name != 'MARSH INDONESIA, PT.'");



    //     $this->migrasi->where("company_name != 'SHERATON GRAND JAKARTA GANDARIA CITY HOTEL'");



    //     $this->migrasi->where("company_name != 'SOLAR SERVICES INDONESIA, PT.'");



    //     $this->migrasi->where("company_name != 'SORINI AGRO ASIA, PT.'");



    //     $this->migrasi->where("company_name != 'CATERPILLAR INDONESIA - BATAM, PT.'");



    //     $this->migrasi->where("member_code != 'CAM0016'");



    //     $this->migrasi->where("member_code != 'CAM0006'");



    //     $this->migrasi->where("member_code != 'CAM0068'");







    //     $data = $this->migrasi->get('migrasi_user_individual')->result_array();



    //     foreach ($data as $key => $value) {



    //         $check_user = $this->db->get_where('auth_member', array('id'=>$value['id']))->result_array();



    //         if (empty(array_filter($check_user))) {



    //             // /* company */



    //             // $data_company['id']            = $value['id'] ;



    //             // $data_company['name_in']       = $value['company_name'] ;



    //             // $data_company['name_out']      = $value['company_name'] ;



    //             // $data_company['address']       = $value['alamat'] ;



    //             // $data_company['website']       = $value['website'] ;







    //             // $data_company_old              = $this->db->get_where('company_old',array('name_in'=>$value['company_name']) )->row_array();







    //             // // ambil_dari datacompany migrasi lama 



    //             // $data_company['t_number']      = $data_company_old['t_number'] ? $data_company_old['t_number'] : "";



    //             // $data_company['m_number']      = $data_company_old['m_number'] ? $data_company_old['m_number'] : "";



    //             // $data_company['description']   = $data_company_old['description'] ? $data_company_old['description'] : "";



    //             // $data_company['img']           = $data_company_old['img'] ? $data_company_old['img'] : "";



    //             // $data_company['create_date']   = $data_company_old['create_date'] ? $data_company_old['create_date'] : "";



    //             // $data_company['date_modified'] = $data_company_old['date_modified'] ? $data_company_old['date_modified'] : "";



    //             // $data_company['modify_date']   = $data_company_old['modify_date'] ? $data_company_old['modify_date'] : "";







    //             // $this->db->insert('company', $data_company);



    //             // $id_company               = $this->db->insert_id();



    //             // // $id_company               = $this->db->insert_id();







    //             // /* Sector */



    //             // $data_sector                 = array_filter(explode(";", $value['sector']));



    //             // $id_sector                   = array();







    //             // foreach ($data_sector as $value2) {



    //             //     $id_sector_db = db_get_one('sector','id',array('name'=>$value2));



    //             //     $id_sector[]  = array('sector_id'=>$id_sector_db,'company_id'=>$id_company);



    //             // }







    //             // if (!empty($id_sector)) {



    //             // // insert per company



    //             //     $this->db->insert_batch('auth_member_sector', $id_sector);



    //             // }



    //              End Sector 







    //             /* end company */







    //             /* user */



    //             $id_company = $this->db->get_where('company',array('name_in'=>$value['company_name']))->row_array()['id'];



    //             if (!$id_company) {



    //                 print($value['company_name']);



    //                 exit;



    //             }



    //             $data_user['id']                        = $value['id'] ;



    //             $user_name                              = explode(', ', $value['name']);



    //             $data_user['firstname']                 = $user_name[0] ;



    //             $data_user['lastname']                  = $user_name[1] ?$user_name[1] :"";



    //             $data_user['job']                       = $value['job'] ?$value['job'] :"";



    //             $data_user['citizenship']               = $value['country'] ?$value['country'] :"";



    //             $data_user['company_id']                = $id_company ;



    //             $data_user['email']                     = $value['email'] ?$value['email']:"";



    //             $data_user['password']                  = md5($value['member_code']) ;



    //             $data_user['membership_information_id'] = $value['id'];



    //             $data_user_old                          = $this->db->get_where('auth_member_old',array('firstname'=>$user_name[0],'lastname'=>$user_name[1]))->row_array();







    //             $data_user['m_m_number']                = empty($data_user_old['m_m_number']) ? "":$data_user_old['m_m_number'];



    //             $data_user['m_t_number']                = empty($data_user_old['m_t_number'])? "": $data_user_old['m_t_number'] ;



    //             $data_user['create_date']               = empty($data_user_old['create_date'])?"":$data_user_old['create_date'] ;



    //             $data_user['modify_date']               = empty($data_user_old['modify_date'])?"":$data_user_old['modify_date'] ;



    //             $data_user['member_category_id']        = 3 ; // company 1 individu 2 representatif 3



    //             // $data_user['member_category_id']        = 3 ; // company 1 individu 2 representatif 3



    //             $data_user['status_payment_id']         = 1 ;







    //             $this->db->insert('auth_member', $data_user);



    //             $id_user               = $this->db->insert_id();



    //             /* end user */



            



    //             /* membership information */



    //             $data_membership["id"]                    = $id_user;



    //             $data_membership["member_id"]             = $id_user;



    //             $data_membership["company_id"]            = $id_company ;



    //             $data_membership["membership_code"]       = $value['member_code'] ;



    //             // ambil dari lama                        



    //             $data_membership_old                      = $this->db->get_where('membership_information_old',array('member_id'=>$data_user_old['id']))->row_array();



    //             $data_membership["expired_date"]          = date('Y-12-31') ;



    //             $data_membership["registered_date"]       = empty($data_membership_old['registered_date'])?date('Y-m-d'):$data_membership_old['registered_date'] ;



    //             $data_membership["last_visited_date"]     = $data_membership_old['last_visited_date'] ;



    //             $data_membership["first_registered_date"] = $data_membership_old['first_registered_date'] ;



    //             $this->db->insert('membership_information', $data_membership);







    //             /* end membership information */







    //                 /*[company_name] => AIG INSURANCE INDONESIA, PT.



    //                 [alamat] => Indonesia Stock Exchange Bldg., Tower 2, 3A Floor



    //             Jl. Jend. Sudirman Kav 52-53



    //             Jakarta - 12190



    //             [website] => www.aig.co.id



                



    //             [name] => Morris, Michael J.



    //             [job] => Finance Director



    //             [email] => Mick.morris@aig.com



    //             [country] => Ireland







    //             [member_code] => CAM0053







    //             [sector] => Banking, Investment, Financial Services;Insurance, Insurance Brokers*/



    //         }



    //     }



    //         print("selesai");



    // }







    // function migrasi_user_company(){



    //     exit;



    //     $CI=& get_instance();



    //     $CI->migrasi = $CI->load->database('migrasi', TRUE);



    //     // $this->migrasi->or_like('member_code',"cam", 'BOTH');



    //     /* corporate */



    //     // $this->migrasi->where('member_code like',"COR%");



    //     /* corporate */



    //     // $this->migrasi->where('member_code like',"COR%");



    //     /* corporate child */







    //     // $this->migrasi->where("company_name","BLACK & VEATCH INTERNATIONAL COMPANY");



    //     $this->migrasi->where("company_name","BTI CONSULTANTS ");



    //     // $this->migrasi->or_where("company_name","CARGILL TRADING INDONESIA, PT.");



    //     // $this->migrasi->or_where("company_name","CATERPILLAR FINANCE INDONESIA, PT");



    //     // $this->migrasi->or_where("company_name","DANA PENSIUN FREEPORT INDONESIA");



    //     // $this->migrasi->or_where("company_name","JW MARRIOTT HOTEL JAKARTA");



    //     // $this->migrasi->or_where("company_name","KPMG ADVISORY INDONESIA");



    //     // $this->migrasi->or_where("company_name","MARSH INDONESIA, PT.");



    //     // $this->migrasi->or_where("company_name","SHERATON GRAND JAKARTA GANDARIA CITY HOTEL");



    //     // $this->migrasi->or_where("company_name","SOLAR SERVICES INDONESIA, PT.");



    //     // $this->migrasi->or_where("company_name","SORINI AGRO ASIA, PT.");



    //     // $this->migrasi->or_where("company_name","CATERPILLAR INDONESIA - BATAM, PT.");



    //     // $this->migrasi->or_where("member_code","CAM0016");



    //     // $this->migrasi->or_where("member_code","CAM0006");



    //     // $this->migrasi->or_where("member_code","CAM0068");







    //     $data = $this->migrasi->get('migrasi_user_individual')->result_array();



    //     foreach ($data as $key => $value) {



    //         $check_company = $this->db->get_where('company', array('id'=>$value['id']))->result_array();



    //         if (empty(array_filter($check_company))) {



    //             /* company */



    //             $data_company['id']            = $value['id'] ;



    //             $data_company['name_in']       = $value['company_name'] ;



    //             $data_company['name_out']      = $value['company_name'] ;



    //             $data_company['address']       = $value['alamat'] ;



    //             $data_company['website']       = $value['website'] ;







    //             $data_company_old              = $this->db->get_where('company_old',array('name_in'=>$value['company_name']) )->row_array();







    //             // ambil_dari datacompany migrasi lama 



    //             $data_company['t_number']      = $data_company_old['t_number'] ? $data_company_old['t_number'] : "";



    //             $data_company['m_number']      = $data_company_old['m_number'] ? $data_company_old['m_number'] : "";



    //             $data_company['description']   = $data_company_old['description'] ? $data_company_old['description'] : "";



    //             $data_company['img']           = $data_company_old['img'] ? $data_company_old['img'] : "";



    //             $data_company['create_date']   = $data_company_old['create_date'] ? $data_company_old['create_date'] : "";



    //             $data_company['date_modified'] = $data_company_old['date_modified'] ? $data_company_old['date_modified'] : "";



    //             $data_company['modify_date']   = $data_company_old['modify_date'] ? $data_company_old['modify_date'] : "";







    //             $this->db->insert('company', $data_company);



    //             $id_company               = $this->db->insert_id();



    //             // $id_company               = $this->db->insert_id();







    //             /* Sector */



    //             $data_sector                 = array_filter(explode(";", $value['sector']));



    //             $id_sector                   = array();







    //             foreach ($data_sector as $value2) {



    //                 $id_sector_db = db_get_one('sector','id',array('name'=>$value2));



    //                 $id_sector[]  = array('sector_id'=>$id_sector_db,'company_id'=>$id_company);



    //             }







    //             if (!empty($id_sector)) {



    //             // insert per company



    //                 $this->db->insert_batch('auth_member_sector', $id_sector);



    //             }



    //             /* End Sector */







    //             /* end company */







    //             /* user */



    //             $data_user['id']                        = $value['id'] ;



    //             $user_name                              = explode(', ', $value['name']);



    //             $data_user['firstname']                 = $user_name[0] ;



    //             $data_user['lastname']                  = $user_name[1] ;



    //             $data_user['job']                       = $value['job'] ?$value['job'] :"";



    //             $data_user['citizenship']               = $value['country'] ?$value['country'] :"";



    //             $data_user['company_id']                = $id_company ;



    //             $data_user['email']                     = $value['email'] ?$value['email']:"";



    //             $data_user['password']                  = md5($value['member_code']) ;



    //             $data_user['membership_information_id'] = $value['id'];



    //             $data_user_old                          = $this->db->get_where('auth_member_old',array('firstname'=>$user_name[0],'lastname'=>$user_name[1]))->row_array();







    //             $data_user['m_m_number']                = empty($data_user_old['m_m_number']) ? "":$data_user_old['m_m_number'];



    //             $data_user['m_t_number']                = empty($data_user_old['m_t_number'])? "": $data_user_old['m_t_number'] ;



    //             $data_user['create_date']               = empty($data_user_old['create_date'])?"":$data_user_old['create_date'] ;



    //             $data_user['modify_date']               = empty($data_user_old['modify_date'])?"":$data_user_old['modify_date'] ;



    //             $data_user['member_category_id']        = 3 ; // company 1 individu 2 representatif 3



    //             // $data_user['member_category_id']        = 3 ; // company 1 individu 2 representatif 3



    //             $data_user['status_payment_id']         = 1 ;







    //             $this->db->insert('auth_member', $data_user);



    //             $id_user               = $this->db->insert_id();



    //             /* end user */



            



    //             /* membership information */



    //             $data_membership["id"]                    = $id_user;



    //             $data_membership["member_id"]             = $id_user;



    //             $data_membership["company_id"]            = $id_company ;



    //             $data_membership["membership_code"]       = $value['member_code'] ;



    //             // ambil dari lama                        



    //             $data_membership_old                      = $this->db->get_where('membership_information_old',array('member_id'=>$data_user_old['id']))->row_array();



    //             $data_membership["expired_date"]          = date('Y-12-31') ;



    //             $data_membership["registered_date"]       = empty($data_membership_old['registered_date'])?date('Y-m-d'):$data_membership_old['registered_date'] ;



    //             $data_membership["last_visited_date"]     = $data_membership_old['last_visited_date'] ;



    //             $data_membership["first_registered_date"] = $data_membership_old['first_registered_date'] ;



    //             $this->db->insert('membership_information', $data_membership);







    //             /* end membership information */







    //                 /*[company_name] => AIG INSURANCE INDONESIA, PT.



    //                 [alamat] => Indonesia Stock Exchange Bldg., Tower 2, 3A Floor



    //             Jl. Jend. Sudirman Kav 52-53



    //             Jakarta - 12190



    //             [website] => www.aig.co.id



                



    //             [name] => Morris, Michael J.



    //             [job] => Finance Director



    //             [email] => Mick.morris@aig.com



    //             [country] => Ireland







    //             [member_code] => CAM0053







    //             [sector] => Banking, Investment, Financial Services;Insurance, Insurance Brokers*/



    //         }



    //     }



    //         print("selesai");







    // }



    /*function migrasi_user_sector_check_spasi(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);        



        $this->migrasi->where('sector like "%Accounting, Tax and Auditing Services %" or 



        sector like "%Agricultural Products %" or 



        sector like "%Aircraft, Aviation Equipment, Services %" or 



        sector like "%Airline Industry %" or 



        sector like "%Animal Health Products %" or 



        sector like "%Automobiles, Automotive Equipment, Supplies %" or 



        sector like "%Banking, Investment, Financial Services %" or 



        sector like "%Chemicals, Chemical Plant Design %" or 



        sector like "%Club & Restaurant %" or 



        sector like "%Computers, Components, Software, and Services %" or 



        sector like "%Construction, Construction Materials, Services %" or 



        sector like "%Consumer Goods, Cosmetics, Household Products Distribution and Direct Marketing %" or 



        sector like "%Education, Training Services %" or 



        sector like "%Electrical Equipment, Appliances, Lighting Equipment %" or 



        sector like "%Environmental Services %" or 



        sector like "%Foodstuffs, Beverages, Tobacco %" or 



        sector like "%Freight forwarding %" or 



        sector like "%Governmental Organization %" or 



        sector like "%Healthcare, Medical Services %" or 



        sector like "%Hotels & Hospitality Industry %" or 



        sector like "%Human Resources %" or 



        sector like "%Import, Export %" or 



        sector like "%Information Technology %" or 



        sector like "%Infrastructure Development %" or 



        sector like "%Insurance, Insurance Brokers %" or 



        sector like "%Legal Consultants %" or 



        sector like "%Management Consultants, Market Research, Business Services %" or 



        sector like "%Manufacturing -- Consumer Goods %" or 



        sector like "%Manufacturing -- Light Industrialand Industrial %" or 



        sector like "%Medical Equipment, Supplies %" or 



        sector like "%Mining, Extraction Industry %" or 



        sector like "%Non-Governmental Organization %" or 



        sector like "%Office Equipment and Supplies %" or 



        sector like "%Other %" or 



        sector like "%Packaging, Pulp and Paper %" or 



        sector like "%Petroleum Industry/Exploration and Production Equipment and Services, Pipe Support %" or 



        sector like "%Pharmaceuticals %" or 



        sector like "%Power Generation %" or 



        sector like "%Real Estate Services and Property Management %" or 



        sector like "%Security and Investigative Services, Equipment %" or 



        sector like "%Services, Miscellaneous %" or 



        sector like "%Shipping and Storage, Moving Services %" or 



        sector like "%Sport and Fitness %" or 



        sector like "%Telecommunications and Equipment %" or 



        sector like "%Transportation and Tourism %"');







        $data = $this->migrasi->get('migrasi_user_individual')->result_array();



        foreach ($data as $key => $value) {



            $id[] = $value['id'];



        }



        print_r($id);exit;



    }*/



    // function migrasi_user_excel_normalisasi_sector(){



    //     $CI=& get_instance();



    //     $CI->migrasi = $CI->load->database('migrasi', TRUE);        







    //     $data = $this->migrasi->get('migrasi_user_individual')->result_array();



    //     foreach ($data as $key => $value) {



    //         $update['sector'] = $value['sector'];



    //         $data_sector_salah_ketik = array("Automobiles,Automotive Equipment,Supplies","Banking, Investment, and Financial Services","Club and Restaurant","Construction and Construction Materials, Services","Consumer Goods, Cosmetics, Household Products Distribution, Dir.Mktg","Electrical Equipment, Appliances, Lighting Equip.","Engineering, Design Consultants, Services","Governmental Organztn.","Health Care, Medical Services","Instruments, Scientific and Laboratory","Machinery, Machine Parts","Manufacturing-Consumer Goods","Manufacturing-Light Industrial and Industrial","Non-Governmental Organizations","Petroleum Industry/Exploration and Production,Equipment and Services, Pipe Support","Electrical Equipment, Appliances, Lighting Equipmentent");



    //         $data_sector_yang_benar = array("Automobiles, Automotive Equipment, Supplies","Banking, Investment, Financial Services","Club & Restaurant","Construction, Construction Materials, Services","Consumer Goods, Cosmetics, Household Products Distribution and Direct Marketing","Electrical Equipment, Appliances, Lighting Equipment","Engineering, Design Consultants and Services","Governmental Organization","Healthcare, Medical Services","Instruments, Scientific, Laboratory, Fire Protection","Machinery Machine Parts","Manufacturing -- Consumer Goods","Manufacturing -- Light Industrialand Industrial","Non-Governmental Organization","Petroleum Industry/Exploration and Production Equipment and Services, Pipe Support","Electrical Equipment, Appliances, Lighting Equipment");



    //         $data_sector_coma = array("Accounting, Tax and Auditing Services","Advertising, Public Relations","Agricultural Machinery, Heavy Equipment, Supplies","Aircraft, Aviation Equipment, Services","Architectural Services, Interior Design","Automobiles,Automotive Equipment,Supplies","Banking, Investment, Financial Services","Chemicals, Chemical Plant Design","Computers, Components, Software, and Services","Construction, Construction Materials, Services","Consumer Goods, Cosmetics, Household Products Distribution and Direct Marketing","Education, Training Services","Electrical Equipment, Appliances, Lighting Equipment","Engineering, Design Consultants and Services","Entertainment, Music and Movies","Foodstuffs, Beverages, Tobacco","Healthcare, Medical Services","Import, Export","Instruments, Scientific, Laboratory, Fire Protection","Insurance, Insurance Brokers","Lubricants, Services","Management Consultants, Market Research, Business Services","Media, News and Information","Medical Equipment, Supplies","Mining, Extraction Industry","Packaging, Pulp and Paper","Petroleum Industry/Exploration and Production Equipment and Services, Pipe Support","Publishing, Printing Supplies and Equipment","Security and Investigative Services, Equipment","Services, Miscellaneous","Shipping and Storage, Moving Services","Textile and Apparel, Garment","Wood Products, Furniture");







    //         foreach ($data_sector_salah_ketik as $key2 => $value2) {



    //             preg_match_all("!.*(".$value2.").*!is", $update['sector'],$sector);



    //             if (!empty(array_filter($sector) ) ) {



    //                 $update['sector']   = preg_replace("!".$sector[1][0]."!is", $data_sector_yang_benar[$key2], $update['sector']);



    //             }



    //         }



    //             if ($update['sector'] != $value['sector']) {



                    



    //                 $temp['sector']     = $update['sector'];



    //                 $temp['id']         = $value['id'];



    //                 $list_data_update[] = $temp;



    //             }







    //     }



    //     $this->migrasi->update_batch('migrasi_user_individual',$list_data_update,'id');



    //     // print_r($this->migrasi->last_query());







    //     print_r($list_data_update);exit;



    // }



    



    /*function junk(){



        $this->article->junk();



    }*/



    /*function get_web_page( $url ){



          $options = array(



              CURLOPT_CUSTOMREQUEST  => "GET",    // Atur type request, get atau post



              CURLOPT_POST           => false,    // Atur menjadi GET



              CURLOPT_FOLLOWLOCATION => true,    // Follow redirect aktif



              CURLOPT_CONNECTTIMEOUT => 120,     // Atur koneksi timeout



              CURLOPT_TIMEOUT        => 120,     // Atur response timeout



              CURLOPT_RETURNTRANSFER => true,



          );







          $ch      = curl_init( $url );          // Inisialisasi Curl



          curl_setopt_array( $ch, $options );    // Set Opsi



          $content = curl_exec( $ch );           // Eksekusi Curl



          curl_close( $ch );                     // Stop atau tutup script







          // $header['content'] = $content;



          // $header = $content;



          return $content;



    }*/



    /*  function test_function(){



       $c = $this->get_web_page("http://tri-pc/amcham/en/event/more/past-events/0/1/2017/0");



       print_r(



        $c



       );



    }*/



   /*function migrasi_content_member(){



    $CI=& get_instance();



    $CI->migrasi = $CI->load->database('migrasi', TRUE);



        // $this->migrasi->limit(100);



    $this->migrasi->order_by('name_out',"asc");



    $data_content  = $this->migrasi->get('view_company')->result_array();



    foreach ($data_content as $key => $value) {



            $temp['id']                 = $value['id_company'];



            $temp['name_out']           = $value['name_out'];



            $temp['img']                = $value['img'];



            // $temp['description'] = preg_replace("!<!--.*-->!is", "", $value['description']);



            preg_match("/<!--.*-->/is", $value['description'],$description_raw);



            // print_r($description_raw);



            if (!empty($description_raw)) {



                $temp['description'] = preg_replace("/<!--.*-->/is", "", $value['description']);



            }else{



                $temp['description']        = $value['description'];



            }







            $temp['name_in']            = $value['name_in'];



            $temp['address']            = $value['address'];



            $temp['t_number']           = $value['t_number'];



            $temp['m_number']           = $value['m_number'];



            // preg_match_all("/.*<a.*>(?<name>.+?)<\/a>/is",$value['representative'] , $representative);



            $temp['raw_representative'] = strip_tags($value['representative'],"<br>");



            // $temp['representative'] = $representative;



            $temp['website']            = $value['website'];



            $temp['uri_path_name_out']           = $value['uri_path'];



            $temp['is_delete']          = $value['is_delete'];



            $temp['create_date']        = (is_null($value['create_date']))? date("Y-m-d") : $value['create_date'] ;



            $temp['member_id_create']   = (is_null($value['create_date_migrasi']))? 0 : $value['create_date_migrasi'];



            $temp['date_modified']      = (is_null($value['user_modified']))? date("Y-m-d") : $value['user_modified'] ;



            $temp['user_id_modify']     = (is_null($value['user_modified_migrasi']))? 0 : $value['user_modified_migrasi'];



            $this->db->insert('company',$temp);



            // $data_save[] = $temp ; 



        }



            // print_r($data_save);



        exit;



    }*/



      /*function migrasi_kita_user(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        // $this->migrasi->limit(10);



        $data_content  = $this->migrasi->get('view_member_company')->result_array();



        foreach ($data_content as $key => $value) {



            $name                       = explode(",", $value['name']);



            



            $data["migrasi_id"]         = $value["id"];



            $data["firstname"]          = ($name[1])?$name[1]:$name[0];



            $data["lastname"]           = ($name[1])?$name[0]:"";



            $data["job"]                = $value["job_position"];



            $data["m_t_number"]         = $value["phone1"];



            $data["m_m_number"]         = $value["phone2"];



            $data["company_id"]         = $value["id_company"];



            $data["member_category_id"] = 3;



            $data["email"]              = $value["email"];



            $data["password_old"]       = $value["password"];



            $data["create_date"]        = $value["registerDate"];



            



            $data["is_delete"]          = ($value['block'] == 1) ? 1: 0 ;



            $data["status_payment_id"]  = ($value['lastvisitDate'] == "0000-00-00 00:00:00") ? 2: 1;



            $this->db->insert('auth_member', $data);



            $id_member = $this->db->insert_id();



            // print_r($data);exit;



                                                



            $data2["member_id"]             = $id_member;



            $data2["registered_date"]       = $value["registerDate"];



            $data2["expired_date"]          = ($value["block"] == 0 and $value["registerDate"] !="0000-00-00 00:00:00") ? 



                                                date('Y-m-d H:i:s', strtotime($value["registerDate"]. " + 1 year"))



                                                :"0000-00-00 00:00:00";



            $data2["last_visited_date"]     = $value["lastvisitDate"];



            $data2["first_registered_date"] = ($value["block"] == 0 and $value["registerDate"] !="0000-00-00 00:00:00") 



                                                ? $value["registerDate"]



                                                : $value["lastvisitDate"];            







            $this->db->insert('membership_information', $data2);



            $id_membership = $this->db->insert_id();



            







            $data3["membership_information_id"] = $id_membership;



            $this->db->where('id', $id_member);



            $this->db->update('auth_member', $data3);







        }



        print_r("updated");



        // $data_content  = $this->migrasi->get('view_member')->result_array();







      }*/



       /*function migrasi_content_company(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        // $this->migrasi->limit(100);



        $this->migrasi->order_by('company_name',"asc");



        $this->migrasi->where('id_company is null');



        $this->migrasi->where('company_name !=""');



        $this->migrasi->group_by('company_name');



        $data_content  = $this->migrasi->get('view_member_company')->result_array();



        // print_r(







        // $this->migrasi->last_query()



        // );exit;



        foreach ($data_content as $key => $value) {



            // $temp['id']                 = $value['id_company'];



            $temp['field_32']  = $value['company_name'];



            $temp['field_35']  = $value['company_name'];



            $temp['field_37']  = $value['address1'].' / '.$value['address2'];



            $temp['field_39']  = $value['phone1'];



            $temp['field_49']  = $value['phone2'];



            $temp['field_42']  = $value['website'];



            // $temp['is_delete'] = $value['is_invis_company'];



            $temp['field_100'] = 1;



            // $temp['img']                = $value['img'];



            // $temp['description'] = preg_replace("!<!--.*-->!is", "", $value['description']);



            // preg_match("/<!--.*-->/is", $value['description'],$description_raw);



            // // print_r($description_raw);



            // if (!empty($description_raw)) {



            //     $temp['description'] = preg_replace("/<!--.*-->/is", "", $value['description']);



            // }else{



            //     $temp['description']        = $value['description'];



            // }







            // preg_match_all("/.*<a.*>(?<name>.+?)<\/a>/is",$value['representative'] , $representative);



            // $temp['raw_representative'] = strip_tags($value['representative'],"<br>");



            // // $temp['representative'] = $representative;



            // $temp['uri_path_name_out']           = $value['uri_path'];



            // $temp['member_id_create']   = (is_null($value['create_date_migrasi']))? 0 : $value['create_date_migrasi'];



            // $temp['date_modified']      = (is_null($value['user_modified']))? date("Y-m-d") : $value['user_modified'] ;



            // $temp['user_id_modify']     = (is_null($value['user_modified_migrasi']))? 0 : $value['user_modified_migrasi'];



            // print_r($temp);exit;



            // $this->migrasi->insert('jos_joomd_type1',$temp);



            // print_r($temp);exit;



            $this->migrasi->insert('jos_joomd_type1',$temp);



            // $data_save[] = $temp ; 



        }



            print_r("ok");



        exit;



    }*/







    /*function migrasi_kita_company(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        // $this->migrasi->limit(100);



        $this->migrasi->order_by('name_out',"asc");



        $data_content  = $this->migrasi->get('view_company')->result_array();



        foreach ($data_content as $key => $value) {



            $temp['id']                 = $value['id_company'];



            $temp['name_out']           = $value['name_out'];



            $temp['img']                = $value['img'];



            // $temp['description'] = preg_replace("!<!--.*-->!is", "", $value['description']);



            preg_match("/<!--.*-->/is", $value['description'],$description_raw);



            // print_r($description_raw);



            if (!empty($description_raw)) {



                $temp['description'] = preg_replace("/<!--.*-->/is", "", $value['description']);



            }else{



                $temp['description']        = $value['description'];



            }







            $temp['name_in']            = $value['name_in'];



            $temp['address']            = $value['address'];



            $temp['t_number']           = $value['t_number'];



            $temp['m_number']           = $value['m_number'];



            // preg_match_all("/.*<a.*>(?<name>.+?)<\/a>/is",$value['representative'] , $representative);



            $temp['raw_representative'] = strip_tags($value['representative'],"<br>");



            // $temp['representative'] = $representative;



            $temp['website']            = $value['website'];



            $temp['uri_path_name_out']           = $value['uri_path'];



            $temp['is_invis_company']          = ($value['is_invis_company'] == 1 ? 1 : ($value['is_delete'] == 1 ? 1 : 0) )



            // $temp['is_delete'] = $value['is_invis_company'];



            $temp['create_date']        = (is_null($value['create_date']))? date("Y-m-d") : $value['create_date'] ;



            $temp['member_id_create']   = (is_null($value['create_date_migrasi']))? 0 : $value['create_date_migrasi'];



            $temp['date_modified']      = (is_null($value['user_modified']))? date("Y-m-d") : $value['user_modified'] ;



            $temp['user_id_modify']     = (is_null($value['user_modified_migrasi']))? 0 : $value['user_modified_migrasi'];



            $this->db->insert('company',$temp);



            // $data_save[] = $temp ; 



        }



            // print_r($data_save);



        exit;



    }*/







  /*  function migrasi_kita_company_2(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        $this->migrasi->where("is_invis = 1");



        $this->migrasi->order_by('name_out',"asc");



        $data_content  = $this->migrasi->get('view_company')->result_array();



        foreach ($data_content as $key => $value) {



            // print_r($value);exit;



            // $object['alias']     = generate_url($value['name_out']);



            // $object['id']        = $value['id_company'];



            // $object['typeid']    = 1;



            // $object['published'] = 0;            



            // $this->migrasi->insert('jos_joomd_item', $object);



            $temp['id']                 = $value['id_company'];



            $temp['name_out']           = $value['name_out'];



            $temp['name_in']            = $value['name_in'];



            $temp['img']                = $value['img'];



            // $temp['description'] = preg_replace("!<!--.*-->!is", "", $value['description']);



            // preg_match("/<!--.*-->/is", $value['description'],$description_raw);



            // print_r($description_raw);



            // if (!empty($description_raw)) {



            //     $temp['description'] = preg_replace("/<!--.*-->/is", "", $value['description']);



            // }else{



            //     $temp['description']        = $value['description'];



            // }







            $temp['address']            = $value['address'];



            $temp['t_number']           = $value['t_number'];



            $temp['m_number']           = $value['m_number'];



            // preg_match_all("/.*<a.*>(?<name>.+?)<\/a>/is",$value['representative'] , $representative);



            $temp['raw_representative'] = strip_tags($value['representative'],"<br>");



            // $temp['representative'] = $representative;



            $temp['website']            = $value['website'];



            $temp['uri_path_name_out']           = $value['uri_path'];



            $temp['is_invis_company']          = $value['is_delete'];



            // $temp['is_delete'] = $value['is_invis_company'];



            $temp['create_date']        = (is_null($value['create_date']))? date("Y-m-d") : $value['create_date'] ;



            $temp['member_id_create']   = (is_null($value['create_date_migrasi']))? 0 : $value['create_date_migrasi'];



            $temp['date_modified']      = (is_null($value['user_modified']))? date("Y-m-d") : $value['user_modified'] ;



            $temp['user_id_modify']     = (is_null($value['user_modified_migrasi']))? 0 : $value['user_modified_migrasi'];



            $this->db->insert('company',$temp);



            // $data_save[] = $temp ; 



        }



            print_r("ok");



        exit;



    }*/



    /*function migrasi_kita_company_sector(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        // $this->migrasi->limit(100);



        $this->migrasi->order_by('name_sector',"asc");



        $this->migrasi->where('name_sector is not null');



        $data_content  = $this->migrasi->get('view_company_sector')->result_array();



        foreach ($data_content as $key => $value) {



            $data_save['company_id'] = $value['id_migrasi_company'];



            $this->db->where('id_migrasi', $value['id_migrasi_sector']);



            $id_sector = $this->db->get('sector')->row_array()['id'];



            // print_r($value['id_migrasi_sector']);



            // print_r("<br/>");



            // print_r($id_sector);



            $data_save['sector_id'] = $id_sector;



            $this->db->insert('auth_member_sector', $data_save);



        }



            print_r("sudah jalan");exit;







    }*/



    /*function file_migrasi_normal($file){



        $arr             = explode("/", $file);



        $ret['filename']      = end(explode("/", $file));



        unset($arr[count($arr)-1]);



        // $ret['dir']    = $arr[0];



        $ret['path']     = implode("/",$arr);



        return $ret;







    }*/



    /*function migrasi_content_update_file(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        $data_content  = $this->migrasi->get('jos_content')->result_array();







        foreach ($data_content as $key => $value) {



            preg_match_all('!<a href="(.*?)".*>!i', $value['introtext'], $full_content);



            if (!empty($full_content[1])) {



                foreach ($full_content[1] as $key1 => $value1) {



                    preg_match('!\.(.*)!i', $full_content[1][$key1],$extension);



                    if ($extension) {



                        $file_extension = array("docx","doc","pdf");



                        if (in_array($extension[1],$file_extension)) {



                            if ( array_shift(explode(  "/",$full_content[1][$key1] )) == "images") {



                            $file = $this->file_migrasi_normal($full_content[1][$key1]);



                            $dir = dirname(__FILE__).'/../../file_upload/migrasi/'.$file['path'];



                            // print_r($dir);exit;











                            if (!is_dir($dir))



                            {



                                mkdir($dir,"0755",true);



                                if(!is_writable($dir)){    



                                    print_r($dir);



                                    print_r("dir not writable");exit;



                                }else{



                                    print_r($dir);



                                    print_r("dir is made");exit;



                                }



                            } 



                            if(is_dir($dir)){



                    // print_r($full_content[1][$key1]);exit;



                                $img      = str_replace('%20', ' ', $file['filename']) ;



                                if (!file_exists($dir."/".$img)) {



                                    print_r($full_content[1][$key1]);



                                print_r("<br/>");



                                    



                                    print_r("dir not found");



                                    print_r($dir);



                                    print_r("<br/>");



                                    print_r($img);



                                    print_r("<br/>");



                                    $awal = dirname(__FILE__).'/../../images_migrasi/'.$file['path']."/".$img;



                                    $akhir = dirname(__FILE__).'/../../file_upload/migrasi/'.$file['path']."/".$img;



                                    copy($awal, $akhir);                                



                                    print_r("<br/>");



                                }else{



                                    // print_r($dir);



                                    // print_r($img);



                                    // print_r("dir found");



                                    



                                }



                                // print_r("<br/>");



                                // print_r("Ok");







                                // print_r($dir);



                                // print_r("<br/>");



                            }



                            }











                            // $update['introtext'] = str_replace($full_content[1][$key1], 'file_upload/migrasi/'.$img, $value["introtext"]);







                            // $this->migrasi->where('id',$value['id']);



                            // $this->migrasi->update('jos_content',$update);



                        }



                    }



                }







                // exit;



            }



        }



    }*/



   /* function migrasi_membership_privileges(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);    



        // $this->migrasi->where_in('id', array("551"));        



        $this->migrasi->where_in('id', array("491","521","531","541","551"));



        



        $this->db->order_by('id', 'asc');



        $idcat = array(6,4,1,3,5);



        $data_content  = $this->migrasi->get('jos_content')->result_array();







        foreach ($data_content as $key => $value) {



            $string = $value['introtext'];



            preg_match_all('!<tr.*?["|>](.*?)<\/tr>!is', $string, $full_content);



            // print_r($string);



            // print_r($full_content);exit;



            foreach ($full_content[0] as $key1 => $value1) {



                 if($key1 %2 == 0 ){



                   preg_match_all('!<td.*?["|>].*?<\/td>!is', $full_content[0][$key1], $full_td);



                   // td nama dan gambar 



                   // td address,phone,email,website



                   preg_match_all('!<p.*?>(.*?)<\/p>!is', $full_td[0][1], $full_td_2);



                   // td description







                   // preg_match('!<strong>(.*)</strong>!is', $full_td[0][0], $full_td_1);



                   preg_match('!<strong>.*</strong>!is', $full_td[0][0], $name);











                   $clear_name = strip_tags($name[0]);







                   $temp['name']        = $clear_name;



                   



                   preg_match('!img src="(?<src>.*?)"!is', $full_td[0][0], $img);







                   $temp['img']         = end(explode("/", $img['src']));



                   // if($idcat[$key] == 1){



                   //  print_r($full_content);



                   //  print_r($full_td);



                   //  print_r($full_td_2);



                   //  exit;



                   // }



                   



                   $clear_address       = strip_tags($full_td_2[0][0]);



                   // print_r($img  );exit;   



                   $temp['address']     = $clear_address;



                   



                   preg_match_all('!Phone (?<number>.*)<!is', $full_td_2[0][1], $phone);



                   $temp['number']      = $phone['number'][0];



                   



                   $clear_email         = strip_tags($full_td_2[0][2]);



                   $temp['email']       = $clear_email;



                   



                   if ($full_td_2[0][3]) {



                        $clear_website       = strip_tags($full_td_2[0][3]);



                    }else{



                        preg_match_all('!<a href.*>(.*)<\/a>!i', $full_td[0][1], $website);



                        // print_r($website    );exit;



                        if ($website) {



                            $clear_website = strip_tags(end($website[1]));



                        }else{



                            $clear_website = "";



                            }



                        // if ($data[38]) {



                        //     print_r($full_td_2[0][3]);exit;



                        // }



                       



                   }



                   $temp['website']           = $clear_website;



                   $temp['page_content']      = $full_td[0][2];



                   $temp['id_status_publish'] = 2;



                   $temp['id_category']       = $idcat[$key];



                   // $data[]                    = $temp;



                   $ins                    = $temp;



                   $this->db->insert('membership_privileges', $ins);







                   // print_r($full_td);







                   // print_r("ini".$key1."<br/>"); 



                   // print_r($full_content[0][$key1]); 



                 }



            }



            // print_r($full_content[1]);



            // exit;



            // if ($full_content %2 != 0 || $full_content == 0) {



            //     // preg_match_all('!<tr>.*</tr>!is', $string, $full_content);



            //     print_r($full_content);exit;



            // }



        }



        print_r("succes insert");



                   print_r($data);



            exit;



    }*/



   /* function migrasi_content_reset(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        $this->migrasi->where('state', 1);    



        $data_content  = $this->migrasi->get('jos_content')->result_array();



        foreach ($data_content as $key => $value) {



            $update['introtext']           = $value['introtext_old'] ;



            $update['img_gallery_id']      = "" ;



            $update['img']                 = "" ;



            $update['thumb']               = "" ;



            $update['top_gallery_content'] = "" ;



            // print_r($update);exit;



            $this->migrasi->where('id', $value['id']);



            $this->migrasi->update('jos_content',$update);



            // print_r($this->db->last_query());exit;



        }



        print_r("sudah jalan");



    }*/







    /*  function migrasi_content_update(){ // update all jos_content



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);







        $this->migrasi->where('state', 1);    



        // $this->migrasi->where('id', 5654);    



        // $this->migrasi->limit(8);    



        // $this->migrasi->limit(1);    



        $data_content  = $this->migrasi->get('jos_content')->result_array();



        $no = 0 ;



        foreach ($data_content as $key => $value) {



            $string = $value['introtext'];



            unset($image);



            preg_match_all('/<img[^>]+src="([^">]+)"/i', $string, $image); // get all image



            // print_r($image);exit;



            $string_db = $string;



            if ($image[1]) {



                foreach ($image[1] as $key2 => $value2) {



                    $img[$key2]         = $value2;



                    $img_name[$key2]    = str_replace("%20"," ",end(explode("/", $img[$key2])));



                    $img_replace[$key2] = "images/article/large/".str_replace("%20"," ",$img_name[$key2]);                



                    // $string_db = str_replace($img[$key2], $img_replace[$key2], $string_db); // replace image 



                    if ($key2 == 0 ) {



                        $thumb  = $img_name[$key2];                    



                    }



                }







                $data = array(



                                'thumb' => $thumb, // first img



                                // 'introtext' => $string_db, 



                                'img' => implode("|",$img)."|" // implode all image



                            );



                $this->migrasi->where('id', $value['id']);



                $this->migrasi->update('jos_content', $data);



                



            }



            $this->migrasi->where('state', 1);    



            // $this->migrasi->limit(8);    







            //image bracket update



            $dataaa = $this->migrasi->get_where('jos_content',array("id"=>$value['id']))->row_array();



            $string = $dataaa['introtext'];



            preg_match('/({phocagallery.*categoryid=(.*)(\|.*)})/i', $string, $image_bracet);



            if ($image_bracet) {



                $last = strpos($image_bracet[2],"|");



                $var  = substr($image_bracet[2],0,$last);



                if ($value['catid'] == ",191,") {



                    preg_match("/(.*){/is",$string,$var_top_gallery);



                    // print_r($post);exit;



                    // $last_kurawal                 = strpos($string,"{");



                    // $var_top_gallery              = substr($string,0,$last_kurawal);



                    $string  = str_replace( $var_top_gallery[1], "", $string);



                // print_r($string);exit;



                    $data2['sponsor_gallery_content'] = $var_top_gallery[1]; // top img_gallery for annual report



                }else{



                    $data2['top_gallery_content'] = "";



                }



                $data2['img_gallery_id'] = $var; // get img_gallery_id



                $data2['introtext']      = str_replace($image_bracet[0], "", $string); // remove {}



                // print_r($data2);



                



                $this->migrasi->where('id', $value['id']);



                $this->migrasi->update('jos_content', $data2);



            }



        }



        print_r("ok");



        exit;



    



     }



    */



    /*  function kita_news_update(){ // insert all news (annual golf report, us-insiative,)



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);







        // $this->migrasi->where('state', 1);



        $this->migrasi->or_where('catid',',8,');



        $this->migrasi->or_where('catid',',77,');



        $this->migrasi->or_where('catid',',78,');



        $this->migrasi->or_where('catid',',79,');



        $this->migrasi->or_where('catid',',84,');



        $this->migrasi->or_where('catid',',161,');



        $this->migrasi->or_where('catid',',181,');



        $this->migrasi->or_where('catid',',221,');



        $this->migrasi->or_where('catid',',231,');



        $this->migrasi->or_where('catid',',241,');



        $this->migrasi->or_where('catid',',281,');



        $this->migrasi->or_where('catid',',301,');



        $this->migrasi->or_where('catid',',311,');



        $this->migrasi->or_where('catid',',321,');



        $this->migrasi->or_where('catid',',331,');



        $this->migrasi->or_where('catid',',341,');



        $this->migrasi->or_where('catid',',411,');



        $this->migrasi->or_where('catid',',421,');



        $this->migrasi->or_where('catid',',737,');



        $this->migrasi->or_where('catid',',738,');



        $this->migrasi->or_where('catid',',742,');



        $this->migrasi->or_where('catid',',748,');



        $this->migrasi->or_where('catid',',749,');



        $this->migrasi->or_where('catid',',750,');



        $this->migrasi->or_where('catid',',751,');



        $this->migrasi->or_where('catid',',752,');



        $this->migrasi->or_where('catid',',753,');



        $this->migrasi->or_where('catid',',754,');



        $this->migrasi->or_where('catid',',764,');







        $data_content  = $this->migrasi->get('jos_content')->result_array();



        $no = 0 ;



        foreach ($data_content as $key => $value) {



            switch ($value["catid"]) {



                case ',8,': $catid_tripc   =   "67" ; break;



                case ',77,': $catid_tripc  =  "77" ; break;



                case ',78,': $catid_tripc  =  "78" ; break;



                case ',79,': $catid_tripc  =  "68" ; break;



                case ',84,': $catid_tripc  =  "86" ; break;



                case ',161,': $catid_tripc =  "52" ; break;



                case ',181,': $catid_tripc = "52" ; break;



                case ',221,': $catid_tripc = "63" ; break;



                case ',231,': $catid_tripc = "64" ; break;



                case ',241,': $catid_tripc = "65" ; break;



                case ',281,': $catid_tripc = "54" ; break;



                case ',301,': $catid_tripc = "69" ; break;



                case ',311,': $catid_tripc = "70" ; break;



                case ',321,': $catid_tripc = "72" ; break;



                case ',331,': $catid_tripc = "71" ; break;



                case ',341,': $catid_tripc = "73" ; break;



                case ',411,': $catid_tripc = "66" ; break;



                case ',421,': $catid_tripc = "13" ; break;



                case ',737,': $catid_tripc = "74" ; break;



                case ',738,': $catid_tripc = "79" ; break;



                case ',742,': $catid_tripc = "75" ; break;



                case ',748,': $catid_tripc = "80" ; break;



                case ',749,': $catid_tripc = "68" ; break;



                case ',750,': $catid_tripc = "81" ; break;



                case ',751,': $catid_tripc = "82" ; break;



                case ',752,': $catid_tripc = "83" ; break;



                case ',753,': $catid_tripc = "85" ; break;



                case ',754,': $catid_tripc = "84" ; break;



                case ',764,': $catid_tripc = "76" ; break;



                



                default: break;



            }



            // print_r($value["catid"]);



            // print_r($catid_tripc);exit;







             $data = array(



             "publish_date"        => $value["publish_up"],



             "user_id_create"      => $value["created_by"],



             "create_date"         => $value["created"],



             "modify_date"         => $value["modified"],



             "user_id_modify"      => $value["modified_by"],



             "id_news_category"    => $catid_tripc,



             "id_migrasi_category" => $value["catid"],



             "id_migrasi"          => $value["id"],



             "img"                 => $value["thumb"],



             "id_gallery"          => $value["img_gallery_id"],



             "sponsor"             => $value["sponsor_gallery_content"],



             "page_content"        => $value["introtext"],



             "uri_path"            => $value["alias"],



             "news_title"          => $value["title"],



             "id_lang"             => 1,



             "approval_level"      => 100,



             "id_status_publish"   => 2



            );



            $this->db->insert('news', $data);



        



        }



        print_r("ok");



        exit;



    } */







   /* function kita_event_golf_update(){ // insert all news (annual golf report, us-insiative,)



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);







        $this->migrasi->or_where('catid',',191,');







        $data_content  = $this->migrasi->get('jos_content')->result_array();



        $no = 0 ;



        foreach ($data_content as $key => $value) {



            // switch ($value["catid"]) {



            //     case ',191,': $catid_tripc = "40" ; break;



                



            //     default: break;







            // }



            // print_r($value["catid"]);



            // print_r($catid_tripc);exit;







             $data = array(



             // "publish_date"        => $value["publish_up"],



             "user_id_create"         => $value["created_by"],



             "create_date"            => $value["created"],



             "modify_date"            => $value["modified"],



             "user_id_modify"         => $value["modified_by"],



             // "id_news_category"    => $catid_tripc,



             // "id_migrasi_category" => $value["catid"],



             "id"                     => $value["id"],



             "img"                    => $value["thumb"],



             "id_gallery"             => $value["img_gallery_id"],



             "sponsor"                => $value["sponsor_gallery_content"],



             "content"                => $value["introtext"],



             "id_event_category"      => 40,



             "create_date"            => $value["created"],



             "publish_date"           => $value["publish_up"],



             // "teaser"              => $value["introtext"],



             "uri_path"               => $value["alias"],



             "name"                   => $value["title"],



             "id_lang"                => 1,



             // "approval_level"      => 100,



             "id_status_publish"      => 2



            );



            $this->db->insert('event', $data);



        



        }



        print_r("ok");



        exit;



    }*/



    // function kita_event_all_update(){ // insert all event except golf



    //     $CI=& get_instance();



    //     $CI->migrasi = $CI->load->database('migrasi', TRUE);



    //     $this->migrasi->select('*,a.created as create_date');



    //     // $this->migrasi->limit(10);



    //     $this->migrasi->join('jos_jevents_vevdetail b','b.evdet_id = a.detail_id');



    //     $data_content  = $this->migrasi->get('jos_jevents_vevent a')->result_array();



    //     // print_r($data_content);exit;



    //     $no = 0 ;



    //     foreach ($data_content as $key => $value) {



    //         $data_save['id']                        = $value['ev_id'];



    //         $data_save['name']                      = $value['summary'];



    //         $data_save['hits']                      = $value['hits'];



    //         $data_save['content']                   = $value['description'];



    //         $data_save['uri_path']                  = generate_url($value['summary']);



    //         $data_save['id_template_form_register'] = 0;



    //         $data_save['id_lang']                   = 1;



    //         $data_save['is_delete']                 = 0;



    //         $data_save['user_id_create']            = $value['created_by'];



    //         $data_save['user_id_modify']            = $value['modified_by'];



    //         $data_save['create_date']               = $value['create_date'];



    //         // 26 = event 



            



    //         $start_date = date("Y-m-d",$value['dtstart']);



    //         $start_time = date("h:i:s",$value['dtstart']);



    //         $data_save['start_date']                = $start_date;



    //         $data_save['start_time']                = $start_time;







    //         $end_date = date("Y-m-d",$value['dtend']);



    //         $end_time = date("h:i:s",$value['dtend']);







    //         $data_save['end_date']                  = $end_date;



    //         $data_save['end_time']                  = $end_time;







    //         $data_save['is_open']                   = (date("Y-m-d",$value['dtend']) < date("Y-m-d") ) ? 1:0;







    //         $data_save['id_event_category']         = ($value['catid'] == 82 )?26  :28;



    //         $data_save['is_close']                  = $data_save['is_open'] == 1 ? 0:1;



    //         $data_save['id_status_publish']         = $value['state'];



    //         $data_save['publish_date ']             = $value['create_date'];



    //         // 26 = event 



    //         // 28 =non-event



    //         // print_r($data_save);exit;



    //         $this->db->insert('event', $data_save);



        



    //     }



    //     print_r("ok");



    //     exit;



    // }



    //  function migrasi_gallery(){



    //     $CI=& get_instance();



    //     $CI->migrasi = $CI->load->database('migrasi', TRUE);



    //     $this->load->model('gallerymodel');



    //     $this->load->model('galleryImagesModel');



    //     // $this->migrasi->limit(1);    



    //     $data_content  = $this->data();



    //     // $data_content  = $this->migrasi->get('view_gallery')->result_array();



    //     echo "<pre>";



    //     foreach ($data_content as $key => $value) {



    //         $check_gallery = $this->gallerymodel->findById($value['gallery_id']);



    //         $rmv_spc  = str_replace('%20', ' ', $value['filename']);



    //         $filename = is_array(explode("/",$rmv_spc)) ? end(explode("/",$rmv_spc)) : $rmv_spc;







    //         $unik_name = uniqid()." - " .$filename;



    //         //upload image 







    //         $path      = UPLOAD_DIR."../../images/phocagallery/".$rmv_spc;



    //         $path2     = UPLOAD_DIR."../../images/gallery/".$unik_name;







    //         if(copy($path, $path2 )) {



    //             print_r("success ... <br/>");



    //         }







    //         $lastname[] = $unik_name;



    //         $ins_images['id_gallery']     =  $value['catid'];



    //         $ins_images['name']           =  $value['title'];



    //         $ins_images['filename']       =  $unik_name;



    //         $ins_images['create_date']    =  $value['date'];



    //         $ins_images['id_lang']        =  1;



    //         $ins_images['user_id_create'] =  1;



    //         $ins_images['is_delete']      =  0;







    //         $this->db->insert('gallery_images', $ins_images);           



    //     }







    //     print_r("sudah jalan");



    // }











    /*function migrasi_img_move(){



        $CI =& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        



        $this->migrasi->where("img != ''");



        // $this->migrasi->limit(100);



        $data_migrasi = $this->migrasi->get("jos_content")->result_array();







        foreach ($data_migrasi as $key => $value) {



            $awal    = $value['thumb'];











            if(isset($awal)) { 



                // print_r($arr_img);exit;



                    unset($arr_img);



                $arr_img = array_filter(explode("|", $value['img']));



                //foreach gambar 



                foreach ($arr_img as $key2 => $value2) {



                    unset($target_path);



                    $target_path[0] = UPLOAD_DIR.'../../images/article/large/';



                    //kalo gambar pertama



                    if ($key2 == 0) {



                        $target_path[1] = UPLOAD_DIR.'../../images/article/small/';



                    }else{



                        unset($target_path[1]);



                    }



                           // print_r($arr_img[$key2]);



                           // print_r("<br/>");



                    $sourcefile    = UPLOAD_DIR.'../../images_migrasi/'.$arr_img[$key2];



                    //hilangin %20



                    $sourcefile    = str_replace('%20', ' ', $sourcefile);



                           // print_r($sourcefile);



                           // print_r("<br/>");



                    //ambil nama gambar



                    $img           = str_replace('%20', ' ', end(explode("/", $arr_img[$key2])) );



                           // print_r("<br/>");



                           // print_r($img);







                    if (file_exists(UPLOAD_DIR.'../../images/article/large/'.$img)) {



                           // exit;



                    }else{



                        foreach ($target_path as $key3 => $value3) {



                           // print_r($arr_img[$key2]);



                           // print_r($value['img']);



                           // print_r($arr_img);



                           // print_r($sourcefile);



                           // print_r($img);



                           // print_r($target_path);



                        $temp['awal'] = $sourcefile;



                        $temp['akhir'] = $target_path[$key3]. pathinfo($sourcefile, PATHINFO_BASENAME);



                        print_r("<br/>");



                        print_r($value['id']);



                        print_r("<br/>");



                        print_r($sourcefile);



                        print_r("<br/>");



                        print_r($temp['akhir']);



                        // foreach ($go_move as $key => $value) {



                            if(copy($temp['awal'], $temp['akhir'])) {



                                echo "The file ".  $img. " has been uploaded<br />";



                            } else{



                                // error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);



                                // $error = error_get_last();



                                // echo $error;



                                echo "There was an error uploading the file, please try again!";



                            }



                        }



                        // print_r($temp);exit;



                            // $go_move[] = $temp;



                        // if(copy($sourcefile, $target_path[$key3]. pathinfo($sourcefile, PATHINFO_BASENAME))) {



                        //     // echo "The file ".  $img. " has been uploaded<br />";



                        // } else{



                        //     // error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);



                        //     // $error = error_get_last();



                        //     // echo $error;



                        //     // echo "There was an error uploading the file, please try again!";



                        // }



                    }



                }    



            }







            // foreach ($go_move as $key => $value) {



            //     if(copy($value['awal'], $value['akhir'])) {



            //         echo "The file ".  $img. " has been uploaded<br />";



            //     } else{



            //         error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);



            //         $error = error_get_last();



            //         echo $error;



            //         echo "There was an error uploading the file, please try again!";



            //     }



            // }







        }            



                        



        print_r('success');



        exit;



    }*/        



    /*function migrasi_img(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        $this->migrasi->where('state', 1);



        $this->migrasi->where_in('catid', array("77","78","221","231","241","281","411","421","738","748","749","750","751","752","753","754"));



        $all_data = $this->migrasi->get('jos_content');



        $ttl_row  = $all_data->num_rows();



        $offset   = 200;



        $run      = 0;



        $enough   = 0;







        // $this->migrasi->limit(200,0);



        $this->migrasi->where('state', 1);



        $this->migrasi->where_in('catid', array("77","78","221","231","241","281","411","421","738","748","749","750","751","752","753","754"));







        $data_content  = $this->migrasi->get('jos_content')->result_array();



        $no = 0 ;



        foreach ($data_content as $key => $value) {



            $string    = $value['img'];



            if ($string) {



                $img_src = end(explode('/', $string));



                $data = array(



                        'img_src' => $img_src



                );



                $this->migrasi->where('id', $value['id']);



                $this->migrasi->update('jos_content', $data);



            }



        }



        exit;



    }*/



    /*function migrasi(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        $this->migrasi->where('state', 1);



        $this->migrasi->where_in('catid', array("77","78","221","231","241","281","411","421","738","748","749","750","751","752","753","754"));



        $all_data = $this->migrasi->get('jos_content');



        $ttl_row  = $all_data->num_rows();



        $offset   = 200;



        $run      = 0;



        $enough   = 0;



        // print_r($ttl_row);exit;







        // $this->migrasi->limit(200,0);



        $this->migrasi->where('state', 1);



        $this->migrasi->where_in('catid', array("77","78","221","231","241","281","411","421","738","748","749","750","751","752","753","754"));







        $data_content  = $this->migrasi->get('jos_content')->result_array();



        $no = 0 ;



        foreach ($data_content as $key => $value) {



            $string    = $value['introtext'];







            $dom = new DOMDocument();



            $dom->loadHTML($string);            



            



            preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $string, $image);



            if ($image) {



                // $img       = $dom->getElementsByTagName('img')->item(0)->getAttribute('src');



                $b = preg_replace("/<img[^>]+\>/i", "", $string);







                // if (!empty($image[0])) {



                    // $tagremove = $image[0];



                    // $string    = str_replace($tagremove,'',$string);



                        $data = array(



                                'introtext' => $b,



                                'img' => $image['src'],



                                // 'tag' => $b,



                                // 'id' => $value['id']



                        );



                        ++$no;



                        // print_r($data);



                        $this->migrasi->where('id', $value['id']);



                        $this->migrasi->update('jos_content', $data);



                



            }







            // }



        



        }



        exit;



    }*/



   /* function migrasi_data(){



        $CI=& get_instance();



        $CI->migrasi = $CI->load->database('migrasi', TRUE);



        $this->migrasi->where('state', 1);



        // $this->migrasi->where_in('catid', array("77","78","221","231","241","281","411","421","738","748","749","750","751","752","753","754"));



        $all_data = $this->migrasi->get('jos_content');



        $ttl_row  = $all_data->num_rows();



        $offset   = 200;



        $run      = 0;



        $enough   = 0;



        // print_r($ttl_row);exit;







        // $this->migrasi->limit(1,0);



        $this->migrasi->where('state', 1);



        $this->migrasi->where_in('catid', array("77","78","221","231","241","281","411","421","738","748","749","750","751","752","753","754"));







        $data_content  = $this->migrasi->get('jos_content')->result_array();



        $no = 0 ;



        foreach ($data_content as $key => $value) {



            $data = array(



             "publish_date"      => $value["publish_up"],



             "user_id_create"    => $value["created_by"],



             "create_date"       => $value["created"],



             "modify_date"       => $value["modified"],



             "user_id_modify"    => $value["modified_by"],



             "id_news_category"  => $value["catid"],



             "id_migrasi"        => $value["id"],



             "id_gallery"        => $value["id"],



             "img"               => $value["img_src"],



             "page_content"      => $value["introtext"],



             "uri_path"          => $value["alias"],



             "news_title"        => $value["title"],



             "id_status_publish" => 2



            );



            $this->db->insert('news', $data);



        



        }



        exit;







    }*/



    function index(){



        $this->load->model('eventmodel');



        $this->load->model('newsmodel');



        $this->load->model('gallerymodel');



        $this->load->model('footerimagesmodel');



        $this->load->model('AboutPartnersModel');







        $id_lang            = id_lang();







        //slideshow



        $data['slider']  = slider_widget();







        //featured partners



        $data_featured_partners = $this->AboutPartnersModel->findby(array('id_partners_category'=>8,'id_status_publish' => 2,'id_lang' => $id_lang));



        $data['featured_partners_hide'] = '';



        if (!$data_featured_partners) {



            $data['featured_partners_hide'] = 'hide';



        }else{



            foreach ($data_featured_partners as $key => $value) {



                $temp['id_com']              = $value['id'];



                $temp['img']                 = image($value['img'],'large');



                $temp['url']                 = $value['url'] ? $value['url'] : '#';



                $data['featured_partners'][] = $temp;



            }



        }







        //highlight 



        $where_news['a.id_status_publish'] = 2;



        $where_news['a.approval_level']    = 100;



        $where_news['a.publish_date <=']   = date('Y-m-d');



        $where_news['a.id_lang']           = $id_lang;







        $id_category_newsletters           = id_news_newsletters(1);



        $id_category_newsletters           = array_diff($id_category_newsletters, array('54'));











        $this->db->where_in('a.id_news_category',$id_category_newsletters);



        $this->db->limit(6);



        $this->db->order_by('publish_date', 'desc');



        $data_news  = $this->newsmodel->highlight($where_news);



        foreach ($data_news as $key => $value) {



            $temp['tanggal']     = iso_date($value['publish_date']);



            $temp['title']       = $value['news_title'];



            $temp['teaser']      = character_limiter($value['teaser'],151,'...');



            $temp['img_url']     = base_url().'images/article/small/'.$value['img'];



            $temp['link']        = 'news/detail/'.$value['uri_path'];



            



            $data['highlight'][] = $temp;



        }







        //partners



        $id_category_not_show = '8';



        $this->db->where('id_partners_category not in('.$id_category_not_show.')');



        $this->db->group_by('id_partners_category');



        $data_partners = $this->AboutPartnersModel->findby(array('id_status_publish' => 2,'id_lang' => $id_lang));



        $data['partners_hide'] = '';



        if (!$data_partners) {



            $data['partners_hide'] = 'hide';



        }



        foreach ($data_partners as $key => $value) {



            $data['partners'][$key]['category']       = db_get_one('partners_category','name',array('id'=>$value['id_partners_category']));



            $data['partners'][$key]['id_com']         = $value['id'];



            $data['partners'][$key]['category_class'] =  ($value['id_partners_category'] != 7) ? 'slider-sponsors' :'slider-sponsors-legal';



            $data_partners_list = '';



            $data_partners_list = $this->AboutPartnersModel->findby(array('id_status_publish' => 2,'id_lang' => $id_lang,'id_partners_category'=>$value['id_partners_category']));







            foreach ($data_partners_list as $key1 => $value1) {



                if ($key1 != count($data_partners_list)) {



                    $img_sp = ($value['id_partners_category'] != 7) ?image($value1['img'],'small'):image($value1['img'],'large');



                    $data['partners'][$key]['data'][$key1]['legal_class'] = ($value['id_partners_category'] != 7) ?'':'legal-item';



                    $data['partners'][$key]['data'][$key1]['img'] = $img_sp;



                    $data['partners'][$key]['data'][$key1]['url'] = $value1['url'] ? $value1['url'] : '#';



                }



            }



        }



        //event







        // $id_eventcat_upcoming = id_child_event(26,1);



        $id_cat_event_amcham                = id_child_event(26,1);



        $id_cat_event_golf                  = id_child_event(40,1);



        $id_eventcat_upcoming               = array_merge($id_cat_event_amcham,$id_cat_event_golf);



        $this->db->start_cache();



        $where_event['a.is_not_available']  = 0;



        $where_event['a.id_status_publish'] = 2;



        $where_event['a.publish_date <=']   = date('Y-m-d');



        $where_event['a.id_lang']           = $id_lang;



        $this->db->order_by('a.start_date','desc');



        $this->db->order_by('a.start_time','desc');







        $this->db->where_in('a.id_event_category', $id_eventcat_upcoming);



        $this->db->stop_cache();



        $where_event['a.end_date >=']       = date('Y-m-d');



        $data_event = $this->eventmodel->findBy($where_event);



        $jml_event = count($data_event);



        if ($jml_event != 0) {



            if ($jml_event >= 9) {



              $offset = $jml_event - 9;



              $this->db->limit(9,$offset);



            }if ($jml_event >=6 ) {



              $offset = $jml_event - 6;



              $this->db->limit(6,$offset);



            }else {



              $offset = $jml_event - 3;



              $this->db->limit(3,$offset);



            }



            unset($where_event['a.end_date >=']);



            $data_event2 = $this->eventmodel->findBy($where_event);



            $this->db->flush_cache();



            krsort($data_event2);



            foreach ($data_event2 as $key => $value) {



                // $temp['color']    = (in_array($value['id_event_category'],id_child(array('table'=>'event_category','colomn' => 'id_parent_category','with_parent'=>'1','colomn_select'=>'id','id'=>26,'array'=>1 )))) ?'darkblue':'red';//id amcham event



                $temp['color']    = 'darkblue';//id amcham event



                $temp['category'] = $value['subcategory'];



                $temp['url']      = base_url().'event/detail/'.$value['uri_path'];



                $temp['name']     = $value['name'];



                $temp['time']     = event_date($value['start_date'],$value['start_time'],$value['end_time']);



                // $temp['place']    = nl2br($value['location_name']);



                $temp['place']       = (!$value['location_name']) ? '-' : $value['location_name'];







                $data['events'][] = $temp;



            }



           $data['dsps_events_highlights'] = '';



        }else{



           $this->db->flush_cache();



           $data['dsps_events_highlights'] = 'hidden';



           $data['lang_see_events_not_found'] = 'All Events';



        }







        // publication



        $where_publication['a.id_status_publish'] = 2;



        $where_publication['a.approval_level']    = 100;



        $where_publication['a.publish_date <=']   = date('Y-m-d H:i:s');



        $where_publication['a.id_lang']           = $id_lang;



        $id_category_publication           = id_news_publication(1);







        $this->db->where_in('a.id_news_category',$id_category_publication);



        $this->db->limit(5);



        $data_news  = $this->newsmodel->highlight($where_publication);



        foreach ($data_news as $key => $value) {



            $temp['img']           = getImg($value['img'],'large');



            $temp['url']           = $value['uri_path'];



            $temp['title']         = $value['category'];



            $data['publication'][] = $temp;



        }



        // latest feed



        $this->data['feed_twitter']      = feed_twitter();







        // VIDEO BANNER



        $id_lang = id_lang();



        $this->db->limit(5, 0);







        $this->db->order_by('a.publish_date','desc'); 



          $this->db->order_by('a.id','desc');



        $where_2['id_gallery_category'] = 4;



        $where_2['id_status_publish']   = 2;



        $video_gallery                  = $this->gallerymodel->findBy($where_2);







        foreach ($video_gallery as $key => $value) {



          $data_video['video'][] = array(



            'url'=>$value['youtube_url'],



            'description'=>closetags(character_limiter($value['description'],52,'...')),



            'name'=>$value['name']



          );



        }



        $data_video['base_url_lang_video']  = base_url_lang();



        $data_video['base_url_video']  = base_url();



        $this->data['feed_latest_video'] = $this->parser->parse('layout/ddi/widget_video.html', $data_video,true);



          



        $data['widget_latest_feed'] = $this->parser->parse('layout/ddi/widget_latest_feed.html', $this->data,true);







        // gallery slider



        $this->db->limit(7);



        $this->db->where('id_gallery_category',1);



        $this->db->order_by('publish_date', 'desc');



        $data_gallery = $this->gallerymodel->findBy();



        foreach ($data_gallery as $key => $value) {



          $data['gallery_photo'][] = array(



            'url_photo' => base_url_lang().'/gallery/detailphoto/'. $value['uri_path'],



            'img_photo' =>  getImg($value['img'],'small'), 



            'description_photo' => $value['name'] 



          );



        }







        if($data['seo_title'] == ''){



            $data['seo_title'] = "MJM";



        }







        $data['meta_description'] = preg_replace('/<[^>]*>/', '', $data['meta_description']);




        $data['active_home'] = 'active';


        render('home',$data);



    }



    function sitemap(){



        $data['sitemap']  = header_menu();



        $data['sitemap'] .= footer_menu();



        render('sitemap',$data);



    }



    function get_iframe()



    {



        $post = $this->input->post();



        $ret['iframe']  = get_video_iframe_yt($post['url']);







        $where['youtube_url'] = $post['url'];



        $where['id_gallery_category'] = 4;



        $data_gallery = $this->db->get_where('gallery', $where)->row_array();







        $ret['title'] = $data_gallery['name'];



        $ret['desc'] = $data_gallery['description'];



        



        echo json_encode($ret);



    }



    function morelistphoto($id,$page)



    {



        



    }







/*    function data() {



        $jos_phocagallery = $jos_phocagallery = array(



          array(



            "id" => 7716,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/img_7845.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7715,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/img_7843.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7714,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/img_7832.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7713,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/img_7822.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7712,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/img_7797.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7711,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/amcham-join1 49.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7710,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/amcham-join1 44.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7709,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/amcham-join1 42.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7708,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/amcham-join1 28.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7707,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/amcham-join1 26.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7706,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/004.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7705,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/003.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7704,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/002.jpg",



            "date" => "2018-06-29 08:06:24",



          ),



          array(



            "id" => 7703,



            "catid" => 537,



            "title" => "Amcham-IABC-IBAI-ICCC Joint Chamber Business Networking ",



            "alias" => "amcham-iabc-ibai-icc",



            "filename" => "Cocktails/2018/JUN/001.jpg",



            "date" => "2018-06-29 08:06:24",



          )



        );











        return $jos_phocagallery;



    }*/







}

