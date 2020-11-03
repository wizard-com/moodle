require(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function($, ajax, templates, notification) {
    return {
        refresh: function() {
            $('#add_form').submit(function() {

                var timestamp = Math.floor(Date.now() / 1000);

                var note = $('#new-note').val();

                var classes = $("#hidden-data").attr("class").split();

                var uid = classes[0].substr(5);
                var mid = classes[4].substr(5);

                var intuid = parseInt(uid);
                var intmid = parseInt(mid);

                var promises = ajax.call([
                    // eslint-disable-next-line max-len
                    {methodname: 'add_notes', args: {note: {modid: intmid, userid: intuid, timecreated: timestamp, notecontent: note}}}
                ]);

                promises[0].done(function(response) {
                    templates.render('note-input', response, 'theme_ycampus').done(function(html, js) {
                        $("#cards").replaceWith(html);
                        templates.runTemplateJS(js);
                    }).fail(notification.exception);
                }).fail(notification.exception);

            });
        }
    };
});
