/**
 * Created by jjpro on 1/13/16.
 */
jQuery(function($){
    $materials_control = $("#artwork-materials-select-box");
    $materials_control_alert = $("#artwork-materials-select-box-alert");
    $materials_control.chosen({
        max_selected_options: 5,
        search_contains: true,
        single_backstroke_delete: false,
        display_disabled_options: false,
        display_selected_options: false
    });

    $materials_control.on('chosen:maxselected', function(event, params, chosen){
        $materials_control_alert.css('visibility', 'visible');
    });
});