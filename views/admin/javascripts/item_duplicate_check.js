(function($) {
  $(document).ready(function() {
    $('form#item-form').submit(function() {
      var form = $(this);

      // Don't check for duplicates if checkbox is checked
      if (form.find('[name="item_duplicate_check_ignore"]').is(':checked')) {
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

        $('#item_duplicate_check').remove();
        $(data).insertBefore('#item-metadata');
        window.scroll(0, 0);
      });

      return noDuplicates;
    })
  });
})(jQuery);
