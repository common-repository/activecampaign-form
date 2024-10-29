<?php

// checkACapikey handler function...
add_action('wp_ajax_checkACapikey', 'checkACapikey_callback');
function checkACapikey_callback() {
    $cUs_api = new cUsACComAPI();
    $cUs_api->resetData();
    
    $api_key = $_REQUEST[apikey];
    $api_url = $_REQUEST[apiurl];
    
    $AC_api = new ActiveCampaign($api_url, $api_key);
    
     if ((int) $AC_api->credentials_test()) :
        echo 1; //VALID APIKEY , APIURL
        update_option('cUsAC_settings_api_key', $api_key);
        update_option('cUsAC_settings_api_url', $api_url);
    else:
        echo 2; //INVALID APIKEY
    endif;
    die();
    
}

// getACList handler function...
add_action('wp_ajax_getACList', 'getACList_callback');
function getACList_callback() {
    $data['status'] = 2;
    $data['options'] = "";
    $data['existlname'] ="0";
    
    $api_key = $_REQUEST[apikey];
    $api_url = $_REQUEST[apiurl];
    
    $AC_api = new ActiveCampaign($api_url, $api_key);
    $account = $AC_api->api("account/view");
    
    if($account->lname) { $data['existlname']="1"; }
    
    if ((int) $AC_api->credentials_test()) {
        $lists = $AC_api->api("list/list");
            
            $counter = 0;
            foreach ($lists as $value) {
                if ($value->id) { 
                    $counter++; 
                    $data['options'] .= "<option value='{$value->id}'>{$value->name}</option>"; 
                }
            }
          if(!$counter) {
              $data['status'] = 1;
          } else {
              $data['status'] = 0;
          }
    }
    
    echo json_encode($data);
    die();
}

// sendClientList handler function...
add_action('wp_ajax_sendACClientList', 'sendClientListAC_callback');
function sendClientListAC_callback() {
    $AC_apikey = get_option('cUsAC_settings_api_key'); //get the saved activecampaign apikey
    $AC_apiurl = get_option('cUsAC_settings_api_url'); //get the saved activecampaign apikey
    global $current_user;
    get_currentuserinfo();
    $AC_api = new ActiveCampaign($AC_apiurl, $AC_apikey);
    $account = $AC_api->api("account/view");
    $cUs_api = new cUsACComAPI();
    
    if (empty($_REQUEST[acSurName])) {
        $last_name = $account->lname;
    } else {
        $last_name = $_REQUEST[acSurName];
    }
    
    if (empty($account->fname)) {
        $first_name = $current_user->user_firstname;
    } else {
        $first_name = $account->fname;
    }
    
    $postData = array(
        'fname' => $first_name,
        'lname' => $last_name,
        'email' => $account->email,
        'AC_apikey' => $AC_apikey,
        'website' => $_SERVER['HTTP_HOST'],
        'listID' => $_REQUEST['listID'],
        'acListName' => $_REQUEST['acListName'],
        'AC_apiurl'  => $AC_apiurl
    );
    
    update_option('cUsAC_settings', $postData);

    $cusAPIresult = $cUs_api->createCustomer($postData);

    if($cusAPIresult) :

        $cUs_json = json_decode($cusAPIresult);

        switch ( $cUs_json->status  ) :

            case 'success':
                echo 1;//GREAT
                update_option('cUsAC_settings_form_key', $cUs_json->form_key );
                $aryFormOptions = array( //DEFAULT SETTINGS / FIRST TIME
                    'tab_user'          => 1,
                    'cus_version'       => 'tab'
                ); 
                update_option('cUsAC_FORM_settings', $aryFormOptions );//UPDATE FORM SETTINGS
            break;

            case 'error':

                if($cUs_json->error[0] == 'Email exists'):
                    echo 2;//ALREDY CUS USER
                else:
                    //ANY ERROR
                    echo $cUs_json->error;
                    $cUs_api->resetData(); //RESET DATA
                endif;
            break;

        endswitch;
     else:
         //echo 3;//API ERROR
         echo $cUs_json->error;
         $cUs_api->resetData(); //RESET DATA
     endif;
    
    die();
}

// loginAlreadyUser handler function...
add_action('wp_ajax_ACloginAlreadyUser', 'loginAlreadyUserAC_callback');
function loginAlreadyUserAC_callback() {
    
    $cUs_api = new cUsACComAPI();
    $cUs_email = $_REQUEST['email'];
    $cUs_pass = $_REQUEST['pass'];
    $postData = get_option('cUsAC_settings'); //get the saved user data
    $cusAPIresult = $cUs_api->getFormKeyAPI($cUs_email, $cUs_pass); //api hook;
    if($cusAPIresult){
        $cUs_json = json_decode($cusAPIresult);

        switch ( $cUs_json->status  ) :
            case 'success':
                
                $cUsMC_API_UPDATE = $cUs_api->updateDeliveryOptions($cUs_email, $cUs_pass, $postData, $cUs_json->form_key); //UPDATE DELIVERY OPTIONS;
                
                update_option('cUsAC_settings_form_key', $cUs_json->form_key);
                
                $aryFormOptions = array( //DEFAULT SETTINGS / FIRST TIME
                    'tab_user'          => 1,
                    'cus_version'       => 'tab'
                ); 
                update_option('cUsAC_FORM_settings', $aryFormOptions );//UPDATE FORM SETTINGS
                echo 1;
                break;

            case 'error':
                echo $cUs_json->error;
                break;
        endswitch;
    }
    
    die();
}

// logoutUser handler function...
add_action('wp_ajax_AClogoutUser', 'logoutUserAC_callback');
function logoutUserAC_callback() {
    $cUs_api = new cUsACComAPI();
    echo 1; //none list
    $cUs_api->resetData(); //RESET DATA
    
    die();
}
// sendTemplateID handler function...
add_action('wp_ajax_ACsendTemplateID', 'sendTemplateIDAC_callback');
function sendTemplateIDAC_callback() {
    echo 1; //none list
    
    die();
}


?>
