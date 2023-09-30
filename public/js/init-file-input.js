function callFileInput(inputId, fileCount, fileSize, ext){
	$(inputId).fileinput({
    	showUpload: false,
    	showRemove: false,
    	showClose: false,
    	maxFileCount: fileCount,
    	maxFileSize: fileSize,
    	browseLabel: "Browse",
    	msgPlaceholder: "Choose {files}",
    	dropZoneTitle: "Drag & drop your file here.",
    	allowedFileExtensions: ext,
    	browseClass: "btn btn-success custom_btn_primary fileinput-btns",
	    theme: "fa",
	    uploadUrl: "/",
	    layoutTemplates: {
	    	main1: "{preview}\n" +
            "<div class=\'input-group {class}\'>\n" +
            "   <div class=\'input-group-btn\ input-group-prepend'>\n" +
            "       {browse}\n" +
            "       {upload}\n" +
            "       {remove}\n" +
            "   </div>\n" +
            "   {caption}\n" +
            "</div>",
	    },
	    fileActionSettings: {
	      showDrag: false,
	      showZoom: false,
	      showUpload: false,
	      showDelete: true,
	    },
    	msgSizeTooLarge: 'File "{name}" (<b>{customSize}</b>) exceeds maximum allowed upload size of <b>{customMaxSize}</b>. Please retry your upload!'
	}).on('fileuploaderror', function(event, data, msg) {
		var size = data.files[0].size,
			maxFileSize = $(this).data().fileinput.maxFileSize,
			formatSize = (s) => {
				i = Math.floor(Math.log(s) / Math.log(1024));
				sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
				out = (s / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
				return out;
			};
		msg = msg.replace('{customSize}', formatSize(size));
		msg = msg.replace('{customMaxSize}', formatSize(maxFileSize * 1024 /* Convert KB to Bytes */));
		$('li[data-file-id="'+data.id+'"]').html(msg);
	});
}