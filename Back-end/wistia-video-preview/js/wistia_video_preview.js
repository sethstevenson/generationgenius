
jQuery(document).ready(function($) {

    var pausedVideo; // Used to identify which video on the page has been paused if there are multiple videos on the page.

    window._wq = window._wq || [];
    _wq.push({ id: "_all", onReady: function(video) {

    video.bind("secondchange", function() {

        // If the user is not logged in and the video has been watched for more than 60 seconds
        if ( ! document.body.classList.contains( 'logged-in' ) && video.secondsWatched() >= 3) {
        video.pause(); // Pause the video
        pausedVideo = video; // Define which video needs to be resumed after logged in

        // Show the login form
        $('body').prepend('<div class="login_overlay"></div>');
        $('form#login').fadeIn(500);
        }
    });

    }});

    // Perform AJAX login on form submit
    $('form#login').on('submit', function(e){
        $('form#login p.status').show().text(ajax_login_object.loadingmessage);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_login_object.ajaxurl,
            data: { 
                'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                'username': $('form#login #username').val(), 
                'password': $('form#login #password').val(), 
                'security': $('form#login #security').val() },
            success: function(data){
                $('form#login p.status').text(data.message);
                if (data.loggedin == true){

                    // Add the logged-in class to the body which will prevent the video from pausing in the future
                    $('body').addClass('logged-in');

                    // Hide the login form
                    $('div.login_overlay').remove();
                    $('form#login').hide();

                    // Resume the video
                    pausedVideo.play();
                }
            }, 
            error: function(data){
                $('form#login p.status').text(data.message);
            }
        });
        e.preventDefault();
    });

});