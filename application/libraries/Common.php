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

    public function preg_replace_filename($filename){
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        $ignore_spacial_char = array(" ","%20",",","@","$","%","&","\\","/",":","*","?","\"","'","<",">","|","~","`","#","^","+","=","(",")","₹","×","÷","{","}","[","]",";","!");
        $filename = str_replace($ignore_spacial_char,"",basename($filename));
        $filename = substr($filename,0,20);
        return strtolower(time().uniqid()."_".$filename.".".$extension);
    }

    public function slug_generator($string){
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    }



}