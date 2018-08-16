 <!-- Main Header -->
      <header class="main-header">


        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
                  <!-- Logo -->
        
		<a href="<?php echo $this->Html->url('/'); ?>" class="logo">
			<img alt="pic" src="/market/images/logo.png" class="img-responsive logo-img">
		</a>	
       
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
			  <?php 	
					// to fetch cart data							
						$cartData = $this->requestAction('/orders/getcartdata');
						$cartItemCount=count($cartData);
						$cartqty=0;
						if($cartItemCount>0){
							foreach ($cartData as $cart_key => $cart_value) {
								$cartqty+=$cart_value['Tempcart']['quantity'];
								
							}		
						}				           			
				?> 
              <!-- Tasks Menu -->
              <li class="dropdown tasks-menu" style="display:none;">
                <!-- Menu Toggle Button -->
				
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                 <img alt="pic" src="/theme1/images/shopbasket.png" class="img-responsive logo-img">
                  <span class="label label-danger"><?php echo $cartqty;?></span>
                </a>
                
                <ul class="dropdown-menu pad10">
              
							
				 <li class="header text-center"><h4>You have <?php echo $cartqty;?> items</h4></li>
                  <li>
                    <!-- Inner menu: contains the tasks -->
                    <ul class="menu">
					<?php
					$AmazonS3 = new AmazonS3(array('AKIAJUN4MN7X6EOAMK4A', 'iQwPlU6b6XK3b1G8oBQ/RLxwdjOcYaMF5rbbSq3c', 'shoptt'));
					$total=0;
					if(isset($cartItemCount) && $cartItemCount > 0){									
						foreach ($cartData as $cart_key => $cart_value) { 
							//$pimg=$cart_value['Picture']['picture1url'];
							$pimg=$AmazonS3->publicUrl('product/small/'.$cart_value['Picture']['picture1url']);
							$ptitle=$cart_value['Picture']['title'];
							$psubtotal=$cart_value['Tempcart']['quantity']*$cart_value['Tempcart']['unit_price'];
							$tempcartid=$cart_value['Tempcart']['id'];
							$total+=$psubtotal;
							
								if(isset($value['Tempcart']['currency']) && !empty($value['Tempcart']['currency'])){
									$currency = $value['Tempcart']['currency'];
									if($currency == 'USD' || $currency == 'AUD' || $currency == 'CAN' || $currency == 'SGD'){$sign = '$';}
									if($currency == 'EUR'){$sign = '€';}
									if($currency == 'GBP'){$sign = '£';}
								}else{
									$currency = 'USD';$sign = '$';
								}
							 
							?>
						<li>
							<table class="table table-striped table-bordered bootstrap-datatable datatable dataTable">
								<tbody role="alert" aria-live="polite" aria-relevant="all">
									<tr>
										<td>
											<a href="#" title=""><figure><img width="70" height="70" src="<?php echo $pimg; ?>" alt="Women Top"></figure></a>
										</td>	
										<td>
										<span><a class="carttitle" href="#" title=""><?php echo $ptitle; ?></a></span>
										</td>	
										<td>
											<?php echo $sign.$psubtotal.' '.$currency;  ?>
											</td>	
										<td>
											<a href="<?php echo $this->Html->url(array('controller'=>'orders','action'=>'cartdel',$tempcartid)); ?>" title="Remove" class="remove"><img src="<?php echo $this -> webroot . 'maintheme/'; ?>images/remove.png" alt=""></a>
										</td>
									</tr>
								</tbody>
							</table>		
							
						</li>
						<li class="text-right">
							<h4 class="fright">Total: <?php echo $sign.$total;?>&nbsp;<?php echo $currency;?></h4>
						  </li>
						   <li class="cartview text-right">
								<a class="btn btn-primary fright" href="<?php echo $this->Html->url(array('controller'=>'orders','action'=>'cart')); ?>" title="View Cart">View Cart</a>
						   </li>
						<?php } 
						} else {
								echo '<li class="cartview text-right">
										<a class="btn btn-primary fright" href="#" title="View Cart">Your Cart is Empty</a>
										</li>';
						}	?>
                    </ul>
                  </li>
				 
                  
                </ul>
              </li>
			  
              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <!-- The user image in the navbar-->
					<?php if(isset($this->request->data['User']['profilepic'])){?>

						<?php if (strpos($this->request->data['User']['profilepic'],'graph') !== false) {?>
							<img src="<?php echo $this->request->data['User']['profilepic']; ?>" class="user-image" alt="User Image">
					  
						<?php }else if($this->request->data['User']['profilepic'] == 'http://shoptt.co/default.gif'){?>
							<img src="<?php echo $this->request->data['User']['profilepic']; ?>" class="user-image" alt="User Image">
							
						<?php }else{ ?>
							<img src="<?php echo $AmazonS3->publicUrl('profile/small/'.$this->request->data['User']['profilepic']); ?>" class="user-image" alt="User Image">
						<?php } ?>

					<?php } ?> 
                  <!-- hidden-xs hides the username on small devices so only the image appears. -->
                  <span class="hidden-xs"><?php echo $this->Session->read('Auth.User.User.name');?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- The user image in the menu -->
                  <li class="user-header">
					<?php if(isset($this->request->data['User']['profilepic'])){?>

						<?php if (strpos($this->request->data['User']['profilepic'],'graph') !== false) {?>
							<img src="<?php echo $this->request->data['User']['profilepic']; ?>" class="img-circle" alt="User Image">
					  
						<?php }else if($this->request->data['User']['profilepic'] == 'http://shoptt.co/default.gif'){?>
							<img src="<?php echo $this->request->data['User']['profilepic']; ?>" class="img-circle" alt="User Image">
							
						<?php }else{ ?>
							<img src="<?php echo $AmazonS3->publicUrl('profile/small/'.$this->request->data['User']['profilepic']); ?>" class="img-circle" alt="User Image">
						<?php } ?>

					<?php } ?> 
                    <p>
                      <?php echo $this->Session->read('Auth.User.User.name');?>
                      <small>Member since <?php echo $this->Session->read('Auth.User.User.registeredon');?></small>
                    </p>
                  </li>
                  <!-- Menu Body -->
                   <li class="user-body">
                    <div class="col-xs-4 text-center">
                      <a href="<?php echo $this->Html->url(array('controller'=>'settings','action'=>'account')); ?>">My Account</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="<?php echo $this->Html->url(array('controller'=>'settings','action'=>'sold')); ?>">Sales</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="<?php echo $this->Html->url(array('controller'=>'settings','action'=>'pricingpage')); ?>">Upgrade</a>
                    </div>
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <!--a href="<?php echo $this->Html->url(array('controller'=>'pages','action'=>'profile','slug'=>$this->Session->read('Auth.User.User.name'))); ?>" class="btn btn-default btn-flat" style="border:0">Profile</a-->
                    </div>
                    <div class="pull-right">
					  <a href="<?php echo $this->Html->url(array('controller'=>'settings','action'=>'logout')); ?>" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>