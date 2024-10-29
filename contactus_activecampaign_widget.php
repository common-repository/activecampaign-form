<?php
/*
  The ActiveCampaign Form by ContactUs.com.
 */

//ActiveCampaign Subscribe Box widget extend 

class contactus_activecampaign_Widget extends WP_Widget {

	function contactus_activecampaign_Widget() {
		$widget_ops = array( 
			'description' => __('Displays ActiveCampaign Form by ContactUs.com', 'contactus_ac')
		);
		$this->WP_Widget('contactus_activecampaign_Widget', __('ActiveCampaign Form by ContactUs.com', 'contactus_ac'), $widget_ops);
	}

	function widget( $args, $instance ) {
		if (!is_array($instance)) {
			$instance = array();
		}
		contactus_activecampaign_signup_form(array_merge($args, $instance));
	}
};

function contactus_activecampaign_signup_form($args = array()) {
    extract($args);
    $cUsAC_form_key = get_option('cUsAC_settings_form_key'); //get the saved activecampaign apikey
    
    if(strlen($cUsAC_form_key)):
        $xHTML  = '<aside id="cUsAC_form_widget" style="clear:both;min-height:260px;margin:10px auto;">';
        $xHTML .= '<script type="text/javascript" src="//cdn.contactus.com/cdn/forms/'. $cUsAC_form_key .'/inline.js"></script>';
        $xHTML .= '</aside>';
        
        echo $xHTML;
    endif;
};  

?>
