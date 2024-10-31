jQuery(document).ready(function (){
jQuery.post(
    // see tip #1 for how we declare global javascript variables
    MyAjax.ajaxurl,
    {        
        action : 'myajax-submit',
        // other parameters can be added along with "action"
        postID : MyAjax.postID
    },
    function( response ) {
        //alert( response );
    }
);
});