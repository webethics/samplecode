<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
	  <!-- search form (Optional) -->
	  <form action="#" method="get" class="sidebar-form">
		<div class="input-group">
		  <input type="text" name="q" class="form-control" placeholder="Search...">
		  <span class="input-group-btn">
			<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
		  </span>
		</div>
	  </form>
	  <!-- /.search form -->

	  <?php 
		$selected='';
		if(isset($pageVar['menu']) && !empty($pageVar['menu'])){
			$selected=$pageVar['menu'];	
		}

		?>
		<ul class="sidebar-menu">
		<?php $class=$selected=='account'?'active':''; ?>	
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'account'));?>"><i class="fa fa-user"></i> <span>Account</span></a></li>
		<?php $class=$selected=='mywebsite'?'active':''; ?>	
		<li class="<?php echo $class; ?>">
			<?php if(isset($compdata['Splash']) && !empty($compdata['Splash'])){?>	<span class="complete_check"><i class="fa fa-check"></i></span><?php } ?>
			<a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'splash'));?>"><i class="fa fa-bolt"></i><span>Splash</span></a>
		</li>
		<?php $class=$selected=='menu'?'active':''; ?>	
        <li class="<?php echo $class; ?>">
			<?php if(isset($compdata['Menu']) && !empty($compdata['Menu'])){?>	<span class="complete_check"><i class="fa fa-check"></i></span><?php } ?>
			<a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'menu'));?>"><i class="fa fa-bars"></i><span>Menu</span></a>
		</li>
		<?php $class=$selected=='aboutus'?'active':''; ?>	
        <li class="<?php echo $class; ?>">
			<?php if(isset($compdata['Aboutus']) && !empty($compdata['Aboutus'])){?>	<span class="complete_check"><i class="fa fa-check"></i></span><?php } ?>
			<a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'aboutus'));?>"><i class="fa fa-building-o"></i><span>About Us</span></a>
		</li>
		<?php $class=$selected=='shop'?'active':''; ?>	
        <li class="<?php echo $class; ?>">
			<?php if(isset($compdata['Shop']) && (!empty($compdata['Shop']) || count($compdata['Shop']))){?><span class="complete_check"><i class="fa fa-check"></i></span><?php } ?>
			<a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'shop'));?>"><i class="fa fa-shopping-basket"></i><span>Shop</span></a>
		</li>	
		<?php $class=$selected=='catalogue'?'active':''; ?>	
        <li class="<?php echo $class; ?>">
			<?php if(isset($compdata['Catalogue']) && !empty($compdata['Catalogue'])){?>	<span class="complete_check"><i class="fa fa-check"></i></span><?php } ?>
			<a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'catalogue'));?>"><i class="fa fa-book"></i><span>Catalogue</span></a>
		</li>
		<?php $class=$selected=='products'?'active':''; ?>	
        <li class="<?php echo $class; ?>">
			<?php if(isset($compdata['Product']) && !empty($compdata['Product'])){?>	<span class="complete_check"><i class="fa fa-check"></i></span><?php } ?>
			<a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'products'));?>"><i class="fa fa-tags"></i><span>Products</span></a>
		</li>
		
		<?php $class=$selected=='cart'?'active':''; ?>	
        <li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'cart'));?>"><i class="fa fa-shopping-cart"></i><span>Cart</span></a></li>
		<?php $class=$selected=='setting'?'active':''; ?>
		<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'setting'));?>"><i class="fa fa-cogs"></i><span>Settings</span></a></li>
		<?php $class=$selected=='previewpublish'?'active':''; ?>	
        <?php if(!empty($compdata['Splash']) &&  !empty($compdata['Shop']) && !empty($compdata['Catalogue']) && !empty($compdata['Product'])){?>
			<li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'previewpublish'));?>"><i class="fa fa-eye"></i><span>Preview & Publish</span></a></li>
		<?php } else{ ?>
		<?php $class=$selected=='previewpublish'?'active':''; ?>	
	<!-- <li><a href="<?php echo $this->html->url(array('controller'=>'settings','action'=>'newPost'));?>">Post Product <span>(5)</span></a></li> -->
	 <li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'needCompeteData'));?>"><i class="fa fa-eye"></i><span>Preview & Publish</span></a></li> 
	<?php } ?>
		
		<?php $class=$selected=='Insights'?'active':''; ?>	
        <li class="<?php echo $class; ?>"><a href="#"><i class="fa fa-lightbulb-o"></i><span>Insights</span></a></li>
		<?php $class=$selected=='ShopttPUSH'?'active':''; ?>	
        <li class="<?php echo $class; ?>"><a href="<?php echo $this->html->url(array('controller'=>'myapp','action'=>'ShopttPUSH'));?>"><i class="fa fa-hand-paper-o"></i><span>ShopttPUSH</span></a></li>
		<!---li class=""><a href="<?php echo $this->Html->url(array('controller'=>'settings','action'=>'logout')); ?>"><i class="fa fa-sign-out"></i><span>Logout</span></a></li--->

		</ul>

	</section>
	<!-- /.sidebar -->
  </aside>