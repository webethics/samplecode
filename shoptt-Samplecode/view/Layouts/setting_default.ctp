<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		
		<?php if(isset($pageVar['menu']) && !empty($pageVar['menu'])){?>
			<title>SHOPTT | <?php echo ucwords($pageVar['menu']);?></title>
		<?php }else{ ?>
			<title>SHOPTT </title>
		<?php } ?>	
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap 3.3.5 -->
		<?php 
		$AmazonS3 = new AmazonS3(array('AKIAJUN4MN7X6EOAMK4A', 'iQwPlU6b6XK3b1G8oBQ/RLxwdjOcYaMF5rbbSq3c', 'shoptt'));
		echo $this->Html->charset("utf-8");
		echo $this->Html->meta('keywords','Shoptt');
		echo $this->Html->meta('description','Shoptt');
		echo $this->Html->meta('favicon.ico','/favicon.ico',array('type'=>'icon'));
		
		echo $this->Html->css("/settings/css/bootstrap/css/bootstrap.min.css");
		echo $this->Html->css("/settings/css/bootstrap/css/font-awesome.min.css");
		echo $this->Html->css("/settings/css/AdminLTE.css");
		echo $this->Html->css("/settings/css/skins/skin-blue.css");
		echo $this->Html->css("/settings/css/bootstrap-select.min.css");
		echo $this->Html->css("/settings/css/bootstrap/css/bootstrap-toggle.min.css");
		echo $this->Html->css('/maintheme/css/validationEngine.jquery.css',array('media'=>'screen'))."\n";
		echo $this->Html->css("/redactor/redactor");
		echo $this->Html->script('/settings/js/plugins/jQuery/jQuery-2.1.4.min.js')."\n";
		?>
	</head>

	<body class="hold-transition skin-blue sidebar-mini">
		<div class="wrapper">

		   <?php echo $this->element("settings/header");?>
			<!-- Left side column. contains the logo and sidebar -->
		   <?php echo $this->element("settings/sidebar");?>
			<div class="content-wrapper notification_message">
				<?php echo $this->Session->flash(); ?>
             </div>   
			<?php echo $this->fetch('content'); ;?>

			<div class="control-sidebar-bg"></div>
			<?php echo $this->element("settings/footer");?>
		</div><!-- ./wrapper -->

		<!-- REQUIRED JS SCRIPTS -->
		<?php 
		echo $this->Html->script('/maintheme/js/jquery.validationEngine-en')."\n";
		echo $this->Html->script('/maintheme/js/jquery.validationEngine')."\n";
		echo $this->Html->script('/settings/css/bootstrap/js/bootstrap.min.js')."\n";
		echo $this->Html->script('/settings/js/app.min.js')."\n";
		echo $this->Html->script('/redactor/redactor'); 
		echo $this->Html->script('/settings/js/bootstrap-filestyle.min.js')."\n";
		echo $this->Html->script('/settings/js/bootstrap-select.min.js')."\n";
		echo $this->Html->script('/settings/css/bootstrap/js/bootstrap-toggle.min.js')."\n";
	?>	
	
<script>
  window.intercomSettings = {
    app_id: "u31p22t2",
    name: "<?php echo $this->Session->read('Auth.User.User.name'); ?>", // Full name
    email: "<?php echo $this->Session->read('Auth.User.User.email'); ?>", // Email address
    created_at: '<?php echo $this->Session->read('Auth.User.User.created'); ?>' // Signup date as a Unix timestamp
  };
</script>
<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/u31p22t2';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>

	</body>
</html>