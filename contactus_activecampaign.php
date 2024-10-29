<?php
/*
  Plugin Name: ActiveCampaign Form by ContactUs.com
  Plugin URI:  http://help.contactus.com/entries/24878807-Adding-the-ActiveCampaign-Form-Plugin-for-WordPress
  Description: The ActiveCampaign Form Plugin by ContactUs.com
  Author: contactus.com
  Version: 1.0.3
  Author URI: http://www.contactus.com/
  License: GPLv2 or later
*/

/*  Copyright 2013  ContactUs.com  ( email: support@contactuscom.zendesk.com )

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if(!class_exists('ActiveCampaign')){
    require_once 'libs/activecampaign-api/ActiveCampaign.class.php';
}

if (!class_exists('cUsACComAPI')) {
    require_once('libs/cusACAPI.class.php');
}

//AJAX REQUEST HOOKS
require_once('contactus_activecampaign_ajax_request.php');

if (!function_exists('cUsAC_admin_header')) {

    function cUsAC_admin_header() {
        global $current_screen;

        if ($current_screen->id == 'toplevel_page_cUs_activecampaign_plugin') {
            
            wp_enqueue_style('cUs_Styles', plugins_url('style/cUsAC_style.css', __FILE__), false, '1');

            wp_register_script( 'cUs_Scripts', plugins_url('scripts/cUsAC_scripts.js?pluginurl=' . dirname(__FILE__), __FILE__), array(), '1.0', true);
            wp_localize_script( 'cUs_Scripts', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

            wp_enqueue_script('jquery'); //JQUERY WP CORE

            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-button');
            wp_enqueue_script('jquery-ui-selectable');

            wp_enqueue_script('cUs_Scripts');
        }
    }

}
add_action('admin_enqueue_scripts', 'cUsAC_admin_header'); // cUsAC_admin_header hook
//END CONTACTUS.COM PLUGIN STYLES CSS
// Add option page in admin menu
if (!function_exists('cUsAC_admin_menu')) {

    function cUsAC_admin_menu() {
        add_menu_page('ActiveCampaign Form Plugin by ContactUs.com', 'ActiveCampaign Form', 'edit_themes', 'cUs_activecampaign_plugin', 'cUsAC_menu_render', plugins_url("style/images/Icon-Small_16.png", __FILE__));
    }

}
add_action('admin_menu', 'cUsAC_admin_menu'); // cUsAC_admin_menu hook

function cUsAC_plugin_links($links, $file) {
    if ($file == plugin_basename(__FILE__)) {
        $links[] = '<a target="_blank" style="color: #42a851; font-weight: bold;" href="http://help.contactus.com/">' . __("Get Support", "cUsAC_plugin") . '</a>';
    }
    return $links;
}

add_filter('plugin_row_meta', 'cUsAC_plugin_links', 10, 2);


/*
 * Register the settings
 */
add_action('admin_init', 'contactus_ac_register_settings');

function contactus_ac_register_settings() {
    return false;
}

function contactus_ac_settings_validate($args) {

    //make sure you return the args
    return $args;
}

//Display the validation errors and update messages

/*
 * Admin notices
 */

function contactus_ac_admin_notices() {
    settings_errors();
}

add_action('admin_notices', 'contactus_ac_admin_notices');

function contactUs_AC_JS_into_html() {
    if (!is_admin()) {
        $formOptions = get_option('cUsAC_FORM_settings'); //GET THE NEW FORM OPTIONS
        $getTabPages = get_option('cUsAC_settings_tabpages');
        $getInlinePages = get_option('cUsAC_settings_inlinepages');
        $form_key = get_option('cUsAC_settings_form_key');
        $pageID = get_the_ID();

        $boolTab = $formOptions['tab_user'];
        $cus_version = $formOptions['cus_version'];

        $userJScode = '<script type="text/javascript" src="//cdn.contactus.com/cdn/forms/' . $form_key . '/contactus.js"></script>';

        //the theme must have the wp_footer() function included
        //include the contactUs.com JS before the </body> tag
        switch ($cus_version) {
            case 'tab':
                if (strlen($form_key) && $boolTab):
                    echo $userJScode;
                endif;
                break;
            case 'selectable':
                if (strlen($form_key) && is_array($getTabPages) && in_array($pageID, $getTabPages)):
                    echo $userJScode;
                elseif (is_home()):
                    if (in_array('home', $getTabPages)) {
                        echo $userJScode;
                    } elseif (in_array('home', $getInlinePages)) {
                        ?>
                        <script>
                            try {
                                var eL = $('#content');
                                var html = eL.html();
                                var key = '<?php echo $form_key; ?>';
                                var iFm = '<iframe src="//cdn.contactus.com/cdn/forms/' + key + '/" width="100%" height="300" ></iframe>'

                                eL.html(html + iFm);

                            } catch (err) {
                                console.log('Oops, something wrong happened, please try add a div with #contend ID tag!');
                            }
                            
                        </script>
                    <?php
                    }

                endif;
                break;
        }
    }
}

add_action('wp_footer', 'contactUs_AC_JS_into_html'); // ADD JS BEFORE BODY TAG

function cus_ac_inline_home() {

    $formOptions = get_option('cUsAC_FORM_settings'); //GET THE NEW FORM OPTIONS
    $form_key = get_option('cUsAC_settings_form_key');
    $cus_version = $formOptions['cus_version'];
    if ($cus_version == 'inline' || $cus_version == 'selectable') :
        $inlineJS_output = '<div style="min-height: 300px; width: 350px;clear:both;"><script type="text/javascript" src="//cdn.contactus.com/cdn/forms/' . $form_key . '/inline.js"></script></div>';
    else:
        $inlineJS_output = '';
    endif;

    echo $inlineJS_output;
}

function cus_ac_shortcode_cleaner() {
    $aryPages = get_pages();
    foreach ($aryPages as $oPage) {
        $pageContent = $oPage->post_content;
        $pageContent = str_replace('[show-activecampaign-inline-form]', '', $pageContent);
        $aryPage = array();
        $aryPage['ID'] = $oPage->ID;
        $aryPage['post_content'] = $pageContent;
        wp_update_post($aryPage);
    }
}

add_shortcode("show-activecampaign-inline-form", "cus_ac_shortcode_handler"); //[show-contactus.com-form]

function cus_ac_shortcode_handler() {

    $formOptions = get_option('cUsAC_FORM_settings'); //GET THE NEW FORM OPTIONS
    $form_key = get_option('cUsAC_settings_form_key');
    $cus_version = $formOptions['cus_version'];
    if ($cus_version == 'inline' || $cus_version == 'selectable') :
        $inlineJS_output = '<div style="min-height: 300px; width: 350px;clear:both;"><script type="text/javascript" src="//cdn.contactus.com/cdn/forms/' . $form_key . '/inline.js"></script></div>';
    else:
        $inlineJS_output = '';
    endif;

    return $inlineJS_output;
}

add_shortcode("show-activecampaign-tab-button-form", "cus_ac_shortcode_Tab_hook"); //[show-contactus.com-form]

function cus_ac_shortcode_Tab_handler() {

    $formOptions = get_option('cUsAC_FORM_settings'); //GET THE NEW FORM OPTIONS
    $form_key = get_option('cUsAC_settings_form_key');
    $cus_version = $formOptions['cus_version'];
    if ($cus_version == 'tab' || $cus_version == 'selectable') :
        $inlineJS_output = '<script type="text/javascript" src="//cdn.contactus.com/cdn/forms/' . $form_key . '/contactus.js"></script>';
    else:
        $inlineJS_output = '';
    endif;

    return $inlineJS_output;
}

function cus_ac_shortcode_Tab_hook() {

    add_action('wp_footer', 'cus_ac_shortcode_Tab_handler');
}

function cus_ac_shortcode_add($inline_req_page_id) {

    if ($inline_req_page_id != 'home'):
        $oPage = get_page($inline_req_page_id);
        $pageContent = $oPage->post_content;
        $pageContent = $pageContent . "\n[show-activecampaign-inline-form]";
        $aryPage = array();
        $aryPage['ID'] = $inline_req_page_id;
        $aryPage['post_content'] = $pageContent;
        return wp_update_post($aryPage);
    endif;
}

$cus_dirbase = trailingslashit(basename(dirname(__FILE__)));
$cus_dir = trailingslashit(WP_PLUGIN_DIR) . $cus_dirbase;
$cus_url = trailingslashit(WP_PLUGIN_URL) . $cus_dirbase;
define('CUSAC_DIR', $cus_dir);
define('CUSAC_URL', $cus_url);

// WIDGET CALL
include_once('contactus_activecampaign_widget.php');

function contactus_ac_register_widgets() {
    register_widget('contactus_activecampaign_Widget');
}

add_action('widgets_init', 'contactus_ac_register_widgets');

//CONTACTUS.COM ADD FORM TO PLUGIN PAGE
if (!function_exists('cUsAC_menu_render')) {

    function cUsAC_menu_render() {

        $options = get_option('cUsAC_settings'); //get the values, wont work the first time
        $formOptions = get_option('cUsAC_FORM_settings'); //GET THE NEW FORM OPTIONS
        $cus_version = $formOptions['cus_version'];
        global $current_user;
        get_currentuserinfo();
        $plugins_url = plugins_url();
        if (!is_array($options)) {
            settings_fields('cUsAC_settings');
            do_settings_sections(__FILE__);
        }

        if (isset($_REQUEST['option'])):
            switch ($_REQUEST['option']):

                case 'settings': //SAVING FORM SETTINGS TAB - INLINE - SELECTION 
                    ?>
                    <script>jQuery(document).ready(function($) {
                                                try {
                                                    jQuery("#cUs_tabs").tabs({active: 1})
                                                } catch (err) {
                                                    console.log(err);
                                                }
                                            });</script><?php
                                            
                    if (is_array($options)): //ALREADY LOGGED
                        $settingsMessage = '<div id="message" class="updated fade"><p>Done! Your configuration has been saved correctly.</p></div>';
                        $boolTab = $_REQUEST['tab_user'];

                        $aryFormOptions = array(
                            'tab_user' => $boolTab,
                            'cus_version' => $_REQUEST['cus_version']
                        );

                        delete_option('cUsAC_FORM_settings');
                        delete_option('cUsAC_settings_inlinepages');
                        delete_option('cUsAC_settings_tabpages');
                        update_option('cUsAC_FORM_settings', $aryFormOptions); //UPDATE FORM SETTINGS

                        cus_ac_shortcode_cleaner();
                        $formOptions = get_option('cUsAC_FORM_settings'); //GET THE NEW FORM OPTIONS
                        $cus_version = $formOptions['cus_version'];

                        switch ($_REQUEST['cus_version']):
                            case 'selectable':
                                if (isset($_REQUEST['pages'])):
                                    $aryPages = $_REQUEST['pages'];
                                    $aryInlinePages = array();
                                    $aryTabPages = array();
                                    foreach ($aryPages as $pageID => $version) {
                                        if ($version == 'inline') {
                                            $aryInlinePages[] = $pageID;
                                            cus_ac_shortcode_add($pageID);
                                        } elseif ($version == 'tab') {
                                            $aryTabPages[] = $pageID;
                                        }
                                    }
                                    update_option('cUsAC_settings_inlinepages', $aryInlinePages); //UPDATE OPTIONS
                                    update_option('cUsAC_settings_tabpages', $aryTabPages); //UPDATE OPTIONS
                                endif;
                                break;
                        endswitch;

                    endif;
                    break;

            endswitch;
        endif;
        ?>
        <script>var posturl = '<?php echo plugins_url('ajax-request.php', __FILE__); ?>';</script>
        <div class="plugin_wrap">
            <div class="cUsAC_header">
                <h2>ActiveCampaign Form <a href="http://www.contactus.com" target="_blank">by ContactUs.com</a> </h2>
            </div> 
            <div class="cUsAC_formset">
                <div id="cUs_tabs">
                    <ul> <?php $formkey = get_option('cUsAC_settings_form_key'); ?>
                        <?php if (!$formkey): ?><li><a href="#tabs-1">ActiveCampaign Form Plugin </a></li><?php endif; ?>
                        <?php if (is_array($options) && $formkey): ?><li><a href="#tabs-2">ActiveCampaign Plugin Settings</a></li><?php endif; ?>
                        <?php if (is_array($options) && $formkey): ?><li><a href="#tabs-3">Form Settings</a></li><?php endif; ?>
                        <?php if (is_array($options) && $formkey): ?><li><a href="#tabs-4">Form Examples</a></li><?php endif; ?>
                        <?php if (is_array($options) && $formkey): ?><li><a href="#tabs-5">Tab Examples</a></li><?php endif; ?>
                    </ul>

                    <?php
                   
                    if (!$formkey)://NOT LOGGED ?>
                        
                        <div id="tabs-1">

                            <div class="first_step">
                                <h2>Are You Already a ActiveCampaign User?</h2>
                                <input id="ac_yes" class="btn orange" value="Yes, Get Me My Form" type="button" />
                                <a id="mc_no" class="btn gray ac_lnk" href="http://www.activecampaign.com" target="_blank">No, Signup for ActiveCampaign</a>
                                <h3>Note:</h3>
                                <p>The ContactUs.com ActiveCampaign Form Plugin is designed for existing ActiveCampaign users. If you are not yet a ActiveCampaign user, click on the "No, Signup for ActiveCampaign" button above.</p>

                            </div>

                            <div id="cUsAC_acsettings" class="hidden">

                                <h2>ActiveCampaign Form Plugin Configuration</h2>
                                <div class="loadingMessage"></div>
                                <div class="advice_notice"></div>

                                <form method="post" action="admin.php?page=cUs_activecampaign_plugin" id="cUsAC_sendkey" name="cUsAC_sendkey" class="steps step1" onsubmit="return false;">
                                    <h3 class="step_title"><span>1</span>ContactUs.com Signup</h3>
                                    <ul class="options">
                                        <li>Log into your ActiveCampaign.com account and go to<strong> Your Settings </strong> > <strong> API </strong>:</li>
                                        <img src="<?php echo plugins_url('style/images/cUs_ActiveCampaign2.jpeg', __FILE__) ?>"><br>
                                        <img src="<?php echo plugins_url('style/images/cUs_ActiveCampaign1.jpeg', __FILE__) ?>"><p style="font-size: x-small; padding-left: 180px;">These images are from http://www.activecampaign.com/help/using-the-api</p>
                                        <div class="loadingMessagefs"></div>
                                        <div class="advice_noticefs"></div>   
                                        <li>If you have problems getting to your personal settings, you can view more detailed instructions at:<a href="http://www.activecampaign.com/help/using-the-api" target="_blank" class="blue_link">Show instructions</a> </li>
                                        <li>From within your ActiveCampaign.com account, copy your API URL and API Key into the boxes below and click "Continue to Step 2" below.</li>
                                    </ul>
                                    <table class="form-table">
                                         <tr>
                                            <th><label class="labelform" for="apikey">Enter your API Key :</label></th>
                                            <td><input class="inputform" name="apikey" id="apikey" type="text" value=""></td>
                                        </tr> 
                                        <tr>
                                            <th><label class="labelform" for="apiurl">Enter your API URL address :</label></th>
                                            <td><input class="inputform" name="apiurl" id="apiurl" type="text" value=""></td>
                                        </tr>
                                        <tr>
                                            <th></th><td><input id="craccbtn" class="btn orange sendapikey" value="Continue to Step 2" type="button" /></td>
                                        </tr>
                                        <tr>
                                            <th></th><td>We will use your API information to download your ActiveCampaign lists and account information so you can configure this plugin.</td>
                                        </tr>
                                    </table>
                                </form>
                                
                                <form method="post" action="admin.php?page=cUs_activecampaign_plugin" id="cUsAC_sendlistid" name="cUsAC_sendkey" class="steps step2" onsubmit="return false;">
                                    <h3 class="step_title"><span>2</span>Select Your ActiveCampaign Client List</h3>

                                    <table class="form-table">
                                        <tr>
                                            <th><label class="labelform" for="listid">Select your Client List</label></th>
                                            <td>
                                                <select name="listid" id="listid"></select>
                                            </td>
                                            <th class="lastname"><label class="labelform" for="acc_surname">Enter your surname :</label></th>
                                            <td>
                                                <input type="text" id="acc_surname" name="acc_surname" value="<?php echo $current_user->user_lastname; ?>" class="lastname" required="true">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th><td><input id="craccbtn" class="btn orange sendlistid" value="Continue to Step 3" type="button" /></td>
                                        </tr>
                                    </table>
                                </form>

                                <form method="post" action="admin.php?page=cUs_activecampaign_plugin" id="cUsAC_template" name="cUsAC_sendkey" class="steps step3dis temp" onsubmit="return false;">
                                    <h3 class="step_title"><span>3</span>Select Your Form Template</h3>
                                    <div class="previews_cont">
                                        <ul id="selectable">
                                            <li class="ui-state-default"><a class="templates_gallery" title="ContactUs.com Form Template" data-fancybox-group="forms_gallery" href="<?php echo plugins_url('style/images/form_preview/large/f1.png', __FILE__) ?>"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f1.png', __FILE__) ?>" alt="ContactUs.com Form Template" /></a></li>
                                            <li class="ui-state-default"><a class="templates_gallery" title="ContactUs.com Form Template" data-fancybox-group="forms_gallery" href="<?php echo plugins_url('style/images/form_preview/large/f2.png', __FILE__) ?>"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f2.png', __FILE__) ?>" /></a></li>
                                            <li class="ui-state-default"><a class="templates_gallery" title="ContactUs.com Form Template" data-fancybox-group="forms_gallery" href="<?php echo plugins_url('style/images/form_preview/large/f3.png', __FILE__) ?>"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f3.png', __FILE__) ?>" /></a></li>
                                            <li class="ui-state-default"><a class="templates_gallery" title="ContactUs.com Form Template" data-fancybox-group="forms_gallery" href="<?php echo plugins_url('style/images/form_preview/large/f4.png', __FILE__) ?>"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f4.png', __FILE__) ?>" /></a></li>
                                            <li class="ui-state-default"><a class="templates_gallery" title="ContactUs.com Form Template" data-fancybox-group="forms_gallery" href="<?php echo plugins_url('style/images/form_preview/large/f5.png', __FILE__) ?>"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f5.png', __FILE__) ?>" /></a></li>
                                            <li class="ui-state-default"><a class="templates_gallery" title="ContactUs.com Form Template" data-fancybox-group="forms_gallery" href="<?php echo plugins_url('style/images/form_preview/large/f6.png', __FILE__) ?>"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f6.png', __FILE__) ?>" /></a></li>
                                            <li class="ui-state-default"><a class="templates_gallery" title="ContactUs.com Form Template" data-fancybox-group="forms_gallery" href="<?php echo plugins_url('style/images/form_preview/large/f7.png', __FILE__) ?>"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f7.png', __FILE__) ?>" /></a></li>
                                            <li class="ui-state-default"><a class="templates_gallery" title="ContactUs.com Form Template" data-fancybox-group="forms_gallery" href="<?php echo plugins_url('style/images/form_preview/large/f8.png', __FILE__) ?>"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f8.png', __FILE__) ?>" /></a></li>
                                        </ul>
                                    </div>
                                    <table class="form-table">
                                        <tr>
                                            <th></th><td><input id="craccbtn" class="btn orange sendtemplate" value="Review your Information" type="button" /></td>
                                        </tr>
                                        <input type="hidden" value="" name="templateid" id="templateid" />
                                        <input type="hidden" value="sendtemplateid" name="option" />
                                    </table>
                                </form>

                                <form method="post" action="admin.php?page=cUs_activecampaign_plugin" id="cUsAC_data" name="cUsAC_sendkey" class="steps step3" onsubmit="return false;">
                                    <h3 class="step_title">Login to your ContactUs.com Account</h3>
                                    <table class="form-table">
                                        <tr>
                                            <th></th><td><p class="validateTips">All form fields are required.</p></td>
                                        <tr>
                                        <tr>
                                            <th><label class="labelform" for="login_email">Email</label><br>
                                            <td><input class="inputform" name="cUsAC_settings[login_email]" id="login_email" type="text" value="<?php echo (strlen($cUs_email)) ? $cUs_email : ''; ?>"></td>
                                        </tr>
                                        <tr>
                                            <th><label class="labelform" for="user_pass">Password</label></th>
                                            <td><input class="inputform" name="cUsAC_settings[user_pass]" id="user_pass" type="password" value="<?php echo (strlen($cUs_pass)) ? '--------' : ''; ?>"></td>
                                        </tr>
                                        <tr><th></th>
                                            <td>
                                                <input id="loginbtn" class="btn orange" value="Login" type="submit">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <td>
                                                <a href="https://www.contactus.com/client-login.php" target="_blank">I forgot my password</a>
                                            </td>
                                        </tr>

                                    </table>
                                </form>

                            </div>



                        </div>
        <?php elseif ($formkey): ?>

                        <script>jQuery(document).ready(function($) {
                            try {
                                jQuery("#cUs_tabs").tabs({active: 1})
                            } catch (err) {
                                console.log(err);
                            }
                        });</script>

                        <div id="tabs-2">
                            <form method="post" action="admin.php?page=cUs_activecampaign_plugin" id="cUsAC_data" name="cUsAC_sendkey" class="steps step5 mainWindow" onsubmit="return false;">
                                <h3 class="step_title">Your ContactUs.com Account</h3>
                                <table class="form-table">
                                    <tr>
                                        <th><label class="labelform">Names</label><br>
                                        <td><span class="cus_names"><?php echo $options[fname]; ?> <?php echo $options[lname]; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th><label class="labelform">Email</label><br>
                                        <td><span class="cus_email"><?php echo $options[email]; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th><label class="labelform">ActiveCampaign API KEY</label><br>
                                        <td><span class="cus_apikey"><?php echo $options[AC_apikey]; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th><label class="labelform">ActiveCampaign Delivery List</label><br>
                                        <td><span class="cus_list"><?php echo $options[acListName]; ?>  &nbsp; [ <?php echo $options[listID]; ?> ] </span></td>
                                    </tr>
                                    <tr><th></th>
                                        <td>
                                            <hr/>
                                            <input id="logoutbtnAC" class="btn orange" value="Unlink Account" type="button">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div>

                        <div id="tabs-3">
                            <h2>Form Settings</h2>
            <?php echo $settingsMessage; ?>

                            <div class="versions_options">
                                <table class="form-table">
                                    <tr>
                                        <th>Choose Your Implementation</th>
                                        <td>
                                            <span class="message"><?php _e("Would you like tabs on all of your pages or would you like to select which pages you want tabs or newsletter forms to appear?", 'cus_plugin'); ?></span><br/>
                                            
                                            <select name="form_version" class="form_version" <?php echo ($userStatus == 'inactive') ? 'disabled' : ''; ?>>
                                                <option value="tab_version" <?php echo ( $cus_version == 'tab' ) ? 'selected="selected"' : ''; ?>>Tabs on all</option>
                                                <option value="select_version" <?php echo ( $cus_version == 'selectable' ) ? 'selected="selected"' : ''; ?>>Let me pick</option>
                                            </select>

                                        </td>
                                    </tr>
                                </table>
                                <hr/>
                            </div>


                            <form method="post" action="admin.php?page=cUs_activecampaign_plugin" id="cUs_button" class="cus_versionform tab_version <?php echo ( strlen($cus_version) && $cus_version != 'tab') ? 'hidden' : ''; ?>" name="cUs_button">
                                <table class="form-table">
                                    <tr>
                                        <th><?php _e("Contact Us Tab Button Enabled :", 'cus_plugin'); ?> </th>
                                        <td>
                                            <select id="tab_user" name="tab_user" <?php echo ($userStatus == 'inactive') ? 'disabled' : ''; ?> >
                                                <option <?php echo ($formOptions[tab_user] == 1) ? 'selected="selected"' : ''; ?>value="1">Yes</option>
                                                <option <?php echo ($formOptions[tab_user] == 0) ? 'selected="selected"' : ''; ?> value="0">No</option>
                                            </select>
                                            <br/><span><?php _e("You can manage the visibility of the ContactUs Button Tab", 'cus_plugin'); ?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <td>
                                            <input type="submit" style='display: none' class="btn orange displaybtn" value="<?php _e('Save Changes') ?>" />
                                        </td>
                                    </tr>
                                </table>

                                <input type="hidden" name="cus_version" value="tab" />
                                <input type="hidden" value="settings" name="option" />
                                <h3>Notice:</h3>
                                <p> Your default theme must have the <b>"wp_footer()"</b> function added.</p>
                            </form>


                            <form method="post" action="admin.php?page=cUs_activecampaign_plugin" id="cUs_selectable" class="cus_versionform select_version <?php echo (!strlen($cus_version) || $cus_version == 'tab') ? 'hidden' : ''; ?>" name="cUs_selectable">
                                <h3>Page Selection</h3>
                                <div class="pageselect_cont">
            <?php
            $mypages = get_pages(array('parent' => 0, 'sort_column' => 'post_date', 'sort_order' => 'desc'));
            if (is_array($mypages)) :
                $getTabPages = get_option('cUsAC_settings_tabpages');
                $getInlinePages = get_option('cUsAC_settings_inlinepages');
                ?>
                                        <ul class="selectable_pages">
                                            <li class="pages-header">Wordpress pages</li>
                                            <li class="ui-widget-content">
                                                <div class="options home">
                                                    <input type="radio" name="pages[home]" class="home-page" id="pageradio-home" value="tab" <?php echo (is_array($getTabPages) && in_array('home', $getTabPages)) ? 'checked' : '' ?> />
                                                    <label class="label-home" for="pageradio-home">TAB</label>
                                                    <input type="radio" name="pages[home]" value="inline" id="pageradio-home-2" class="home-page" <?php echo (is_array($getInlinePages) && in_array('home', $getInlinePages)) ? 'checked' : '' ?> />
                                                    <label class="label-home" for="pageradio-home-2">INLINE</label>
                                                    <a class="ui-state-default ui-corner-all pageclear-home" href="javascript:;" title="Clear Home page settings"><label class="ui-icon ui-icon-circle-close">&nbsp;</label></a>
                                                </div>
                                                <div class="page_title">
                                                    <span class="bullet ui-icon ui-icon-circle-zoomin">
                                                        <a target="_blank" href="<?php echo get_option('home'); ?>" title="Home Preview">&nbsp;</a>
                                                    </span>
                                                    <span class="title">Home Page</span>
                                                </div>
                                            </li>
                                            <script>
                    jQuery('.pageclear-home').click(function() {
                    jQuery('.home-page').removeAttr('checked');
                    jQuery('.label-home').removeClass('ui-state-active');
                });
                                            </script>
                                            <?php foreach ($mypages as $page) : ?>
                                                <li class="ui-widget-content">
                                                    <div class="options">
                                                        <input type="radio" name="pages[<?php echo $page->ID; ?>]" value="tab" id="pageradio-<?php echo $page->ID; ?>-1" class="<?php echo $page->ID; ?>-page" <?php echo (is_array($getTabPages) && in_array($page->ID, $getTabPages)) ? 'checked' : '' ?> />
                                                        <label class="label-<?php echo $page->ID; ?>" for="pageradio-<?php echo $page->ID; ?>-1">TAB</label>
                                                        <input type="radio" name="pages[<?php echo $page->ID; ?>]" value="inline" id="pageradio-<?php echo $page->ID; ?>-2" class="<?php echo $page->ID; ?>-page" <?php echo (is_array($getInlinePages) && in_array($page->ID, $getInlinePages)) ? 'checked' : '' ?> />
                                                        <label class="label-<?php echo $page->ID; ?>" for="pageradio-<?php echo $page->ID; ?>-2">INLINE</label>
                                                        <a class="ui-state-default ui-corner-all pageclear-<?php echo $page->ID; ?>" href="javascript:;" title="Clear <?php echo $page->post_title; ?> page settings"><label class="ui-icon ui-icon-circle-close">&nbsp;</label></a>
                                                    </div>
                                                    <div class="page_title">
                                                        <span class="bullet ui-icon ui-icon-circle-zoomin">
                                                            <a target="_blank" href="<?php echo get_permalink($page->ID); ?>" title="Preview <?php echo $page->post_title; ?> page">&nbsp;</a>
                                                        </span>
                                                        <span class="title"><?php echo $page->post_title; ?></span>
                                                    </div>
                                                </li>
                                                <script>
                                                    jQuery('.pageclear-<?php echo $page->ID; ?>').click(function() {
                                                        jQuery('.<?php echo $page->ID; ?>-page').removeAttr('checked');
                                                        jQuery('.label-<?php echo $page->ID; ?>').removeClass('ui-state-active');
                                                    });
                                                </script>
                                            <?php endforeach; ?>
                                        </ul>
                                        <div class="advanced_info">
                                            <h3>ADVANCED ONLY!</h3>
                                            <div>
                                                <div class="terminology_c">
                                                    <h4>Copy this code into your template to place the form wherever you want it.  If you use this advanced method, do not select any pages from the section on the left or you may end up with the form displayed on your page twice.</h4>
                                                    <ul class="hints">
                                                        <li><b>Inline</b><br/><code>&#60;&#63;php echo do_shortcode("[show-activecampaign-inline-form]"); &#63;&#62;</code></li>
                                                        <li><b>Widget Tool</b><br/><p>Go to <a href="widgets.php"><b>Widgets here </b></a> and drag the ContactUs.com ActiveCampaign Newsletter widget into one of your widget areas</p></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="submit_data">
                                            <hr />
                                            <input type="submit" class="btn orange save_page" value="<?php _e('Save Changes') ?>" />
                                            <br/><p><?php _e("Do you need to create a new page? Click ", 'cus_plugin'); ?><a href="post-new.php?post_type=page">here.</a></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" name="cus_version" value="selectable" />
                                <input type="hidden" value="settings" name="option" />
                            </form>
                            <div id="terminology">
                                <h3>Terminology</h3>
                                <div>
                                    <div class="terminology_c">
                                        <ul class="hints">
                                            <li><b>Tab</b> - Uses tab callouts with “Contact Us” messaging on the page margins across your website. When pressed, contact form appears as a lightbox above the underlying page.</li>
                                            <li><b>Custom</b> - You can also choose a “Custom” implementation in order to a) use a combination of Tab and Inline, and b) choose specific pages on your site to place Tab or Inline forms.</li>
                                        </ul>
                                    </div>
                                </div>
                                <h3>Helpful Hints</h3>
                                <div>
                                    <div class="terminology_c">
                                        <ul class="hints">
                                            <li>Take a moment to log into ContactUs.com (with the user name/password you registered with) to see the full set of solutions offered.</li>
                                            <li>You can choose different form design templates from the ContactUs.com library by logging into your account at <a href="http://www.contactus.com" target="_blank">www.ContactUs.com</a></li>
                                            <li>You can also generate leads and newsletter signups from your Facebook page by enabling the ContactUs.com Facebook App.  It only takes two clicks!</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tabs-4">
                            <div class="versions_options">
                                <h2>Change Design Instructions</h2>
                                <p>We have sent you an email with a temporary password so you can log into <a href="https://www.contactus.com" target="_blank">www.contactus.com</a> and fully configure your form.</p>
                                <p>Once you log in you will be able to change your form template, tab template, and much more.</p>
                                <div id="form_examples">
                                    <h3>Form Examples</h3>
                                    <div>
                                        <div class="terminology_c">
                                            <ul id="sortable">
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f1.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f2.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f3.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f3.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f5.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f6.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f7.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f8.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f9.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f10.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f11.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f12.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f13.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/f14.png', __FILE__) ?>" alt="ContactUs.com Newsletter Form Template" /></li>
                                            </ul>
                                        </div>
                                    </div>

                                </div> 
                            </div>
                        </div>
                        <div id="tabs-5">
                            <div class="versions_options">
                                <h2>Change Design Instructions</h2>
                                <p>We have sent you an email with a temporary password so you can log into <a href="https://www.contactus.com" target="_blank">www.contactus.com</a> and fully configure your form.</p>
                                <p>Once you log in you will be able to change your form template, tab template, and much more.</p>
                                <div id="tab_examples">
                                    <h3>Button Tab Examples</h3>
                                    <div>
                                        <div class="terminology_c">
                                            <ul id="sortable" class="tabs">
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/t1.png', __FILE__) ?>" alt="ContactUs.com Newsletter Button Tab" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/t2.png', __FILE__) ?>" alt="ContactUs.com Newsletter Button Tab" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/t3.png', __FILE__) ?>" alt="ContactUs.com Newsletter Button Tab" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/t4.png', __FILE__) ?>" alt="ContactUs.com Newsletter Button Tab" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/t5.png', __FILE__) ?>" alt="ContactUs.com Newsletter Button Tab" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/t6.png', __FILE__) ?>" alt="ContactUs.com Newsletter Button Tab" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/t7.png', __FILE__) ?>" alt="ContactUs.com Newsletter Button Tab" /></li>
                                                <li class="ui-state-default"><img src="<?php echo plugins_url('style/images/form_preview/thumb/t8.png', __FILE__) ?>" alt="ContactUs.com Newsletter Button Tab" /></li>
                                            </ul>
                                        </div>
                                    </div>

                                </div> 
                            </div>
                        </div>

                    <?php endif; ?>

                </div>

            </div>
            <a href="http://www.contactus.com" target="_blank" class="powered">Powered By ContactUs.com</a>
        </div>

        <?php
    }

}
?>