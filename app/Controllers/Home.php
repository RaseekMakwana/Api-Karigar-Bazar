<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {

$builder = $this->db->table('category_master')->get()->getResult();



    p($builder);
        return view('welcome_message');
    }
}
