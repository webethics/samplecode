<?php echo form_open('', array('id' => 'business_login_form')); ?>
	<input type="hidden" name="business_login" value="1" />
	<input type="hidden" name="redirect" value="<?php echo $redirect ?>" />

<div class="business-login-popup">
	<div class="business-login-popup1"> </div>
	<div class="business-login-popup2">
		<div class="login-hold">
			<h2 class="green">Login</h2> 
			<div class="fl">
                <div class="toprow">
				<?php if (isset($error)) echo '<span class="error">'.$error.'</span>'; ?>
                </div>
				<div class="row">
					<label >User Name:</label>
					<span class="set1-input">
						<input class="" type="text" name="login" value="<?php echo $login; ?>" />
					</span>
					<?php echo '<span class="error">'.form_error('login').'</span>' ?>
				</div>
				<div class="row">
					<label >Password:</label>
					<span class="set1-input">
						<input class="" type="password" name="password" value="" />
					</span>
					<?php echo '<span class="error">'.form_error('password').'</span>' ?>
				</div>
				<div class="row">
					<label class="remember_me">
						<input type="checkbox" name="remember_me" <?php echo $remember_me ? 'checked="checked"' : '' ?> > Remember my login on this computer
					</label>
				</div>
			</div>
			<div class="creat_account">
				<a class="bussiness show_popup_signup" href="javascript:void(0)">Create a business account</a>    
				<a href="javascript:void(0)" onclick="business_sign_in()" class="sign"></a>
				<a class="bloggers register_popup" href="/blogger_steps">Create a blogger account</a>    
                
               <div class="bor-top">
                   <a href="/forgot_password" class="">Forgot Password?</a> 
				</div>
			</div>
		</div>
	</div>
	<div class="business-login-popup3"> </div>
</div>

</form>