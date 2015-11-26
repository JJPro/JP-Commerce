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

    // disable auto discover
    Dropzone.autoDiscover = false;

    var action = "meta_box_artwork_media_upload",
        post_id = $("#media-upload-wrap").attr("data-post_id"),
        nonce = $("#media-upload-wrap").attr("data-nonce");

    // initialize and configure dropzone
    $("#media-upload-wrap").dropzone(
        {
            url: myAjax.ajaxurl,
            method: "post",
            maxFiles: 5,
            //acceptedFiles: "image/*",
            //autoProcessQueue: false,
            previewTemplate: '' +
                    '<div class="dz-preview dz-file-preview">' +
                    '   <div class="dz-details">' +
                    '       <img data-dz-thumbnail />' +
                    '   </div>' +
                    '   <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div> ' +
                    '   <a class="dz-feature">K</a>' +
                    '</div> ',
            clickable: ".dz-clickable",
            addRemoveLinks: true,
            dictInvalidFileType: "Invalid file type.",
            dictResponseError: "Upload Failed. \nError Code: {{statusCode}}.",
            dictRemoveFile: "✘",
            dictMaxFilesExceeded: "Exceeded number of files allowed."
        }
    );

    // retrieve the dropzone instance for further event handlers
    var myDropzone = Dropzone.instances[0];
    myDropzone.on("addedfile", function() {$("#media-upload-wrap").append($("#upload-indicator-wrap"));});
    myDropzone.on("maxfilesreached", function() {this.disable();});
    myDropzone.on("maxfilesexceeded", function(file) {this.removeFile(file);});
    myDropzone.on("removedfile", function(file) {
        // re-enable dropzone if it is out of the "max file reached" situation
        if (this.files.length == 4){
            this.enable();
        }

        /**
         * TODO: Remove file from the server
         */
    });
    myDropzone.on("sending", function(file, xhr, data){
        data.action = action;
        data.post_id = post_id;
        data.nonce = nonce;
        console.log(data);
    });
    myDropzone.on("success", function(file, response){
        console.log(response);
    });


});