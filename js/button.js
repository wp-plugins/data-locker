function tinyplugin() {
    return "[datalocker-plugin]";
}

(function() {
    tinymce.create('tinymce.plugins.datalockerplugin', {
        init : function(ed, url){
            ed.addButton('datalockerplugin', {
                title : 'Lock your data with Data Locker.',
                onclick : function() {
					ilc_sel_content = tinyMCE.activeEditor.selection.getContent();
					tinyMCE.activeEditor.selection.setContent('[data-locker]' + ilc_sel_content + '[/data-locker]')
                },
                image: url + "/../images/locker.png"
            });
        }
	});
    tinymce.PluginManager.add('datalockerplugin', tinymce.plugins.datalockerplugin);
    
})();