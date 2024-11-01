<?php
class sdfwr_simpledisplay_helper{
static function sdfwr_simpledisplay_get_review($allcats,$allauthors,$alltags,$params)
{
$output=array();
//removing zero value from  categories,authors,tags IDs array
 if (($key1 = array_search("0", $allcats)) !== false) {
    unset($allcats[$key1]);
    $allcats=array_values($allcats);
   }
  if (($key2 = array_search("0", $allauthors)) !== false) {
    unset($allauthors[$key2]);
    $allauthors=array_values($allauthors);
   }
   if (($key3 = array_search("0", $alltags)) !== false) {
    unset($alltags[$key3]);
    $alltags=array_values($alltags);
   }
   //getting posts or pages based on filters
   $metakey='';
   if($params['orderby']=='review')
   {
    $orderby='meta_value_num';
    $metakey='_wc_review_count';
   }
   else if($params['orderby']=='rating')
   {
    $orderby='meta_value_num';
    $metakey='_wc_review_count';
   }
   else if($params['orderby']=='totalsale')
   {
    $orderby='meta_value_num';
    $metakey='total_sales';
   }
  else if($params['orderby']=='price')
   {
    $orderby='meta_value_num';
    $metakey='_price';
   }
   else if ($params['orderby']=='date'){
   $orderby=$params['orderby'];
   $metakey='';
   }
   else{
   $orderby='date';
   $metakey='';
   }
   $data = get_userdata( get_current_user_id() );
    $current_user_caps = $data->allcaps;
    $post_status=($current_user_caps['read_private_posts']==1)?array('publish','private'):array('publish');
 if($params['count']!=0 && (!empty($allcats) || !empty($allauthors) || !empty($alltags)))
 { $output=get_posts(array('tax_query'=>array('relation'=>'AND',array('taxonomy'=>'product_cat','field'=>'term_id','terms'=>$allcats,'operator'=>$allcats?'IN':'NOT IN'),array('taxonomy'=>'product_tag','field'=>'term_id','terms'=>$alltags,'operator'=>$alltags?'IN':'NOT IN')),'author__in'=>$allauthors,'orderby'=>$orderby,'order'=>$params['sort'],'meta_key'=>$metakey,'post_type'=>'product' ,'post_status'=>$post_status,'comment_count'=>array('value'=>0,'compare'=>'>')));
 }
  if(!empty($output))
  {  //adding extra attributes
    foreach($output as $key=>$p)
   { $products[$key]=$p->ID;

   }
   $comment_key='';
   if($params['orderby']=='commentrating')
   {
    $com_order='meta_value_num';
    $comment_key='rating';
   }
   else if ($params['orderby']=='comment_date_gmt'){
   $com_order=$params['orderby'];
   $comment_key='';
   }
   else
   {
   $com_order='comment_date_gmt';
   $comment_key='';
   }
   $args=array('post__in'=>$products,'post_type'=>'product','status'=>'approve','number'=>$params['count'],'offset'=>$params['offset'],'orderby'=>$com_order,'order'=>$params['sort'],'meta_key'=>$comment_key);

   $comments=get_comments($args);
   if(!empty($comments))
   {
    $reviews= array();
   // $reviews= json_encode($reviews);
   foreach ( $comments as $key=>$comment ) {
        $review= new stdClass;
        $review->id= intval( $comment->comment_ID );
        $review->created_at=date_i18n(get_option('date_format'),strtotime($comment->comment_date_gmt));
        $review->review= $comment->comment_content;
        $review->rating= get_comment_meta( $comment->comment_ID, 'rating', true );
        $review->reviewer_name=$comment->comment_author;
        $review->reviewer_email=$comment->comment_author_email;
        $review->post_title=get_the_title($comment->comment_post_ID);
        $review->post_link=get_permalink($comment->comment_post_ID);
       $review->avatar_url=get_avatar_url($comment->comment_author_email);
       $reviews[]=$review;

    }
    }

   }
 return $reviews;
}
}