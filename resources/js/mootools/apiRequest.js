// Usage:
// 
// api_request({
//   action: 'search',
//   payload: {
//     mykey: "myvalue"
//   },
//   onSuccess: function(response){
//     if(response.status){
//       doSomething(response) // Response is an object
//     }
//   }.bind(this)
// });


function apiRequest(params){


  var api_req;

  var payload;

  var decoded_response;

  payload = JSON.encode(params.payload);

  if (typeof(API_TOKEN) !== 'undefined') { 
    params.payload['token'] = API_TOKEN; 
  }

  var api_request_root = '/';

  if (typeof(API_TOKEN) !== 'undefined') { api_request_root = API_ROOT; }

  var request_url = api_request_root + params.action;

  api_req = new Request({
    url: request_url,
    method: 'post',
    onRequest: function(){
      console.log('API:',params.action,payload);
    },
    onSuccess: function(response){
      try {
        decoded_response = JSON.decode(response);
        if(decoded_response.token != null){
          API_TOKEN = decoded_response.token;
          try {
            localStorage.setItem('API_TOKEN',API_TOKEN);
          } catch(err){ /* Do Nothing */ }
        }
      } catch(err){
        console.error('Malformed response from server: \n',params.action,params.payload,response)
        return(false);
      }
      console.log('API REQUEST ',params.action,params.payload,decoded_response);
      if (params.onSuccess != null) params.onSuccess(decoded_response);
    },
    onFailure: function(response){
      if (params.onFailure != null) params.onFailure(response);
    }
  });
  api_req.send('payload='+payload);
};


function sign_in_request(params){

  var sign_in_req;

  var api_request_root = '/';

  if (API_ROOT != null) { api_request_root = API_ROOT; }

  var request_url = api_request_root + 'sign_in';  

  sign_in_req = new Request({
    url: request_url,
    method: 'post',
    onSuccess: function(response){
      try {
        var decoded_response = JSON.decode(response);
        console.log('sign in',decoded_response);
      } catch(err){
        console.error('Malformed response from server: \n',params.action,params.payload,response)
        return false;
      }
      if(decoded_response.status){
        if (params.onSuccess != null) params.onSuccess(decoded_response);
        API_TOKEN = decoded_response.token;
        //Store the token and credentials locally to prevent logging in every refresh
        try {
          localStorage.setItem('API_TOKEN',API_TOKEN);
          localStorage.setItem('email',params.email);            
        } catch(err){
          //Do nothing
        }    
        
        
      } else {
        if (params.onSuccess != null) params.onSuccess(decoded_response);
        response.messages.each(console.warn);
      }
    }
  });
 
  sign_in_req.send('email='+params.email+'&password='+params.password);  

};

