 <?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\User;
use App\Article;
use App\models\Slide;
use DB;
use Input;
use Validator;
use Auth;
use Redirect;
use Session;
use Response;
use Image;
use URL;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class UserController extends Controller
{

	public function index(){


	    $countries =  getAllCountries();
		$slide = Slide::all();
		$article = new Article;
		//echo "<pre>";
		//print_r($countries_data);die;

		/******* featured article limit */
        $featured_per_page_limit =featured_page_limit();
        $featured_end = $featured_per_page_limit;
        $featured_start = 0;
        $feauture_article = $article->get_featured_article($featured_start,$featured_end);

		$per_page_record =per_page_limit();
		$end = $per_page_record;
		$start = 0;
		$articles = $article->get_articles($start,$end);
		$Allarticles = $article->getAllarticles($start,$end);
      	return view("frontend.user.user",compact('countries','feauture_article','articles','slide','Allarticles'));
	}


    /***********************  Step 1 Form signup ******************************/
	public function SignupStep1(Request $request){
				$rules = array('firstname'    => 'required',
							   'lastname'    => 'required',
							   'month'    => 'required',
							   'day'    => 'required',
							   'year'    => 'required',
							   'gender'    => 'required',
							   'country'    => 'required',

							  );
				$validator = Validator::make($request->input(), $rules);
				if ($validator->fails()) {

					 return Response::json(array(
					'success' => false,
					'errors' => $validator->getMessageBag()->toArray()
					), 200);

				} else {
					  return Response::json(array(
						'success' => true,
						'errors' => ''
					), 200);
				  }
   }
   /*  Step 2Form signup */
   public function SignupStep2(Request $request){
								$rules = array('email'    => 'required|email|unique:users|confirmed',
							   'email_confirmation'    => 'required',
							   'username'    => 'required|unique:users',
							   'password'    => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/|confirmed',
							   'password_confirmation' => 'required|min:6'

							  );
	            $messages = ['password.regex' => "Your password must contain 1 lower case character 1 upper case character one number"];
				$validator = Validator::make($request->input(), $rules,$messages);
				if ($validator->fails()) {
					 return Response::json(array(
					'success' => false,
					'errors' => $validator->getMessageBag()->toArray()
					), 200);

				} else {

				    $token = $this->getToken();

					$month = $request->input('month');
					$day = $request->input('day');
					$leadingzero = 0;
					$month =  (strlen($month)==1)?$leadingzero.$month:$month;
					$day =  (strlen($day)==1)?$leadingzero.$day:$day;

					$user = new User;
					$year = $request->input('year');
					$dob = $year.'-'.$month.'-'.$day;
					$user->firstname = $request->input('firstname');
					$user->lastname = $request->input('lastname');
					$user->birhtdate = $dob;
					$user->country_id = $request->input('country');
					$user->gender = $request->input('gender');
					$user->email = $request->input('email');
					$user->username = $request->input('username');
					$user->password = md5($request->input('password'));
					$user->created_at = date('Y-m-d H:i:s');
					$user->ip_address = $request->ip();
					$user->user_type = 'user';
					$user->verify_token = $token;
					$user->save();
					$insertedId = $user->id;


					if($request->file('logo'))
					{
							$media =$request->file('logo');
							$destinationPath = storage_path().'/app/public/uploads/users/'.$insertedId.'/';
							$filename = microtime().'.'.$media->getClientOriginalExtension();
							if (!file_exists($destinationPath)) {
									mkdir($destinationPath, 0777, true);
										chmod($destinationPath,0777);
								}
							 $large_image_path = $destinationPath.$filename ;
							 Image::make($media)->resize(200, 200)->save($large_image_path);

							 DB::table('users')->where('id', '=', $insertedId)
						->update(['image'=>$filename]);
				  	 }


					 $url= url('/verify/'.$token);
					 $link = $url ;
					 $path= resource_path('views/frontend/email/email_template.blade.php');
					 $template = file_get_contents($path);
					 $replace_array =array('[name]','[message]');
					 $username =  ucwords($request->input('firstname').' '. $request->input('lastname'));
					 $msg = 'Please Click <a href="'.$link.'">here</a> to verify your account';
					 $replace_by = array($username,$msg) ;
					 $message = str_replace($replace_array,$replace_by,$template);

					 $to = $request->input('email');
					 $subject = 'Account Verification Email';

					 $fromname ='';
					 $from = "test@webethicssolutions.com";
					 $this->send_email($to,$subject,$message,$from,$fromname);
					  return Response::json(array(
						'success' => true,
						'errors' => ''
					), 200);
				  }
           }

 /******  Thanks page after registration *******/
	 function thanks_register (){
	    $countries_data =  DB::table('countries')->get();
	    $countries =array(''=>'Select Country');
	 	return view('frontend.email.verification',compact('countries'));
	 }

/************  Verify your teoken ******************/
	public function verify($token)
	{
	   $user = User::where('verify_token',$token)->first();
	   $countries_data =  DB::table('countries')->get();
	   $countries =array(''=>'Select Country');
	   foreach($countries_data as $key=>$val){
		  $countries[$val->con_id] = $val->name;
	   }
		if($user){
			$user->verify_token = NULL;
			if($user->save()){
			return view('frontend.email.emailconfirm',compact('countries'));
			}
		}

	}
/************ Check admin Login **********************/
	function login(Request $request){

				$rules = array('login_email'    => 'required',
								'login_password' => 'required'
							  );

				$validator = Validator::make($request->input(), $rules);
				if ($validator->fails()) {
					 return Response::json(array(
					'success' => false,
					'errors' => $validator->getMessageBag()->toArray()
					), 200);
					/*  return Response::json(array(
						'success' => true,
						'errors' => ''
					), 200); */

				} else {

					 $result = DB::table('users')
					->where('email', '=', $request->get('login_email'))
					->where('password', '=', md5($request->get('login_password')))
				    ->where('user_type', '=', 'user')
					->where('verify_token', '=', NULL)
					->get();

					 if(count($result)>0){

							Session::put('user_id', $result[0]->id);
							Session::put('user_type','user');
						   $referer ='';

							 $data = array('last_login'=>'','current_login'=>date('Y-m-d H:i:s'),);
							 DB::table('users')->where('id', '=',$result[0]->id)
						 ->update($data);
						   /*if(isset($_SERVER['HTTP_REFERER'])){
						     $referer =  $_SERVER['HTTP_REFERER'];
						   }
		                   */

						 	return Response::json(array(
							'success' => true,
							'errors' => '',
							'referer' => $referer
							), 200);

					  }else{
				    return Response::json(array(
					'success' => false,
					'errors' => array('login_password'=>array('Please fill correct credential.'))
					), 200);

					  }

				  }

	   }
/************    Send email for forgot password   ****************************/
public function forgot_password(Request $request){

				$rules = array('forgot_email'    => 'required|email');
				$validator = Validator::make($request->input(), $rules);
				if ($validator->fails()) {
					 return Response::json(array(
					'success' => false,
					'errors' => $validator->getMessageBag()->toArray()
					), 200);
				} else {
					 $result = DB::table('users')
					->where('email', '=', $request->get('forgot_email'))
				    ->where('user_type', '=', 'user')
					->get();

					 if(count($result)>0){
							 $token = $this->getToken();

						     $password_email = DB::table('password_resets')
							->where('email', '=', $request->get('forgot_email'))
							->get();
					     	 $time = date('Y-m-d H:i:s');
							 $data = array('email'=>$request->get('forgot_email'),'token'=>$token,'created_at'=>$time);
						     if(count($password_email)>0){
								 DB::table('password_resets')->where('email', '=', $request->get('forgot_email'))
								->update($data);
							 }else{
							    DB::table('password_resets')->insert($data);
							 }
							 $url= url('/reset_password/'.$token);
							 $link = $url ;
							 $path= resource_path('views/frontend/email/email_template.blade.php');
							 $template = file_get_contents($path);
							 $replace_array =array('[name]','[message]');
							 $username =  ucwords($result[0]->firstname.' '. $result[0]->lastname);
							 $msg = 'Please Click <a href="'.$link.'">here</a> to reset your password.';
							 $replace_by = array($username,$msg) ;
							 $message = str_replace($replace_array,$replace_by,$template);

							 $to = $result[0]->email;
							 $subject = 'Reset Password';
							 //$this->send_email($to,$subject,$message);
						      $fromname ='';
							  $from = "test@webethicssolutions.com";
							  $this->send_email($to,$subject,$message,$from,$fromname);



						 	return Response::json(array(
							'success' => true,
							'errors' => ''
							), 200);

					  }else{
							return Response::json(array(
							'success' => false,
							'errors' => array('forgot_email'=>array('Please enter correct email.'))
							), 200);
					  }
				  }

}
/******************* Open Reset Password Form  *************************/
public function reset_password($token){
	  $result = DB::table('password_resets')
			->where('token', '=', $token)
			->get();
	  $countries_data =  DB::table('countries')->get();
	  $countries =array(''=>'Select Country');
	  foreach($countries_data as $key=>$val){
		  $countries[$val->con_id] = $val->name;
	   }
	  $flag = false;
	  if(count($result)>0){
		 $email =  $result[0]->email;
		 $date =  date('Y-m-d H:i:s');
		 $hourdiff = round((strtotime($date) - strtotime($result[0]->created_at))/3600, 1);
		  if($hourdiff>24){
			  $flag =true;
			  return view('frontend.user.reset_password',compact('countries','token','flag','email'));

		  } else{

			   return view('frontend.user.reset_password',compact('countries','token','flag','email'));

		  }
	  }else{
	  	 return redirect('/');
	  }
}
/**************** Save Password from reset form page  ***********************/
public function save_reset_password (Request $request){

				$rules = array(
							   'password'    => 'required|min:6|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/|confirmed',
							   'password_confirmation' => 'required|min:6'

							  );
	            $messages = ['password.regex' => "Your password must contain 1 lower case character 1 upper case character one number"];
				$validator = Validator::make($request->input(), $rules,$messages);

				if ($validator->fails()) {

					 return redirect('/reset_password/'.$request->input('password_token'))->withInput()->withErrors($validator);
				} else {
						 $result = DB::table('users')
						->where('email', '=', $request->get('email'))
						->where('user_type', '=', 'user')
						->get();

						 if(count($result)>0){
							  $date = date('Y-m-d H:i:s');
							  $data = array('password'=>md5($request->get('password')),'updated_at'=>$date);
							   DB::table('users')->where('email', '=', $request->get('email'))
									->update($data);
							Session::flash('success', 'Your Password has been Updated.You can Login now');
							return redirect('/reset_password/'.$request->input('password_token'));
						 }else{
						   return redirect('/');
						 }
				}
}


/******************** Get rendom number  *******************************/
 public function getToken()
{
	$length =20;
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[rand(0, $max-1)];
    }

    return $token;
}

  /* User Logout function */
	public function logout()
	{

			$data = array('last_login'=>date('Y-m-d H:i:s'),'current_login'=>'',);
			DB::table('users')->where('id', '=',Session::get('user_id'))
			->update($data);
			Session::put('user_id', '');
			Session::put('fullname', '');
		    Session::put('username', '');
		    Session::put('user_type', '');
			return redirect('/');
	}

   /************** opens the view of profile ******************/
 public function editprofile()
{
		$user_id = Session::get('user_id');
		$user_data =  DB::table('users')->where('id','=',$user_id)->get();
		$countries_data =  DB::table('countries')->get();
	  $countries =array(''=>'Select Country');
		foreach($countries_data as $key=>$val)
		 		$countries[$val->con_id] = $val->name;
 		return view('frontend.user.profile' , compact('countries','user_data'));
}
 /************** validates and saves the chages made to profile******************/
public function profile_edit(Request $request)
	{
			$user_id = Session::get('user_id');
			$user = DB::table('users')->where('id','=',$user_id)->get();
			$rules = array('firstname'    => 'required',
							 'lastname'    => 'required',
							 'month'    => 'required',
							 'day'    => 'required',
							 'year'    => 'required',
							 'gender'    => 'required',
							 'country'    => 'required',
							);

		$validator = Validator::make($request->input(),$rules);
			if ($validator->fails())
			return redirect('/profile')->withErrors($validator)->withInput();
			else{
				 $year = $request->input('year');
				 $about = $request->input('about');
				 $month = $request->input('month');
				 $day = $request->input('day');
				 $leadingzero = 0;
				 $month =  (strlen($month)==1)?$leadingzero.$month:$month;
				 $day =  (strlen($day)==1)?$leadingzero.$day:$day;
				 $dob = $year.'-'.$month.'-'.$day;
				 $date = date('Y-m-d H:i:s');
				 $data = array('firstname'=>$request->input('firstname'),'lastname'=>$request->input('lastname'),'gender'=>$request->input('gender'),'country_id'=>$request->input('country'),'birhtdate'=>$dob,'updated_at'=>$date,);
				 DB::table('users')->where('id', '=', $user_id)
				 ->update($data);
	}

		if($request->file('image'))
		 {
		$messages = [
							'image.dimensions' => "Please upload 400x400 or bigger image"
			    ];
			 $files = ['image' => 'mimes:jpg,png,jpeg|dimensions:min_width=400,min_height=400' ];
			 $validator = Validator::make($request->file(),$files,$messages);
 			if ($validator->fails())
			{
				return back()->withErrors($validator)->withInput();
				//return redirect('/profile/'.$user_id)->withErrors($validator)->withInput();
			}

			 	 $this->old_pic_remove($user_id);
				 $media =$request->file('image');
				 $destinationPath = storage_path().'/app/public/uploads/users/'.$user_id.'/';
				 $filename = microtime().'.'.$media->getClientOriginalExtension();
				 if (!file_exists($destinationPath))
				 {
						 mkdir($destinationPath, 0777, true);
						 chmod($destinationPath,0777);
					}
				 $large_image_path = $destinationPath.$filename ;
				 Image::make($media)->resize(200, 200)->save($large_image_path);
				 DB::table('users')->where('id', '=', $user_id)
				 ->update(['image'=>$filename]);
				 Session::flash('success', 'Your profile has been Updated.');
				 return redirect('/profile');
				 }
			   else{
					 Session::flash('success', 'Your profile has been Updated.');
	 				 return redirect('/profile');
			 }
}
 /************** OPENS CHANGE PASSWORD VIEW ******************/
public function password_change(){
	$user_id = Session::get('user_id');
	$countries_data =  DB::table('countries')->get();
	$countries =array(''=>'Select Country');
	foreach($countries_data as $key=>$val)
				$countries[$val->con_id] = $val->name;
			 	return view('frontend.user.change_password' , compact('countries'));
}
 /************** validates and saves the chages made to password******************/
public function new_password(Request $request){
	$user_id = Session::get('user_id');
		if($request->input('old_password')!='' || $request->input('password')!='' || $request->input('password_confirmation')!=''){
			$rules = [
			'password'    => 'min:6|regex:/^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%@]).*$/|confirmed|required',
			'password_confirmation' => 'min:6|required',
			'old_password' => 'required'
	  ];
		$messages = ['password.regex' => "Your password must contain 1 lower case character 1 upper case character one number"];
	  $validator = Validator::make($request->input(),$rules,$messages);
			if ($validator->fails())
					return redirect('/change_password')->withErrors($validator)->withInput();
			else{
				 $date = date('Y-m-d H:i:s');
				 $data = array('password'=>md5($request->input('password')),'updated_at'=>$date,);
				 DB::table('users')->where('id', '=', $user_id)
			   ->update($data);
				 Session::flash('success', 'Your profile has been Updated.');
			 	return redirect('/change_password');

			 }

	}

	return redirect('/change_password');
}
/************** removes the old pic from  the folder******************/
	public function old_pic_remove($user_id='')
	{
			$data = DB::table('users')->find($user_id);
			$image = $data->image;
			$image_thumb = storage_path('app/public/uploads/users/'.$user_id.'/'.$image);
			@unlink($image_thumb);
		  return true;
	}
	/************** matches the old and new passwords ******************/
	public function check_old_password(Request $request)
	{
		 $user_id = Session::get('user_id');
		 $password = md5($request->input('old_password'));
		 $user = DB::table('users')->where('id','=',$user_id)->where('password','=',$password)->get();
					if(count($user)>0)
					{
							return Response::json(array(
						 'success' => true,
						 'errors' => ''
						 ), 200);
					}
					else
					{
						return Response::json(array(
					 'success' => false,
					 'errors' => ''
					 ), 200);
					}
	}



/****************** Send email **********************/

function send_email($to='',$subject='',$message='',$from='',$fromname=''){
		try {
		$mail = new PHPMailer();
		$mail->isSMTP(); // tell to use smtp
		$mail->CharSet = "utf-8"; // set charset to utf8
		$mail->Host = "webethicssolutions.com";
		$mail->SMTPAuth = true;
		$mail->Port = 587;
		$mail->Username = "test@webethicssolutions.com";
		$mail->Password = "el*cBt#TuRHbb^mmm";
		$mail->From = $from;
		$mail->FromName = $fromname;
		$mail->AddAddress($to);
		$mail->IsHTML(true);
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->SMTPOptions= array(
		'ssl' => array(
		'verify_peer' => false,
		'verify_peer_name' => false,
		'allow_self_signed' => true
		)
		);

		$mail->send();
		return true ;
		} catch (phpmailerException $e) {
		dd($e);
		} catch (Exception $e) {
		dd($e);
		}
		echo "My name is Harsh";
         return false ;
	 }
	 
public function harsh(Type $var = null)
{
	echo "My name is Harsh";
}

}
