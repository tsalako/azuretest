/**
 * Sends a request to the database via an ajax call. 
 * 
 * @param urlInput     php file to send the request to
 * @param callback     function to call with the data received from the database
 * @param functionName name of the function to call in the php file
 * @param params       parameters to send to the function (may not exist)
 */
function sendRequest (urlInput, callback, functionName, params) {
	$.ajax({
		url: urlInput,
		type: 'post',
		data: {'function': functionName, 'params': escapeObject(params, true)},
		success: function(data, status) {
			callback({'data': escapeObject(data, false), 'status' : status});
		},
		error: function(xhr, desc, err) {
			errorCallback({'xhr': xhr, 'status': desc, 'error': err});
		}
	});
	console.log('sent');
}

/**
 * Escapes or unescaped data object passed to and from the database.
 * When data is passed into the database, this function escapes all the strings
 * in the data. When data is retrieved from the database, the function unescapes
 * all the string in the data.
 * 
 * @param data     data to be escaped/unescaped
 * @param isEscape whether the data passed in is to be escaped or unescaped
 * @return	       the escaped/unescaped version of the data that was passed in
 */
function escapeObject (data, isEscape) {
	for (var key in data) {
	   var value = data[key];
	   if(typeof(value) == 'string'){
	   		data[key] = isEscape ? escape(value) : unescape(value);
	   } else if (typeof(value) == 'object') {
	   		data[key] = escapeObject(value, isEscape);
	   }
	}
	return data;
}

/**
 * Parses url parameters (forum.html, thread.html)
 * 
 * @param name the name of the parameter that you want to get from the url
 * @return	   null if that parameter was not found or the paramter from the url
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

/**
 * Logouts the use by sending a request to the backend with specified callback.
 */
function logoutUser() {
	sendRequest('../php/UserService.php',
                        logoutCallback,
                        'logoutUser');
	
}

/**
 * Handles errors that occur on request sent to the backend.
 * 
 * @param response    response received from backend, sent from sendRequest
 * @see   sendRequest failure
 */
var errorCallback = function (response) {
	if (response.xhr.responseText == 'notLoggedIn') {
		window.location.href = "../login.html";
		alert("Please login or register.");
	} else if(response.xhr.responseText == 'deniedAccess') {
		alert("You dont have access to this page.");
		logoutUser();
	}
	else if(response.xhr.responseText != ''){
		console.log(response);
		alert("An error had occurred. Please reload the page. \n\nMessage: " + response.xhr.responseText);
	}
	
}

/**
 * Handles a successful logout by redirected to the logout page.
 * 
 * @param response    response received from backend
 * @see   sendRequest success
 */
var logoutCallback = function(response){
	window.location.href = "../login.html";
}

/**
 * Handles a successful request that does not have a specific callback function
 * 
 * @param response    response received from backend
 * @see   sendRequest success
 */
var basicCallback = function (responce) {
	console.log(responce);
}

/**
 * Converter of groupNo when it comes from the database. Since out database auto increments
 * by 10 instead of 1, the numbers are not between 1-20. This could have been avoided by removing
 * auto increment, but the added abstraction from the database primary key was welcome.
 * 
 * @param groupNo groupNo from backend that needs to be converted
 * @return 	      the groups' number (between 1-20) 	  
 */
function convertGroupNo(groupNo){
	return convertMap[groupNo];
}

/**
 * Reverter of groupNo when it is sent to the database. Since out database auto increments
 * by 10 instead of 1, the numbers are not between 1-20. This could have been avoided by removing
 * auto increment, but the added abstraction from the database primary key was welcome.
 * 
 * @param groupNo groupNo from frontend that needs to be reverted
 * @return 	      the groupNo of the group in the database
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