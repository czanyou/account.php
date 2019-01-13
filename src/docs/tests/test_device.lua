local request 	= require('http/request')
local json 		= require('json')
--console.log(request)

local clientId = 'test-client'
local userInfo = {}

function test_get_info()
	local url = "http://localhost/aaa/?api=g/get_info"
	url = url .. "&openid=" .. userInfo.id
	url = url .. "&openkey=" .. userInfo.openkey
	url = url .. "&client_id=" .. clientId
	url = url .. "&id=" .. userInfo.group_id


	console.log(url)
	request.get(url, options, function(err, response, result)
		console.log(err, json.parse(result))
	end)
end

function test_list()
	local url = "http://localhost/aaa/?api=gd/list"
	url = url .. "&openid=" .. userInfo.id
	url = url .. "&openkey=" .. userInfo.openkey
	url = url .. "&client_id=" .. clientId
	url = url .. "&group_id=" .. userInfo.group_id

	console.log(url)
	request.get(url, options, function(err, response, result)
		console.log(err, json.parse(result))
	end)
end

function test_login()

	local url = "http://localhost/aaa/?api=u/login&username=cz&password=xyyily&client_id=" .. clientId
	request.get(url, options, function(err, response, result)
		--console.log(err, result)

		userInfo = json.parse(result) or {}
		userInfo = userInfo.data or {}

		console.log(userInfo)

		test_list()
		--test_list_public()

		--test_get_info()
	end)	

end

test_login()

