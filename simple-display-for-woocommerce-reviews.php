<?php
/*
Plugin Name: Simple Display for Woocommerce Reviews
Plugin URI: https://github.com/mostafa272/Simple-Display-For-Woocommrce-Reviews
Description: Simple Display for Woocommerce Reviews is a simple widget to display woocommerce reviews in a pretty style
Version: 1.0
Text Domain: simple-display-for-woocommerce-reviews
WC requires at least: 3.0.0
WC tested up to: 3.3
Author: Mostafa Shahiri<mostafa2134@gmail.com>
Author URI: https://github.com/mostafa272/
*/
/*  Copyright 2009  Mostafa Shahiri(email : mostafa2134@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//prevent direct access and checking woocommerce
if ( !defined('ABSPATH')) exit;
//register widget
add_action("widgets_init", function () { register_widget("sdfwr_SimpleDisplay"); });

class sdfwr_SimpleDisplay extends WP_Widget
{
    public function __construct() {
        parent::__construct("sdfwr_simpledisplay", "Simple Display for WC Reviews",
            array("description" => "A simple widget to show woocommerce reviews in a pretty style"));
            add_action( 'wp_enqueue_scripts',array($this,'sdfwr_simpledisplay_scripts') );
            add_action( 'wp_ajax_sdfwreviews',array($this, 'sdfwreviews_ajax_callback') );
            add_action( 'wp_ajax_nopriv_sdfwreviews',array($this, 'sdfwreviews_ajax_callback')  );
            require_once( plugin_dir_path( __FILE__ ) . 'class.simpledisplay-helper.php');
            add_action( 'plugins_loaded', array( $this, 'sdfwr_load_textdomain' ));
    }
    public function sdfwr_load_textdomain() {
	  load_plugin_textdomain( 'simple-display-for-woocommerce-reviews', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}
    public function form($instance) {

    //initial values

        $title=$instance["title"];
        $alltags=(!empty($instance["alltags"]))?$instance["alltags"]:array("0");
        $allcats=(!empty($instance["allcats"]))?$instance["allcats"]:array("0");
        $allauthors=(!empty($instance["allauthors"]))?$instance["allauthors"]:array("0");
        $count=$instance["count"];
        $limit=$instance["limit"];
        $height=$instance["height"];
        $width=$instance["width"];
        $orderby=$instance["orderby"];
        $sort=$instance["sort"];
        $showauthor=$instance["showauthor"];
        $showavatar=$instance["showavatar"];
        $showemail=$instance["showemail"];
        $showdate=$instance["showdate"];
        $showrating=$instance["showrating"];
        $showlink=$instance["showlink"];
        $showmorebtn=$instance["showmorebtn"];
        $readmore=(!empty($instance["readmore"]))?$instance["readmore"]:'Read More ...';

    //title field for widget
    $titleId = $this->get_field_id("title");
    $titleName = $this->get_field_name("title");
    echo '<p><label for="'.$titleId.'">Title:</label><br>';
    echo '<input id="'.$titleId.'" type="text" name="'.$titleName.'" value="'.$title.'"></p>';
     //select source type

   //get tags
    $alltagsId = $this->get_field_id("alltags");
    $alltagsName = $this->get_field_name("alltags");
   $tags = get_terms('product_tag');
   echo '<p><label for="'.$alltagsId.'">Tags:</label><br>';
  echo '<select id="'.$alltagsId.'" name="'.$alltagsName.'[]" multiple="true">';
  echo '<option value="0" '.selected('true',in_array('0' , $alltags)?'true':'false' ).'>-- Select Tags --</option>';
   foreach ($tags as $tag){
   echo '<option value="'.$tag->term_id.'" '.selected( 'true' , in_array($tag->term_id,$alltags)?'true':'false' ).'>'.$tag->name.'</option>';
   }
   echo '</select></p>';

  //get categories
    $allcatsId = $this->get_field_id("allcats");
    $allcatsName = $this->get_field_name("allcats");
    $arg2=array('taxonomy'=>'product_cat');
   $cats = get_categories($arg2);
   echo '<p><label for="'.$allcatsId.'">Categories:</label><br>';
  echo '<select id="'.$allcatsId.'" name="'.$allcatsName.'[]" multiple="true">';
  echo '<option value="0" '.selected('true',in_array('0' , $allcats)?'true':'false' ).'>-- Select Categories --</option>';
   foreach ($cats as $cat){
   echo '<option value="'.$cat->term_id.'" '.selected( 'true' , in_array($cat->term_id,$allcats)?'true':'false' ).'>'.$cat->cat_name.'</option>';
   }
   echo '</select></p>';

   //get authors
     $allauthorsId = $this->get_field_id("allauthors");
    $allauthorsName = $this->get_field_name("allauthors");
   $authors = get_users();
   echo '<p><label for="'.$allauthorsId.'">Authors:</label><br>';
  echo '<select id="'.$allauthorsId.'" name="'.$allauthorsName.'[]" multiple="true">';
  echo '<option value="0" '.selected('true',in_array('0' , $allauthors)?'true':'false' ).'>-- Select Authors --</option>';
   foreach ($authors as $author){
   echo '<option value="'.$author->ID.'" '.selected( 'true' , in_array($author->ID,$allauthors)?'true':'false' ).'>'.$author->display_name.'['.$author->user_login.']</option>';
   }
   echo '</select></p>';

   //number of posts to fetch from categories
    $countId = $this->get_field_id("count");
    $countName = $this->get_field_name("count");
    echo '<p><label for="'.$countId.'">Count:</label><br>';
    echo '<input id="'.$countId.'" type="number" name="'.$countName.'" value="'.$count.'"></p>';
    //limit description length
    $limitId = $this->get_field_id("limit");
    $limitName = $this->get_field_name("limit");
    echo '<p><label for="'.$limitId.'">Limit Review Length:</label><br>';
    echo '<input id="'.$limitId.'" type="number" name="'.$limitName.'" value="'.$limit.'"></p>';
    //orderby box
    $orderbyId = $this->get_field_id("orderby");
    $orderbyName = $this->get_field_name("orderby");
    echo '<p><label for="'.$orderbyId.'">Order By:</label><br>';
    echo '<select id="'.$orderbyId.'" name="'.$orderbyName.'">';
    echo '<option value="date" '.selected( 'date', $orderby ).'>Post Created Date</option>';
    echo '<option value="comment_date_gmt" '.selected( 'comment_date_gmt', $orderby ).'>Comment Created Date</option>';
    echo '<option value="review" '.selected( 'review', $orderby ).'>Reviews Count</option>';
    echo '<option value="rating" '.selected( 'rating', $orderby ).'>Post Rating</option>';
    echo '<option value="commentrating" '.selected( 'commentrating', $orderby ).'>Comment Rating</option>';
    echo '<option value="totalsale" '.selected( 'totalsale', $orderby ).'>Total Sales</option>';
    echo '<option value="price" '.selected( 'price', $orderby ).'>Price</option>';
    echo '</select></p>';
    //order type
    $sortId = $this->get_field_id("sort");
    $sortName = $this->get_field_name("sort");
    echo '<p><label for="'.$sortId.'">Order:</label><br>';
    echo '<select id="'.$sortId.'" name="'.$sortName.'">';
    echo '<option value="DESC" '.selected( 'DESC', $sort ).'>Descending</option>';
    echo '<option value="ASC" '.selected( 'ASC', $sort ).'>Ascending</option>';
    echo '</select></p>';

    //text for readmore link
    $readmoreId = $this->get_field_id("readmore");
    $readmoreName = $this->get_field_name("readmore");
    echo '<p><label for="'.$readmoreId.'">Read More Text:</label><br>';
    echo '<input id="'.$readmoreId.'" type="text" name="'.$readmoreName.'" value="'.$readmore.'"></p>';
    $heightId = $this->get_field_id("height");
    $heightName = $this->get_field_name("height");
    echo '<p><label for="'.$heightId.'">Avatar Height:</label><br>';
    echo '<input id="'.$heightId.'" type="number" name="'.$heightName.'" value="'.$height.'"></p>';
    $widthId = $this->get_field_id("width");
    $widthName = $this->get_field_name("width");
    echo '<p><label for="'.$widthId.'">Avatar Width:</label><br>';
    echo '<input id="'.$widthId.'" type="number" name="'.$widthName.'" value="'.$width.'"></p>';

   //an option for showing authors names of the posts or pages
   $showauthorId = $this->get_field_id("showauthor");
    $showauthorName = $this->get_field_name("showauthor");
    ?><p><input id="<?php echo $showauthorId;?>" type="checkbox" name="<?php echo $showauthorName;?>" value="1" <?php checked( 1, $showauthor );?>>Show Author</p>
   <?php
   //an option for showing published dates of the posts or pages
   $showratingId = $this->get_field_id("showrating");
   $showratingName = $this->get_field_name("showrating");
    ?><p><input id="<?php echo $showratingId;?>" type="checkbox" name="<?php echo $showratingName;?>" value="1" <?php checked( 1, $showrating );?>>Show Rating</p>
   <?php
   //an option for showing modified dates of the posts or pages
   $showavatarId = $this->get_field_id("showavatar");
   $showavatarName = $this->get_field_name("showavatar");
    ?><p><input id="<?php echo $showavatarId;?>" type="checkbox" name="<?php echo $showavatarName;?>" value="1" <?php checked( 1, $showavatar );?>>Show Avatar</p>
   <?php
   //an option for showing comments count of the posts or pages
   $showemailId = $this->get_field_id("showemail");
   $showemailName = $this->get_field_name("showemail");
    ?><p><input id="<?php echo $showemailId;?>" type="checkbox" name="<?php echo $showemailName;?>" value="1" <?php checked( 1, $showemail );?>>Show Email</p>
   <?php
    $showdateId = $this->get_field_id("showdate");
   $showdateName = $this->get_field_name("showdate");
    ?><p><input id="<?php echo $showdateId;?>" type="checkbox" name="<?php echo $showdateName;?>" value="1" <?php checked( 1, $showdate );?>>Show Date</p>
   <?php
      $showlinkId = $this->get_field_id("showlink");
   $showlinkName = $this->get_field_name("showlink");
    ?><p><input id="<?php echo $showlinkId;?>" type="checkbox" name="<?php echo $showlinkName;?>" value="1" <?php checked( 1, $showlink );?>>Show Product Link</p>
    <?php
      $showmorebtnId = $this->get_field_id("showmorebtn");
   $showmorebtnName = $this->get_field_name("showmorebtn");
    ?><p><input id="<?php echo $showmorebtnId;?>" type="checkbox" name="<?php echo $showmorebtnName;?>" value="1" <?php checked( 1, $showmorebtn );?>>Show Read More Button</p>
    <?php
}
//sanitizing widget parameters
public function update($newInstance, $oldInstance) {
    $values = array();
    $values["title"] = sanitize_text_field($newInstance["title"]);
    $values["alltags"] = $newInstance["alltags"];
    $values["allcats"] = $newInstance["allcats"];
    $values["allauthors"] = $newInstance["allauthors"];
    $values["count"] = intval($newInstance["count"]);
    $values["limit"] = intval($newInstance["limit"]);
    $values["height"] = intval($newInstance["height"]);
    $values["width"] = intval($newInstance["width"]);
    $values["orderby"] = $newInstance["orderby"];
    $values["sort"] = $newInstance["sort"];
    $values["showauthor"] = $newInstance["showauthor"];
    $values["showavatar"] = $newInstance["showavatar"];
    $values["showrating"] = $newInstance["showrating"];
    $values["showemail"] = $newInstance["showemail"];
    $values["showdate"] = $newInstance["showdate"];
    $values["showlink"] = $newInstance["showlink"];
    $values["showmorebtn"] = $newInstance["showmorebtn"];
    $values["readmore"] = sanitize_text_field($newInstance["readmore"]);
    return $values;
}
//adding CSS file and jquery accordion
function sdfwr_simpledisplay_scripts() {
         wp_register_style( 'sdfwr-simple-display', plugins_url( 'css/simpledisplay.css', __FILE__ ) );
     wp_register_script( 'sdfwr-simple-display-script', plugins_url( 'js/simpledisplay.js', __FILE__ ),array('jquery'),'1.0',true );
     wp_localize_script( 'sdfwr-simple-display-script', 'sdfwr_ajax_url', array( 'ajax_url' => admin_url('admin-ajax.php'),'check_nonce'=>wp_create_nonce('sdfwr-nonce')) );
}
function sdfwreviews_ajax_callback(){
$params=array();
global $woocommerce;
check_ajax_referer( 'sdfwr-nonce', 'security' );
$params['offset']= isset($_POST['current_count'])?intval(sanitize_text_field($_POST['current_count'])):0;
$params['count']= isset($_POST['load_count'])?intval(sanitize_text_field($_POST['load_count'])):0;
$params['orderby']=isset($_POST['load_orderby'])?sanitize_text_field($_POST['load_orderby']):'';
$params['sort']= isset($_POST['load_sort'])?sanitize_text_field($_POST['load_sort']):'';
$allcats= isset($_POST['load_cats'])?explode(',',sanitize_text_field($_POST['load_cats'])):array();
$allauthors= isset($_POST['load_authors'])?explode(',',sanitize_text_field($_POST['load_authors'])):array();
$alltags= isset($_POST['load_tags'])?explode(',',sanitize_text_field($_POST['load_tags'])):array();
//$params['count']= $params['count']+$cur_count;
$attribs=explode(',',sanitize_text_field($_POST['load_attribs']));
  $limit=(!empty($attribs[0]))?intval($attribs[0]):0;
  $height=(!empty($attribs[1]))?intval($attribs[1]):0;
  $width=(!empty($attribs[2]))?intval($attribs[2]):0;
  $avatar=(!empty($attribs[3]))?intval($attribs[3]):0;
  $author=(!empty($attribs[4]))?intval($attribs[4]):0;
  $email=(!empty($attribs[5]))?intval($attribs[5]):0;
  $date=(!empty($attribs[6]))?intval($attribs[6]):0;
  $rating=(!empty($attribs[7]))?intval($attribs[7]):0;
  $link=(!empty($attribs[8]))?intval($attribs[8]):0;
  $morebtn=(!empty($attribs[9]))?intval($attribs[9]):0;
  $readmore=(!empty($attribs[10]))?sanitize_text_field($attribs[10]):'';
$reviews_info= sdfwr_simpledisplay_helper::sdfwr_simpledisplay_get_review($allcats,$allauthors,$alltags,$params);
if(!empty($reviews_info))
{
 foreach($reviews_info as $c)
{
 $av_tmp=($avatar==1)?'<div class="sdfwravatar"><img src="'.$c->avatar_url.'" alt="" width="'.$width.'" height="'.$height.'"></div>':'';
  $au_tmp=($author==1)?'<span class="sdfwrauthor">'.$c->reviewer_name.'</span>':'';
  $email_tmp=($email==1)?'<span class="sdfwremail"> ('.$c->reviewer_email.') </span>':'';
  $date_tmp=($date==1)?'<span class="sdfwrdate">'.$c->created_at.'</span><br>':'';
  $rating_tmp=($rating==1 && !empty($c->rating))?wc_get_rating_html($c->rating).'<br>':'';
  $link_tmp=($link==1)?'<span class="sdfwrlink">'.__('Review for').' <a href="'.esc_url($c->post_link).'">'.$c->post_title.'</a></span>':'';
  $desc_tmp='<p>'.wp_trim_words($c->review,$limit,'...').'</p>';
  $morebtn_tmp= ($morebtn==1)?'<a class="sdfwr_readmore" href="'.esc_url($c->post_link).'">'.esc_html($readmore).'</a>':'';
  $info_block='<div class="sdfwr_infoblock">'.$au_tmp.$email_tmp.'<br>'.$date_tmp.$rating_tmp.$link_tmp.$desc_tmp.$morebtn_tmp.'</div>';
echo '<div class="sdfwr_item">'.$av_tmp.$info_block.'</div>';
}
}

wp_die();
}

public function widget($args, $instance) {
     wp_enqueue_style( 'sdfwr-simple-display');
   wp_enqueue_script( 'sdfwr-simple-display-script');
 global $woocommerce;
  $title=$instance["title"];
  $alltags=$instance["alltags"];
  $allcats=$instance["allcats"];
  $allauthors=$instance["allauthors"];
  $count=$instance["count"];
  $limit=$instance["limit"];
  $height=$instance["height"];
  $width=$instance["width"];
  $orderby=$instance["orderby"];
  $sort=$instance["sort"];
  $avatar=$instance["showavatar"];
  $author=$instance["showauthor"];
  $email=$instance["showemail"];
  $date=$instance["showdate"];
  $rating=$instance["showrating"];
  $link=$instance["showlink"];
  $morebtn=$instance["showmorebtn"];
  $readmore=$instance["readmore"];
   $attribs=$limit.','.$height.','.$width.','.$avatar.','.$author.','.$email.','.$date.','.$rating.','.$link.','.$morebtn.','.$readmore;
  //getting posts by selected filters
  $params=array('count'=>$count,'orderby'=>$orderby,'sort'=>$sort,'offset'=>0);
  $reviews_info= sdfwr_simpledisplay_helper::sdfwr_simpledisplay_get_review($allcats,$allauthors,$alltags,$params);

  //displaying the widget on frontend. It shows the title of widget if it is not empty
  echo $args['before_widget'];
  if(!empty($title))
  {	echo $args['before_title'];
    echo esc_html($title);
  	echo $args['after_title'];
  }
//showing the selected widgets
echo '<div class="simple_display" id="sdfwr_'.$this->id.'">';
//display posts
echo '<div class="sdfwr_container">';
if(!empty($reviews_info))
{
 foreach($reviews_info as $c)
{
 $av_tmp=($avatar==1)?'<div class="sdfwravatar"><img src="'.$c->avatar_url.'" alt="" width="'.$width.'" height="'.$height.'"></div>':'';
  $au_tmp=($author==1)?'<span class="sdfwrauthor">'.$c->reviewer_name.'</span>':'';
  $email_tmp=($email==1)?'<span class="sdfwremail"> ('.$c->reviewer_email.') </span>':'';
  $date_tmp=($date==1)?'<span class="sdfwrdate">'.$c->created_at.'</span><br>':'';
  $rating_tmp=($rating==1 && !empty($c->rating))?wc_get_rating_html($c->rating).'<br>':'';
  $link_tmp=($link==1)?'<span class="sdfwrlink">'.__('Review for','simple-display-for-woocommerce-reviews').' <a href="'.esc_url($c->post_link).'">'.$c->post_title.'</a></span>':'';
  $desc_tmp='<p>'.wp_trim_words($c->review,$limit,'...').'</p>';
  $morebtn_tmp= ($morebtn==1)?'<a class="sdfwr_readmore" href="'.esc_url($c->post_link).'">'.esc_html($readmore).'</a>':'';
  $info_block='<div class="sdfwr_infoblock">'.$au_tmp.$email_tmp.'<br>'.$date_tmp.$rating_tmp.$link_tmp.$desc_tmp.$morebtn_tmp.'</div>';
echo '<div class="sdfwr_item">'.$av_tmp.$info_block.'</div>';
}

}
echo '</div>';
echo '<div class="load_container"><div class="sdfwr_loading" ><div class="sdfwr_loader"></div><div class="sdfwr_loadtext"> '.__('Loading ...','simple-display-for-woocommerce-reviews').'</div></div><button class="sdfwr_load" > '.__('Load More','simple-display-for-woocommerce-reviews').'</button></div>';
echo '<div style="display:none;" class="sdfwr_id" data-name="sdfwr_'.$this->id.'" data-cats="'.implode(',',$allcats).'" data-authors="'.implode(',',$allauthors).'" data-tags="'.implode(',',$alltags).'"  data-count="'.$params['count'].'" data-orderby="'.$params['orderby'].'" data-sort="'.$params['sort'].'" data-attribs="'.$attribs.'"></div>';
echo '</div>';

 echo $args['after_widget'];
}
}