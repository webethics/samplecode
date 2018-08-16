<div class="form_result"> </div>
<div id="register">
<div class="signup-popup1"></div>
    <div class="signup-popup2">
        <div class="signup-hold">
		<?php if ($hash){
			echo form_open("/blogger_steps/claimed?hash=".$hash,array("name"=>"subform","id"=>"subform","class"=>"cmxform"));    
		}else{    
			echo form_open("/blogger_steps",array("name"=>"subform","id"=>"subform","class"=>"cmxform"));    
		}   ?>
			<fieldset class="">
                    <input type="hidden" name="type" value="" >
					<input type="hidden" id="manager" name="manager" value="<?php echo $manager;?>" >
                    <div class="fl">
                        <div class="row">
                            <label>First Name:</label>
                            <span class="set1-input">
								<?php if ($hash){?>
									<input  type="text" id="first_name" name="first_name" <?php if(strlen($first_name)>=2){ echo 'value="'.$first_name.'"'; }else{  echo 'value=""'; }?> />    
								<?php }else{ ?>    
									<input  type="text" name="first_name" value="<?php echo set_value('first_name'); if($this->session->userdata('first_name')) echo $this->session->userdata('first_name');?>" id="first_name" />      
								<?php } ?>
							</span>
							<label  class="error"><?php echo form_error('first_name')?></label>
					   </div>
                        <div class="row">
                            <span><label>Last Name:</label> </span>
                            <span class="set1-input">
								<?php if ($hash){?>		
									<input  type="text" name="last_name" id="last_name" <?php if(strlen($last_name)>=2){ echo 'value="'.$last_name.'"'; }else{  echo 'value=""'; }?> />    
								<?php }else{ ?>    
									<input  type="text" name="last_name" id="last_name" value="<?php echo set_value('last_name'); if($this->session->userdata('last_name')) echo $this->session->userdata('last_name');?>" />    
								<?php } ?>  
							</span>
							<label class="error"><?php echo form_error('last_name')?></label>
                        </div>
						
						 <div class="row">
                            <span><label>Email:</label></span>
                            <span class="set1-input">
                              <input class="" style="size:100px;"type="text" name="email" id="email" value="<?php echo set_value('email');?>"/>
							</span>
							<label class="error"><?php echo form_error('email')?></label>
	<span id="email_loader" style="display:none;"> <img alt="Refresh Captcha" src="/images/elem/ajax-loader.gif"/>	
                            </span>
                        </div>
						 <div class="row">
                            <label>Username:</label>
                            <span class="set1-input">
                               <input class="" type="text" name="username" id="username" value="<?php echo set_value('username');?>" />
                            </span>
							 <label class="error"><?php echo form_error('username');?></label>
							<span id="user_loader" style="display:none;"> <img alt="Refresh Captcha" src="/images/elem/ajax-loader.gif"/>	
                            </span>
						
                        </div> 
						<div class="row">
                            <label>Password:</label>
                            <span class="set1-input">
                               <input class="" type="password" name="password" id="password" value="<?php echo set_value('password');?>" />
                            </span>
							<label class="error"><?php echo form_error('password')?></label>
                        </div>
						<div class="row">
                            <label>Confirm Password:</label>
                            <span class="set1-input">
                               <input class="cufon" type="password" name="passconf" id="passconf" value="<?php echo set_value('passconf');?>" />
                            </span>
							<label class="error"><?php echo form_error('passconf')?></label>
                        </div>
					<?php  /* if signin with twitter  then does not show captcha */
					  $twitter=$this->session->userdata('captcha'); if(!$twitter){?>
						<div id="captcha_hide" >
						<div class="row row1 ">
                            <label>&nbsp;</label>
                            <span >
							  <img id ="img" src="/blogger_steps/captcha"/> 
							 <img id="loader-im" onclick="captcharefresh(110,35,6);" alt="Refresh Captcha" class="refresh_btn"  src="/images/elem/refresh.png"/>	
													 
                            </span>
                        </div>
					
						<div class="row">
                            <label for="security_code">Security Code: </label>
                            <span class="set1-input">
                          <input id="security_code" name="security_code" type="text" value=""  />
						 
                            </span>
							<label class="error"><?php echo form_error('security_code')?></label>
							<span ></span>
                        </div>
						</div>
						<?php }?>
                    </div>
					 <span id="form_img" style="display:none" ><img alt="" src="/images/elem/ajax-loader1.gif"/></span>
					 <div class="creat_account">
						<?php if (!$hash){?>
							<a href="javascript:void(0)" class="tw-sign" onclick="open_twitter_popup();"></a>  
							<a href="javascript:void(0)" class="sign" onclick="$('#subform').submit();" ></a>    
						<?php }else{ ?>  
							<a href="javascript:void(0)" style="margin-right:190px;" class="sign" onclick="$('#subform').submit();" ></a>    
						<?php } ?>
                   </div>
				
                </fieldset>
		    <?php echo form_close();?>
        </div>
    </div>
    <div class="signup-popup3"></div>
  </div>
   
       
            
             
             