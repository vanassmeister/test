/* 
 * @author Ivan Nikiforov
 * Apr 10, 2016
 */

(function($){
    var picturesContainer = $("#pictures_container");
    var uploadError = $("#upload_error");
    
    function showUploadError(errors) {
        uploadError.show().find("span.error-text").text(errors.join(", "));        
    }
    
    window.onFileUploadDone = function(e, data) {
        var responseJson = data.jqXHR.responseJSON;
        
        uploadError.hide();
        if(responseJson.status === "ok") {
            picturesContainer.html(responseJson.html);
        } else {
            showUploadError(responseJson.errors);
        }
    };
    
    window.onFileUploadAdd = function(e, data) {
        var acceptFileTypes = /^image\/(gif|jpe?g|png)$/i;
        var type = data.originalFiles[0]['type'];
        if(type.length && !acceptFileTypes.test(type)) {
            showUploadError(["Недопустимый тип файла, разрешены типы gif, jpeg, png (клиентская валидация)"]);
            return false;
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
