<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Home extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }


    function index()
    {
        $id_lang = id_lang();

        if ($data["seo_title"] == "") {
            $data["seo_title"] = "MJM";
        }

        $data["meta_description"] = preg_replace(
            "/<[^>]*>/",
            "",
            $data["meta_description"]
        );

        $data["active_home"] = "active";

        render("home", $data);
    }

}
