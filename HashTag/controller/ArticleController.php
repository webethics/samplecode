<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\User;
use App\Article;
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
class ArticleController extends Controller
{ 

  /*********  load More by ajax **************/
	public function loadMoreArticle($page){
		$article = new Article;
        /******* featured article limit */ 
        $featured_per_page_limit =featured_page_limit();
        $featured_end = $featured_per_page_limit;
        $featured_start = ($page*$featured_per_page_limit)-$featured_per_page_limit;
        $feauture_article = $article->get_featured_article($featured_start,$featured_end);
    
     // pr($feauture_article) ; die;
        $per_page_record =per_page_limit();
		    $end = $per_page_record;
		    $start = ($page*$per_page_record)-$per_page_record;
			  $articles = $article->get_articles($start,$end);
    		$view = view('frontend.article.loadmore',compact('articles'))->render();
    		$feauture_article = view('frontend.article.load_more_feature',compact('feauture_article'))->render();
            return response()->json(['html'=>$view,'feauture_article'=>$feauture_article]);
     
	}
	
	/********* Like Article ***************/
	public function likeArticle($article_id,$button_type){
	
		$user_id = Session::get('user_id');
		if($user_id!=''){
		$article = new Article;
		/** check and insert visited article */
		
		$getcount = $article->like_article($article_id,$user_id,$button_type);
		//echo $getcount;
	
		$result['user_id'] = $user_id;
		$result['count'] = $getcount;
     // $like = ($button_type=='like') ?'unlike':'like';
		$like = ($button_type=='like') ?'unlike':'like';
		$result['like'] = $like;
		}else{
		   $result['user_id']='';
		}
		return response()->json($result);
		
	}
	
  
  /********* Show Article Single page ***************/
	public function singlePage($slug){

   
		$article = new Article;
		$countries = getAllCountries();
		/** check and insert visited article */
		$ip = $_SERVER['REMOTE_ADDR'];
		$article->visited_article($ip,$slug);
		
    /******** check and insert visited article ***************/
		$article_data =  DB::table('articles')->where('slug',$slug)->get();
		$category_id = $article_data[0]->category_id ;
		$article_id  = $article_data[0]->id ;
		//$you_may_article = $article->youMayArticle($category_id);
    
    /*********** you may total record count */
    $total_you_may_count_page =  ceil(youMayArticleCount($category_id) / you_may_like_record_limit());
    
    /*************Get all images for single page slider *****/
    $slider_data = $article->getArticleAllImagesSlider($article_id);
    $per_page_record =per_page_limit();
	$end = $per_page_record;
	$start = 0;
    $comment = $article->get_comments($article_id,$start,$end);
    $allcomment = $article->getAllcomments($article_id);
      	return view("frontend.article.singlepage",compact('countries','article_id','slider_data','comment','allcomment','category_id','total_you_may_count_page'));
     
	}
	
  function loadmore_you_may_like($page,$category_id){
    
    $article = new Article; 
    $you_may_like_record_limit =you_may_like_record_limit();
    $you_may_end = $you_may_like_record_limit;
    $you_may_start = ($page*$you_may_like_record_limit)-$you_may_like_record_limit;   
    $you_may_article = $article->youMayArticle($category_id,$you_may_start,$you_may_end);
    
    $you_may_article_html = view('frontend.article.load_you_may_like',compact('you_may_article'))->render();
    return response()->json(['html'=>$you_may_article_html]);

  }
	
/***************
* Comment
*
****************/
/********* Like Comment ***************/
	public function likeComment($comment_id,$button_type){
	
		$user_id = Session::get('user_id');
		if($user_id!=''){
		$article = new Article;
		/** check and insert visited article */
		
		$getcount = $article->like_comment($comment_id,$user_id,$button_type);
		//echo $getcount;
	
     /************insert notification on like comment *****************/
       if($button_type=='like'){
         $comment_data =  DB::table('comments')->where('id','=', $comment_id)->where('status', '=', 0)->get();
         if($comment_data[0]->user_id != $user_id ){

           $notification_msg =  DB::table('notification_msg')->where('type','=', 'like_comment')->get();
           $notification_data =  DB::table('notification')
             ->where('sender_id','=', $user_id)
             ->where('recipient_id','=', $comment_data[0]->user_id)
             ->where('notification_id','=', $notification_msg[0]->id)
             ->where('article_id','=',  $comment_data[0]->article_id)
             ->where('comment_id','=',  $comment_data[0]->id)
             ->get();
           
           $notification = array(
              'sender_id'=>$user_id ,
              'recipient_id'=> $comment_data[0]->user_id,
              'notification_id'=> $notification_msg[0]->id,
              'article_id'=> $comment_data[0]->article_id,
              'comment_id'=> $comment_data[0]->id,
              'is_read'=>0,
              'created_at'=> date('Y-m-d H:i:s'),
            );
           if(count($notification_data)>0){
                 DB::table('notification')
                    ->where('sender_id','=', $user_id)
                     ->where('recipient_id','=', $comment_data[0]->user_id)
                    ->where('notification_id','=', $notification_msg[0]->id)
                    ->where('article_id','=',  $comment_data[0]->article_id)
                    ->where('comment_id','=',  $comment_data[0]->id)
                    ->update($notification);
           }else{
               DB::table('notification')->insert($notification);
           }
         
         }
       }
        /************ insert notification on like comment   END **************/
      
			$result['user_id'] = $user_id;
			$result['count'] = $getcount;
			$like = ($button_type=='like') ?'unlike':'like';
			$result['like'] = $like;
		}else{
		   $result['user_id']='';
		}
		return response()->json($result);
		
	}
  
  
  /********* Like Reply ***************/
	public function likeReply($reply_id,$button_type){
	
		$user_id = Session::get('user_id');
		if($user_id!=''){
		$article = new Article;
		/** check and insert visited article */
		
		$getcount = $article->like_reply($reply_id,$user_id,$button_type);
		//echo $getcount;
	
       /************insert notification on reply like *****************/
       if($button_type=='like'){
         $reply_data =  DB::table('comment_reply')->where('id','=', $reply_id )->get();
         if($reply_data[0]->user_id != $user_id ){
           $comment_data =  DB::table('comments')->where('id','=', $reply_data[0]->comment_id)->where('status', '=', 0)->get();
           $notification_msg =  DB::table('notification_msg')->where('type','=', 'like_reply')->get();
           
            $notification_data =  DB::table('notification')
             ->where('sender_id','=', $user_id)
             ->where('recipient_id','=', $reply_data[0]->user_id)
             ->where('notification_id','=', $notification_msg[0]->id)
             ->where('article_id','=',  $comment_data[0]->article_id)
             ->where('comment_id','=',  $comment_data[0]->id)
             ->where('reply_id','=',  $reply_data[0]->id)
             ->get();
         
               $notification = array(
                  'sender_id'=>$user_id ,
                  'recipient_id'=> $reply_data[0]->user_id,
                  'notification_id'=> $notification_msg[0]->id,
                  'article_id'=> $comment_data[0]->article_id,
                  'comment_id'=> $comment_data[0]->id,
                  'reply_id'=> $reply_data[0]->id,
                  'is_read'=>0,
                  'created_at'=> date('Y-m-d H:i:s'),
              );
           if(count($notification_data)>0){
                 DB::table('notification')
                    ->where('sender_id','=', $user_id)
                     ->where('recipient_id','=', $reply_data[0]->user_id)
                    ->where('notification_id','=', $notification_msg[0]->id)
                    ->where('article_id','=',  $comment_data[0]->article_id)
                    ->where('comment_id','=',  $comment_data[0]->id)
                    ->where('reply_id','=',  $reply_data[0]->id)
                    ->update($notification);
           }else{
               DB::table('notification')->insert($notification);
           }
        
         }
       }
        /************ insert notification on reply like   END **************/
			$result['user_id'] = $user_id;
			$result['count'] = $getcount;
			$like = ($button_type=='like') ?'unlike':'like';
			$result['like'] = $like;
		}else{
		   $result['user_id']='';
		}
		return response()->json($result);
		
	}
	
	function addComment(Request $request){ 
	
        $article = new Article;
        $user_id = Session::get('user_id');
        if($user_id ==''){
        return Response::json(array(
					'success' => false,
					'user' => ''
					), 200);
         }
		    $rules = array('comment'    => 'required');
				$validator = Validator::make($request->input(), $rules);
				if ($validator->fails()) {
					 return Response::json(array(
					'success' => false,
					'errors' => $validator->getMessageBag()->toArray()
					), 200);
				  
				} else {
          
            $article_id =  $request->get('article_id');
            $comment = array(
            'comment'=> $request->get('comment'),
            'user_id'=> $user_id,
            'article_id'=> $article_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
            );
            
           $comment_id = DB::table('comments')->insertGetId($comment);
           $comment =$article->get_single_comment($comment_id);
      
          $view = View::make('frontend.comment.add_comment_ajax',['comment'=>$comment]);
					
		  $comment_count = comment_count($article_id);
          $contents = $view->render();
            return Response::json(array(
                  'success' => true,
                  'errors' => '',
                  'view' => $contents,
                  'comment_count' => $comment_count
             ), 200);
				  }
	}
  
  	function editComment(Request $request){ 
	
        $article = new Article;
        $user_id = Session::get('user_id');
        $article_id = $request->get('article_id');
        $comment_id = $request->get('comment_id');
        $comment = $request->get('comment');
        if($user_id ==''){
        return Response::json(array(
					'success' => false,
					'user' => ''
					), 200);
         }
		
				if ($comment=='') {
					 return Response::json(array(
					'success' => false,
					'errors' => 'Please enter Comment.'
					), 200);
				  
				} else {
    
            $comment_data = array(
            'comment'=> $comment,
            'user_id'=> $user_id,
            'article_id'=> $article_id,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
            );
            DB::table('comments')->where('article_id','=',$article_id)->where('user_id','=',$user_id)->where('id','=',$comment_id)->update($comment_data);   
         
            return Response::json(array(
                  'success' => true,
                  'errors' => '',
                  'view' => $comment
             ), 200);
				  }
	}

  /********* delete comment *********/
  function deleteComment($comment_id){ 
	
        $article = new Article;
        $user_id = Session::get('user_id');
        if($user_id ==''){
        return Response::json(array(
					'success' => false,
					'user' => ''
					), 200);
         }

            $comment_data = array(
            'status'=>1,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
            );
            DB::table('comments')->where('user_id','=',$user_id)->where('id','=',$comment_id)->update($comment_data);   
         
            return Response::json(array(
                  'success' => true,
                  'errors' => '',
             ), 200);
				  
	}
  
  /************* Report Comment ************/
  
   	function reportComment(Request $request){ 
        $article = new Article;
        $user_id = Session::get('user_id');
        $article_id =  $request->get('article_id');
        $comment_id =  $request->get('comment_id');
        $comment_user_id =  $request->get('comment_user_id');
        $reason =  $request->get('reason');

        if($user_id ==''){
        return Response::json(array(
					'success' => false,
					'user' => '',
					'errors' => '',
					), 200);
         }

        $get_data =  DB::table('report_comments')
                    ->where('comment_id','=', $comment_id)
                    ->where('report_by_user','=', $user_id)
                    ->where('comented_user_id','=', $comment_user_id)
                    ->where('article_id','=',  $article_id)
                    ->get();

       if(count($get_data)>0){
          return Response::json(array(
					'success' => false,
					'errors' => 'You have already reported on this comment.'
					), 200);
       }else{
          if ($reason=='') {
             return Response::json(array(
            'success' => false,
            'errors' => 'Please enter reason'
            ), 200);

          } else {

              $report_comment = array(
              'comment_id'=> $comment_id,
              'report_by_user'=> $user_id,
              'comented_user_id'=> $comment_user_id,
              'article_id'=> $article_id,
              'reason'=> $reason,
              'report_date'=>date('Y-m-d H:i:s'),
              );

             DB::table('report_comments')->insertGetId($report_comment);

              return Response::json(array(
                    'success' => true,
                    'errors' => '',
               ), 200);
            }
       }
   	}
  /************* Add Reply ****************/
  	function addReply(Request $request){ 
        $article = new Article;
        $user_id = Session::get('user_id');
        if($user_id ==''){
          return Response::json(array(
            'success' => false,
            'user' => ''
            ), 200);
         }
        $reply ='comment_reply_'.$request->get('comment_id');
		    $rules = array( $reply => 'required');
        $messages = ['required' => "Please Enter Reply."];
				$validator = Validator::make($request->input(), $rules,$messages);
				if ($validator->fails()) {
					 return Response::json(array(
					'success' => false,
					'errors' => $validator->getMessageBag()->toArray()
					), 200);
				  
				} else {
          
            $comment = array(
            'reply'=> $request->get($reply),
            'user_id'=> $user_id,
            'comment_id'=> $request->get('comment_id'),
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
            );
            
          $reply_id = DB::table('comment_reply')->insertGetId($comment);
          $reply =$article->get_single_reply($reply_id);
          
          /************ notification of comment reply *****************/
           $comment_data =  DB::table('comments')->where('id','=', $request->get('comment_id'))->where('status', '=', 0)->get();
           if($comment_data[0]->user_id != $user_id ){
             
             $notification_msg =  DB::table('notification_msg')->where('type','=', 'reply_comment')->get();
             $notification = array(
                'sender_id'=>$user_id ,
                'recipient_id'=> $comment_data[0]->user_id,
                'notification_id'=> $notification_msg[0]->id,
                'article_id'=> $comment_data[0]->article_id,
                'comment_id'=> $comment_data[0]->id,
                'is_read'=>0,
                'created_at'=> date('Y-m-d H:i:s'),
            );
             DB::table('notification')->insert($notification);
           }
          /************ notification    on reply end **************/
      
          $view = View::make('frontend.comment.comment_reply_ajax',['reply_data'=>$reply]);
          $contents = $view->render();
            return Response::json(array(
                  'success' => true,
                  'errors' => '',
                  'view' => $contents
             ), 200);
				  }
	}
  /*********  load More comment  by ajax **************/
	public function loadMoreComment($article_id,$page){
			  $article = new Article;
		    $per_page_record =per_page_limit();
		    $end = $per_page_record;
		    $start = ($page*$per_page_record)-$per_page_record;
        $comment = $article->get_comments($article_id,$start,$end);
			 // $articles = $article->get_articles($start,$end);
    		$view = view('frontend.comment.loadmore_comment',compact('comment'))->render();
        return response()->json(['html'=>$view]);
     
	}
  
  /****************  Header Icon functionality *******************/
   function like_heart($article_id,$button_type,$like_type){
      $user_id = Session::get('user_id');
      if($user_id!=''){
      $article = new Article;
      /** check and insert visited article */
      $getcount = $article->like_heart_visit($article_id,$user_id,$button_type,$like_type);
      //echo $getcount;

        $result['user_id'] = $user_id;
        $result['count'] = $getcount;
        $like = ($button_type=='like') ?'unlike':'like';
        $result['like'] = $like;
      }else{
         $result['user_id']='';
      }
      return response()->json($result);

  }
  
  
  /************* Invite friend for article *******************/
  
  function invite_article($search=''){
      
      $user_id = Session::get('user_id');
 
      $freind_list = DB::select( DB::raw("select * from friends as f where (f.user_id = '".$user_id."' or f.friend_id =  '".$user_id."') and f.status = 1") );

     //pr($freind_list);
      if(count($freind_list)>0){
      $friend_ids = array();
      foreach($freind_list as $k=>$val){
        if( $val->user_id != $user_id  and $val->friend_id==$user_id){
          $friend_ids[]  = $val->user_id;
         }
         if( $val->user_id == $user_id  and $val->friend_id!=$user_id){
          $friend_ids[] = $val->friend_id;
         }     
      }
	    $friends_info = DB::table('users as u')
		    ->whereIn('u.id',$friend_ids ) 
        ->where('username','LIKE','%'.$search.'%')
            ->select('u.*')
            ->get()->toArray();	
    
    $final_data = array();
    foreach($friends_info as $key => $value ){
      $final_data[$key]['text']= $value->firstname .' '.$value->lastname;
      $final_data[$key]['value']= $value->id ;
      
     }
         echo json_encode($final_data);
      } else{
        echo ' ';
      }
  }
  /********** Invite And recommend ******/
  function invite_recommend(Request $request){
  
       $user_id = Session::get('user_id');
       if(count($request->input('users'))>0){
         
           $users_ids =  $request->input('users');
           $article_id =  $request->input('invite_article_id');
           $type_invite =  $request->input('type_invite');
           $invite =0;
           $recommend=0;
           if($type_invite == 'heart_invite'){    
                $invite = 1;
                $type = 'heart';
            } 
           if($type_invite == 'heart_recommend'){
               $recommend = 1;
               $type = 'heart';
           } 
           
           if($type_invite == 'visit_invite'){    
                $invite = 1;
                $type = 'visit';
            } 
           if($type_invite == 'visit_recommend'){
               $recommend = 1;
               $type = 'visit';
           } 
           foreach($users_ids as $key=>$user_request_id){
             $invite_recommend = DB::table('invite_recommend as ir')
		        ->where('ir.user_id',$user_id ) 
		        ->where('ir.invite_recommend_user_id',$user_request_id ) 
		        ->where('ir.article_id',$article_id ) 
		        ->where('ir.like_type',$type ) 
            ->select('ir.*')
            ->get()->toArray();	
             
             $date = date('Y-m-d H:i:s'); 
            if(count($invite_recommend)>0){
              
              /******** if invite from heart or recommend */
               if($type_invite == 'heart_invite'){
                    if($invite_recommend[0]->invite==0)
                      $data = array('invite'=>1,'like_type'=>$type,'invite'=>1,'created_at'=>$date);
                     else
                     $data = array('invite'=>1,'created_at'=>$date);
                    
               } 
               if($type_invite == 'heart_recommend'){
                  if($invite_recommend[0]->recommend==0)
                      $data = array('invite'=>1,'like_type'=>$type,'recommend'=>1,'created_at'=>$date);
                  else
                   $data = array('recommend'=>1,'created_at'=>$date);
                    
               } 
             /*************** end invite from heart or recommend article */
              
            /******** if invite from visit loaction icon or recommend */   
              if($type_invite == 'visit_invite'){    
                 if($invite_recommend[0]->invite==0)
                      $data = array('invite'=>1,'like_type'=>$type,'invite'=>1,'created_at'=>$date);
                     else
                     $data = array('invite'=>1,'created_at'=>$date);
            } 
             if($type_invite == 'visit_recommend'){
                 if($invite_recommend[0]->recommend==0)
                      $data = array('invite'=>1,'like_type'=>$type,'recommend'=>1,'created_at'=>$date);
                  else
                   $data = array('recommend'=>1,'created_at'=>$date);
             } 
             /*************** end invite from from visit loaction icon and recommend article */  
              
               DB::table('invite_recommend as ir')
                    ->where('ir.user_id',$user_id ) 
                    ->where('ir.invite_recommend_user_id',$user_request_id ) 
                    ->where('ir.article_id',$article_id ) 
                    ->where('ir.like_type',$type) 
                    ->update($data);
 
              }else{
              
                $data = array('user_id'=>$user_id,'invite_recommend_user_id'=>$user_request_id,'article_id'=>$article_id,'invite'=>$invite,
                              'recommend'=>$recommend,'like_type'=>$type,'created_at'=>$date);
                DB::table('invite_recommend')->insert($data);
  
             }              
      /************ Notification of comment reply *****************/
			 if($type_invite == 'heart_invite')   
            $notification_type = 'invite_heart';
        if($type_invite == 'heart_recommend')
            $notification_type = 'recommend_heart';
       
         if($type_invite == 'visit_invite')   
            $notification_type = 'invite_visit';
        if($type_invite == 'visit_recommend')
            $notification_type = 'recommend_visit';
             
       $notification_msg =  DB::table('notification_msg')->where('type','=', $notification_type)->get();
			 $notification_data =  DB::table('notification')
             ->where('sender_id','=', $user_id)
             ->where('recipient_id','=', $user_request_id)
             ->where('article_id','=', $article_id)
             ->where('notification_id','=', $notification_msg[0]->id)
             ->get();
			
             $notification = array(
                'sender_id'=>$user_id ,
                'recipient_id'=> $user_request_id,
                'article_id'=> $article_id,
                'notification_id'=> $notification_msg[0]->id,
                'is_read'=>0,
                'created_at'=> date('Y-m-d H:i:s'),
            );
			
			if(count($notification_data)>0){
                 DB::table('notification')
                 ->where('sender_id','=', $user_id)
					       ->where('recipient_id','=', $user_request_id)
                 ->where('article_id','=', $article_id)        
					       ->where('notification_id','=', $notification_msg[0]->id)
                     ->update($notification);
           }else{
               DB::table('notification')->insert($notification);
           }
			
          /************ notification    on reply end **************/  
           }
         
         /************** invite Red heart count if invite ***********/
          $red_heart=$blue_heart=$blue_location=$green_location='';
           $red_heart = DB::table('invite_recommend as ir')
		        ->where('ir.user_id',$user_id ) 
		        ->where('ir.article_id',$article_id ) 
		        ->where('ir.like_type','heart' ) 
		        ->where('ir.invite',1) 
            ->select('ir.*')
            ->get()->toArray();
         
          /************** invite blue heart count  if recommend***********/
           $blue_heart = DB::table('invite_recommend as ir')
		        ->where('ir.user_id',$user_id ) 
		        ->where('ir.article_id',$article_id ) 
		        ->where('ir.like_type','heart' ) 
		        ->where('ir.recommend',1) 
            ->select('ir.*')
            ->get()->toArray();
         
           /************** invite blue heart count  if recommend***********/
           $blue_location = DB::table('invite_recommend as ir')
		        ->where('ir.user_id',$user_id ) 
		        ->where('ir.article_id',$article_id ) 
		        ->where('ir.like_type','visit' ) 
		        ->where('ir.invite',1) 
            ->select('ir.*')
            ->get()->toArray();
         
          /************** invite blue heart count  if recommend***********/
           $green_location = DB::table('invite_recommend as ir')
		        ->where('ir.user_id',$user_id ) 
		        ->where('ir.article_id',$article_id ) 
		        ->where('ir.like_type','visit' ) 
		        ->where('ir.recommend',1) 
            ->select('ir.*')
            ->get()->toArray();
         
            return Response::json(array(
            'success' => true,
            'red_heart' => count($red_heart),
            'blue_heart' => count($blue_heart),
            'blue_location' => count($blue_location),
            'green_location' => count($green_location),
            ), 200);
         
         }       
      else{   
           return Response::json(array(
            'success' => false,
            'red_heart' => '',
            'blue_heart' => '',
            'blue_location' => '',
            'green_location' => '',
            ), 200);
       }

  }
  
 /**********************  
 
 Show view on click of header icon heart,location 
 Show liked,invite,recommend view 
 **********************/
  function showHeaderIconView($show_type){
     $user_id = Session::get('user_id');
     $flag =false;
     if($show_type=='like'){
        $like_type = 'heart';
        $flag =true;
      }
     if($show_type=='like_location'){
        $like_type = 'visit';
        $flag =true;
      }
     if($show_type=='near_you'){
        $like_type = 'near';
        $flag =true;
      }
     if($flag){
     $like_visit = DB::table('like_heart_visit as lhv')
		        ->where('lhv.user_id',$user_id ) 
		        ->where('lhv.like_type',$like_type ) 
		        ->where('lhv.liked',1) 
            ->select('lhv.article_id')
            ->get()->toArray();
       $article_ids = array();
       foreach($like_visit as $key=> $val){
         $article_ids[]= $val->article_id;
       } 
     }
       
     $flag1 =false;
     if($show_type=='invite'){
        $like_type = 'heart';
        $field = 'invite';
        $flag1 =true;
      }
     if($show_type=='recommend'){
        $like_type = 'heart';
        $field = 'recommend';
        $flag1 =true;
      }
     if($show_type=='invite_location'){
        $like_type = 'visit';
        $field = 'invite';
        $flag1 =true;
      }
     if($show_type=='recommend_location'){
        $like_type = 'visit';
        $field = 'recommend';
        $flag1 =true;
      }
      if($flag1){
      $invite_recommend = DB::table('invite_recommend as ir')
		        ->where('ir.user_id',$user_id ) 
		        ->where('ir.'.$field,1) 
		        ->where('ir.like_type',$like_type ) 
            ->select('ir.article_id')
            ->get()->toArray();
      $article_ids = array();
       foreach($invite_recommend as $key=> $val){
         $article_ids[]= $val->article_id;
       }  
      }
    
        $articles = Article::whereIn('id',$article_ids)
            ->orderBy('created_at' ,'DESC')
		    ->orderBy('updated_at','DESC')
       ->get();
         //dd(DB::getQueryLog()); die;
         if(count($articles)>0){
               foreach ($articles as $key => $val){
                $article_image = DB::table('article_image as ai')
                 ->where('ai.article_id', '=',  $val->id)
                 //->orderBy(DB::raw('RAND()'))
                 ->select('ai.article_id','ai.medium_image','ai.small_image','ai.large_image','ai.embeded_code')
                 ->get();
				    $article_color = DB::table('categories as c')
                    ->where('c.id', '=',  $val->category_id)
                    ->select('c.color')
                    ->get();
                $articles[$key]->color = $article_color[0]->color;
                $articles[$key]->small_image = $article_image[0]->small_image;
                $articles[$key]->large_image = $article_image[0]->large_image;
                $articles[$key]->medium_image = $article_image[0]->medium_image;
                $articles[$key]->embeded_code = $article_image[0]->embeded_code;
              }
           
              foreach($articles as $key1=>$val1){
              $liked = DB::table('article_liked as al')
               ->select('*')
               ->where('al.article_id', '=', $val1->id)
               ->where('al.user_id', '=', $user_id)
               ->get();
			        $articles[$key1]->liked = @$liked[0]->liked;
		   }
           
          }
       
       $view = view('frontend.article.like_invite_recommend',compact('articles'))->render();
       return response()->json(['view'=>$view]);
 
  }
  
	/****************** Send email **********************/
	 public function send_email($to,$subject,$message){

		 // Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		// More headers
		$headers .= 'From: <webmaster@example.com>' . "\r\n";
		$headers .= 'Cc: myboss@example.com' . "\r\n";
		return mail($to,$subject,$message,$headers);

	 }	
}