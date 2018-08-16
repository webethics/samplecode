@extends('frontend.layouts.master')
@section('content')

<div class="container">
<div class="row">
<div class="col-md-8 col-md-offset-2">
<div class="panel panel-default">
<div class="panel-body">
	 @if(Session::has('success'))
                 <div class="success-msg" style="color:green">
                   {{Session::get('success')}}
                 </div>
    @else
<div class="lc-content">
	                       @if(!$flag)
							<h1>Reset Password</h1>
							<br>
							
							 {{ Form::open(array('url' => 'save_password', 'method' => 'post','class'=>'reset_form')) }}		
								{{ Form::password('password',
                                    array('class'=>'form-control','id'=>'password','placeholder'=>'Password')) }}	
      							 <span class="error"> {{ $errors->first('password')  }} </span>
								{{ Form::password('password_confirmation',
                                    array('class'=>'form-control','id'=>'password_confirmation','placeholder'=>'Confirm Password')) }}					
                                 <span class="error" >{{ $errors->first('password_confirmation')  }}  </span>
                                <div class="clearfix"> </div>
	                            @if($token !='' )
								<input name="password_token"  value="{{$token}}" type="hidden">
								@endif
								<input name="email"  value="{{$email}}" type="hidden">
								<input name="login"  id="reset_save_password" class="login loginmodal-submit" value="Reset" type="submit">
							
							   {{ Form::close() }}
	                       @else
						 <h1 style="color:red"> Your Link has been expired. </h1> 

						 @endif
			
</div>
    @endif
</div>

</div>

</div>

</div>

</div>
@include('frontend.layouts.login_register_popup')
@endsection