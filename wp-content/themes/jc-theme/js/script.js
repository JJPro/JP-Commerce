jQuery(document).ready(
    function($){
        /**
            ========================
                single-artwork page
            ========================
            List of Items:
            1. Modal
            2. Click to update #current-image
            3. Auto-adjust .artwork-info div min-height to match height of left panel (.images-container)
         */

        /**
                1. Modal
         */

        // Get the modal
        var modal = document.getElementById('modal');

        // Get the image and insert it inside the modal - use its "alt" text as a caption
        var img = document.getElementById('current-image');
        var modalImg = document.getElementById("modal-image");
        $(img).on('click', function(){
            modal.style.display = "block";
            modalImg.src = this.src;
        });

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        $(span).on('click', function() {
            modal.style.display = "none";
        });


        /**
                2. Click to update #current-image
         */

        var current_img = '#current-image',
            artwork_thumbnail = '.artwork-thumbnail';

        // detecting click
        $(document).on('click', artwork_thumbnail, function(){
            //get the image from data- attribute
            var ori_url = $(this).data('original-image');
            //set it to #current-image src
            $(current_img).attr('src', ori_url);
        });


        /**
                3. Auto-adjust .artwork-info div min-height to match height of left panel (.images-container)
         */
        var images_div = '.images-container',
            artwork_info_div = '.artwork-info';
        $(window).on('resize', function(){
            $(artwork_info_div).css('min-height', $(images_div).css('height'));
        });

});