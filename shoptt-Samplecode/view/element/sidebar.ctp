<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
	
	  <?php 
		$selected='';
		if(isset($pageVar['menu']) && !empty($pageVar['menu'])){
			$selected=$pageVar['menu'];	
		}

		?>
		<ul class="sidebar-menu">
		<?php $class=$selected=='account'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'account'));?>"><i class="fa fa-user"></i> <span>Account</span></a></li>
		
		<?php $class=$selected=='Invoices'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'invoicedetails'));?>"><i class="fa fa-dollar"></i><span>Billing</span></a></li>
		
		
		<?php $class=$selected=='dashboard'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'dashboard'));?>"><i class="fa fa-lightbulb-o"></i><span>Dashboard</span></a></li>
		
	

		<?php $class=$selected=='Customer'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'customers'));?>"><i class="fa fa-user-md"></i> <span>My Customers</span></a></li>
		
		<?php $class=$selected=='mywebsite'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'mywebsite'));?>"><i class="fa fa-safari"></i> <span>My Website</span></a></li>

		<?php $class=$selected=='myapp'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'splash'));?>"><i class="fa fa-mobile"></i> <span>My App</span></a></li>

		<?php $class=$selected=='social'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'social'));?>"><i class="fa fa-share-alt"></i> <span>Social</span></a></li>
		<?php  $class=$selected=='faq'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'faq'));?>"><i class="fa fa-info-circle"></i> <span>FAQs</span></a></li>
		<?php $class=$selected=='products'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'products'));?>"><i class="fa fa-shopping-basket"></i> <span class="dnger">Products</span> <span class="label label-danger"><?php if(isset($product_count) && !empty($product_count)){echo $product_count;}else {echo '0';}?></span></a></li>
		<?php if(isset($paypal_data['User']['paypal_email']) && !empty($paypal_data['User']['paypal_email'])){ ?>
			<?php $class=$selected=='post_product'?'active':''; ?>	
			<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'newPost'));?>"><i class="fa fa-shopping-bag"></i> <span>Post Products</span></a></li>

		<?php }else{ ?>
			<?php $class=$selected=='post_product'?'active':''; ?>	
			<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'needPaypal'));?>"><i class="fa fa-shopping-bag"></i> <span>Post Products</span></a></li> 
		<?php } ?>
		
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'sold'));?>"><i class="fa fa-share-square"></i> <span>Sold</span></a></li>
		<li class=""><a href="<?php echo $this->Html->url(array('controller'=>'settings','action'=>'logout')); ?>"><i class="fa fa-sign-out"></i><span>Logout</span></a></li>

	</ul>

	</section>
	<!-- /.sidebar -->
  </aside>