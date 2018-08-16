@extends('frontend.layouts.master')

@section('content')
 <script src="{{url('js/frontend/tokenize2.js')}}"></script> 
 <link href="{{ url('css/frontend/tokenize2.css')}}" rel="stylesheet">
<div class="bottom">
		<div class="container">
			<div class="row">
				<div class=" col-md-9 col-sm-8 col-xs-12">
					<div class="iner-slider">
		<div id="first-slider">
   		 <div id="carousel-example-generic" class="carousel slide carousel-fade">
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
			<?php  /******** For share Url of Social Media **************/
			$image = url('/public/storage/uploads/article/'.$slider_data[0]->id.'/medium/'.$slider_data[0]->medium_image); 
			?>
			@section('og')
			<title>{{ $slider_data[0]->title }}</title>
			<meta name="description" content="<?php echo  $description = \Illuminate\Support\Str::words($slider_data[0]->description, 15,'...');
		    ?>"/>
			<meta property="og:title" content="{{ $slider_data[0]->title }}" />
			<meta property="og:image" content="{{$image}}" />
			<meta property="og:type" content="website" />
			<meta property="og:description" content="<?php echo $description = \Illuminate\Support\Str::words($slider_data[0]->description, 15,'...');?>"/>
			
		    @endsection		
			
		 	
			<?php $s=1; 
		    
			foreach($slider_data as $kslide=>$slider_val){  
					  $active = ($s==1)?'active':'' ; 
					  $slug_url1= url('article/'.$slider_val->slug); 
					  $article_type = $slider_val->article_type;
					  $img	='';
					  $embeded_code	='';
					  $flag= false;
					   if($article_type =='image'){
						 $image = url('storage/uploads/article/'.$slider_val->id.'/large/'.$slider_val->large_image); 
						 $img = '<img  data-animation="animated zoomInLeft" src="'.$image.'">';
					   } 
					  if($article_type =='video'){
							  $embeded_code = $slider_val->embeded_code;
							  $flag= true;
					   } 
						if($article_type =='both'){
						  if($slider_val->embeded_code!=''){
							$embeded_code = $slider_val->embeded_code;	 
							$flag= true;
						 }
						if($slider_val->large_image!=''){
							 $image = url('storage/uploads/article/'.$slider_val->id.'/large/'.$slider_val->large_image); 
							 $img = '<img  data-animation="animated zoomInLeft" src="'.$image.'">';
						 }
						} 											  

					  if($flag){
							  $height = 487;
							  $width = 872;
							  $embeded_code = preg_replace('/height="(.*?)"/i', 'height="' . $height .'"', $embeded_code);
							  $embeded_code = preg_replace('/width="(.*?)"/i', 'width="' . $width .'"', $embeded_code);
							  $embeded_code = preg_replace('/style="(.*?)"/i', 'style=""', $embeded_code);

					  }															  
					 $liked = ($slider_val->liked==1)?'unlike':'like';	
				
				   /****** like slider heart */
					$liked_heart = (like_count_heart($slider_val->id)==1)?'unlike':'like';									  
					$liked_visit = (visit_count_location($slider_val->id)==1)?'unlike':'like';									  
					$near_you = (near_you_count($slider_val->id)==1)?'unlike':'like';						  
			?>
            <!-- Item 1 -->
            <div class="item  {{$active}} slide{{$s}}">
			<div class="slide-cont-wrap">
				
			<div class="slide-cont">
                      <?php echo $img ;?>
				       <?php echo $embeded_code; ?>
				       <?php  if($slider_val->sponsored==1)  ?>
						<div class="sponsored-tag">Sponsored</div>
						
		<?php if(count($slider_data)>1){ ?>		
        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
            <i class="fa fa-angle-left"></i><span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
            <i class="fa fa-angle-right"></i><span class="sr-only">Next</span>
        </a>	
				<?php } ?>
				
				
						</div>
				<div class="slider-bottom-bar">
						<h4><a href="javascript:void(0)">{{$s}}/{{count($slider_data)}} Slides <span>&copy; Hastagbarbados images</span></a></h4>
						<div class="icon-set">
						<a href="javascript:void(0);" class="heart_acnhor_like" > 
							<i class="fa fa-heart like_heart heart_<?php echo $liked_heart; ?>" aria-hidden="true" data-article_id="<?php echo $slider_val->id;?>"
						   data-like_heart_button="<?php echo $liked_heart; ?>" data-like_type="heart" data-user_id="<?php echo Session::get('user_id'); ?>"></i> 
							
							
							<div class="hide_like_recommend l_r_box">
							<span href="javascript:void(0)" class="red  like_invite" data-button_type="invite" title="invite">
								<i class="fa fa-heart" aria-hidden="true"></i>
							
							</span>
							<span href="javascript:void(0)" class="blue like_recommend" data-button_type="recommend"  title="recommend">
									<i class="fa fa-heart" aria-hidden="true"></i>
							</span>
								<!--button type="button" class="btn btn-primary like_invite" data-button_type="invite">Invite</button>
								<button type="button" class="btn btn-primary like_recommend" data-button_type="recommend">Recommend</button-->
						   </div>
							
							</a>
						
						  <a href="javascript:void(0);" class="visit_acnhor_like" > 
							<i class="fa fa-map-marker like_visit heart_<?php echo $liked_visit; ?>" aria-hidden="true" data-article_id="<?php echo $slider_val->id;?>"
						   data-like_visit_button="<?php echo $liked_visit; ?>" data-like_type="visit" data-user_id="<?php echo Session::get('user_id'); ?>"></i> 
							
							
							<div class="hide_like_recommend l_r_box_location">
							
							<span href="javascript:void(0)" class="blue  visit_invite" data-button_type="invite" title="invite">
										<i class="fa fa-map-marker" aria-hidden="true"></i>
							</span>
							<span href="javascript:void(0)" class="green visit_recommend" data-button_type="recommend"  title="recommend">
									<i class="fa fa-map-marker" aria-hidden="true"></i>
							</span>
								<!--button type="button" class="btn btn-primary visit_invite" data-button_type="invite">Invite</button>
								<button type="button" class="btn btn-primary visit_recommend" data-button_type="recommend">Recommend</button-->
						   </div>
							
							</a>  
					
						<a href="javascript:void(0)">
					
							<i class="fa fa-compass like_near heart_<?php echo $near_you; ?>" aria-hidden="true" data-article_id="<?php echo $slider_val->id;?>"
						   data-like_near_button="<?php echo $near_you; ?>" data-like_type="near" data-user_id="<?php echo Session::get('user_id'); ?>"></i> 
							
							</a>
							<!--a href="javascript:void(0)"><i class="fa fa-arrows-alt" aria-hidden="true"></i></a-->
						
						
							</div>
							
						</div>
						</div>
	<!-- Top-Post -->
	
				<div class="tp-post box-style">
					<div class="title-wrap">
					<h3 class="title"><?php echo ucwords($slider_val->title); ?></h3>
					<div class="icon-wrap">
						<ul>
						<li><a href="javascript:void(0);"><i class="fa fa-eye" aria-hidden="true"></i><?php echo article_visited_count($article_id); ?></a></li>
							
						
						<li class="share_article"><a href="javascript:void(0);">
							<i class="fa fa-share-alt" aria-hidden="true">
							</i><?php echo count_social_share($article_id); ?></a>
						
						
						
						<?php $slug_url= url('article/'.$slider_val->slug );  echo social_icon($slug_url) ;?>
						</li>

						<li><a href="javascript:void(0);" data-button_type="{{$liked}}" class="like_article" data-article-id="{{$article_id}}" id="like_{{$article_id}}" data-single_page="1"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i><span>
							<?php echo article_like_count($article_id); ?></span></a></li>
							</ul>
					</div>
					</div>
					<div class="show">
					<p><?php echo ucwords($slider_val->description); ?></p> 

					</div>
	</div>
				
				

														
													
	<!-- Top-Post ENd -->						
             </div> 
       		 <?php $s++; } ?>
        </div>
        <!-- End Wrapper for slides-->
        <!-- Indicators -->
     <!--ol class="carousel-indicators">
		    <?php for($dot=0;$dot<count($slider_data);$dot++){
	             $active_dot = '';
		         if($dot==0) $active_dot = 'active';
		    ?>
            <li data-target="#carousel-example-generic" data-slide-to="<?php echo $dot; ?>" class="<?php echo $active_dot; ?>"></li>
		 
		   <?php } ?>
          
	 </ol--> 		
    </div>
</div>
						</div>

  <!-----COMMENT SECTION ------------>			
	@include('frontend.comment.comment')				
					
					</div>
<!-- featured Post -->
				<div class="col-md-3 col-sm-4 col-xs-12">
					<div class="featured-post you_may_like">
						<div class="feature-heading">
							<h1>You May Like</h1>
						</div>
								
					</div>				
			 </div>
<!-- featured Post End -->				
						
				</div>
				</div>	
				</div>

<!------------------ Invite and recommend Popup -------------------->
		   <div class="modal fade" id="invite_recommend" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Find Friends</h4>
                    </div>
					<div style="color:green;margin:0 auto; text-align:center" id="msg_success"></div>
					 {{ Form::open(array('url' => '', 'method' => 'post','id'=>'invite_recommend_form')) }}	
                    <div class="modal-body">
                        <select class="tokenize-remote-modal" name="users[]" multiple></select>
						<span id="select_user" class="error"></span>
                    </div>
					<input type="hidden" name="type_invite" id="type_invite" value="" />
					<input type="hidden" name="invite_article_id" id="invite_article_id" value="" />
					  {{ Form::close() }}
							
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary send_article_invite" >Send</button>
                    </div>
                </div>
            </div>
        </div>
<!------------------ Login Popup End -------------------->

        
<div class="modal fade" id="delete_comment_confirm" tabindex="-1" role="dialog" style="padding-right: 10px;" aria-hidden="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title" id="myModalLabel">Are you sure to delete this comment ?</h4>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="delete">Yes</button>
      </div>
    </div>
  </div>
</div>
     
<!------ For You may like Article load  -->
<div id="category_id" data-category_id="<?php echo $category_id; ?>" style="display:none"> </div>
<div id="total_you_may_count_page" data-total_you_may_count_page="<?php echo $total_you_may_count_page; ?>" style="display:none"> </div>
<div id="page_num" data-page_num="1" style="display:none"> </div>


@include('frontend.layouts.login_register_popup')
 @stop