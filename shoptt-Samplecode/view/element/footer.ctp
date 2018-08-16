
<?php if($pageVar['menu'] == 'Cart' ){?>
<?php include('billing_address.ctp')?>
<?php } ?>

<?php if($pageVar['menu'] == 'Invoices' ){?>
<?php include('card_details.ctp');include('plan_details.ctp');?>
<?php } ?>

<?php if($pageVar['menu'] == 'Customer' ){?>
<?php include('customer_address.ctp')?>
<?php } ?>


<?php if($pageVar['menu'] == 'mywebsite' ){?>

<?php include('custom_domain.ctp');?>
<?php include('connectpayment.ctp');?>
<?php include('shipping_cost.ctp');?>


<?php } ?>

    <script>
// Add slideDown animation to dropdown
$('.dropdown').on('show.bs.dropdown', function(e){
  $(this).find('.dropdown-menu').first().stop(true, true).slideDown();
});

// Add slideUp animation to dropdown
$('.dropdown').on('hide.bs.dropdown', function(e){
  $(this).find('.dropdown-menu').first().stop(true, true).slideUp();
});

</script>		

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-50186695-1', 'auto');
  ga('send', 'pageview');

</script>
<script>
$(document).on('click', '.yamm .dropdown-menu', function(e) {
  e.stopPropagation()
})
</script> 


