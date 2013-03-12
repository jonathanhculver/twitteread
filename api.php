<?php
session_start();

/* Exchange request token for access token and connect */
function authenticate() {
    require('config.php');
    $userData= $_SESSION['userData'];
    $to = new TwitterOAuth($consumer_key, $consumer_secret,
    $userData['Twitter_Request_Token'], $userData['Twitter_Request_Token_Secret']);
    $tok = $to->getAccessToken();
    
    $userData['Twitter_Access_Token'] 	= $tok['oauth_token'];
    $userData['Twitter_Access_Token_Secret'] = $tok['oauth_token_secret'];
    
    $_SESSION['userData']= $userData; 
    
    return new TwitterOAuth($consumer_key, $consumer_secret, $userData['Twitter_Access_Token'], $userData['Twitter_Access_Token_Secret']);    
}

/* returns account info for authenticated user */
function getAccountInfo($connection) {
    
    return $connection->get('http://api.twitter.com/1/account/verify_credentials.json');
}

/* returns list of friends by sn */
function getListOfFriendIds($connection) {
    $friendIds= $connection->get('friends/ids');
    $friendIds= $friendIds->ids;
    
    $friendsNum= count($friendIds);
    $friendsIdArray = array();
    if($friendsNum>23) {
        $pages = $friendsNum/23;
        $start = 0;
        
        for($i=0; $i<ceil($pages); $i++) {
            if($i!=$pages) {
                $friendsIdArray[$i]=array_slice($friendIds, $start, 23);
                $friendsIdArray[$i]= implode(",", $friendsIdArray[$i]);
                $start= $start+23;
            } else {
                $friendsIdArray[$i]= array_slice($friendIds, $start, 23);
                $friendsIdArray[$i]= implode(",", $friendsIdArray[$i]);
            }
            
        }
    
    } else {
        $friendsIdArray[0] = implode(",", $friendIds);
    }
    
    return $friendsIdArray;
}

/* returns details about each user the authenicated user is following */
function getFriends($connection) {
    
    $comma_separated = getListOfFriendIds($connection);
    $friends = array();
    $pageNum = count($comma_separated);
    
    for($i=0; $i<$pageNum; $i++) {
        $friends[$i]= $connection->get('users/lookup', array('user_id' => $comma_separated[$i]));
    }
    
    return $friends;

}

/* returns tweets for list of friends */
function getTweets($connection) {
    
    return $connection->get('statuses/home_timeline', array('exclude_replies'=> false));
     
}

/*replaces hashtags and links with links */
function hashtagReplace($text) {
    $regex= '/\s*#[A-Za-z0-9]+/';
    $matches= array();
    preg_match_all($regex, $text, $matches);

    $newTweet= $text;
    for($i=0; $i<count($matches[0]); $i++) {
        $match= str_replace('#', '%23', $matches[0][$i]);
        $newTweet= preg_replace('/'.$matches[0][$i].'/', "<a href='http://twitter.com/#!/search/".trim($match)."'>".$matches[0][$i]."</a>", $newTweet);     
    }
    
    $regex= '/\s+@[A-Za-z0-9]+/';
    $matches= array();
    preg_match_all($regex, $text, $matches);
    
    for($i=0; $i<count($matches[0]); $i++) {
        $match= str_replace('@', '', $matches[0][$i]);
        $newTweet= preg_replace('/'.$matches[0][$i].'/', "<a href='http://twitter.com/#!/".trim($match)."'>".$matches[0][$i]."</a>", $newTweet);     
    }
    
    $regex= '/http:\/\/+[A-Za-z0-9]+\.*[A-Za-z0-9]*\/*[A-Za-z0-9]*/';
    $matches= array();
    preg_match_all($regex, $text, $matches);
    
    for($i=0; $i<count($matches[0]); $i++) {
        $match= addcslashes($matches[0][$i], "/.");
        $newTweet= preg_replace('/'.$match.'/', "<a href='".trim($matches[0][$i])."'>".$matches[0][$i]."</a>", $newTweet);     
    }        
    
    return $newTweet;
    
}

/* follows a user by screen name */
function postUser($screenName, $connection) {
    
    return $connection->post('friendships/create', array('screen_name' => $screenName));
    
}



?>