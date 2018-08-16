	<!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
			<h1>New Post</h1>
		</section>

        <!-- Main content -->
        <section class="content">

          <!-- Your Page Content Here -->
          <div class="main">
			<?php echo $this->form->create('Picture',array('enctype'=>"multipart/form-data",'id'=>'fileupload','type'=>'file','url'=>array('controller'=>'settings','action'=>'newPost')));?>
			<?php echo $this->form->input('latitude',array('id'=>'lat','type'=>'text','value'=>'0','style'=>'display:none','label'=>false,'div'=>false));?>
			<?php echo $this->form->input('longitude',array('id'=>'long','type'=>'text','value'=>'0','style'=>'display:none','label'=>false,'div'=>false));?>
		    <div class="row">
				<div class="col-lg-5 col-md-6  col-xs-12">
					<div class="form-group ">
						<label class="control-label " for="name">
							Title
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->input('title',array('id'=>'title','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[required,custom[onlyLetterSpSomeMore]] form-control','placeholder'=>'ENTER TITLE')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label requiredField" for="email">
							Product Description
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->textarea('description',array('label'=>false,'div'=>false,'class'=>'validate[required,custom[onlyLetterSpSomeMore]] form-control','placeholder'=>'DESCRIPTION','style'=>'padding:10px;')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label " for="name3">
							All Category
						</label>
						<span class="asteriskField">*</span>
						 <?php  echo $this->form->input('maincategory',array('id'=>'maincategory','type'=>'select','options'=>$mainCategory,'empty'=>'Select Main Category','class'=>'validate[required]','label'=>false,'div'=>false)); ?>
					</div>
					<div class="form-group ">
						 <?php  echo $this->form->input('subcategoryid',array('id'=>'category','type'=>'select','options'=>$category,'empty'=>'Select Category','class'=>'','label'=>false,'div'=>false)); ?>
					</div>
					<div class="form-group ">	
						 <?php  echo $this->form->input('subcatid',array('id'=>'subcategory','type'=>'select','options'=>$subcat,'empty'=>'Select Category','class'=>'','label'=>false,'div'=>false)); ?>
						 
					</div>
					
					<div class="row"  id="mySizeQtymain" >
						<div class="form-group col-lg-4 col-sm-4 col-md-4">
							<label class="control-label " for="name3">
								Size
							</label>
							<span class="asteriskField">*</span>
							 <?php  echo $this->form->input('size',array('id'=>'sizeid','type'=>'select','options'=>$sizes,'empty'=>'Select Size','class'=>'validate[required]  n-post-sel','label'=>false,'div'=>false,'name'=>'data[Picture][size][]')); ?>
						</div>
						<div class="form-group col-lg-4 col-sm-4 col-md-4">
							<label class="control-label " for="name">
								Quantity
								<span class="asteriskField">*</span>
							</label>
							<?php  echo $this->form->input('quantity' ,array('id'=>'quantity','type'=>'text','class'=>'validate[required,custom[onlyNumberSp]] form-control','label'=>false,'div'=>false,'placeholder'=>'Quantity of Your Item','name'=>'data[Picture][qty][]')); ?>
						</div>
						<div class="form-group col-lg-4 col-sm-4 col-md-4">
							<label class="control-label " for="name">
								Price per Size <span title="" class="circle" data-toggle="tooltip" data-original-title="Add price here only if your price varies for size - this is predominantly for repeat product that are different sizes like prints. If the price is the same for all sizes please enter price below">?</span>
								
							</label>
							<?php echo $this->form->input('sizeprice',array('id'=>'sizeprice','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[custom[number]] form-control','placeholder'=>'Price of Your Item','name'=>'data[Picture][sizeprice][]')); ?>
						</div>
					</div>
					<div id="mySizeQty"></div>
					<a id="addNewSize"  style=" float: right;margin: 2px 3px;" href="javascript:void(0);" onclick="addSize();"><i class="fa fa-plus-circle"></i> ADD MORE SIZES</a>
					
					<div class="form-group">
						<label class="control-label " for="name">
							Price per Item
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->input('price',array('id'=>'price','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[required,custom[number]] form-control','placeholder'=>'Price of Your Item')); ?>
					</div>
					
					<?php  $currencies  = array('USD'=>'USD','AUD'=>'AUD','CAN'=>'CAN','EUR'=>'EUR','GBP'=>'GBP','SGD'=>'SGD'); ?>
					<div class="form-group ">
						<label class="control-label " for="name3">
							Currency
						</label>
						<span class="asteriskField">*</span>
						 <?php  echo $this->form->input('currency',array('id'=>'currency','type'=>'select','empty'=>'Select Currency','options'=>$currencies,'label'=>false,'div'=>false,'class'=>'validate[required] ','default'=>$this->request->data['User']['currency'])); ?>
					</div>
					<?php $this->Form->unlockField('Picture.sizeid','Picture.sizeprice','Picture.quantity'); ?>
					<?php
					if(isset($shipping['Shipping']['international_single']) && !empty($shipping['Shipping']['international_single'])){
						$international_single = $shipping['Shipping']['international_single'];
					}else{
						$international_single = '';
					}
					if(isset($shipping['Shipping']['international_multiple']) && !empty($shipping['Shipping']['international_multiple'])){
						$international_multiple = $shipping['Shipping']['international_multiple'];
					}else{
						$international_multiple = '';
					}
					if(isset($shipping['Shipping']['national_single']) && !empty($shipping['Shipping']['national_single'])){
						$national_single = $shipping['Shipping']['national_single'];
					}else{
						$national_single = '';
					}
					if(isset($shipping['Shipping']['national_multiple']) && !empty($shipping['Shipping']['national_multiple'])){
						$national_multiple = $shipping['Shipping']['national_multiple'];
					}else{
						$national_multiple = '';
					}
					?>	
					<div class="row">
					
						<div class="form-group col-lg-6 col-sm-6 col-md-6">
							<label class="control-label " for="name">
								National Shipping Cost
								<span class="asteriskField">*</span>
							</label>
							
							<?php echo $this->form->input('nationalprice',array('id'=>'nationalprice','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[required,custom[number]] form-control','placeholder'=>'National','value'=>$national_single)); ?>
						</div>
						<div class="form-group col-lg-6 col-sm-6 col-md-6">
							<label class="control-label " for="name">
								International Shipping Cost
								<span class="asteriskField">*</span>
							</label>
							
							<?php echo $this->form->input('internationalprice',array('id'=>'internationalprice','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[required,custom[number]] form-control','placeholder'=>'International','value'=>$international_single)); ?>

						</div>
					</div>
				</div>
				<div class="col-lg-5 col-md-6  col-xs-12 col-lg-offset-2">
					
					<!-- Sidebar user panel (optional) -->
					<div class="form-group user-panel">
						<label class="control-label " for="name">
							Upload Images
							<span class="asteriskField">*</span>
						</label>
						<div class="clearfix"></div>
						<div class="pull-left info no-padding table">
							<div class="row fileupload-buttonbar">
								<div class="col-lg-7">
									<!-- The fileinput-button span is used to style the file input field as button -->
									<span class="btn btn-success fileinput-button">
										<span>ADD FILES</span>
										<input type="file" class="" name="files[]" multiple>
									</span>
									 
									<span class="fileupload-process"></span>
								</div>
								<!-- The global progress state -->
								<div class="col-lg-5 fileupload-progress fade">
									<!-- The global progress bar -->
									<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
										<div class="progress-bar progress-bar-success" style="width:0%;"></div>
									</div>
									<!-- The extended global progress state -->
									<div class="progress-extended">&nbsp;</div>
								</div>
							</div>
							
								<!-- The table listing the files available for upload/download -->
							<div class="" id="unseen">	
								<table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
							</div>	
						</div>
					</div>
					<div class="social-bar">
						<div class="row">
						  <div class="col-lg-12">
							
							<div class="social-fb">
							  <div class="social-img ">
							  
							  <?php if(isset($this->request->data['User']['twitter_profile_pic']) && $this->request->data['User']['twitter_profile_pic'] != ''){ ?>
									<img alt="User Image" class="img-circle" src="<?php echo $this->request->data['User']['twitter_profile_pic']; ?>" class="left">
								<?php }else{?>
								<?php 
								$AmazonS3 = new AmazonS3(array('AKIAJUN4MN7X6EOAMK4A', 'iQwPlU6b6XK3b1G8oBQ/RLxwdjOcYaMF5rbbSq3c', 'shoptt'));
								if (strpos($this->request->data['User']['profilepic'],'graph') !== false) {?>
									<img height="52" width="52" src="<?php echo $this->request->data['User']['profilepic']; ?>" alt="User Image" class="img-circle">
								<?php }else{?>
									<img height="52" width="52" src="<?php echo $AmazonS3->publicUrl('profile/small/'.$this->request->data['User']['profilepic']); ?>" alt="User Image" class="img-circle">
								<?php } ?> 
								<?php } ?>	
							 </div>
							  <div class="social-top">
								<h3 class="social-text">
									<?php if($this->request->data['User']['twitter_name']){ ?>
										<?php echo $this->request->data['User']['twitter_name']; ?>
									<?php }else{?>
										<?php echo $this->request->data['User']['name'];?>
									<?php } ?>	</h3>
								<div class="social-cont">
								  <p>Twitter</p>
								  <div class="social-icon">
									<?php
									if($show_twitter == '1' ){	
										echo $this->form->input('show_on_twitter',array('id'=>'myonoffswitch1','type'=>'checkbox','label'=>false,'div'=>false,'class'=>'onoffswitch-checkbox','data-toggle'=>"toggle1", 'data-style'=>"ios1","onchange"=>"tw_checkBoxClicked(this.checked)",'placeholder'=>'Enter Title','checked'=>'checked')); 
									}else{
										echo $this->form->input('show_on_twitter',array('id'=>'myonoffswitch1','type'=>'checkbox','label'=>false,'div'=>false,'class'=>'onoffswitch-checkbox','data-toggle'=>"toggle1", 'data-style'=>"ios1","onchange"=>"tw_checkBoxClicked(this.checked)",'placeholder'=>'Enter Title')); 
									}	
										?>
								  </div>
								</div>
							  </div>
							</div>
							
						  </div>
						</div>
					  </div>
					  
					   <?php if($show_facebook == '1' && $fbpages){ ?>
						<div class="form-group ">
							<label class="control-label " for="name">
								Facebook Pages
							</label>
							<?php  echo $this->form->input('size',array('id'=>'fbpage','type'=>'select','options'=>$fbpages,'name'=>'data[Picture][fbpage]','empty'=>'Select Page','class'=>' select n-post-sel','label'=>false,'div'=>false,'style'=>"width:100% !important")); ?>
						</div>
						<?php } ?>
						
					<div class="form-group fileupload-buttonbar">
						<div class="bttns-wrap">
							<?php echo  $this->form->submit('Publish',array('id'=>"withfiles" ,'div'=>false,'type'=>'button', 'name'=>'submit','class'=>'btn btn-primary start','style'=>'float: left')); ?>	
							<a style="float: left;"  href="<?php echo $this->Html->url(array('action'=>'products')); ?>" class="btn reset-btn">Discard</a>
							<span id="loader" style="display: none; float: left;margin-left:10px"><img src="/img/loading.gif"></span>
						</div>
					</div>
				</div>
            </div>
			<?php echo $this->form->end(); ?>
			
 <?php echo $this->element("settings/uploadtable");?>

		</div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script type="text/javascript">
	function addSize(){
		var myData=$('#mySizeQtymain').html();   
		myData='<div class="mySizeQtydiv row">'+myData+'<a class="mydel" style="" href="javascript:void(0);" onclick="deleteSize(this);"><i class="fa fa-minus-circle"></i> REMOVE</a></div>';
		$('#mySizeQty').append(myData);
	}
	 
	function deleteSize(obj){
		$(obj).parent().remove();
	}
	$(document).ready(function(){
		var countryVal1 = $("#currency").val();
		$( "#currency" ).change(function() {
			if (confirm('Changing Currency will change your products into that currency')) {
				var countryVal = $("#currency").val();
				$(this).val(countryVal);
			} else {
				$(this).val(countryVal1); //set back
				return;
			}
		});
		
		$("#fileupload").validationEngine('attach', {promptPosition : "topRight", scroll: false});
		// binds form submission and fields to the validation engine
		$("#changepass").validationEngine();
		$('#maincategory').change(function() {
			var wire= $(this).val();
			var Token=$('form#fileupload input[name="data[_Token][key]"]').val(); 
			var fields = $('form#fileupload input[name="data[_Token][fields]"]').val();
			$.ajax({
				type: "POST",
				url: "<?php echo $this->webroot;?>settings/getcategory/"+wire,
				data: { wire: wire , 'data[_Token][key]':Token,'data[_Token][fields]':fields, 'data[_Token][unlocked]':'submit','_method':'Put', submit: "submit" },
				success: function(result){
					$("#category").html(result);
				}
			});
		});
		 $('#category').change(function() {
			var wire	= $(this).val();
			var Token	= $('form#fileupload input[name="data[_Token][key]"]').val(); 
			var fields 	= $('form#fileupload input[name="data[_Token][fields]"]').val();
			$.ajax({
				type: "POST",
				url: "<?php echo $this->webroot;?>settings/getsubcategory/"+wire,
				data: { wire: wire , 'data[_Token][key]':Token,'data[_Token][fields]':fields, 'data[_Token][unlocked]':'submit','_method':'Put', submit: "submit" },
				success: function(result){	 
					$("#subcategory").html(result);
				}
			});
		}); 
		
		$('#radio-1-1M,#radio-1-2F').click(function() {
			var wire= $(this).val();
			$.ajax({
			    type: "POST",
			    url: "<?php echo $this->webroot;?>settings/getSize/"+wire,
			    data: { wire: wire , submit: "submit" },
			    success: function(result){
			        $(".n-post-sel").html(result);
			    }
			});
		});
		function getLocation() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(showPosition);
			} else { 
				x.innerHTML = "Geolocation is not supported by this browser.";
			}
		}
	
		function showPosition(position) {
		   $('#lat').val(position.coords.latitude);
		   $('#long').val(position.coords.longitude); 

		}
		getLocation();
	});
        
/*jslint unparam: true */
/*global window, $ */
$('.start').click(function(){
	'use strict';
    // Change this to the location of your server-side upload handler:
    var url = window.location.hostname === 'blueimp.github.io' ?
                '//jquery-file-upload.appspot.com/' : '/settings/newPost';
		$('#loader').show();		
	if ( $("#fileupload").validationEngine('validate') ) {		
		
		$('#fileupload').fileupload({
			url: url,
			dataType: 'json',
			done: function (e, data) {
				$.each(data.result.files, function (index, file) {
					$('<p/>').text(file.name).appendTo('#files');
				});
			},
			progressall: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				
				$('#progress .progress-bar').css(
					'width',
					progress + '%'
				);
			},
			always: function (e, data) {
				//console.log(data);
				//console.log(data.jqXHR.responseText);
				var error ='';
				$('#loader').hide();
				if(data.jqXHR.responseText){
					window.location = "/settings/products";
				}
			},
			fail : function (jqXHR, textStatus, errorThrown) {
				console.log(errorThrown);
			}
		}).prop('disabled', !$.support.fileInput)
			.parent().addClass($.support.fileInput ? undefined : 'disabled');
	}	
});

function checkBoxClicked(id){
	if(id == true){
		var myWindow = window.open("/users/facebook_connect", "MsgWindow", "width=500, height=500");
		
	}
	if(id == false){
		$.ajax({
			type: "POST",
			url: "<?php echo $this->webroot;?>settings/facebookoff/",
			data: {  submit: "submit" },
			success: function(result){	 
				window.location.reload();
			}
		});
	}
		
}

function tw_checkBoxClicked(id){
	if(id == true){
		var myWindow = window.open("<?php echo $twitterObj->getAuthorizationUrl(); ?>", "MsgWindow", "width=500, height=500");
		
	}
	if(id == false){
		$.ajax({
			type: "POST",
			url: "<?php echo $this->webroot;?>settings/twitteroff/",
			data: {  submit: "submit" },
			success: function(result){	 
				window.location.reload();
			}
		});
	}
		
}
</script> 