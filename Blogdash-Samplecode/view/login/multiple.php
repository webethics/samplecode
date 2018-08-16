<div class="login-hold register">
    <p class="error"><?php echo $this->session->flashdata('error'); ?></p>
    <?php echo form_open('/login/choose', $form_attributes); ?>
        <fieldset class="">
            <div class="fl">
                <div class="row">
                    <label>You have the same username for your business and blogger accounts. Please select the type of account you would like to open and then click on Login</label>
					  
                        <select name="login">
                            <option value="0" selected="selected">-- Chose One --</option>
                            <?php foreach($items as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value['username']; ?></option>
                            <?php endforeach; ?>
                        </select>
                </div>
                <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
                <div class="row">
                    <a class="login-button" id="loginSubmit" href="javascript:void(0);"></a>
                </div>
            </div>
        </fieldset>
    </form>
</div>
