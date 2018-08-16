<?php

namespace App;
use DB;
use Session;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
     protected $table = 'articles';
	
	/****** Get Featured Artcle **************/
	function get_featured_article($start,$end){
	  // DB::enableQueryLog();    
	 $article = DB::table('articles as a')
		    ->where('a.featured', '=', 1)
		    ->where('a.published', '=', 1)
            ->select('a.id','a.title','a.slug','a.description','a.sponsored','a.article_type','a.created_at')
		    ->orderBy('a.created_at' ,'DESC')
		    ->orderBy('a.updated_at','DESC')
		     ->skip($start)
		    ->take($end)
		    //->take(10)
            ->get();
		
	  if(count($article)>0){	
		  foreach ($article as $key => $val){
			 $article_image = DB::table('article_image as ai')
		    ->where('ai.article_id', '=',  $val->id)
		    //->orderBy(DB::raw('RAND()'))
            ->take(1)
            ->select('ai.article_id','ai.medium_image','ai.small_image','ai.large_image','ai.embeded_code')
            ->get();
			  
			  	  
			 $article[$key]->small_image = $article_image[0]->small_image;
			 $article[$key]->large_image = $article_image[0]->large_image;
			 $article[$key]->medium_image = $article_image[0]->medium_image;
			 $article[$key]->embeded_code = $article_image[0]->embeded_code;
		  
		  }
		  return $article;
	    }else{
	  		return $article =array();
	  
	  }
	
	}
	
	/*************** get All article ***********/
	
	function getAllarticles(){
	  // DB::enableQueryLog();    
	 $article = DB::table('articles as a')
		 ->join('categories as c','a.category_id', '=', 'c.id')
		    ->where('a.published', '=', 1)
            ->select('a.id','a.title','a.slug','a.description','a.sponsored','a.article_type','a.created_at','c.color')
		    ->orderBy('a.created_at' ,'DESC')
		    ->orderBy('a.updated_at','DESC')
            ->get();
		return count($article);	
	}		
	
	/******** Get all Article on home page ***************/
	function get_articles($start,$end){
	  // DB::enableQueryLog();    
	 $article = DB::table('articles as a')
		   ->join('categories as c','a.category_id', '=', 'c.id')
		    ->where('a.published', '=', 1)
            ->select('a.id','a.title','a.slug','a.description','a.sponsored','a.article_type','a.created_at','c.color')
		    ->orderBy('a.created_at' ,'DESC')
		    ->orderBy('a.updated_at','DESC')
		    ->skip($start)
		    ->take($end)
            ->get();
	  if(count($article)>0){	
		  foreach ($article as $key => $val){
			 $article_image = DB::table('article_image as ai')
		    ->where('ai.article_id', '=',  $val->id)
            ->take(1)
            ->select('ai.article_id','ai.medium_image','ai.small_image','ai.large_image','ai.embeded_code')
            ->get();
			  
			  	  
			 $article[$key]->small_image = $article_image[0]->small_image;
			 $article[$key]->large_image = $article_image[0]->large_image;
			 $article[$key]->medium_image = $article_image[0]->medium_image;
			 $article[$key]->embeded_code = $article_image[0]->embeded_code;
		  
		  }
		  $user_id = Session::get('user_id');
		   foreach($article as $key1=>$val1){
				$liked = DB::table('article_liked as al')
				 ->select('*')
				 ->where('al.article_id', '=', $val1->id)
				 ->where('al.user_id', '=', $user_id)
				 ->get();
			   
			     $article[$key1]->liked = @$liked[0]->liked;
		   }
		  
		  
		  return $article;
	    }else{
	  		return $article =array();
	  
	  }
	
	}
	
	/************** check and insert visited article *****************/
	function visited_article($ip,$slug){
		
		$article_data =  DB::table('articles')->where('slug','=',$slug)->get();
		//dd($article_data) ;
		$article_visited =  DB::table('article_visited')->where('article_id','=',$article_data[0]->id)->where('ip_address','=',$ip)->get();
		if(count($article_visited)>0){	
		}else{
		   $user_id =  (Session::get('user_id')!='')?Session::get('user_id'):0;
		   $visited = array('article_id'=>$article_data[0]->id,'ip_address'=>$ip,'visited_date'=>date('Y-m-d H:i:s'),'user_id'=>$user_id);
		   DB::table('article_visited')->insert($visited);
			
		}
		
	}
	
	
	
/************** Like article *****************/
	function like_article($article_id,$user_id,$button_type){
		$ip = $_SERVER['REMOTE_ADDR'];
		$like_article =  DB::table('article_liked')->where('article_id','=',$article_id)->where('user_id','=',$user_id)->get();
		$date = date('Y-m-d H:i:s');
		if(count($like_article)>0){	
		   $liked = ($button_type=='like')?1:0;
		   $visited = array('article_id'=>$article_id,'ip_address'=>$ip,'liked'=>$liked,'user_id'=>$user_id,'liked_at'=>$date);
		   DB::table('article_liked')->where('article_id','=',$article_id)->where('user_id','=',$user_id)->update($visited);
		}else{
		   $liked = ($button_type=='like')?1:0;
		   $visited = array('article_id'=>$article_id,'ip_address'=>$ip,'liked'=>$liked,'user_id'=>$user_id,'liked_at'=>$date);
		   DB::table('article_liked')->insert($visited);
		}
		$article_count =  DB::table('article_liked')->where('article_id','=',$article_id)->where('liked','=',1)->get();
		return count($article_count);
		
	}
	
	
	
	/************** You May like Article *****************/
	function youMayArticle($cat_id,$start,$end){
	 // DB::enableQueryLog();    
	 $article = DB::table('articles as a')
		    ->where('a.published', '=', 1)
		    ->where('a.category_id', '=', $cat_id)
            ->select('a.id','a.title','a.slug','a.description','a.sponsored','a.article_type','a.created_at')
		    ->orderBy('a.created_at' ,'DESC')
		    ->orderBy('a.updated_at','DESC')
		    ->skip($start)
		    ->take($end)
            ->get();
	  if(count($article)>0){	
		  foreach ($article as $key => $val){
			 $article_image = DB::table('article_image as ai')
		    ->where('ai.article_id', '=',  $val->id)
		    //->orderBy(DB::raw('RAND()'))
            ->take(1)
            ->select('ai.article_id','ai.large_image','ai.embeded_code')
            ->get();

			 //$article[$key]->small_image = $article_image[0]->small_image;
			 $article[$key]->large_image = $article_image[0]->large_image;
			// $article[$key]->small_image = $article_image[0]->small_image;
			 $article[$key]->embeded_code = $article_image[0]->embeded_code;
		  
		  }
		  return $article;
	    }else{
	  		return $article =array();
	  
	  }
		
	}

	
	/******** Get Article All images for single page slider ***************/
	function getArticleAllImagesSlider($id){
	  // DB::enableQueryLog();    
	 $article = DB::table('articles as a')
		     ->join('article_image as ai', 'a.id', '=', 'ai.article_id')
            ->select('a.id','a.title','a.slug','a.description','a.sponsored','a.article_type','a.created_at', 'ai.medium_image','ai.small_image','ai.large_image','ai.embeded_code')
		    ->where('a.published', '=', 1)
		    ->where('a.id', '=',$id)
            ->get();
	  if(count($article)>0){	
		  	$user_id = Session::get('user_id');
		   foreach($article as $key1=>$val1){
				$liked = DB::table('article_liked as al')
				 ->select('*')
				 ->where('al.article_id', '=', $val1->id)
				 ->where('al.user_id', '=', $user_id)
				 ->get();
			   
			     $article[$key1]->liked = @$liked[0]->liked;
		   }
		 	return $article;
		  }
		  
	    else{
	  		return $article =array();
	  
	  }
	
	}
	
/***************************************************************************************************
							Get Comment of particular article 

***************************************************************/
	
	/************** get All comments of particluar article **********/
	function getAllcomments($article_id){
	  // DB::enableQueryLog();    
	 $comments = DB::table('comments as c')
		     ->join('users as u', 'u.id', '=', 'c.user_id')
             ->select('u.username','u.firstname','u.image','u.lastname','c.comment','c.id','c.created_at','c.article_id','c.user_id')
		     ->where('c.article_id', '=', $article_id)
		     ->where('c.status', '=', 0)
		     ->orderBy('c.created_at' ,'DESC')
            ->get();
		
		return count($comments);
	}
	
	/*** get comment of particlar article */ 
	function get_comments($article_id,$start,$end){
	  // DB::enableQueryLog();    
	 $comments = DB::table('comments as c')
		     ->join('users as u', 'u.id', '=', 'c.user_id')
             ->select('u.username','u.firstname','u.image','u.lastname','c.comment','c.id','c.created_at','c.article_id','c.user_id')
		     ->where('c.article_id', '=', $article_id)
		     ->where('c.status', '=', 0)
		     ->skip($start)
		     ->take($end)
		     ->orderBy('c.created_at' ,'DESC')
            ->get();
	    if(count($comments)>0){	
		   foreach($comments as $key=>$val){
				$liked = DB::table('comment_like as cl')
				 ->select('*')
				 ->where('cl.comment_id', '=',  $val->id)
				 ->where('cl.liked', '=', 1)
				 ->get();
			     $comments[$key]->liked_count = count($liked);
		   }
		  /************ check if liked or not by user ***/
		  	$user_id = Session::get('user_id');
		   foreach($comments as $key1=>$val1){
				$liked = DB::table('comment_like as cl')
				 ->select('*')
				 ->where('cl.comment_id', '=', $val1->id)
				 ->where('cl.user_id', '=', $user_id)
				 ->get();
			     $comments[$key1]->liked = @$liked[0]->liked;
		    }
		 	return $comments;
		  }
	    else{
	  		return $comments =array();
	  
	  } 
	}
	
	/*** get single comment by Comment id */ 
	function get_single_comment($comment_id){
	  // DB::enableQueryLog();    
	 $comments = DB::table('comments as c')
		     ->join('users as u', 'u.id', '=', 'c.user_id')
             ->select('u.username','u.firstname','u.image','u.lastname','c.comment','c.id','c.created_at','c.article_id','c.user_id')
		     ->where('c.id', '=', $comment_id)
		      ->where('c.status', '=', 0)
		     ->orderBy('c.created_at' ,'DESC')
            ->get();
	  if(count($comments)>0){	
		   foreach($comments as $key=>$val){
				$liked = DB::table('comment_like as cl')
				 ->select('*')
				 ->where('cl.comment_id', '=',  $val->id)
				 ->where('cl.liked', '=', 1)
				 ->get();
			     $comments[$key]->liked_count = count($liked);
		   }
		  /************ check if liked or not by user ***/
		  	$user_id = Session::get('user_id');
		   foreach($comments as $key1=>$val1){
				$liked = DB::table('comment_like as cl')
				 ->select('*')
				 ->where('cl.comment_id', '=', $val1->id)
				 ->where('cl.user_id', '=', $user_id)
				 ->get();
			     $comments[$key1]->liked = @$liked[0]->liked;
		    }
		 	return $comments;
		  }
	    else{
	  		return $comments =array();
	  
	  } 
	}
	
	
	/************** Like comment *****************/
	function like_comment($comment_id,$user_id,$button_type){
		$ip = $_SERVER['REMOTE_ADDR'];
		$like_comment =  DB::table('comment_like')->where('comment_id','=',$comment_id)->where('user_id','=',$user_id)->get();
		$date = date('Y-m-d H:i:s');
		if(count($like_comment)>0){	
		   $liked = ($button_type=='like')?1:0;
			
		   $data = array('comment_id'=>$comment_id,'ip_address'=>$ip,'liked'=>$liked,'user_id'=>$user_id,'liked_at'=>$date);
		   DB::table('comment_like')->where('comment_id','=',$comment_id)->where('user_id','=',$user_id)->update($data);
		}else{
		   $liked = ($button_type=='like')?1:0;
		   $data = array('comment_id'=>$comment_id,'ip_address'=>$ip,'liked'=>$liked,'user_id'=>$user_id,'liked_at'=>$date);
		   DB::table('comment_like')->insert($data);
		}
		$comment_count =  DB::table('comment_like')->where('comment_id','=',$comment_id)->where('liked','=',1)->get();
		return count($comment_count);
		
	}
	
	
	/************** Like Reply *****************/
	function like_reply($reply_id,$user_id,$button_type){
		$ip = $_SERVER['REMOTE_ADDR'];
		$like_reply =  DB::table('comment_reply_like')->where('reply_id','=',$reply_id)->where('user_id','=',$user_id)->get();
		$date = date('Y-m-d H:i:s');
		if(count($like_reply)>0){	
		   $liked = ($button_type=='like')?1:0;
			
		   $data = array('reply_id'=>$reply_id,'ip_address'=>$ip,'liked'=>$liked,'user_id'=>$user_id,'liked_at'=>$date);
		   DB::table('comment_reply_like')->where('reply_id','=',$reply_id)->where('user_id','=',$user_id)->update($data);
		}else{
		   $liked = ($button_type=='like')?1:0;
		   $data = array('reply_id'=>$reply_id,'ip_address'=>$ip,'liked'=>$liked,'user_id'=>$user_id,'liked_at'=>$date);
		   DB::table('comment_reply_like')->insert($data);
		}
		$reply_count =  DB::table('comment_reply_like')->where('reply_id','=',$reply_id)->where('liked','=',1)->get();
		return count($reply_count);
		
	}
	
	/*** get single reply by Comment id */ 
	function get_single_reply($reply_id){
	  // DB::enableQueryLog();    
	 $reply = DB::table('comment_reply as cr')
		     ->join('users as u', 'u.id', '=', 'cr.user_id')
             ->select('u.username','u.firstname','u.image','u.lastname','cr.reply','cr.id','cr.created_at','cr.comment_id','cr.user_id')
		     ->where('cr.id', '=', $reply_id)
		     ->orderBy('cr.created_at' ,'DESC')
            ->get();
	  if(count($reply)>0){	
		   foreach($reply as $key=>$val){
				$liked = DB::table('comment_reply_like as crl')
				 ->select('*')
				 ->where('crl.reply_id', '=',  $val->id)
				 ->where('crl.liked', '=', 1)
				 ->get();
			     $reply[$key]->liked_count = count($liked);
		   }
		  /************ check if liked or not by user ***/
		  	$user_id = Session::get('user_id');
		   foreach($reply as $key1=>$val1){
				$liked = DB::table('comment_reply_like as crl')
				 ->select('*')
				 ->where('crl.reply_id', '=', $val1->id)
				 ->where('crl.user_id', '=', $user_id)
				 ->get();
			     $reply[$key1]->liked = @$liked[0]->liked;
		    }
		 	return $reply;
		  }
	    else{
	  		return $reply =array();
	  
	  } 
	}

/*******************************like heart or like  visit *******************************************/
	function like_heart_visit($article_id,$user_id,$button_type,$like_type){
		$ip = $_SERVER['REMOTE_ADDR'];
		$like_data =  DB::table('like_heart_visit')->where('article_id','=',$article_id)
			->where('user_id','=',$user_id)->where('like_type','=',$like_type)->get();
		$date = date('Y-m-d H:i:s');
		$ip = $_SERVER['REMOTE_ADDR'];
		$liked = ($button_type=='like')?1:0;
		if(count($like_data)>0){	
	
		   $data = array('article_id'=>$article_id,'ip_address'=>$ip,'liked'=>$liked,'user_id'=>$user_id,'liked_at'=>$date,'like_type'=>$like_type);
		   DB::table('like_heart_visit')
			   ->where('article_id','=',$article_id)
			   ->where('user_id','=',$user_id)
			   ->where('like_type','=',$like_type)
			   ->update($data);
		}else{
		
		  $data = array('article_id'=>$article_id,'ip_address'=>$ip,'liked'=>$liked,'user_id'=>$user_id,'liked_at'=>$date,'like_type'=>$like_type);
		   DB::table('like_heart_visit')->insert($data);
		}
		$like_count =  DB::table('like_heart_visit')->where('user_id','=',$user_id)->where('like_type','=',$like_type)->where('liked','=',1)->get();
		return count($like_count);
		
	}
	
	
	
	
	
	
}
