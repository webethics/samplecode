/* global backorder_select_params */

jQuery( document ).ready(function($) {


function getTuesday(num) {
    var d = new Date(),
        month = d.getMonth(),
        tuedays= [];

    d.setDate(1);

    // Get the first Monday in the month
    while (d.getDay() !== num) {
        d.setDate(d.getDate() + 1);
    }

    // Get all the other Tuesdays in the month
    while (d.getMonth() === month) {
        tuedays.push(new Date(d.getTime()));
        d.setDate(d.getDate() + 7);
    }
	
    return tuedays[0];
}	





prope = $('.woocommerce-variation').css("display");
   $('#variable_txt_instock').css('display',prope);
   $('#variable_desc_instock').css('display',prope);

   var backorder = $('#_backorders').val();
   if(backorder == 'no'){
	   $('#_profile_name_field').removeAttr('required');
	   $('#_estimated_ship_field').removeAttr('required');
	   $('._profile_name_field_field').hide();
	   $('._estimated_ship_field_field').hide();   
   }
  
	$('#_backorders').on('change', function() {
	var backorder = this.value;
	  if(backorder == 'no'){
			$('#_profile_name_field').removeAttr('required');
			$('#_estimated_ship_field').removeAttr('required');
		   $('._profile_name_field_field').hide();
		   $('._estimated_ship_field_field').hide();   
	   }else{
		   $('#_profile_name_field').attr('required','required');
			$('#_estimated_ship_field').attr('required','required');
		   $('._profile_name_field_field').show();
		   $('._estimated_ship_field_field').show();  
	   }
	});  

	
	$(".variation_id").on('change', function() {	
	var variation_id = $(this).val();
$('.instockdesc_estimate').css('display','block');
	//alert($('input.checkbox_check').is(':checked'));
	
	if(variation_id != ''){
		 
	$.ajax({type:"POST",
	url: backorder_select_params.ajax_url,
	data:{
      action: 'variation_id_time', // this is the function in your functions.php that will be triggered
      postid:variation_id
    },
	success: function(data ){
       // $("#_estimated_ship_field").val(data);
	   $('.instockdesc_estimate').css('display','block');
        $("#estimate_ship_time").html(data);
      // alert(data);
    }});
	
	}else{
		//$('#_estimated_ship_field').val('');
		$('.instockdesc_estimate').css('display','none');
	}
	
	
	});
	
$("#_profile_name_field").on('change', function() {		
 
var profile_val = $("#_profile_name_field").val();
if(profile_val != ''){
	$.ajax({type:"POST",
	url: ajaxurl,
	data:{
      action: 'myaction', // this is the function in your functions.php that will be triggered
      postid:profile_val
    },
	success: function(data ){
        $("#_estimated_ship_field").val(data);
    }});
	
}else{
	$('#_estimated_ship_field').val('');
}
}); 


function nextDate(dayIndex) {
    var today = new Date();
    today.setDate(today.getDate() + (dayIndex - 1 - today.getDay() + 7) % 7 + 1);
    return today;
}

function showDays(firstDate,secondDate){
                
                  

                  var startDay = new Date(firstDate);
                  var endDay = new Date(secondDate);
                  var millisecondsPerDay = 1000 * 60 * 60 * 24;

                  var millisBetween = startDay.getTime() - endDay.getTime();
                  var days = millisBetween / millisecondsPerDay;

                  // Round down.
				  var totdays = Math.floor(days);
                  return totdays;

              }
var days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

function next(day) {

    var today = new Date();
    var today_day = today.getDay();

    day = day.toLowerCase();

    for (var i = 7; i--;) {
        if (day === days[i]) {
            day = (i <= today_day) ? (i + 7) : i;
            break;
        }
    }

    var daysUntilNext = day - today_day;

    return new Date().setDate(today.getDate() + daysUntilNext);

}


$('#selday').css('display','inline-block');

$("#arrivaldays, #selweek, #datetype, #selday").bind("change paste keyup", function() {

var arrivaldays = $('#arrivaldays').val();
var weekday = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
/* select week from 1 to 6*/
/* day or week */
var datetype = $('#datetype').val();
if(datetype == 'Month' || datetype == 'Week'){
	$('#selday').css('display','inline-block');
}
/* select date 1 to 31 */
var selday = $('#selday').val();

var current_year = $('#current_year').val();

var currentTime = new Date();

var yearn = currentTime.getFullYear();

//var freq_order_day = new Date(next(selweekday));


//var selcdates = yearn +'-'+ (currentTime.getMonth() + 1) +'-'+ selday;

$('#first_ship_date').val(selday);


var d = new Date(selcdates);
var curr = new Date();
var seldat = d.getDay();
var year = d.getFullYear();
var day = d.getDay();
var comingdayyy = weekday[d.getDay()];

var nextdates = nextDate(d.getDay());

var secondDate = curr.getFullYear()+ "-" + curr.getMonth() + "-" + curr.getDate();
var firstDate = nextdates.getFullYear() + "-" +nextdates.getMonth() + "-" +nextdates.getDate();

var freq_of_days = showDays(firstDate,secondDate);
//alert(freq_of_days);
var num_of_days = parseInt(arrivaldays)+parseInt(freq_of_days);
                                                        
$('#my_meta_box_post_type').val(num_of_days);
});
	
});