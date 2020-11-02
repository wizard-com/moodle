require(['jquery', 'core/ajax', 'core/templates', 'core/notifications'], function($, ajax, templates, notification) {
    return {
        refresh: function() {
            $('#add_form').submit(function() {


                var promises = ajax.call([
                    {methodname: 'get_notes', args: {component: 'mod_wiki', stringid: 'pluginname'}}
                ]);

                promises[0].done(function(response) {
                    console.log('mod_wiki/pluginname is' + response);
                }).fail(function(ex) {
                    // Do something with the exception
                });

            });
        }
    };
});
