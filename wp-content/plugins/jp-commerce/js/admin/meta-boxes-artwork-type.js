/**
 * Created by jjpro on 11/23/15.
 */

jQuery( function( $ ) {
    // #selectables-wrap, nodes, end_nodes
    // base level selectable
    if (ui_selected instanceof Array && ui_selected.length > 0) {
        var tmp = nodes.slice();
        for (var i = 0, len = ui_selected.length; i<len; i++){
            $("#selectables-wrap").append(make_selectable(ul_from_nodes(tmp)));

            var current_selected_node = $.grep(tmp, function (e, index){
                return e.term_id == ui_selected[i];
            })[0];
            tmp = current_selected_node.children;
        }
    } else {
        $("#selectables-wrap").append(make_selectable(ul_from_nodes(nodes)));
    }


    /**
     * Makes the element selectable widget
     *
     * @param {jQuery} object
     *
     * @returns {jQuery}
     */
    function make_selectable( object ) {
        object.selectable({
            selecting: function(event, ui){
                disable_multiple_selection(event, ui);
            },
            selected: function(event, ui) {
                // create new selectable
                var selected_node = $(ui.selected).data("node-obj");
                if (has_children(selected_node)) {
                    var ul = ul_from_nodes(find_children(selected_node));
                    $("#selectables-wrap").append(make_selectable(ul));
                }

                $("#artwork_type").val(selected_node.term_id);
            },
            unselected: function(event, ui) {
                // clear ui_selected array
                ui_selected = [];
                // remove sibling boxes
                var siblings = $(event.target).nextAll();
                siblings.remove();

            }
        });
        return object;
    }

    /**
     * @internal
     * Disables multi-selection of calling selectable widget.
     *
     * @param event
     * @param ui
     */
    function disable_multiple_selection(event, ui) {
        if ( $(".ui-selected, .ui-selecting", event.target).length > 1 ) {
            $(ui.selecting).removeClass("ui-selecting");
        }
    }

    /**
     * Checks if this node has children.
     *
     * @param {Object} node
     * @returns {boolean}
     */
    function has_children (node) {
        var end_nodes_term_ids = [];
        for ( var i= 0, len = end_nodes.length; i<len; i++){
            end_nodes_term_ids.push(end_nodes[i].term_id);
        }
        return !( $.inArray( node.term_id, end_nodes_term_ids ) >= 0 );
    }

    /**
     * Gets children nodes.
     *
     * @param {Object} node
     * @returns {Array}
     */
    function find_children (node) {
        if ( has_children(node) ) {
            return node.children;
        } else {
            return [];
        }
    }


    /**
     * Creates new selectable list with given elemnts.
     *
     * @param nodes
     * @returns {jQuery}
     */
    function ul_from_nodes( nodes ) {
        var new_selectable = $('<ul/>', {"class":"selectable"});
        for (var i= 0, arrayLen = nodes.length; i < arrayLen; i++) {
            var node = nodes[i];
            var li = $("<li>").html(node.name).data('node-obj', node);
            for (j in ui_selected) {
                if (node.term_id == ui_selected[j]){
                    li.addClass("ui-selected");
                }
            }
            if ( has_children(node) )
                li.addClass("has-children");
            new_selectable.append(li);
        }
        return new_selectable;
    }

});