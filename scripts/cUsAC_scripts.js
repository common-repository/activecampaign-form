jQuery(document).ready(function($) {
    
    var cUsAC_myjq = jQuery.noConflict();

    cUsAC_myjq(window).error(function(e){
        e.preventDefault();
    });
    
    var fname = cUsAC_myjq( "#fname" ),
    lname = cUsAC_myjq( "#lname" ),
    remail = cUsAC_myjq( "#remail" ),
    pass1 = cUsAC_myjq( "#pass1" ),
    pass2 = cUsAC_myjq( "#pass2" ),
    email = cUsAC_myjq( "#login_email" ),
    password = cUsAC_myjq( "#user_pass" ),
    allFields = cUsAC_myjq( [] ).add(email).add(password),
    allFields_reg = cUsAC_myjq( [] ).add(fname).add(lname).add(remail).add(pass1).add(pass2),
    tips = cUsAC_myjq( ".validateTips" );
    
    function updateTips( t ) {
        tips
        .text( t )
        .addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }

    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }

    function comparePass( o, n ) {
        if ( o.val() != n.val() ) {
            o.addClass( "ui-state-error" );
            updateTips( "Password don't match." );
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o ) ) ) {
            return false;
        } else {
            return true;
        }
    }
    
    
    cUsAC_myjq('#cUs_login').submit(function(){
        var bValid = true;
        allFields.removeClass( "ui-state-error" );
        bValid = bValid && checkLength( email, "email", 6, 80 );
        bValid = bValid && checkLength( password, "password", 8, 16 );
        // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
        bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Please enter a valid email eg. sergio@contactus.com" );
        //bValid = bValid && checkRegexp( password, /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/, "Password field only allow : A-z 0-9" );
        if ( bValid ) {
            return true;
            cUsAC_myjq( this ).dialog( "close" );
        }else{
            return false;
        }
    });
    cUsAC_myjq('#cUs_registform').submit(function(){
        var bValid = true;
        allFields_reg.removeClass( "ui-state-error" );
        bValid = bValid && checkLength( fname, "first name", 2, 16 );
        bValid = bValid && checkLength( lname, "last name", 2, 16 );
        bValid = bValid && checkLength( remail, "email", 6, 80 );
        bValid = bValid && checkLength( pass1, "password", 8, 16 );
        bValid = bValid && comparePass( pass1, pass2 );
        bValid = bValid && checkRegexp( remail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
        //bValid = bValid && checkRegexp( pass1, /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/, "Password field only allow : A-z 0-9" );
        if ( bValid ) {
            return true;
            cUsAC_myjq( this ).dialog( "close" );
        }else{
            return false;
        }
    });
    
    try{
        cUsAC_myjq( "#cUs_tabs" ).tabs({active: false});
        cUsAC_myjq( "#cUs_exampletabs" ).tabs({active: false});

        cUsAC_myjq( '.options' ).buttonset();
        cUsAC_myjq( '#inlineradio' ).buttonset();

        cUsAC_myjq( "#terminology" ).accordion({
            collapsible: true,
            heightStyle: "content",
            active: false,
            icons: { "header": "ui-icon-info", "activeHeader": "ui-icon-arrowreturnthick-1-n" }
        });
        cUsAC_myjq( "#form_examples, #tab_examples" ).accordion({
            collapsible: true,
            heightStyle: "content",
            icons: { "header": "ui-icon-info", "activeHeader": "ui-icon-arrowreturnthick-1-n" }
        });
        
        
        
       
    }catch(err){
        cUsAC_myjq('.advice_notice').html('Oops, something wrong happened, please try again later!').slideToggle().delay(1200).fadeOut(1200);
    }
    
    
    try{
//        cUsAC_myjq( '.examples_gallery, .ui-state-default, .page_title' ).tooltip({
//           track: true
//        });
        cUsAC_myjq("#selectable").selectable({
            selected: function(event, ui) { 
                cUsAC_myjq(ui.selected).addClass("ui-selected").siblings().removeClass("ui-selected");           
            }                   
        });
        
    }catch(err){
        console.log('Please upadate you WP version. ['+ err +']');
    }
    
    
    //SEND API KEY AJAX CALL /////// STEP 1
    try{ 
       cUsAC_myjq('.sendapikey').click(function() {
           
           var acApiKey = cUsAC_myjq('#apikey').val();
           var acApiUrl = cUsAC_myjq('#apiurl').val();
           
           if(!acApiKey.length){
               cUsAC_myjq('.advice_noticefs').html('ActiveCampaign API Key is a required field!').slideToggle().delay(1200).fadeOut(1200);
               cUsAC_myjq('#apikey').focus();
               cUsAC_myjq('.loadingMessagefs').fadeOut();
               
           }else if(!acApiUrl.length){
               cUsAC_myjq('.advice_noticefs').html('ActiveCampaign API URL address is a required field!').slideToggle().delay(1200).fadeOut(1200);
               cUsAC_myjq('#apiurl').focus();
               cUsAC_myjq('.loadingMessagefs').fadeOut(); }
               else{
               
                cUsAC_myjq('.sendapikey').val('Loading . . .').attr({disabled:'disabled'});
                cUsAC_myjq('.loadingMessagefs').show();
                
                cUsAC_myjq.ajax({ type: "POST", url: ajax_object.ajax_url, data: {action: 'checkACapikey', apikey:acApiKey, apiurl:acApiUrl},
                    success: function(data) {

                        switch(data){
                            case '1':
                                
                                message = 'You are already logged into you ActiveCampaign account, please continue with next steps.';
                                cUsAC_myjq('.sendapikey').val('Connected . . .');
                                setTimeout(function(){
                                    getAClist(acApiKey,acApiUrl);
                                },2000)
                                
                            break;
                            case '2':
                                message = 'There something wrong with your ActiveCampaign API Key or API Url address, please try again!';
                                cUsAC_myjq('.advice_noticefs').html(message).show().delay(3000).fadeOut(800);
                                cUsAC_myjq('.sendapikey').val('Continue to Step 2').removeAttr('disabled');
                            break;
                            case '3': 
                                  cUsAC_myjq('#listid').html(data);
                                  cUsAC_myjq('.step1').slideUp().fadeOut();
                                  cUsAC_myjq('.step2').slideDown().delay(800);
                        break;
                        }
                        
                        cUsAC_myjq('.loadingMessagefs').fadeOut();
                        //cUsAC_myjq('.advice_notice').html(message).show().delay(1900).fadeOut(800);

                    },
                    async: false
                });
           }
           
            
       });
       
       function getAClist(acApiKey, acApiUrl){
           if(!acApiKey && !acApiUrl) return false;
           cUsAC_myjq('.loadingMessage').show();
           cUsAC_myjq('.sendapikey').val('Loading Lists. . .');
           cUsAC_myjq.ajax({ type: "POST", url: ajax_object.ajax_url, dataType: 'json', data: {action:'getACList',apikey:acApiKey, apiurl:acApiUrl},
                success: function(data) {
                        if(data.status == 1 ) {
                             var matches = acApiUrl.match(/\/\/+([a-zA-Z1-9]*)/);
                             message = "Seems like you don't have Contact List in you ActiveCampaign Account, please add at least one <a href='http://"+matches[1]+".activehosted.com/admin/main.php?action=list' target='_blank'>here</a> to continue.";
                            cUsAC_myjq('.advice_noticefs').html(message).slideToggle();
                            
                            cUsCtCt_myjq('.sendapikey').val('Reloading . . .');
                            
                            setTimeout(function(){
                                cUsCtCt_myjq('.sendapikey').val('Continue to Step 2').removeAttr('disabled');
                            },3000)
                            
                        }else if (data.status == 2) {
                            message = 'There something wrong with your ActiveCampaign API Key, please try again!';
                            cUsAC_myjq('.advice_notice').html(message).slideToggle().delay(1800).fadeOut(600);
                            
                            setTimeout(function(){
                                cUsCtCt_myjq('.sendapikey').val('Continue to Step 2').removeAttr('disabled');
                            },3000)
                            
                        }else {
                            cUsAC_myjq('#listid').html(data.options);
                            if(data.existlname == 0) { cUsAC_myjq('.lastname').show(); }
                            cUsAC_myjq('.step1').slideUp().fadeOut();
                            cUsAC_myjq('.step2').slideDown().delay(800);
                        }
                        
                            cUsAC_myjq('.loadingMessage').fadeOut();

                },
                async: false
            });
       }
       
    }catch(err){
        cUsAC_myjq('.advice_notice').html('Oops, something wrong happened, please try again later!').slideToggle().delay(1200).fadeOut(1200);
        setTimeout(function(){
            cUsCtCt_myjq('.sendapikey').val('Continue to Step 2').removeAttr('disabled');
        },3000)
    }
    
    
    //SENT LIST ID AJAX CALL /// STEP 2
    try{
        cUsAC_myjq('.sendlistid').click(function() {
           
           var acApiKey = cUsAC_myjq('#apikey').val();
           var acListID = cUsAC_myjq('#listid').val();
           var acListName = cUsAC_myjq('#listid option:selected').text();
           var acSurName = cUsAC_myjq('#acc_surname').val();
           
           cUsAC_myjq('.loadingMessage').show();
           
           if(!acApiKey.length){
               cUsAC_myjq('.advice_notice').html('ActiveCampaign API Key is a required field!').slideToggle().delay(1200).fadeOut(1200);
               cUsAC_myjq('#apikey').focus();
               cUsAC_myjq('.loadingMessage').fadeOut();
                }else if(!acSurName.length && cUsAC_myjq('#acc_surname').is(':visible')){

               cUsAC_myjq('.advice_notice').html('Surname is a required field!').slideToggle().delay(1200).fadeOut(1200);
               cUsAC_myjq('#acc_surname').focus();
               cUsAC_myjq('.loadingMessage').fadeOut();
               
           } else {
                cUsAC_myjq('.sendlistid').val('Loading . . .').attr({disabled:'disabled'});
                cUsAC_myjq.ajax({ type: "POST", url: ajax_object.ajax_url, data: {action:'sendACClientList',listID:acListID,acListName:acListName, acSurName:acSurName },
                    success: function(data) {

                        switch(data){
                            case '1':
                                message = '<p>Welcome to ContactUs.com, and thank you for your registration.</p>';
                                message += '<p>We have sent a verification email.</b>.<br/>Please find the email, and login to your new ContactUs.com account.</p>';
                                
                                setTimeout(function(){
                                    cUsAC_myjq('.step3').slideUp().fadeOut();
                                    location.reload();
                                },2000)
                            break;
                            case '2':
                                message = 'Seems like you already have one Contactus.com Account, Please Login below!';
                                setTimeout(function(){
                                    cUsAC_myjq('.step2').slideUp().fadeOut();
                                    cUsAC_myjq('.step3').slideDown().delay(800);
                                },2000)
                            break;
                            default:
                                message = '<p>Ouch! unfortunately there has being an error during the application: <b>' + data + '</b>. Please try again!</a></p>';
                                cUsAC_myjq('.sendlistid').val('Continue to Step 3').removeAttr('disabled');
                            break;
                        }
                        
                        cUsAC_myjq('.loadingMessage').fadeOut();
                        cUsAC_myjq('.advice_notice').html(message).show().delay(1900).fadeOut(800);

                    },
                    async: false
                });
           }
           
            
        });
    }catch(err){
        cUsAC_myjq('.advice_notice').html('Oops, something wrong happened, please try again later!').slideToggle().delay(1200).fadeOut(1200);
    }
    
    
    cUsAC_myjq('#loginbtn').click(function(){//LOGIN ALREADY USERS
        var email = cUsAC_myjq('#login_email').val();
        var pass = cUsAC_myjq('#user_pass').val();
        cUsAC_myjq('.loadingMessage').show();
        
        if(!email.length){
            cUsAC_myjq('.advice_notice').html('User Email is a required and valid field!').slideToggle().delay(1200).fadeOut(1200);
            cUsAC_myjq('#login_email').focus();
            cUsAC_myjq('.loadingMessage').fadeOut();
        }else if(!pass.length){
            cUsAC_myjq('.advice_notice').html('User password is a required field!').slideToggle().delay(1200).fadeOut(1200);
            cUsAC_myjq('#user_pass').focus();
            cUsAC_myjq('.loadingMessage').fadeOut();
        }else{
            var bValid = checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. sergio@jquery.com" );  
            if(!bValid){
                cUsAC_myjq('.advice_notice').html('Please enter a valid User Email!').slideToggle().delay(1200).fadeOut(1200);
                cUsAC_myjq('.loadingMessage').fadeOut();
            }else{
                cUsAC_myjq.ajax({ type: "POST", url: ajax_object.ajax_url, data: {action:'ACloginAlreadyUser',email:email,pass:pass},
                    success: function(data) {

                        switch(data){
                            case '1':
                                message = '<p>Welcome to ContactUs.com, and thank you for your registration.</p>';
                                //message += '<p>First we’ll need to activate your account. We have sent a verification email to <b>' + email + '</b>.<br/>Please find the email, and click on the activation link in the email.  Then, come back to this page.</p>';
                                
                                setTimeout(function(){
                                    cUsAC_myjq('.step3').slideUp().fadeOut();
                                    //cUsAC_myjq('.mainWindow').slideDown().delay(800);
                                    location.reload();
                                },2000)
                                
                            break;
                            default:
                                message = '<p>Ouch! unfortunately there has being an error during the application: <b>' + data + '</b>. Please try again!</a></p>';
                                break;
                        }
                        
                        cUsAC_myjq('.loadingMessage').fadeOut();
                        cUsAC_myjq('.advice_notice').html(message).show();

                    },
                    async: false
                });
            }
        }
    });
    
    cUsAC_myjq('#logoutbtnAC').click(function(){
        if(confirm('Are you sure you want to quit?')){
            cUsAC_myjq('.loadingMessage').show();
            cUsAC_myjq.ajax({ type: "POST", url: ajax_object.ajax_url, data: {action:'AClogoutUser'},
                success: function(data) {
                    cUsAC_myjq('.loadingMessage').fadeOut();
                    location.reload();
                },
                async: false
            });
        }
    });
    
    
    try{ cUsAC_myjq('.sendtemplate').click(function() {
           
           var acApiKey = cUsAC_myjq('#apikey').val();
           var acTemplateID = cUsAC_myjq('#templateid').val();
           cUsAC_myjq('.loadingMessage').show();
           
           if(!acApiKey.length){
               cUsAC_myjq('.advice_notice').html('ActiveCampaign API Key is a required field!').slideToggle().delay(1200).fadeOut(1200);
               cUsAC_myjq('#apikey').focus();
               cUsAC_myjq('.loadingMessage').fadeOut();
           }else{
                
                cUsAC_myjq.ajax({ type: "POST", url: ajax_object.ajax_url, data: {action:'ACsendTemplateID',templateID:acTemplateID},
                    success: function(data) {

                        switch(data){
                            case '1':
                                message = 'Template saved succesfuly . . . .';
                                
                                setTimeout(function(){
                                    cUsAC_myjq('.step3').slideUp().fadeOut();
                                    cUsAC_myjq('.step4').slideDown().delay(800);
                                },2000)
                                
                            break;
                        }
                        
                        cUsAC_myjq('.loadingMessage').fadeOut();
                        cUsAC_myjq('.advice_notice').html(message).show().delay(1900).fadeOut(800);

                    },
                    async: false
                });
           }
           
            
        });
    }catch(err){
        cUsAC_myjq('.advice_notice').html('Oops, something wrong happened, please try again later!').slideToggle().delay(1200).fadeOut(1200);
    }
    
    cUsAC_myjq('#tab_user').change(function (){
        cUsAC_myjq('.displaybtn').show();
    });
    
    cUsAC_myjq('.form_version').change(function(){
        cUsAC_myjq('.displaybtn').show();
        var val = cUsAC_myjq(this).val();
        cUsAC_myjq('.cus_versionform').fadeOut();
        cUsAC_myjq('.' + val).slideToggle();
    });
    
    cUsAC_myjq('#contactus_settings_page').change(function(){
        cUsAC_myjq('.show_preview').fadeOut();
        cUsAC_myjq('.save_page').fadeOut( "highlight" ).fadeIn().val('>> Save your settings');
    });
    
    cUsAC_myjq('.callout-button').click(function() {
        cUsAC_myjq('.getting_wpr').slideToggle('slow');
    });
    
    cUsAC_myjq('.insertShortcode').click(function() {
        contactUs_mediainsert();
    });
    
    
    cUsAC_myjq('#ac_yes').click(function() {
        cUsAC_myjq('#cUsAC_acsettings').slideToggle('slow');
    });
    
    
});

function contactUs_mediainsert() {
    send_to_editor('[show-contactus.com-form]');
}