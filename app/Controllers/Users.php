<?php

Class Users extends Controller{
    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function register(){
        //check for post
        if($_SERVER['REQUEST_METHOD']==='POST'){
            //process form

            //Sanitize post data
            $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

            $data = [
                'name'=>trim($_POST['name']),
                'email'=>trim($_POST['email']),
                'password'=>trim($_POST['password']),
                'confirm_password'=>trim($_POST['confirm_password']),
                'name_err'=>'',
                'email_err'=>'',
                'pass_err'=>'',
                'confirm_pass_err'=>''
            ];
            //validate email
            if(empty($data['email'])){
                $data['email_err'] = 'Please enter email';
            }else{
                //check email
                if($this->userModel->findUserByEmail($data['email'])){
                    $data['email_err'] = 'Email is allready taken';
                }
            }
            //validate name
            if(empty($data['name'])){
                $data['name_err'] = 'Please enter name';
            }
            //validate password
            if(empty($data['password'])){
                $data['pass_err'] = 'Please enter password';
            }elseif(strlen($data['password']) < 6 ){
                $data['pass_err'] = 'Password must be at least 6 characters';
            }
            //validate confirm pass
            if(empty($data['confirm_password'])){
                $data['confirm_pass_err'] = 'Please confirm password';
            }else{
                if($data['password'] != $data['confirm_password']){
                    $data['confirm_pass_err'] = 'Passwords do not match';
                }
            }

            //Make sure errors are empty
            if(empty($data['email_err']) && empty($data['name_err']) && empty($data['pass_err']) && empty($data['confirm_pass_err'])){
                    //validated

                    //hash password
                    $data['password'] = password_hash($data['password'],PASSWORD_DEFAULT);

                    //REGISTER USER
                    
                    if($this->userModel->register($data)){
                        flash('register_success','You are registered and can log in');
                        redirect('users/login');  
                    }else{
                        die('something went wrong');
                    }

            }else{
                //LOAD VIEW WITH ERRORS
                $this->view('users/register',$data);
            }
        }else{
          //Init data
          $data = [
              'name'=>'',
              'email'=>'',
              'password'=>'',
              'confirm_password'=>'',
              'name_err'=>'',
              'email_err'=>'',
              'pass_err'=>'',
              'confirm_pass_err'=>''
          ];
          //load view
          $this->view('users/register',$data);
        }
    }
    public function login(){
        //check for post
        if($_SERVER['REQUEST_METHOD']==='POST'){
              //process form
              //Sanitize post data
              $_POST = filter_input_array(INPUT_POST,FILTER_SANITIZE_STRING);

              $data = [
                  'email'=>trim($_POST['email']),
                  'password'=>trim($_POST['password']),
                  'email_err'=>'',
                  'pass_err'=>'',
              ];
               //validate email
            if(empty($data['email'])){
                $data['email_err'] = 'Please enter email';
            }
            //validate email
            if(empty($data['password'])){
                $data['pass_err'] = 'Please enter password';
            }
    
            //check for user/email
            if($this->userModel->findUserByEmail($data['email'])){
                //user found
            }else{
                // user not found
                $data['email_err'] = 'No user found';
            }

            //MAKE SURE ERRORS ARE EMPTY
             //Make sure errors are empty
             if(empty($data['email_err']) && empty($data['pass_err'])){
            //validated
            //check and set logged in user
            $loggedInUser = $this->userModel->login($data['email'],$data['password']);
            if($loggedInUser){
                //create session
                $this->createUserSession($loggedInUser);
            }else{
                $data['pass_err']  = 'Password incorect';
                $this->view('users/login',$data);
            }
          }else{
            //LOAD VIEW WITH ERRORS
            $this->view('users/login',$data);
        }
        }else{
          //Init data
          $data = [
              'email'=>'',
              'password'=>'',
              'pass_err'=>'',
              'email_err'=>''
            ];
          //load view
          $this->view('users/login',$data);
        }
    }
    public function createUserSession($user){
        $_SESSION['user_id'] = $user->id; 
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        redirect('posts');
    }
    public function logout(){
        unset( $_SESSION['user_id'] );
        unset( $_SESSION['user_email'] );
        unset( $_SESSION['user_name'] );
        session_destroy();
        redirect('users/login');
    }

   
}

