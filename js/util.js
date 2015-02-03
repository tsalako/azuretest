function sendRequest (urlInput, callback, functionName, params) {
	$.ajax({
		url: urlInput,
		type: 'post',
		data: {'function': functionName, 'params': params},
		success: function(data, status) {
			callback({'data': data, 'status' : status});
		},
		error: function(xhr, desc, err) {
			callback({'xhr': xhr, 'status': desc, 'error': err});
		}
	});
}