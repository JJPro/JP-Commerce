/**
 * Created by jjpro on 11/25/15.
 *
 * Handles Ajax of the artwork-media meta box to upload media files to the server
 */
jQuery(function ($){
    console.log("initializing media meta box");

    // disable drop file to open in browser feature
    $(window).on("dragover", function(e){e = e||event;e.preventDefault();});
    $(window).on("drop", function(e){e = e||event;e.preventDefault();});


    /***
     * Set featured image
     */
    $("body").on("click", ".dz-feature", function(event) {
        if (!$(this).hasClass("fa-heart")){
            var clicked_obj = this;
            var index = $(".dz-feature").index(this);
            var file = Dropzone.instances[0].files[index];
            $.ajax({
                    url: jc_data.ajaxurl,
                    method: "post",
                    data: {
                        action: "jp_commerce_set_artwork_featured_image",
                        nonce: nonce,
                        name: file.name,
                        post_id: post_id,
                        author_id: author_id
                    }
            })
            .success(function() {
                $(".fa-heart").removeClass("fa-heart").addClass("fa-heart-o");
                $(clicked_obj).removeClass("fa-heart-o").addClass("fa-heart");
            })
            .error(function() {
                console.log( "failed to set featured image on server.");
            });
        }
    });






    /*************** DROPZONE ******************/

    // disable auto discover
    Dropzone.autoDiscover = false;

    var action = "jp_commerce_upload_artwork_media",
        post_id = $("#media-upload-wrap").attr("data-post_id"),
        author_id = $("#media-upload-wrap").attr("data-author_id"),
        nonce = $("#media-upload-wrap").attr("data-nonce");

    // initialize and configure dropzone
    $("#media-upload-wrap").dropzone(
        {
            url: jc_data.ajaxurl,
            method: "post",
            maxFiles: 5,
            acceptedFiles: "image/*",
            //autoProcessQueue: false,
            uploadMultiple: false,
            previewTemplate: '' +
                    '<div class="dz-preview dz-file-preview">' +
                    '   <div class="dz-details">' +
                    '       <img data-dz-thumbnail />' +
                    '   </div>' +
                    '   <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div> ' +
                    '   <div class="controls">' +
                    '       <i class="dz-remove fa fa-close" data-dz-remove></i>' +
                    '       <i class="dz-feature fa fa-heart-o"></i>' +
                    '   </div>' +
                    '</div> ',
            clickable: ".dz-clickable",
            dictInvalidFileType: "Invalid file type.",
            dictResponseError: "Upload Failed. \nError Code: {{statusCode}}.",
            dictMaxFilesExceeded: "Exceeded number of files allowed."
        }
    );

    // retrieve the dropzone instance for further event handlers
    var myDropzone = Dropzone.instances[0];
    myDropzone.on("addedfile", function() {$("#media-upload-wrap").append($("#upload-indicator-wrap"));});
    //myDropzone.on("maxfilesreached", function(file) {this.enqueueFile(file); this.disable();});
    myDropzone.on("maxfilesexceeded", function(file) {this.removeFile(file);});
    myDropzone.on("removedfile", function(file) {
        // re-enable dropzone if it is out of the "max file reached" situation
        if (this.files.length == 4){
            this.enable();
        }

        // Debug
        console.log(file);

        // remove the file from server
        $.ajax({
                url: jc_data.ajaxurl,
                method: "post",
                data: {
                    action: "jp_commerce_remove_artwork_media",
                    nonce: nonce,
                    name: file.name,
                    post_id: post_id,
                    author_id: author_id
                }
            })
            .done(function() {
                alert( "file is successfully deleted.");
            })
            .fail(function() {
                alert( "failed to delete file from server.");
            });
    });
    myDropzone.on("sending", function(file, xhr, data){
        data.append("action", action);
        data.append("post_id", post_id);
        data.append("author_id", author_id);
        data.append("nonce", nonce);
    });
    myDropzone.on("success", function(file, response){
        console.log(response);
    });




    /***
     * Load existing images
     */
    var existing_file_count = existing_thumbnails.length;
    console.log("file count: " + existing_file_count);
    myDropzone.options.maxFiles -= existing_file_count;
    var thumbnail;
    for ( var i= 0, len=existing_file_count; i<len; i++ ) {
        var thumbnail = existing_thumbnails[i];

        var mockFile = {name: thumbnail.name, size: 123};

        console.log("mockFile: " + mockFile.size);

        myDropzone.emit("addedfile", mockFile);

        myDropzone.emit("thumbnail", mockFile, thumbnail.url);

        // Make sure that there is no progress bar
        myDropzone.emit("complete", mockFile);
    }


});