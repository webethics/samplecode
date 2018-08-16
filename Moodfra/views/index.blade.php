@extends('frontend.common.master')
@section('frontend')
   <div id="wrapper">
      <!-- Sidebar -->
        @include('frontend.layout.sidebar')
      <!-- /#sidebar-wrapper --> 
<div class="main-banner">
   <div class="container">
		<div id="page-content-wrapper" class="page-content">
			<div class="project-section" id="result">  
				<div class="row">
					<div class="mobile-filters visible-xs">
						<div class="col-md-12">
							<div class="small-scalable">
								<div class="custom-form">
								<!-- FILTER FORM START -->
								{{ Form::open(array('url' => '','method' => 'POST')) }}
								<fieldset>
									<div class="left-side-box box-equal-height">
										<div class="">
											<div class="form-group">
												<label>FILTER</label>
													<input type="text" placeholder="Keywords..." class="form-control" id="keyword">
											</div>
										</div>
										<div class="">
											<div class="form-group">
												<label>Exclude</label>
												<input type="text" placeholder="Keywords..." class="form-control" id="excludekeyword">
											</div>
										 </div>
										<div class="">
											 <label class="radio-inline">
											 <input class="callfunction" type="radio" name="optradio" value="video" checked>Videos
											 </label>
											 <label class="radio-inline">
											 <input class="callfunction" type="radio" name="optradio" value="images">Stills
											 </label>
										 </div>
									</div>
									<div class="right-side-box box-equal-height ">
										<div class="form-group">
											<h4>Number of people</h4> 
											 <div class="col-md-6 col-sm-6 padding-left-none">
												 <div class="form-check">
													 <label class="form-check-label">
														 <input class="form-check-input people_value" name="people_value" type="checkbox" value="people">
														 <span> People </span>
													</label>
												 </div>
											 </div>
											 <div class="col-md-6 col-sm-6 padding-left-none ">
												 <div class="form-check">
													 <label class="form-check-label"></label>
													 <input class="form-check-input people_value" name="people_value" type="checkbox" value="no_people">
													 <span> No People </span>
												 </div>
											 </div>
										</div>
									    <div class="form-group">
											<h4>People Composition</h4>  
											 <div class="col-md-6 col-sm-6 padding-left-none">
												 <div class="form-check">
													 <label class="form-check-label">
													 <input class="form-check-input people_comp" name="people_comp" type="checkbox" value="portrait">
													 <span> Portrait </span>
													 </label>
												 </div>
											 </div>
											 <div class="col-md-6 col-sm-6 padding-left-none">
												 <div class="form-check">
													 <label class="form-check-label">
													 <input class="form-check-input people_comp" name="people_comp" type="checkbox" value="no_portrait">
													 <span> No Portrait </span>
													 </label>
												 </div>
											 </div>
										</div>
									    <div class="form-group">
										 	<h4>Time of Day</h4> 
										   <div class="col-md-6 col-sm-6 padding-left-none">
												 <div class="form-check">
													 <label class="form-check-label">
														 <input class="form-check-input day_night" name="day_night" type="checkbox" value="day">
														 <span> Day </span>
													 </label>
												 </div>
											</div>
											
											<div class="col-md-6 col-sm-6 padding-left-none">
												 <div class="form-check">
													 <label class="form-check-label">
													 <input class="form-check-input day_night" name="day_night"  type="checkbox" value="night">
													 <span> Night </span>
													 </label>
												 </div>
											 </div>
										   </div>
										</div>
									  </div>
									  </fieldset>
									   {{ Form::close()}}
									   <!-- FORM CLOSE -->
								   </div>
									<div class="custom-form ">
									  <div class="">
											<label>Brightness: Dark => Light</label>   
											<div id="slidecontainer">
													<div id="slider-range"></div>
													<span style="display:none" id="ex6CurrentSliderValLabel">Current Slider Value: <span id="ex6SliderVal">1,10</span></span>
												</div>
										</div>
									</div>
									<div class="custom-form ">
									  <div class="">
											<label>Saturation: Low => High</label>   
											<div id="slidecontainer">
												<div id="slider-range-sat"></div>
												<span style="display:none" id="ex7CurrentSliderValLabel">Current Slider Value: <span id="ex7SliderVal">1,10</span></span>
												<p></p>
											</div>
										</div>
									</div>
									<div class="custom-form " id="speed">
									  <div class="">
											<label>Edit Speed: Slow => Fast</label>   
											<div id="slidecontainer">
												<div id="slider-range-speed"></div>
												<span style="display:none" id="ex8CurrentSliderValLabel">Current Slider Value: <span id="ex8SliderVal">1,10</span></span>
												
												<p></p>
											</div>
										</div>
									</div>
									<input class="btn btn-primary" type="button" name="resetFillters" onclick="resetFilters()" value="Reset">
						  </div>
					</div>
			</div>
			  <div class="row">
			   <div class="col-md-12">
				  <h1 class="pro-text"><span>Latest Films</span></h1>
			  </div>   
			  </div>   
 
   <div class="row">
   <div class="flex-cols">
			@php
			  shuffle($project);
			@endphp
			@if(count($project) > 0)
		  @foreach($project as $key => $value)
		 <?php
		$your_desired_width = 25;
		$post = $project[$key]->title;
		if (strlen($post) > $your_desired_width)
		{
			$post = wordwrap($post, 25);
			$i = strpos($post, "\n");
			if ($i) {
				$post = substr($post, 0, $i);
			}
		}
		?>
			<div class="col-md-4">
				   <div class="project-cont">
					 <h3 class="prj-title"><a href ="{{url('/project')}}/{{$project[$key]->id}}" >{{ucwords($post)}}</a></h3>
					  @php $agency = agency($project[$key]->agency_credits); @endphp
					 <div class="keyword-text">
					 @if($agency !='')
						<ul class="list-inline more-key">
						  <b>Agency Credit:</b>
							<li><a href = "{{url('/agency-credits')}}/{{$project[$key]->agency_credits}}"><span>{{$agency}}</span></a></li>
					   </ul>
					   @else
					   &nbsp;
						@endif
					  </div>  
						<?php
							$html =$project[$key]->video_code;            
							/**** embed Site array ***/
							$sites = embed_sites_array();
							foreach($sites as $k=>$site_val){
								$pos = strpos($html, $site_val);
								if ($pos === false) {
								
								} else {
									$medium_image_path =  embed_code_image($html,$site_val);
								}
							}
						?>    
						<!-- SHOW Image -->
					   <?php if(isset($medium_image_path) && !empty($medium_image_path)){?>
								<div class="prj-img-box">
									<a href ="{{url('/project')}}/{{$project[$key]->id}}" > 
										<img class="img-responsive" src="<?php echo timthumb($medium_image_path,800,450);?>">
										<i class="fa fa-play-circle  fa-3x" aria-hidden="true"></i>
									</a>
								</div>	
					  <?php }else{ ?> 	
						<div class="prj-img-box">
							<a href ="{{url('/project')}}/{{$project[$key]->id}}" > 
								<img class="img-responsive" src="images/no-video.jpg">
								<i class="fa fa-play-circle  fa-3x" aria-hidden="true"></i>
							</a>
						</div>	
					  <?php } 	
					   if(isset($value->project_keywords) && !empty($value->project_keywords)){ 
						echo '<div class="keyword-text bottom">';
					   $j=1;
						echo '<b>Keywords:</b> ';
						foreach($value->project_keywords as $v){
						  if($j<=3){if(empty($v)){
							$j--;
						  }else{?>
							 <a href="javascript:void(0)" onClick="searchbyKyword('<?php echo trim($v); ?>')"><span><?php echo trim($v); ?></span></a>
						  <?php }
						  }
						  if($j==4){
							echo '<span class="more-key-link" did="collapseKeyword_'.$value->id.'" data-placement="bottom" data-popover="true" data-html="true">More</span>';  
						  }
						  $j++;
						} ?>
						<!-- Collapse Keyowrds -->
						  <div class="collapse" id="collapseKeyword_<?php echo $value->id;?>">
							<div class="keyword-text">
								<ul class="list-inline more-key">
								  <?php $c = 1;
									foreach($value->project_keywords  as $v){ 
									  
									if($c > 3){ ?>  
										<li><a href="javascript:void(0)" onClick="searchbyKyword('<?php echo trim($v); ?>')"><span><?php echo trim($v); ?></span></a></li>
								  <?php } $c++;} ?>
								</ul>
							</div>
						  </div>       
					     <!-- Search Similar projects -->
						 <a onclick="searchsimilar('<?php echo $value->id; ?>')" class="src-similar" href="javascript:void(0)"> Search Similar</a>
					   </div>
					   <?php }else{?>
					   &nbsp;
					   <?php } ?>
							<div class="project-img">
								  <?php  
								  $i = 1;  
								  foreach($value->project_image as $key1=>$value1){
									if($i<=4){
									$image_path = thumb_img_backend($value1->prjct_id,$value1->org_img); ?>
									<a href = "{{url('/still')}}/{{$value1->id}}">
									  <img class="img-responsive" src="{{ timthumb($image_path,138,70) }}"></a>
									 <?php
									 if($i%2=== 0){ 
							   echo '</div><div class="project-img">';
							 } 
						  }
						   $i++; 
					   }
						 ?>
					   </div>
				</div>
			</div>
@endforeach
@else
	<h6 class="errordata text-center" style="display:block" >{{'No Data Found!'}}</h6>
@endif
   </div>
   </div>
		<div class="clearfix"></div>
		   <div class="button_data">
				<button style="display:none" onClick="loadProjects();" id="loadmore_<?php echo $page; ?>" class="btn btn-primary btn-lg thisloadmore" data-page="<?php echo $page; ?>" data-view ="index" data-total-record="<?php echo $total_proejcts; ?>" data-total-page="<?php echo $total_page; ?>" name="loadmore"></button> 
			<div class="loader" style="display:none"><img id="loader" src="{{url('images/Loader.gif')}}"></div>
			</div> 
	   </div>
     </div>
    </div>  
   </div>
 </div>
  @stop