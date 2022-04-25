<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common {

    
    public function header_authentication(){
        $ci =& get_instance();
        $header_request = $ci->input->request_headers();
        $response = array();
        // p($header_request);
        if(!empty($_SERVER['PHP_AUTH_USER']) || !empty($_SERVER['PHP_AUTH_PW'])){
         $error = 0;
           if($_SERVER['PHP_AUTH_USER'] !== HEADER_BASIC_AUTH_USER){
               $error = 1;
           }

           if($_SERVER['PHP_AUTH_PW'] != HEADER_BASIC_AUTH_PW){
               $error = 1;
           }
           if($error == 1){
            $response['status'] = '0';
            $response['message'] = 'Invalid authentication';
            self::response($response);
           }
        } else {
           $response['status'] = '0';
           $response['message'] = 'Required header authentication credential';
           self::response($response);
        }

    }

    public function field_required($field, $post_data){
        foreach($field as $value){
            if(!isset($post_data[$value])){
                $response['status'] = 0;
                $response['message'] = $value." parameter is required";
                self::response($response);
            }
            
            if($post_data[$value] == ""){
                $response['status'] = 0;
                $response['message'] = $value." should not blank";
                self::response($response);
            }
        }  
    }
    
    public function response($response){
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
        exit();
    }



}