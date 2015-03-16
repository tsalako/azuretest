var convertMap = {1:1,
				  11:2,
				  21:3,
				  31:4,
				  41:5,
				  51:6,
				  61:7,
				  71:8,
				  81:9,
				  91:10,
				  101:11,
				  111:12,
				  121:13,
				  131:14,
				  141:15,
				  151:16,
				  161:17,
				  171:18,
				  181:19,
				  191:20};

/*
Allow for easily parsing url parameters (forum.html, thread.html)
*/
$.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results==null){
       return null;
    }
    else{
       return results[1] || 0;
    }
}

function sendRequest (urlInput, callback, functionName, params) {
	$.ajax({
		url: urlInput,
		type: 'post',
		data: {'function': functionName, 'params': params},
		success: function(data, status) {
			callback({'data': data, 'status' : status});
		},
		error: function(xhr, desc, err) {
			errorCallback({'xhr': xhr, 'status': desc, 'error': err});
		}
	});
}

function logoutUser() {
	sendRequest('../php/UserService.php',
                        logoutCallback,
                        'logoutUser');
	
}

var logoutCallback = function(response){
	//window.location.href = "../login.html";
	console.log(response);
}

/**
Functions to keep the database data as hidden as possible, since our database
does not increment by one for auto increment, we should conver/revert in order to
ensure the user does not notice this.
*/

/*
When data come from the database to the front end it is converted.
So an example of conversion would be from 41 -> 5.
*/
function convertGroupNo(groupNo){
	return convertMap[groupNo];
}

/*
When data is being sent to the database it is need to be revereted.
So an example of reversion would be from 5 -> 41.
*/
function revertGroupNo(convertedGroupNo){
	for (var key in convertMap) {
	   var value = convertMap[key];
	   if(value == convertedGroupNo){
	   	return key;
	   }
	}
	return null;
}

var errorCallback = function (response) {
	console.log(response);
	alert("An error had occurred. Please reload the page. \n\nMessage: " + response.xhr.responseText);
}

var basicCallback = function (responce) {
	console.log(responce);
}