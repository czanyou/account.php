local request 	= require('http/request')
local json 		= require('json')
--console.log(request)

local clientId = 'test-client'
local userInfo = {}

function test_is_login()
	local url = "http://localhost/aaa/?api=u/is_login"
	url = url .. "&openid=" .. userInfo.id
	url = url .. "&openkey=" .. userInfo.openkey
	url = url .. "&client_id=" .. clientId

	console.log(url)
	request.get(url, options, function(err, response, result)
		console.log(err, result)
	end)

end

function test_get_info()
	local url = "http://localhost/aaa/?api=u/get_info"
	url = url .. "&openid=" .. userInfo.id
	url = url .. "&openkey=" .. userInfo.openkey
	url = url .. "&client_id=" .. clientId

	console.log(url)
	request.get(url, options, function(err, response, result)
		console.log(err, result)
	end)

end

function test_login()
	local url = "http://localhost/aaa/?api=u/login"
	request.get(url, options, function(err, response, result)
		console.log(err, result)
	end)

	url = "http://localhost/aaa/?api=u/login&username=cz"
	request.get(url, options, function(err, response, result)
		console.log(err, result)
	end)

	url = "http://localhost/aaa/?api=u/login&username=cz&password=xyyily&client_id=" .. clientId
	request.get(url, options, function(err, response, result)
		console.log(err, result)

		userInfo = json.parse(result) or {}
		userInfo = userInfo.data or {}

		console.log(userInfo)

		test_is_login()
		test_get_info()
	end)	

end


test_login()
