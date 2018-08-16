<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\User;
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
use View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use AWS;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;
use Aws\S3\Model\MultipartUpload\UploadBuilder;
use Aws\Exception\MultipartUploadException;

Class ProjectController extends Controller
{
	
	/* Show List of Projects */ 
	public function index($page=1){
		$page = 1;
		$per_page_record = 6;
		$end = $per_page_record;
		$start = ($page*$per_page_record)-$per_page_record;

		$total_proejcts =  DB::table('project')->count();

		$project = DB::table('project')->select('id','title','video_code','credits','agency_credits')->where('publish','=',1)->orderBy('id', 'desc')->offset($start)->limit($end)->get()->toArray();
        /* Total Pages */
		$total_page = 	ceil($total_proejcts/$per_page_record);
		foreach($project as $key=>$value)
		{
			$project_image = DB::table('prjct_img')->where('prjct_id','=',$value->id)->select('id','prjct_id','org_img','keywords')->get();
			$project[$key]->project_image = $project_image;
			$credits_ids[]=explode(",",$project[$key]->credits);

			$credit_selected = DB::table('credits')
				 ->whereIn('id',$credits_ids[$key])->get();
				 $project[$key]->credits = $credit_selected;
			
			foreach($project_image as $key1=>$value1){
				if($value1->keywords){
					$keywords= str_replace(' ','',$value1->keywords);
					$final_keywords[]=explode(",",$keywords);
					$project_keywords1 =$this->array_flatten($final_keywords);
					$project_keywords = array_unique($project_keywords1);
					$project[$key]->project_keywords = $project_keywords;
				}	
			}
			 $project_keywords = '';
			 $project_keywords1 = '';
			 $final_keywords = '';
			 $keywords = '';
		}
		/* if Projects Found then list the projects */
		if(isset($project) && count($project) > 0){
			return view("frontend.views.index",compact('project','page','total_proejcts','total_page'));
		}else{
			$project = array();
			return view("frontend.views.index",compact('project','page','total_proejcts','total_page'));
		}	
	}
/*  Load more project on page scroll */ 
	public function loadMoreProject($page){
		$page = $page;
		$per_page_record = 6;
		$end = $per_page_record;
		$start = ($page*$per_page_record)-$per_page_record;
		$total_proejcts =  DB::table('project')->count();
		$project = DB::table('project')->select('id','title','video_code','credits','agency_credits')->where('publish','=',1)->orderBy('id', 'desc')->offset($start)->limit($end)->get()->toArray();
		$total_page = 	ceil($total_proejcts/$per_page_record);
        
		foreach($project as $key=>$value)
		{
			$project_image = DB::table('prjct_img')->where('prjct_id','=',$value->id)->select('id','prjct_id','org_img','keywords')->get();
			
			$project[$key]->project_image = $project_image;
			$credits_ids[]=explode(",",$value->credits);

			$credit_selected = DB::table('credits')
				 ->whereIn('id',$credits_ids[$key])->get();
				 $project[$key]->credits = $credit_selected;

			foreach($project_image as $key1=>$value1){
				if($value1->keywords){
					$keywords= str_replace(' ','',$value1->keywords);
					$keywords= $value1->keywords;
					$final_keywords[]=explode(",",$keywords);
					$project_keywords1 =$this->array_flatten($final_keywords);
					$project_keywords = array_unique($project_keywords1);
					$project[$key]->project_keywords = $project_keywords;
				}
			}
			$project_keywords = '';
			$project_keywords1 = '';
			$final_keywords = '';
			$keywords = '';
		}
		$view = view('frontend.views.loadMore',compact('project','page','total_proejcts','total_page'))->render();
		return response()->json(['html'=>$view]);
	 
	}
/* Search Keywords and show Project on the bases of keywords  */
	public function searchkeyword(Request $request,$key='',$type=''){
		
		/* Keyword */
		$keyword = $request->input('keyword');
		if(!$keyword){
			$keyword = $key;
		}
		/* Search type */ 
		$searchType = $request->input('searchtype');
		if(!$searchType){
			$searchType = $type;
		}
		/* If Set keywords */
		if(isset($keyword) && !empty($keyword)){
			$whereData1[] = ['keywords','LIKE','%'.$keyword.'%'];
		}
		/* If Condition is set */
		if(isset($whereData1) && !empty($whereData1)){	
			$project = DB::table('prjct_img')->select('id','prjct_id')->where($whereData1)->get();
		}else{
			$project = DB::table('prjct_img')->select('id','prjct_id')->get();
		}
		/* projects Not found */
		if(count($project) == 0){
			$project = array();
			return view("frontend.views.similar_projects",compact('project'));
		}else{
			/* Search type is videos */
			if($searchType == 'videos'){
				$list_project  = array();
				foreach($project as $key=>$value)
				{
					if(!in_array($value->prjct_id,$list_project)){
						$list_project[] = $value->prjct_id;
					}	
				}
				$project = DB::table('project')->select('id','title','video_code','credits','agency_credits')->where('publish','=',1)->whereIn('id',$list_project)->get();
				/* Proejcts found */
				if(count($project) != 0){
					foreach($project as $key=>$value)
					{
						$project_image = DB::table('prjct_img')->where('prjct_id','=',$project[$key]->id)->select('id','prjct_id','org_img','keywords')->get();
						$project[$key]->project_image = $project_image;
						$credits_ids[]=explode(",",$project[$key]->credits);
						/* Get credits */ 
						$credit_selected = DB::table('credits')
							 ->whereIn('id',$credits_ids[$key])->get();
							 $project[$key]->credits = $credit_selected;
							
						$keywords = array(); 
						$project_keywords=array();
						foreach($project_image as $key1=>$value1){
						if($value1->keywords){
							$keywords= str_replace(' ','',$value1->keywords);
							$final_keywords[]=explode(",",$keywords);
							$project_keywords1 =$this->array_flatten($final_keywords);
							$project_keywords = array_unique($project_keywords1);
							$project[$key]->project_keywords = $project_keywords;
						}
						}
						$project_keywords = '';
						$project_keywords1 = '';
						$final_keywords = '';
						$keywords = '';
					}
					foreach($project as $pkkey=>$pkvalue){
						if($keyword && !empty($keyword)){
							if(!in_array($keyword,$pkvalue->project_keywords)){
								unset($project[$pkkey]);
							}	
						}	
					}
					/* Projects found list the simlar projects */
					if(count($project) != 0){
						return view("frontend.views.similar_projects",compact('project','keyword','searchType'));
					}else{
						$project = array();
						return view("frontend.views.similar_projects",compact('project'));
					}
				}else{
					$project = array();
					return view("frontend.views.similar_projects",compact('project'));
				}	
			}
			/* If Search Type is Still */
			if($searchType == 'still'){
				$list_project  = array();
				foreach($project as $key=>$value)
				{
					$list_project[] = $value->id;
				}
				
				$project_image = DB::table('prjct_img')->whereIn('id',$list_project)->get();
	           /* Projects found */
				if(count($project) != 0 && count($project_image) != 0){
					$keywords = array(); 
					foreach($project_image as $k => $val)
					{
						$keywords[] = str_replace(' ','',$project_image[$k]->keywords);
						$final_keywords[]=explode(",",$keywords[$k]);
						$project_keywords1=$this->array_flatten($final_keywords);
						$project_keywords = array_unique($project_keywords1);
						$project_image[$k]->project_keywords = $project_keywords;
					}
					foreach($project_image as $pkkey=>$pkvalue){
						if($keyword && !empty($keyword)){
							if(!in_array($keyword,$pkvalue->project_keywords)){
								unset($project[$pkkey]);
							}	
						}	
					}
					if(count($project) != 0){
						return view("frontend.views.image",compact('project_image','keyword','searchType'));
					}else{
						$project_image = array();
						return view("frontend.views.image",compact('project_image'));
					}
					
					
				}else{
					$project_image = array();
					return view("frontend.views.image",compact('project_image'));
				}
			}
		}
		
	}
/*  Get Search Projects */
	public function getSearchProject(Request $request){
		$key = $request->input('keyword');
		$keyword = array();
		if($key && !empty($key)){
			if(strpos($key,',')){
				$arr = explode(',',$key);
				foreach($arr as $k=>$v){
					$keyword[] = $v;
				}
			}else{
				$keyword[] = $key;
			}
		}
		$exkey = $request->input('excludekeyword');
		$excludekeyword = array();
		/* Exclude Keywords */ 
		if($exkey && !empty($exkey)){
			if(strpos($exkey,',')){
				$arr = explode(',',$exkey);
				foreach($arr as $k=>$v){
					$excludekeyword[] = $v;
				}
			}else{
				$excludekeyword[] = $exkey;
			}
		}
		$searchType = $request->input('searchtype');
		$people_value = $request->input('people_value');
		$people_comp = $request->input('people_comp');
		$day_night = $request->input('day_night');
		$dnslider = $request->input('dnslider');
		$satslider = $request->input('satslider');
		$speedslider = $request->input('speedslider');
		$whereData = array();	$whereData1 = array();$whereData2 = array();
		/* People value */
		if(isset($people_value) && !empty($people_value)){
			if(in_array('people',$people_value)){
				$whereData[] = ['people', '1'];
			}
			if(in_array('no_people',$people_value)){
				$whereData[] = ['no_people', '1'];
			}
			
		}
		/* Portrait value set */ 
		if(isset($people_comp) && !empty($people_comp)){
			if(in_array('portrait',$people_comp)){
				$whereData[] = ['portrait', '1'];
			}
			if(in_array('no_portrait',$people_comp)){
				$whereData[] =['no_portrait', '1'];
			}
		}
		/* Day night Filter */ 
		if(isset($day_night) && !empty($day_night)){
			if(in_array('day',$day_night)){
				$whereData[] = ['day', '1'];
			}
			if(in_array('night',$day_night)){
				$whereData[] = ['night', '1'];
			}
			
		}
		/* Saturation Slider */ 
		if(isset($satslider) && !empty($satslider)){
			$values = explode(',',$satslider);
			if($searchType == 'video'){
				$whereData[] = ['saturation_low','>=', $values[0]];
				$whereData[] = ['saturation_low','<=', $values[1]];
				$whereData[] = ['saturation_high','>=', $values[0]];
				$whereData[] = ['saturation_high','<=', $values[1]];
			}else{
				$whereData[] = ['saturation','>=', $values[0]];
				$whereData[] = ['saturation','<=', $values[1]];
			}	
		}
		/* Brightness slider */
		if(isset($dnslider) && !empty($dnslider)){
			$values = explode(',',$dnslider);
			if($searchType == 'video'){
				$whereData[] = ['brightness_low','>=', $values[0]];
				$whereData[] = ['brightness_low','<=', $values[1]];
				$whereData[] = ['brightness_high','>=', $values[0]];
				$whereData[] = ['brightness_high','<=', $values[1]];
			}else{
				$whereData[] = ['brightness','>=', $values[0]];
				$whereData[] = ['brightness','<=', $values[1]];
			}
		}
		/* Speedslider filter */ 
		if(isset($speedslider) && !empty($speedslider)){
			$values = explode(',',$speedslider);
			if($searchType == 'video'){
				$whereData[] = ['edit_speed','>=', $values[0]];
				$whereData[] = ['edit_speed','<=', $values[1]];
			}
		}
		$orwhereData1 = array();
		/* Keyowrds not emptty */
		if($keyword && !empty($keyword)){
			foreach($keyword as $k=>$value){
				$this->incrementKeywordTracking($value);
				if($k==0){
					$whereData1[] = ['keywords','REGEXP','(^|, *)'.trim($value)];
					$whereData2[] = ['keywords','LIKE',trim($value).'%'];
				}else{
					$orwhereData1[] = ['keywords','REGEXP','(^|, *)'.trim($value)];
					$whereData2[] = ['keywords','LIKE',trim($value).'%'];
				}
				
			}
			
		}
		/* Excluded keyowrds */ 
		if($excludekeyword && !empty($excludekeyword)){
			foreach($excludekeyword as $k1=>$value1){
				if($k1==0){
					$whereData1[] = ['keywords','NOT LIKE','%'.trim($value1).'%'];
				}else{
					$orwhereData1[] = ['keywords','NOT LIKE','%'.trim($value1).'%'];
				}
			}
		}
		/* Conditaion is set */
		if($whereData1 && !empty($whereData1)){	
			$projects = DB::table('prjct_img')->select('id','prjct_id','keywords')->where($whereData1)->orWhere($whereData2)->orWhere($orwhereData1)->get();
		}else{
			$projects = DB::table('prjct_img')->select('id','prjct_id','keywords')->get();
		}	
		/* If project not found */
		if(count($projects) == 0){
			return response()->json(['html'=>'<h6 class="errordata">No Data Found!</h6>']);
		}else{
			/* Search type Video */
			if($searchType == 'video'){
				$list_project  = array();
				foreach($projects as $key=>$value)
				{
					if(!in_array($value->prjct_id,$list_project)){
						$list_project[] = $value->prjct_id;
					}	
				}
					if($whereData){
						$page = $request->input('page');
						$next_page = $request->input('next_page');
						$per_page_record = $request->input('per_page_record');
						$end = $per_page_record;
						$start = ($page*$per_page_record)-$per_page_record;
					    $total_record =  DB::table('project')->where('publish','=',1)->where($whereData)->whereIn('id',$list_project)->count(); 
						$total_page = 	ceil($total_record/$per_page_record);
						$project = DB::table('project')->select('id','title','video_code','credits','agency_credits')->where('publish','=',1)->whereIn('id',$list_project)->where($whereData)->orderBy('id', 'desc')->offset($start)->limit($end)->get()->toArray();
					}else{
						$page = $request->input('page');
						$next_page = $request->input('next_page');
						$per_page_record = $request->input('per_page_record');
						$end = $per_page_record;
						$start = ($page*$per_page_record)-$per_page_record;

						$total_record =  DB::table('project')->where('publish','=',1)->whereIn('id',$list_project)->count();
						$total_page = 	ceil($total_record/$per_page_record);
						$project = DB::table('project')->select('id','title','video_code','credits','agency_credits')->where('publish','=',1)->whereIn('id',$list_project)->orderBy('id', 'desc')->offset($start)->limit($end)->get()->toArray();
					}
                /* Project found */
				if(count($project) != 0){
					foreach($project as $key=>$value)
					{
						$project_image = DB::table('prjct_img')->where('prjct_id','=',$project[$key]->id)->select('id','prjct_id','org_img','keywords')->get();
						$project[$key]->project_image = $project_image;
						$credits_ids[]=explode(",",$project[$key]->credits);
						$credit_selected = DB::table('credits')
							 ->whereIn('id',$credits_ids[$key])->get();
							 $project[$key]->credits = $credit_selected;
						$keywords = array(); 
						$project_keywords=array();
						if($project_image){
							foreach($project_image as $key1=>$value1){
								if($value1->keywords){
									$keywords = str_replace(' ','',$value1->keywords);
									$final_keywords[]=explode(",",$keywords);
									$project_keywords1 =$this->array_flatten($final_keywords);
									$project_keywords = array_unique($project_keywords1);
									$project[$key]->project_keywords = $project_keywords;
							}
						}
							$project_keywords = '';
							$project_keywords1 = '';
							$final_keywords = '';
							$keywords = '';
						}
						
					}
					/* Show Search Video */ 
					if(count($project) != 0){
						
						$view = view('frontend.views.search_videos',compact('project','next_page','total_record','total_page'))->render();
						return response()->json(['html'=>$view]);
					}else{
						return response()->json(['html'=>'<h6 class="errordata">No Data Found!</h6>']);
					}
					
				}else{
					return response()->json(['html'=>'<h6 class="errordata">No Data Found!</h6>']);
				}	
			}
			/* Search Type is Image */
			if($searchType == 'images'){
				$list_project  = array();
				foreach($projects as $key=>$value)
				{
					$project_d = DB::table('project')->select('id')->where('publish','=',1)->where('id',$value->prjct_id)->get();
					if(count($project_d)>0)	
					$list_project[] = $value->id;
				}
				/* Condition is set */ 
				if($whereData){
					$page = $request->input('page');
					$next_page = $request->input('next_page');
					$per_page_record = $request->input('per_page_record');
					$end = $per_page_record;
					$start = ($page*$per_page_record)-$per_page_record;
					$total_record =  DB::table('prjct_img')->whereIn('id',$list_project)->where($whereData)->count();
					$total_page = 	ceil($total_record/$per_page_record);
					$project_image = DB::table('prjct_img')->whereIn('id',$list_project)->where($whereData)->orderBy('id', 'desc')->offset($start)->limit($end)->get()->toArray();
				}else{
					$page = $request->input('page');
					$next_page = $request->input('next_page');
					$per_page_record = $request->input('per_page_record');
					$end = $per_page_record;
					$start = ($page*$per_page_record)-$per_page_record;
					$total_record =  DB::table('prjct_img')->whereIn('id',$list_project)->count();
					$total_page = 	ceil($total_record/$per_page_record);
					$project_image = DB::table('prjct_img')->whereIn('id',$list_project)->orderBy('id', 'desc')->offset($start)->limit($end)->get()->toArray();
				}
				/* If Project Found */ 
				if(count($projects) != 0 && count($project_image) != 0){
					$keywords = array(); 
					$final_keywords = array();
					foreach($project_image as $k => $val)
					{
						$keywords[] = str_replace(' ','',$project_image[$k]->keywords);
						$final_keywords[]=explode(",",$keywords[$k]);
						$project_keywords1=$this->array_flatten($final_keywords);
						$project_keywords = array_unique($project_keywords1);
						$project_image[$k]->project_keywords = $project_keywords;
					}
					/* Show Search Projects */
					if(count($project_image) != 0){
						$view = view('frontend.views.search_image',compact('project_image','next_page','total_record','total_page'))->render();
					return response()->json(['html'=>$view]);
					}else{
						return response()->json(['html'=>'<h6 class="errordata">No Data Found!</h6>']);
					}
					
					$view = view('frontend.views.search_image',compact('project_image'))->render();
					return response()->json(['html'=>$view]);
				}else{
					return response()->json(['html'=>'<h6 class="errordata">No Data Found!</h6>']);
				}
			}
		}
		
	 
	}
	
}