<?php
/*
Plugin Name: Binteva Conversion Suite
Description: Installs Binteva's editable strategic pages, inquiry form, and brand conversion settings.
Version: 1.1.1
Requires PHP: 7.4
Author: Agency 8200
*/
if(!defined('ABSPATH'))exit;
require_once plugin_dir_path(__FILE__).'content.php';
function binteva_install_pages(){
 // Preserve settings so activation can be reversed without losing the prior homepage.
 if (false === get_option('binteva_previous_site_settings', false)) {
  add_option('binteva_previous_site_settings', array('show_on_front'=>get_option('show_on_front'), 'page_on_front'=>get_option('page_on_front'), 'blogdescription'=>get_option('blogdescription')));
 }
 add_option('binteva_booking_url', 'https://calendly.com/agency8200/zoom-call');
 foreach(binteva_page_definitions() as $slug=>$page){$old=get_page_by_path($slug,OBJECT,'page');if($old)continue; $id=wp_insert_post(array('post_title'=>$page['title'],'post_name'=>$slug,'post_type'=>'page','post_status'=>'publish','post_excerpt'=>$page['excerpt'],'post_content'=>$page['content'],'comment_status'=>'closed'),true);}
 $home=get_page_by_path('home',OBJECT,'page');if(!$home){$home=wp_insert_post(array('post_title'=>'Home','post_name'=>'home','post_type'=>'page','post_status'=>'publish','post_content'=>'','comment_status'=>'closed'));}else{$home=$home->ID;}
 if(!is_wp_error($home)){update_option('show_on_front','page');update_option('page_on_front',(int)$home);}
 update_option('blogdescription','Commercial Opportunity Intelligence');
}
register_activation_hook(__FILE__,'binteva_install_pages');
function binteva_admin_menu(){add_options_page('Binteva Settings','Binteva','manage_options','binteva','binteva_settings_page');}add_action('admin_menu','binteva_admin_menu');
function binteva_settings_init(){foreach(array('booking_url','inquiry_email','logo_light','logo_dark','logo_square','logo_gradient') as $key){register_setting('binteva_options','binteva_'.$key,array('type'=>'string','sanitize_callback'=>$key==='inquiry_email'?'sanitize_email':'esc_url_raw','default'=>''));}}add_action('admin_init','binteva_settings_init');
function binteva_settings_page(){if(!current_user_can('manage_options'))return; echo '<div class="wrap"><h1>Binteva Settings</h1><p>Set a branded booking link to send all Book a Conversation buttons directly to your calendar. If blank, they link to the Binteva contact page. Set and verify the inquiry destination before enabling the form. No inquiries are sent to an inferred address.</p><form method="post" action="options.php">';settings_fields('binteva_options');echo '<table class="form-table">';foreach(array('booking_url'=>'Booking calendar URL','inquiry_email'=>'Inquiry destination email','logo_light'=>'Header / light-background logo URL','logo_dark'=>'Footer / dark-background logo URL','logo_square'=>'Square logo URL','logo_gradient'=>'Gradient logo URL') as $key=>$label){echo '<tr><th scope="row"><label for="'.esc_attr($key).'">'.esc_html($label).'</label></th><td><input class="regular-text" type="'.($key==='inquiry_email'?'email':'url').'" id="'.esc_attr($key).'" name="binteva_'.esc_attr($key).'" value="'.esc_attr(get_option('binteva_'.$key,'')).'" placeholder="'.($key==='inquiry_email'?'you@company.com':'https://').'" /></td></tr>';}echo '</table>';submit_button('Save settings');echo '</form><hr><p><strong>Installation note:</strong> The default logo URLs point to your Hostinger staging site. Make sure they remain public, or replace with final media-library URLs. Existing page content is never overwritten when the plugin is reactivated.</p></div>';}
add_shortcode('binteva_contact',function(){
 $status=isset($_GET['binteva_status'])?sanitize_key(wp_unslash($_GET['binteva_status'])):'';
 if (!is_email(get_option('binteva_inquiry_email', ''))) { return '<p>Schedule a strategy call using the booking link above.</p>'; }
 ob_start(); if($status==='sent')echo '<div class="form-feedback success" role="status">Thank you. Your message has been submitted.</div>';elseif($status==='error')echo '<div class="form-feedback error" role="alert">We could not send your inquiry. Please try again or contact us directly.</div>';
 ?><form class="contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>"><input type="hidden" name="action" value="binteva_send_inquiry"><?php wp_nonce_field('binteva_inquiry','binteva_nonce');?><div class="hp-field" aria-hidden="true"><label>Leave this empty<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label></div><div class="form-row"><label>Your name *<input name="person_name" maxlength="120" required autocomplete="name"></label><label>Work email *<input name="person_email" type="email" maxlength="180" required autocomplete="email"></label></div><div class="form-row"><label>Company *<input name="company" maxlength="180" required autocomplete="organization"></label><label>Job title *<input required name="role" maxlength="120" autocomplete="organization-title"></label></div><div class="form-row"><label>Approximate shows per year<select name="show_count"><option value="Not specified">Select one</option><option value="1–3">1–3</option><option value="4–7">4–7</option><option value="8–15">8–15</option><option value="16+">16+</option></select></label><label>Company website *<input name="website" type="url" maxlength="500" required placeholder="https://" autocomplete="url"></label></div><label>What would you like to accomplish? (optional)<textarea name="message" maxlength="2500" placeholder="Tell us about your next event or your commercial goals..."></textarea></label><button type="submit" class="btn btn-primary">Send your inquiry ↗</button><p class="form-note">Your information is used to respond to your inquiry. Please don't submit confidential customer or personal information.</p></form><?php return ob_get_clean();
});
function binteva_handle_inquiry(){
 $referer=wp_get_referer();$return=$referer?remove_query_arg('binteva_status',$referer):home_url('/contact/');
 if(!isset($_POST['binteva_nonce'])||!is_string($_POST['binteva_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['binteva_nonce'])),'binteva_inquiry'))wp_die('Invalid request','',array('response'=>403));
 if(!empty($_POST['company_website'])){wp_safe_redirect(add_query_arg('binteva_status','sent',$return));exit;}
 if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') wp_die('Method not allowed', '', array('response'=>405));
 foreach (array('person_name','person_email','company','role','show_count','website','message','company_website') as $field) { if (isset($_POST[$field]) && !is_scalar($_POST[$field])) wp_die('Invalid input', '', array('response'=>400)); }
 $rate_key='binteva_inquiry_'.hash_hmac('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown', wp_salt('nonce'));
 if ((int)get_transient($rate_key) >= 5) { wp_safe_redirect(add_query_arg('binteva_status','error',$return)); exit; }
 set_transient($rate_key, (int)get_transient($rate_key)+1, 15*MINUTE_IN_SECONDS);
 $name=sanitize_text_field(wp_unslash($_POST['person_name']??''));$email=sanitize_email(wp_unslash($_POST['person_email']??''));$company=sanitize_text_field(wp_unslash($_POST['company']??''));$role=sanitize_text_field(wp_unslash($_POST['role']??''));$show=sanitize_text_field(wp_unslash($_POST['show_count']??''));$website=esc_url_raw(wp_unslash($_POST['website']??''),array('http','https'));$message=sanitize_textarea_field(wp_unslash($_POST['message']??''));
 if(!$name||strlen($name)>120||!is_email($email)||strlen($email)>180||!$company||strlen($company)>180||!$role||strlen($role)>120||!$website||strlen($website)>500||strlen($message)>2500||!in_array($show,array('Not specified','1–3','4–7','8–15','16+'),true)){wp_safe_redirect(add_query_arg('binteva_status','error',$return));exit;}
 $destination=get_option('binteva_inquiry_email');if(!is_email($destination)){wp_safe_redirect(add_query_arg('binteva_status','error',$return));exit;}
 $body="New Binteva inquiry\n\nName: $name\nEmail: $email\nCompany: $company\nRole: $role\nEvents/year: $show\nWebsite: $website\n\nMessage:\n$message";
 $sent=wp_mail($destination,'New Binteva website inquiry - '.$company,$body,array('Reply-To: '.$email));
 wp_safe_redirect(add_query_arg('binteva_status',$sent?'sent':'error',$return));exit;
}
add_action('admin_post_nopriv_binteva_send_inquiry','binteva_handle_inquiry');add_action('admin_post_binteva_send_inquiry','binteva_handle_inquiry');
