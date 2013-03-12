<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link href="http://twitter.com/phoenix/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script type="text/javascript" src="javascript.js"></script>
    <title>Twitteread</title>
    <?php
    
    /* oauth library from - https://github.com/abraham/twitteroauth */
    require("libraries/oauth/twitteroauth/twitteroauth.php");
    require('api.php');
    
    $connection= authenticate();
    $content = getAccountInfo($connection);
    $friends= getFriends($connection);
    $friendsPages = count($friends);
    $friendTweets= getTweets($connection);
    
    ?>
  </head>
  <body>
    <div id="header">
        <div id="innerHeader">    
            <div id="logo"></div>
            <div id="searchContainer">
                <input type="text" id="searchBox" class="textInput" value="Filter For a User" onkeyup="filterTweets()" onblur="resetSearchText()" onclick="clearOnClick('searchBox')"/>
            </div>            
            <div id="user">
                <div id="profileImage"><?="<img height='41px' width='41px' src='".$content->profile_image_url_https."' />";?></div>
                <div id="userName"><?= $content->screen_name ?></div>
            </div>
        </div>
    </div>
    
    <div id="content">
        <div id="leftPane">
            <div id="addUserContainer">
                <input type="text" id="addBox" class="textInput" value="Follow a User" onkeyup="validateUser()" onclick="clearOnClick('addBox')" onblur="resetAddText()" "/>
                <input type="submit" value="Add" id="addButton" class="button" />
            </div>
            <div id="friendContainer">
                
            <? for($j=0; $j<$friendsPages; $j++) { ?>
            
            <div id="friendPage<?= $j+1 ?>" class= "friendPage" <? if($j!=0) { ?>  style="display:none"<? } ?> >
            
            <?php
            for($i=0; $i<count($friends[$j]); $i++) {
            ?>
                <div class="<?php if($i!=count($friends[$j])-1) {print 'friendRow';} else {print 'friendLastRow';}?>" onmouseover="showhide('<?= $friends[$j][$i]->screen_name?>')" onmouseout="showhideMouseOut('<?= $friends[$j][$i]->screen_name?>')" id="<?= $friends[$j][$i]->screen_name?>">
                    <div class="friendPhoto"><img src="<?= $friends[$j][$i]->profile_image_url; ?>" alt="Friend Picture" /></div>
                    <div class="friendName"><?= $friends[$j][$i]->name; ?><span class="showhide" id='showhide_<?= $friends[$j][$i]->screen_name?>' style='display: none'>Hide</span></div>
                    <div class="friendScreenName"><a href="http://twitter.com/#!/<?= $friends[$j][$i]->screen_name; ?>" class="friendScreenNameLink"><?= $friends[$j][$i]->screen_name; ?></a></div>
                </div>
            
            <? } ?>
            
            </div>
            
            <? } ?>
        
            <div id="pages">
                    
                <? for($k=$friendsPages; $k>0; $k--) { ?>
                    <div id="pageNum<?= $k?>" class="page" <? if($k==1){ ?> style="color: black;"<? } ?>onclick="showhidefriendpage(<?= $friendsPages?>, <?= $k?>)"><?= $k ?></div>
                <? } ?>
                <div class="pageLabel">Page:</div>
            </div>
            </div>    
        </div>
        <div id="rightPane">
            <div id="tweetHeader">Tweets</div>
            <div id='tweetcontainer'>
            <?php
            for($i=0; $i<count($friendTweets); $i++) {
            ?>
            
                <div id="<?= $friendTweets[$i]->id_str;?>" class="tweets <?php if($i!=count($friendTweets)-1) {print 'tweetRow';} else {print 'tweetLastRow';}?>">
                    <div class="friendPhoto"><img src="<?= $friendTweets[$i]->user->profile_image_url ?>" alt="friendPhoto" /></div>
                    <div class="tweetName">
                        <span class="tweetSN"><?= $friendTweets[$i]->user->name?></span><span class="tweetN">@<?= $friendTweets[$i]->user->screen_name ?></span>
                        <span class="tweetDate"><?= substr($friendTweets[$i]->created_at, 0, 10) ?></span>
                    </div>
                    <div class="tweetText"><?= hashtagReplace($friendTweets[$i]->text) ?></div>
                </div>
            
            <?php } ?>
            </div>
            
            <div id="footer"><a href="http://jonculverdev.com">&#169 Jon Culver Development LLC</a></div>

        </div>
        
    </div>
    
    
    
  </body>
</html>

