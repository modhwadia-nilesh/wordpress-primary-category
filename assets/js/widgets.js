jQuery( function ( $ ) {
    $(document).ready(function () {
        /**
         *  Fetch taxonomies based on selected Post Type
         */
        $(document).on('change', '.widget-posttypes', function() {
            var current_post_type = this.value;
            var __this = $(this);
            $.ajax({
                url : WPC.ajax_url,
                type : 'post',
                data :{
                    'action': 'fetch_taxonomies',
                    'post_type': current_post_type
                },
                success : function( response ) {
                    var $elem = (__this.closest('.widget-content').find('.widget-taxonomies'));
                    $elem.empty();
                    $elem.append(response);
                    $elem.trigger('change');
                }
            });
        });
        /**
         *  Fetch Terms based on selected taxonomy
         */
        $(document).on('change', '.widget-taxonomies',  function() {
            var current_taxonomy = this.value;
            var __this = $(this);
            $.ajax({
                url : WPC.ajax_url,
                type : 'post',
                data :{
                    'action': 'fetch_taxonomies_ids',
                    'taxonomy': current_taxonomy
                },
                success : function( response ) {
                    var $elem = (__this.closest('.widget-content').find('.widget-taxonomies-ids'));
                    $elem.empty();
                    $elem.append(response);
                    $elem.trigger('change');
                }
            });
        });
    });
});
