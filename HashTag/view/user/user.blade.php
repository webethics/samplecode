@extends('frontend.layouts.master')
@section('content')
<div class="bottom">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<!-- Set up your HTML -->
						<div class="owl-carousel owl-theme home-slider">
							<!--Slide-1-->
						<?php 
							
							if(count($slide)>0){
							/********** slider Loop ***********/
							foreach($slide as $sldie_key =>$slide_data){
								$multiple_array =array();
								$slider_type = $slide_data->slide_type;
								if($slider_type =='multiple'){
									$article = explode(',',$slide_data->article);
									foreach($article as $k=>$art){
									$article_data = getArticleById($art);
									$multiple_array[$k] = $article_data[0];
									}						
								}
								
								/** Single image slide */
								if($slider_type=='single'){
									$article_data = getArticleById($slide_data->article);
									$data_single = $article_data[0];
									//echo "<pre>";
									//print_r($multiple_array);
									//echo "</pre>";
									$data_single = 	slider_images($data_single,'large',10,15);
								
			
								?>	
								<div>
								<div class="slide-wrap">
									<div class="col-md-12 padding-none">
										<div class="large-img">
											<img src="<?php echo $data_single['large_image'];?>" <?php echo $data_single['large_video_img']; ?>>
											<div class="caption">
												<h3><a href="<?php echo $data_single['slug_url'];?>"><?php echo $data_single['title'];?></a></h3>
												<p><?php echo $data_single['description'];?>
												</p>
												<div class="icon"><a href="<?php echo $data_single['slug_url'];?>"><i class="<?php echo $data_single['article_type'];?>" aria-hidden="true"></i></a></div>
											</div>
											<?php echo $data_single['sponsored'];?>
										</div>
									</div>
								</div>
							</div>	
							<?php	}
							if($slider_type =='multiple'){
							 $result_0 = 	slider_images($multiple_array[0],'large',10,15);
							?>
							<div>
								<div class="slide-wrap">
									<div class="col-md-8 padding-none">
										<div class="large-img">
											<img src="<?php echo $result_0['large_image']; ?> "<?php echo $result_0['large_video_img']; ?>>
											<div class="caption">
											   <h3><a href="<?php echo $result_0['slug_url']; ?>"><?php echo $result_0['title']; ?></a></h3>
												<p><?php echo $result_0['description']; ?>
												</p>
												<div class="icon"><a href="<?php echo $result_0['slug_url']; ?>"><i class="<?php echo $result_0['article_type']; ?>" aria-hidden="true"></i></a></div>
											</div>
											<?php echo $result_0['sponsored']; ?>
										</div>
									</div>
									
									<div class="col-md-4 padding-none">
										<div class="thumbnail-main">
									<?php /*** Slider thumb ***/
								       for($i=1;$i<count($multiple_array);$i++){
									   $result_1 = 	slider_images($multiple_array[$i],'thumb_slider',10,6);
									?>
											<div class="thumbnail-img">
												<img src=" <?php echo timthumb_home_slider_image($result_1['thumb_image']) ; ?>"<?php //echo $result_1['video_img_width']; ?>>
												<div class="caption">
													<h5><?php echo $result_1['description']; ?> </h5>
													<div class="icon"><a href="<?php echo $result_0['slug_url']; ?>"><i class="fa fa-play" aria-hidden="true"></i></a></div>									  
												</div>
												<?php echo $result_1['sponsored']; ?>
												<a href="<?php echo $result_1['slug_url']; ?>" class="thumbnail-link"></a>
											</div>
										<?php } ?>
										
										</div>
									</div> 
									
								</div>
							</div>
							<?php }	} }  ?>	
						</div>
					</div>
				</div>
<!----------------------------------------------- featured and main-post -------------------------------------------------->
				<div class="post-section">
					<div class="row">
						<div class="col-md-3 col-sm-4 col-xs-12">
							<div class="featured-post">
								<div class="feature-heading">
									<h1>Featured </h1>
								</div>
							<?php if (count($feauture_article)>0){
								  foreach($feauture_article as $key=>$value){
								   //$desc = substr($value->description,0,60); 
								  // $desc = 
								   $img_cls = 'blog-post-img'; 
								   //$small=  $value->small_image;
								   $small=  $value->large_image;
								  
								 //echo  $slug = str_slug($value->title);
								  
								   $video_img_width =''; 
								   $thumb_image = url('public/storage/uploads/article/'.$value->id.'/large/'.$small); 
								   if($value->article_type =='image'){
										$article_type ='fa fa-camera';      
								   }
								   /** if type is video **/
								   else if($value->article_type =='video'){
									   $article_type ='fa fa-play';	
									   $mystring = $value->embeded_code;							   
									   $html =$value->embeded_code;						 
									    /**** embed Site array ***/
									   $sites = embed_sites_array();
									   foreach($sites as $k=>$site_val){
										   $pos = strpos($html, $site_val);
										   if ($pos === false) {
											} else {
												
												$thumb_image=  embed_code_image($html,$site_val);
											}  
									   
									   }
									   /********** End Video image fetch *****/
								   }
								   /************* type Both ************/
								   else{
								   	$article_type ='fa fa-puzzle-piece';
									  if($small!=''){
										 $thumb_image = url('public/storage/uploads/article/'.$value->id.'/large/'.$small); 

									  }else if($value->embeded_code!=''){
									  
										$html =$value->embeded_code;						 
									    /**** embed Site array ***/
									   $sites = embed_sites_array();
									   foreach($sites as $k=>$site_val){
										   $pos = strpos($html, $site_val);
										   if ($pos === false) {
											} else {
												
												$thumb_image=  embed_code_image($html,$site_val);
											}  
									   
									   }
									   /********** End Video image fetch *****/
									  }	   
								   }

								   $sponsored =''; 
								   if($value->sponsored==1)
									 $sponsored='<span>Sponsored</span>';
								
									 $slug_url= url('article/'.$value->slug); 
								  ?>
								<ul class="media-list main-list">
									<li class="media">
										<a class="" href="{{$slug_url}}">
											<?php echo $sponsored;?>
											<!--img class="{{$img_cls}}"  {{$video_img_width}} src="{{$thumb_image}}"-->
											<img class="{{$img_cls}}" src="<?php echo timthumb_featured_you_may($thumb_image);?>">
											
											<div class="cam"><i class="{{$article_type}}" aria-hidden="true"></i></div>
										</a>
										<div class="media-body">
											<p>{!! \Illuminate\Support\Str::words($value->description, 8,'...')  !!}</p>
											<ul class="icon-ul">
												<li><a href="{{$slug_url}}"> <img class="i-img" src="{{url('images/eye.png')}}" alt="..."> <?php echo article_visited_count($value->id); ?></a></li>
												<li><a href="{{$slug_url}}"> <img class="i-img" src="{{url('images/chat-icon.png')}}" alt="..."> <?php echo comment_count($value->id); ?></a></li>
													<li class="share_article">
															
															<a href="javascript:void(0);">
																<img class="i-img" src="<?php echo url('images/share.png'); ?>" alt="..."> 
																<?php echo count_social_share($value->id); ?>
															</a>
														 	
														  <?php echo social_icon($slug_url) ;?>
														
														</li>
												</ul>
											</div>
										</li>
									</ul>
								 <?php } } else { ?>
								
								<div> <span class="error"> No Featured Article Found. </span></div>
									 <?php }   ?>
								</div>
							</div>
							<div class="clear-fix"></div>
						
							<div class=" col-md-9 col-sm-8 col-xs-12">
								<div class="row">
								<div class="main-blog-post">
								<?php if (count($articles)>0) {
								  foreach($articles as $key1=>$value1){
									  $color= $value1->color;
								   $img_cls1 = 'blog-post-img'; 
								   $medium_image=  $value1->large_image;
								   $video_img_width1 =''; 
								   $medium_image_path = url('public/storage/uploads/article/'.$value1->id.'/large/'.$medium_image); 
								   if($value1->article_type =='image'){
										$article_type1 ='fa fa-camera';      
								   }
								   /** if type is video **/
								   else if($value1->article_type =='video'){
									   $article_type1 ='fa fa-play';	
									   $mystring = $value1->embeded_code;							   
									   $html =$value1->embeded_code;						 
									   /**** embed Site array ***/
									   $sites = embed_sites_array();
									   foreach($sites as $k=>$site_val){
										   $pos = strpos($html, $site_val);
										   if ($pos === false) {
											} else {
												
												$medium_image_path =  embed_code_image($html,$site_val);
											}  
									   
									   }
									   /********** End Video image fetch *****/
								   }
								   /************* type Both ************/
								   else{
								   	$article_type1 ='fa fa-puzzle-piece';
									  if($medium_image!=''){
										 $medium_image_path = url('public/storage/uploads/article/'.$value1->id.'/large/'.$medium_image); 

									  }else if($value1->embeded_code!=''){
									  
										$html =$value1->embeded_code;						 
									   /**** embed Site array ***/
									   $sites = embed_sites_array();
									   foreach($sites as $k=>$site_val){
										   $pos = strpos($html, $site_val);
										   if ($pos === false) {
											} else {
												
												$medium_image_path=  embed_code_image($html,$site_val);
											}  
									   
									   }
									   /********** End Video image fetch *****/
									  }	   
								   }

									
								   $sponsored =''; 
								   if($value1->sponsored==1)
									 $sponsored='<span>Sponsored</span>';
									
									 $slug_url1= url('article/'.$value1->slug); 
									 $liked = ($value1->liked==1)?'unlike':'like';
								    ?>
										<div class="col-md-4 col-sm-6">
											<div class="main-post red"style="border-bottom:3px solid #<?php echo $color; ?>">
												<div class="mp-box">
													
													<a href="{{$slug_url1}}">
														<?php echo $sponsored;?>
														<!--img class="{{$img_cls1}}" {{$video_img_width1}} src="{{url('plugin/timthumb/timthumb.php?src=')}}{{$medium_image_path}}&w=284&h=189" alt="{{$value1->title}}"-->
														
														<img class="{{$img_cls1}}" src="<?php echo timthumb_article_listing($medium_image_path);?>" alt="{{$value1->title}}">
														<div class="cam"><i class="{{$article_type1}}" aria-hidden="true"></i></div>
													</a>
												</div>
												<div class="mp-sub-box">
													<a href="{{$slug_url1}}">
														<h6><?php echo article_title($value1->title); ?> </h6>
													</a>
													<ul class="icon-ul">
														<li><a href="{{$slug_url1}}"><img class="i-img" src="<?php echo url('images/eye.png');?>" alt=""> <?php echo article_visited_count($value1->id); ?></a></li>
														<li><a href="{{$slug_url1}}"><img class="i-img" src="<?php echo url('images/chat-icon.png');?>" alt=""> <?php echo comment_count($value1->id); ?> </a></li>
														<li class="share_article">
															
															<a href="javascript:void(0);">
																<img class="i-img" src="<?php echo url('images/share.png'); ?>" alt="..."> 
																<?php echo count_social_share($value1->id); ?>
															</a>
														 	
														  <?php echo social_icon($slug_url1) ;?>
														
														</li>
													</ul>
													<p><?php echo article_description($value1->description) ;?></p>
													<ul class="icon-ul like-watch">
														<li class="pull-left"> 
															<a href="javascript:void(0);" data-button_type="{{$liked}}" class="like_article" data-article-id="{{$value1->id}}" id="like_{{$value1->id}}"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> <span><?php echo article_like_count($value1->id); ?></span> Likes</a>
														</li>
														<li class="pull-right"> <a href="javascript:void(0);"><i class="fa fa-clock-o" aria-hidden="true"></i>   <?php echo timeAgo($value1->created_at) ?></a></li>
													</ul>
												</div>
											</div>
										</div>
										 <?php } } ?>
										
								</div>
							</div>
								 <?php if($Allarticles > per_page_limit()){ ?>
								<div class="load-more" >
									<a href="javascript:void(0);" data-page='1' id="loadmore" data-per_page_record="<?php echo per_page_limit(); ?>" data-total_record="<?php echo total_article()-per_page_limit();?>" >Load More Posts</a>
									
								</div>
								<div class="loader_img" style="display:none">
									<img src="<?php  echo url('images/loader.gif'); ?>"  width="70" height="70" />
								</div>
								 <?php  } ?>
						</div>
						
								
							
						
					</div>
					<!-- featured and main-post End -->	
				</div>
			</div></div>
@include('frontend.layouts.login_register_popup')
 @stop