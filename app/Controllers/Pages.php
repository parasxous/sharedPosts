<?php

class Pages extends Controller{
    
    public function __construct()
    {
    }
    public function index(){
        if(isLoggedIn()){
            redirect('posts');
        }
        $data = [
        'title'=>'SharedPosts',
        'description'=>'Simple social network built on the PakMVC PHP framework'
        ];

        $this->view('pages/index',$data);
    }

    public function about(){
        $data = [
            'title'=>'About Us',
            'description'=>'APP to share posts with other users'
            ];
        $this->view('pages/about',$data);
    }

}