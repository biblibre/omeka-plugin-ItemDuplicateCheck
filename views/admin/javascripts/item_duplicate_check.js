(function($) {
    $(document).ready(function() {
        $('form#item-form').submit(function() {
            var form = $(this);

            var ignore = form.find('[name="item_duplicate_check_ignore"]').is(':checked');

            $('#item_duplicate_check').remove();

            // Don't check for duplicates if checkbox is checked
            if (ignore) {
                return true;
            }

            var data = new Object;
            $(this).find('input, textarea, select').each(function() {
                data[$(this).attr('name')] = $(this).val();
            });
            var matches = window.location.pathname.match(/\/items\/edit\/(\d+)/);
            if (matches) {
                data.id = matches[1];
            }

            var noDuplicates = false;
            $.ajax({
                method: 'POST',
                async: false,
                url: Omeka.WEB_DIR + '/item-duplicate-check/check/check',
                data: data
            })
            .done(function(data) {
                if (data.length == 0) {
                    noDuplicates = true;
                    return;
                }

                $(data).insertBefore('#item-metadata');
                window.scroll(0, 0);
            });

            return noDuplicates;
        });

        $(document.body).on('click', '#item_duplicate_check_toggle', function(){
            var toggle_button = $(this);
            var duplicates_list = $('#item_duplicate_check_duplicates');
            if (!toggle_button.hasClass('item_duplicate_check-collapsible') && !toggle_button.hasClass('item_duplicate_check-collapsed')) return;
            // open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
            duplicates_list.slideToggle(300, function() {
                // execute this after slideToggle is done
                // change icon of toggle_button based on visibility of duplicates_list
                toggle_button.toggleClass('item_duplicate_check-collapsible item_duplicate_check-collapsed');
            });
        });
    });
})(jQuery);
