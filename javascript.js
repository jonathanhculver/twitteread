/* clears a form input on click */
function clearOnClick(id) {

    var node = $('#'+id);
    node.val('');
    node.css('background-color', 'white');
    node.css('color', 'black');
    
    
}

/* resets the search input on an outside click */
function resetSearchText() {

    var node= $('#searchBox');
    node.val('Filter For a User');
    node.css('background-color', '#777777');
    node.css('color', '#CCCCCC');
    
    var tweets= $('.tweets');
    
    for(var i=0; i<tweets.length; i++) {
        var sn = $('.tweetSN')[i].innerHTML;
        var tweet = $('.tweets')[i].id;
        var tweet = $('#'+tweet);
        
        tweet.removeClass('tweetHighlighted');
        
    }    

}

/* resets the add user input on an outside click */
function resetAddText() {

    var node= $('#addBox');
    node.val('Follow a User');
    node.css('color', '#999999');
}

/* highlights tweets by a searched user name*/
function filterTweets() {

    var query= $('#searchBox').val();
    query= query.toLowerCase();
    
    var tweets= $('.tweets');
    
    for(var i=0; i<tweets.length; i++) {
        var sn = $('.tweetN')[i].innerHTML;
        sn = sn.toLowerCase();
        var tweet = $('.tweets')[i].id;
        var tweet = $('#'+tweet);
        
        if(sn.match(query) && query!='') {
            tweet.addClass('tweetHighlighted');
        } else {
            tweet.removeClass('tweetHighlighted');
        }
        
    }
    

}

/* highlights users on hover and adds show or hide onclick events */
function showhide(sn) {
    
    var showhide= $('#showhide_'+sn);
    showhide.css('display', 'block');
    
    if(showhide[0].innerHTML=='Hide') {
        showhide.attr('onclick', "hideTweets('"+sn+"')");
        $('#'+sn).addClass('highlighted');
    } else {
        showhide.attr('onclick', "showTweets('"+sn+"')");
    }
}

/* removes highlight from users on mouseout */
function showhideMouseOut(sn){
    $('#'+sn).removeClass('highlighted');
    $('#showhide_'+sn).css('display', 'none');
}

/* hides tweets by a certain user */
function hideTweets(sn) {
    var tweets= $('.tweets');
    
    var user = $('#'+sn);
    user.addClass('grayedOut');
    var showhide = $('#showhide_'+sn)[0];
    showhide.innerHTML= 'Show';
    
    for(var i=0; i<tweets.length; i++) {
        sn = sn.toLowerCase();
        var tweet = $('.tweets')[i].id;
        var tweet = $('#'+tweet);
        
        var tweetS= $('.tweetN')[i].innerHTML.substring(1);
        
        if(sn == tweetS.toLowerCase()) {
            tweet.fadeOut();
        } 
        
    }    
}

/* shows tweets by a certain user */
function showTweets(sn) {
    var tweets= $('.tweets');
    
    var user = $('#'+sn);
    user.removeClass('grayedOut');
    var showhide = $('#showhide_'+sn)[0];
    showhide.innerHTML= 'Hide';
    
    for(var i=0; i<tweets.length; i++) {
        sn = sn.toLowerCase();
        var tweet = $('.tweets')[i].id;
        var tweet = $('#'+tweet);
        
        var tweetS= $('.tweetN')[i].innerHTML.substring(1);
        
        if(sn == tweetS.toLowerCase()) {
            tweet.fadeIn();
        } 
        
    }    
}


/* change button state on adding a user*/
function validateUser() {

    var sn = $('#addBox').val();
    
    if(sn.length>0) {
        $('#addButton').addClass('buttonActive');
        $('#addButton').attr('onclick', 'followUser()');
        $('#addBox').attr('onblur', '');
    } else {
        $('#addButton').removeClass('buttonActive');
        $('#addButton').attr('onclick', '');
        $('#addBox').attr('onblur', 'resetAddText()');
    }

}

/* adds sn to list of followers */
function followUser() {

    var sn = $('#addBox').val();
    var container= $('#friendContainer');
    
    $.ajax({
        type: 'POST',
        url: 'ajax_functions/postUser.php',
        data: "screenName="+sn,
        success: function(data) {
        
            resetAddText();
            $('#addButton').removeClass('buttonActive');
            $('#addButton').attr('onclick', '');
            $('#addBox').attr('onblur', 'resetAddText()');
            
            var jsonResponse= eval('(' + data + ')');
              
            if(jsonResponse.name) {
            
                var html= "<div style='display:none' class='friendRow' onmouseover='showhide(\""+jsonResponse.screen_name+"\")' onmouseout='showhideMouseOut(\""+jsonResponse.screen_name+"\")' id='"+jsonResponse.screen_name+"'><div class='friendPhoto'><img src='"+jsonResponse.profile_image_url+"' alt='Friend Picture' /></div><div class='friendName'>"+jsonResponse.name+"<span class='showhide' id='showhide_"+jsonResponse.screen_name+"' style='display: none'>Hide</span></div><div class='friendScreenName'><a href='http://twitter.com/#!/"+jsonResponse.screen_name+"' class='friendScreenNameLink'>"+jsonResponse.screen_name+"</a></div></div>";
                container.prepend(html); 
                $('#'+jsonResponse.screen_name).slideDown('slow');
                updateTweets(sn);
                
            } else {
                alert('Not a valid user.');
            }         
            
            
            
        }

    });

}

/* updates the tweets after a new user is followed */
function updateTweets(sn) {
    var tweetContainer= $('#tweetcontainer');

    $.ajax({
        type: 'POST',
        url: 'ajax_functions/updateTweets.php',
        success: function(data) {
        
            var jsonResponse= eval('(' + data + ')');
            var html='';
            
            for(var i=0; i<jsonResponse.length; i++) {
            
                var tweetText= jsonResponse[i].text;
                tweetText= replaceLinks(tweetText);
            
                html = html+"<div style='display:none;' id='"+jsonResponse[i].id_str+"' class='tweets tweetRow'><div class='friendPhoto'><img src='"+jsonResponse[i].user.profile_image_url+"' alt='friendPhoto' /></div><div class='tweetName'><span class='tweetSN'>"+jsonResponse[i].user.screen_name+"</span><span class='tweetN'>"+jsonResponse[i].user.name+"</span><span class='tweetDate'>"+jsonResponse[i].created_at.substring(0, 10)+"</span></div><div class='tweetText'>"+tweetText+"</div></div>";
                
            
            }
            
            tweetContainer[0].innerHTML= html;
            
            for(var i=0; i<jsonResponse.length; i++) {
                if(jsonResponse[i].user.screen_name==sn) {
                    $('#'+jsonResponse[i].id_str).fadeIn();
                } else {
                    $('#'+jsonResponse[i].id_str).css('display', 'block');
                }
                
            }
            
        
        }
    });


}

/* replaces hash tags with links */
function replaceLinks(text) {
    var regex= /\s*#[A-Za-z0-9]+/g;
    var matches = text.match(regex);
    
    var newTweet= text;
    
    if(matches!=null) {
    
        for(var i=0; i<matches.length; i++) {
            var match= matches[i].replace('#', '%23');
            newTweet= newTweet.replace(matches[i], "<a href='http://twitter.com/#!/search/"+$.trim(match)+"'>"+matches[i]+"</a>");
        }
    
    }
    
    var regex= /\s+@[A-Za-z0-9]+/g;
    var matches = text.match(regex);
    
    if(matches!=null) {
    
        for(var i=0; i<matches.length; i++) {
            var match= matches[i].replace('@', '');
            newTweet= newTweet.replace(matches[i], "<a href='http://twitter.com/#!/"+$.trim(match)+"'>"+matches[i]+"</a>");
        }
    
    }
    
    var regex= /http:\/\/+[A-Za-z0-9]+\.*[A-Za-z0-9]*\/*[A-Za-z0-9]*/g;
    var matches = text.match(regex);
    
    if(matches!=null) {
    
        for(var i=0; i<matches.length; i++) {
            var match= matches[i];
            newTweet= newTweet.replace(match, "<a href='"+$.trim(matches[i])+"'>"+matches[i]+"</a>");
        }
    
    }            
    
    
    return newTweet;
    


}

/*show hide friend page */

function showhidefriendpage(count, page) {

    for(var i=0; i<count+1; i++) {
        $('#friendPage'+(i+1)).css('display', 'none');
        $('#pageNum'+(i+1)).css('color', '#579BBF');
    }
    
    
    $('#friendPage'+page).css('display', 'block');
    $('#pageNum'+page).css('color', 'black');

}


