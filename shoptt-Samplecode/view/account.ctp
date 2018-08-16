<?php 
/*---------------------------creating the amazon object *-------------------------------------*/
$AmazonS3 = new AmazonS3(array('AKIAJUN4MN7X6EOAMK4A', 'iQwPlU6b6XK3b1G8oBQ/RLxwdjOcYaMF5rbbSq3c', 'shoptt'));
?>
<link rel="stylesheet" href="/settings/css/jquery.fileupload.css">
<link rel="stylesheet" href="/settings/css/jquery.fileupload-ui.css">
<noscript><link rel="stylesheet" href="/settings/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="/settings/css/jquery.fileupload-ui-noscript.css"></noscript>
  <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
			<h1>Account</h1>
			<ol class="breadcrumb">
				<li><a href="javascript:void(0);" data-toggle="modal" data-target="#myModalChangePasscode" ><i class="fa fa-lock" ></i> Change Passcode</a></li>
			</ol>

		</section>

        <!-- Main content -->
        <section class="content">

          <!-- Your Page Content Here -->
          <div class="main">
		  <?php echo $this->element("settings/edit_banner");?>
			<?php echo $this->form->create('User',array('enctype'=>"multipart/form-data",'id'=>'fileupload','action'=>'account','type'=>'file','method'=>'post','url'=>array('action'=>'account','controller'=>'settings'))); ?>
			
			
            <div class="row">
				<div class="col-lg-5 col-md-6  col-xs-12">
					<div class="form-group ">
						<label class="control-label " for="name">
							Name
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->input('fullname',array('id'=>'fullname','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[required,custom[onlyLetterSp]] form-control','placeholder'=>'Enter your name')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label requiredField" for="email">
							Email Address
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->input('email',array('id'=>'email','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[required,custom[email]] form-control','placeholder'=>'Email address')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label " for="subject">
							Phone
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->input('phone',array('id'=>'phone_no','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[custom[phone]] form-control','placeholder'=>'Contact No.')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label " for="message">
							Address
						</label>
						<?php echo $this->form->input('address',array('type'=>'textarea','label'=>false,'div'=>false,'class'=>'validate[custom[onlyLetterSpSomeMore]] form-control','required'=>FALSE,'cols'=>"40",'rows'=>"10")); ?>
					</div>
					<div class="form-group ">
						<label class="control-label " for="name1">
							City
						</label>
						<?php echo $this->form->input('city',array('id'=>'city','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[custom[onlyLetterSp]] form-control','placeholder'=>'City name')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label " for="name2">
							State
						</label>
						<?php echo $this->form->input('state',array('id'=>'state','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[custom[onlyLetterSp]] form-control','placeholder'=>'State')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label " for="name3">
							Country
						</label>
						<span class="asteriskField">*</span>
						<?php  echo $this->form->input('country',array('id'=>'country','type'=>'select','empty'=>'Select Country','options'=>$countries,'label'=>false,'div'=>false,'class'=>'validate[required] selectpicker')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label " for="name4">
							Zip Code
						</label>
						<?php echo $this->form->input('zipcode',array('id'=>'zipcode','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[custom[onlyLetterNumber]]  form-control','placeholder'=>'Postal code')); ?>
					</div>
					<div id="div_radio" class="form-group">
						<label for="radio" class="control-label ">
							Sex
						</label>
						<div class="">
							<label class="radio-inline">
								<?php echo $this->form->input('User.sex',array('id'=>'radio-1-1','type'=>'radio','options'=>array('M'=>''),'checked'=>'checked','label'=>false,'div'=>false,'class'=>'regular-radio')); ?>
								Male
							</label>
							<label class="radio-inline">
								<?php echo $this->form->input('sex',array('id'=>'radio-1-2','type'=>'radio','options'=>array('F'=>''),'label'=>false,'div'=>false,'class'=>'regular-radio')); ?>
								Female
							</label>
						</div>
					</div>
					
				</div>
				<div class="col-lg-5 col-md-6  col-xs-12 col-lg-offset-2">
					
					<!-- Sidebar user panel (optional) -->
					<div class="user-panel">
						<div class="pull-left image">
							
							<?php if (strpos($this->request->data['User']['profilepic'],'graph') !== false) {?>
								<img alt="User Image" src="<?php echo $this->request->data['User']['profilepic']; ?>"  class="img-circle"  width="59" height="59">
							<?php }else if($this->request->data['User']['profilepic'] == 'http://shoptt.co/default.gif'){?>
								<img alt="User Image" src="<?php echo $this->request->data['User']['profilepic']; ?>"  class="img-circle"  width="59" height="59">
							<?php }else{ ?>
								<img alt="User Image" class="img-circle"  src="<?php echo $AmazonS3->publicUrl('profile/small/'.$this->request->data['User']['profilepic']); ?>" width="59" height="59">
							<?php } ?> 
						</div>
						<div class="pull-left info">
							<p>Max dimensions 100px by 100px<br/>png, jpg & gif format</p>
							<!-- Status -->
							
							<noscript><input type="hidden" name="redirect" value="http://shoptt.co/settings/accounts"></noscript>
							<div class="row fileupload-buttonbar">
								<div class="col-lg-7">
									<!-- The fileinput-button span is used to style the file input field as button -->
									<span class="btn btn-success fileinput-button">
										<span>ADD FILES</span>
										<input type="file" name="files[]" multiple>
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
							<table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
						</div>
					</div>
					
					<div class="form-group ">
						<label class="control-label " for="name">
							User Name
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->input('name',array('id'=>'username','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[required,funcCall[checkHELLO],custom[onlyLetterNumber]] form-control','placeholder'=>'Username')); ?>
					</div>
					
					<div class="form-group ">
						<label class="control-label " for="name">
							Business Name
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->input('businessname',array('id'=>'businessname','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[required,custom[onlyLetterSp]] form-control','placeholder'=>'Business Name')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label " for="name">
							Tagline
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->input('tagline',array('id'=>'tagline','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[required,custom[onlyLetterSp]] form-control','placeholder'=>'Tagline')); ?>
					</div>
					<div class="form-group ">
						<label class="control-label " for="message">
							About
						</label>
						<?php echo $this->form->input('bio',array('id'=>'about','type'=>'textarea','label'=>false,'div'=>false,'class'=>'validate[required,custom[onlyLetterSpSomeMore]] form-control','placeholder'=>'Enter information','cols'=>"40",'rows'=>"10")); ?>
					</div>
					<div class="form-group ">
						<label class="control-label requiredField" for="email">
							Website
							<span class="asteriskField">*</span>
						</label>
						<?php echo $this->form->input('website',array('id'=>'website','type'=>'text','label'=>false,'div'=>false,'class'=>'validate[custom[url]] form-control','placeholder'=>'https://www.')); ?>
					</div>
					
					<div class="form-group">
						<label class="control-label " for="name1">
							Currency
						</label>
						<?php  $currencies  = array('USD'=>'USD','AUD'=>'AUD','CAN'=>'CAN','EUR'=>'EUR','GBP'=>'GBP','SGD'=>'SGD'); ?>
						<?php  echo $this->form->input('currency',array('id'=>'currency','type'=>'select','empty'=>'Select Currency','options'=>$currencies,'label'=>false,'div'=>false,'class'=>'validate[required] selectpicker')); ?>
					</div>
					<div class="form-group fileupload-buttonbar">
						<div class="bttns-wrap">
							<!-- The fileinput-button span is used to style the file input field as button -->
						  <?php if(isset($slug) &&  ($slug != '')) { ?>
							<?php echo $this->form->input('store',array('id'=>'store','type'=>'hidden','label'=>false,'div'=>false,'value'=>$slug)); ?>
							
						<?php } ?>
							
							<?php echo  $this->form->submit('SAVE ALL CHANGES',array('id'=>"withfiles" ,'div'=>false,'type'=>'button','onclick'=>"$('#submitme').click();", 'name'=>'submit','class'=>'btn btn-primary start','style'=>"display:none")); ?>	
							<?php  echo $this->form->input('filestatus',array('id'=>'filestatus','type'=>'text','name'=>'data[Picture][filestatus]','label'=>false,'div'=>false,'value'=>'nofiles','style'=>"display:none")); ?>
							<?php echo  $this->form->submit('SAVE CHANGES',array('id'=>"withoutfiles" ,'div'=>false,'type'=>'submit','name'=>'submit','class'=>'btn btn-primary'));?>	
							<a href="" class="btn reset-btn">RESET</a>
						</div>
					</div>
				</div>
            </div>
			<?php  echo $this->form->end();?>
			 <?php echo $this->element("settings/uploadtable");?>
		</div>
        <div class="del-row">
			<div class="row">
				<div class="col-md-12">
					<a style="color: #908f8f;" onclick="if(confirm('Are you sure you wish to delete your account.It will delete all your products and other information ')==true){ return true} else { return false; } ;" href="<?php echo $this->Html->url(array('action'=>'deleteAccount',$this->data['User']['userid'])); ?>">Delete my account</a>
				</div>  
			</div>  
        </div>  
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<script>
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
		$('#fileupload').validationEngine();
			// binds form submission and fields to the validation engine
		$("#changepass").validationEngine({ 'custom_error_messages': {
            
            'website': {
                
                'custom[url]': {
						'message': "http:// or https:// must be present in url."
					}
				}
			}
        });
		
	
	});
		
	function checkHELLO(field, rules, i, options){
		
		words=field.val();
		hasSpaces=	words.indexOf(" ");
		  if (hasSpaces  > 0) {
			 // this allows the use of i18 for the error msgs
			  return 'Spaces are not allowed in the username';
		  }
		}

$(function () {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = window.location.hostname === 'blueimp.github.io' ?
                '//jquery-file-upload.appspot.com/' : '/settings/account';
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
        add: function (e, data) {
			var that = this;
			$.blueimp.fileupload.prototype.options.add.call(that, e, data);
			$('#withfiles').show();
			$('#withoutfiles').hide();
			$('#filestatus').val('files');
		},
		always: function (e, data) {
			//console.log(data);
			//console.log(data.jqXHR.responseText);
			var error ='';
			if(data.jqXHR.responseText){
				window.location = "/settings/account";
			}
		}
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});

</script>

<div class="modal edit-banner-pop fade" id="myModalChangePasscode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-lock"></i> Change password</h4>
			</div>
			<div class="modal-body">
				<?php echo $this->form->create('User',array('id'=>'changepass','url'=>array('controller'=>'settings','action'=>'changepassword'),'method'=>'post')); ?>
				<?php echo $this->form->input('userid',array('type'=>'hidden')); ?>
					<div class="ch-pass-pop">
						<div class="row">
							<div class="col-lg-12 col-md-12  col-xs-12">
								<div class="form-group ">
									<label class="control-label " for="name">
										New Password
										<span class="asteriskField">*</span>
									</label>
									<?php echo $this->form->input('User.password',array('id'=>'passwordbox','type'=>'password','label'=>false,'div'=>false,'class'=>'validate[required,minSize[6]] o-pass form-control','placeholder'=>'Type your new password')); ?>
								</div>
								
								<div class="form-group ">
									<label class="control-label " for="name">
										Confirm Password
										<span class="asteriskField">*</span>
									</label>
									<?php echo $this->form->input('User.confpassword',array('id'=>'confpassword','type'=>'password','label'=>false,'div'=>false,'class'=>'validate[required,equals[passwordbox]] o-pass form-control','placeholder'=>'Confirm password')); ?>
								</div>
								<div class="form-group ">
									<input type="submit" class="btn btn-primary" value="Change Password">
								</div>
							</div>
						</div>	
					</div>
				<?php echo $this->form->end(); ?>         
			</div>
		</div>
	</div>
</div>  