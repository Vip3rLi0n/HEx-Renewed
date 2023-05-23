if ($('body').hasClass('upload')) {
    $('#link').on('click', function() {
        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: {func: 'gettext', id: 'loadsoft'},
            success: function(data) {
                langInfo = $.parseJSON(data.msg);
                $('#toBeHidden').children().replaceWith(langInfo[0].loading);
                jQuery.ajax({
                    type: "POST",
                    url: 'ajaxupload.php',
                    dataType: "json",
                    data: {func: 'loadSoftware'},
                    success: function(data) {
                        if (data.status == 'OK') {
                            $('#toBeHidden').hide();
                            function format(item) {
                                return item.tag;
                            }

                            $.getScript("js/select2.js", function() {
                                $('#uploadSelect').select2({
                                    placeholder: langInfo[0].placeholder,
                                    data: $.parseJSON(data.msg),
                                    escapeMarkup: function(m) {
                                        return m;
                                    } //display html inside select2
                                });
                            });
                            $('#uploadForm').html('<br/><input type="submit" value="Upload" class="btn btn-primary">');
                            $('#uploadSelect').on("change", function() {
                                $('#upload-id').prop('value', $(this).val());
                            });
                        } else {
                            $('#toBeHidden').replaceWith('<span class="red">Ops, an error happened! Please, try again in a few minutes. </span>');
                        }
                    }
                });
            }
        });
    });
}