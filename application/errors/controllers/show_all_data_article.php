<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Show_All_Data_Article extends CI_Controller {
	function __construct(){
		parent::__construct();
		
	}
    function index(){
        $this->load->model('newsmodel');
        $this->load->model('newstagsmodel');
        $this->load->model('tagsmodel');
        $data = $this->newsmodel->findBy(array('a.is_delete'=>0));
        echo '<table border="1">';
        foreach ($data as $key_data => $value_data) {
            echo "<tr><td>$value_data[news_title]</td>";
            echo "<td>http://futuready.com/articledetail/index/$value_data[uri_path]</td>";
            echo "<td>".$value_data['publish_date']."</td>";
            echo "<td>$value_data[category]</td>";
            echo '<td>';
            $tags = $this->newstagsmodel->findBy(array('id_news'=>$value_data['id']));
            foreach ($tags as $key => $value) {
                $tag            .=  ', '."<a href='".$this->baseUrl."article/tags/$value[uri_path]'>".$value['tags'].'</a>';
                $id_tags[]       = $value['id_tags'];
                echo $value['tags'].',';
            }
            echo '</td></tr>';
        }
        echo '</table>';
        render('',$data,'blank');
    }
}