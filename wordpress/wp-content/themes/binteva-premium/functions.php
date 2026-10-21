<?php
if (!defined('ABSPATH')) exit;
function binteva_setup(){add_theme_support('title-tag');add_theme_support('post-thumbnails');add_theme_support('html5',array('search-form','gallery','caption','style','script'));register_nav_menus(array('primary'=>'Primary Navigation'));}
add_action('after_setup_theme','binteva_setup');
function binteva_assets(){wp_enqueue_style('binteva-style',get_stylesheet_uri(),array(),'1.1.0');wp_enqueue_script('binteva-motion',get_template_directory_uri().'/assets/site.js',array(),'1.1.0',true);}add_action('wp_enqueue_scripts','binteva_assets');
function binteva_setting($name,$fallback=''){ $v=get_option('binteva_'.$name,''); return is_string($v)&&$v!=='' ? $v : $fallback; }
function binteva_logo($variant='light'){
 $base='https://bisque-jackal-232127.hostingersite.com/wp-content/uploads/2026/10/';
 $files=array('light'=>'ChatGPT-Image-Oct-21-2026-12_40_44-PM-1.png','square'=>'ChatGPT-Image-Oct-21-2026-12_40_45-PM-2.png','dark'=>'ChatGPT-Image-Oct-21-2026-12_40_47-PM-3.png','gradient'=>'ChatGPT-Image-Oct-21-2026-12_40_49-PM-4.png');
 return esc_url(binteva_setting('logo_'.$variant,$base.$files[$variant]));
}
function binteva_book_url(){return esc_url(binteva_setting('booking_url','https://calendly.com/agency8200/zoom-call'));}
function binteva_link($slug){return esc_url(home_url('/'.trim($slug,'/').'/'));}
function binteva_button($label='Request a strategy conversation',$class='btn btn-primary'){return '<a class="'.esc_attr($class).'" href="'.binteva_book_url().'">'.esc_html($label).' <span aria-hidden="true">↗</span></a>';}
function binteva_fallback_menu(){echo '<ul class="nav-links">';foreach(array('how-it-works'=>'How it works','why-binteva'=>'Why Binteva','industries'=>'Industries','platform'=>'Platform','commercial-value'=>'Commercial value','about'=>'About','faq'=>'FAQ') as $s=>$t) echo '<li><a href="'.binteva_link($s).'">'.esc_html($t).'</a></li>';echo '</ul>';}
function binteva_description(){if(is_front_page())return 'Binteva transforms trade-show exhibitor data into verified, prioritized commercial opportunities for industrial B2B teams.';return wp_strip_all_tags(get_the_excerpt()?:get_bloginfo('description'));}
add_action('wp_head',function(){echo '<meta name="description" content="'.esc_attr(binteva_description()).'">' . "\n";},1);
