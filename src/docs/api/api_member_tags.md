# 群组成员开放访问接口

编写: 成真

## 目录

[TOC]

## mtag/add

添加一个新的成员分组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| group_id*     | int       | 群组的 ID
| name*         | string    | 要添加的成员分组的名称
| openid*       | int       | 当前用户的 ID

返回数据:

```javascript
{
    "ret": 0, 
    "data": {
        ...
    }
}
```

可能返回的错误码:

- E_USER_NOT_LOGIN 没有提供有效的登录信息
- E_MISS_REQUIRED_PARAM 没有 group_id,name 等必须的参数
- E_NOT_EXISTS 指定 group_id 的群组并不存在

## mtag/add_member

添加一个成员到指定的成员分组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| tag_id*       | int       | 成员分组的 ID
| member_id*    | int       | 要添加的成员的 ID
| openid*       | int       | 当前用户的 ID

返回数据:

```javascript
{
    "ret": 0, 
    "data": {
        ...
    }
}
```

可能返回的错误码:

- E_USER_NOT_LOGIN 没有提供有效的登录信息
- E_MISS_REQUIRED_PARAM 没有 tag_id,member_id 等必须的参数
- E_NOT_EXISTS 指定 group_id 的群组并不存在

## mtag/del

删除指定的成员分组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| tag_id*       | int       | 成员分组的 ID
| openid*       | int       | 当前用户的 ID

返回数据:

```javascript
{
    "ret": 0, 
    "data": {
        ...
    }
}
```

可能返回的错误码:

- E_USER_NOT_LOGIN 没有提供有效的登录信息
- E_MISS_REQUIRED_PARAM 没有 tag_id 等必须的参数

## mtag/del_member

从成员分组中删除指定的成员

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| tag_id*       | int       | 成员分组的 ID
| member_id*    | int       | 要删除的成员的 ID
| openid*       | int       | 当前用户的 ID

返回数据:

```javascript
{
    "ret": 0, 
    "data": {
        ...
    }
}
```

可能返回的错误码:

- E_USER_NOT_LOGIN 没有提供有效的登录信息
- E_MISS_REQUIRED_PARAM 没有 tag_id,member_id 等必须的参数
- E_NOT_EXISTS 指定 group_id 的群组并不存在


## mtag/edit

修改指定的成员分组的信息

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| tag_id*       | int       | 成员分组的 ID
| openid*       | int       | 当前用户的 ID

返回数据:

```javascript
{
    "ret": 0, 
    "data": {
        ...
    }
}
```

可能返回的错误码:

- E_USER_NOT_LOGIN 没有提供有效的登录信息
- E_MISS_REQUIRED_PARAM 没有 tag_id 等必须的参数
- E_NOT_EXISTS 指定 tag_id 的成员分组并不存在

## mtag/get_info

返回指定的成员分组的信息

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| tag_id*       | int       | 成员分组的 ID
| openid*       | int       | 当前用户的 ID

返回数据:

```javascript
{
    "ret": 0, 
    "data": {
        ...
    }
}
```

可能返回的错误码:

- E_USER_NOT_LOGIN 没有提供有效的登录信息
- E_MISS_REQUIRED_PARAM 没有 tag_id 等必须的参数
- E_NOT_EXISTS 指定 tag_id 的成员分组并不存在

## mtag/list

返回所有的成员分组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| group_id*     | int       | 群组的 ID
| openid*       | int       | 当前用户的 ID

返回数据:

```javascript
{
    "ret": 0,
    "total": 1,
    "data": 
    [
        {
            ...
        }
    ]
}
```

- total 总共包含的设备的总数

可能返回的错误码:

- E_USER_NOT_LOGIN 没有提供有效的登录信息
- E_MISS_REQUIRED_PARAM 没有 group_id,openid 等必须的参数
- E_NOT_EXISTS 指定 group_id 的群组并不存在
