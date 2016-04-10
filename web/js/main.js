/* 
 * @author Ivan Nikiforov
 * Apr 10, 2016
 */

(function($){
    var picturesContainer = $("#pictures_container");
    
    window.onFileUploadDone = function(e, data) {
        var responseJson = data.jqXHR.responseJSON;
        var uploadError = $("#upload_error");
        
        uploadError.hide();
        if(responseJson.status === "ok") {
            picturesContainer.html(responseJson.html);
        } else {
            uploadError.show().find("span.error-text").text(responseJson.errors.join(", "));
        }
    };
    
    $("#button_clean").click(function(){
        $.ajax({
            url: "/site/clean",
            dataType: "json",
            success: function(){
                picturesContainer.html("");
            }
        });
    });
    
    $("#button_reload").click(function(){
        $.ajax({
            url: "/site/pictures",
            dataType: "html",
            success: function(data){
                picturesContainer.html(data);
            }
        });
    });    
})(jQuery);
