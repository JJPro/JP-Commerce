/**
 * Created by jjpro on 11/14/15.
 */
jQuery(document).ready(function($){
    var mediaUploader;
    $("#upload-button").on("click", function (e) {
        e.preventDefault();
        if ( mediaUploader ) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: "Choose a Site Logo Picture",
            button: {
                text: "Choose Picture"
            },
            multiple: false
        });

        mediaUploader.on("select", function(){
            attachement = mediaUploader.state().get("selection").first().toJSON();
            $("#sitelogo_url").val(attachement.url);
            $(".sitelogo-preview.image-container").css("background-image", "url(" + attachement.url + ")");
        })
        mediaUploader.open();
    });
});