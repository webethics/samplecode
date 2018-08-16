<div class="login-hold register">
    <?php echo form_open('/login', $form_attributes); ?>
        <fieldset class="">
            <div class="fl">
                <div class="toprow">
                <?php if ($error) echo '<span class="error">'.$error.'</span>'; ?>
                <?php echo validation_errors(); ?>
                <span class="error"><?php echo $same_user_login; ?></span>
				<span class="error"><?php echo $theft_attempt; ?></span>
                </div>
                <div class="row">
                    <label>Username or email:</label>
                    <span class="set1-input">
                        <input type="text" value="<?php echo $login; ?>" name="login" class="login_text_field">
                    </span>
                </div>
                <div class="row">
                    <label>Password:</label>
                    <span class="set1-input">
                        <input type="password" value="" name="password" class="login_text_field">
                    </span>
                </div>
				<div class="row">
					<label class="remember_me">
						<input type="checkbox" name="remember_me" <?php echo $remember_me ? 'checked="checked"' : '' ?> > Remember my login on this computer
					</label>
				</div>
                <?php if($selectType) { ?>
                    <input type="hidden" name="selectType" value="yes" />
                    <div class="row">
                        <label>Account type:</label>
                        <span class="set1-sel">
                            <select name="type" class="styled">
                                <?php foreach($accountTypes as $key => $value): ?>
                                    <?php if($key == $accountType) { ?>
                                        <option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php }; ?>
                                <?php endforeach; ?>
                            </select>
                        </span>
                    </div>
                <?php } else { ?>
                    <input type="hidden" name="selectType" value="no" />
                    <input type="hidden" name="type" value="<?php echo $accountType; ?>" />
                <?php }; ?>
                <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                <div class="row clearfix">
                    <a class="login-button fl" id="loginSubmit" href="javascript:void(0);"></a>
                </div>
				<div class="creat_account2">
					<a class="bussiness show_popup_signup" href="javascript:void(0)">Create a business account</a>  
					<a class="bloggers register_popup"  href="/blogger_steps">Create a blogger account</a>  
				</div>
			</div>
            <div class="creat_account">
                <div class="bor-top">
                    <a href="/forgot_password">Forgot Password?</a>
				</div>
            </div>
        </fieldset>
    </form>
</div>