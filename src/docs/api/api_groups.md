# 开放 API 接口

编写: 成真

## 目录

[TOC]

## g/add

创建一个新的群组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | int       | 当前用户的 ID
| name*         | string    | 群组的名称
| description   | string    | 群组的简介

返回数据:

```javascript
{
    "ret": 0  
}
```

## g/del

删除指定的群组, 只能删除用户自己创建的群组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | int       | 当前用户的 ID
| id*           | int       | 要删除的群组的 ID

返回数据:

```javascript
{
    "ret": 0  
}
```


## g/edit

修改指定群组的信息, 只能修改用户自己创建群组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | int       | 当前用户的 ID
| id*           | int       | 要修改的群组的 ID
| name          | string    | 群组的名称
| description   | string    | 群组的简介

返回数据:

```javascript
{
    "ret": 0  
}
```

## g/exit

退出指定的群组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | int       | 当前用户的 ID
| id*           | int       | 群组的 ID

返回数据:

```javascript
{
    "ret": 0  
}
```

## g/get_info

返回指定的群组的信息

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | int       | 当前用户的 ID
| id*           | int       | 群组的 ID

返回数据:

```javascript
{
    data = {
        created = 1407814785, 
        description = '这是一个用于演示的群组.', 
        device_count = 9, 
        id = 748, 
        is_public = 1, 
        member_count = 38, 
        name = '网观官方演示', 
        owner_id = 745, 
        privacy = 2
  }, ret = 0 }
```


## g/join

加入指定的群组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | int       | 当前用户的 ID
| id*           | int       | 群组的 ID

返回数据:

```javascript
{
    "ret": 0  
}
```

## g/list

查询指定的用户相关的所有群组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | int       | 当前用户的 ID


返回数据:

```javascript
{
    "ret": 0  
}
```


## g/list_public

返回包含所有企业群组的信息的列表

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---


返回数据:

```javascript
{
    "ret": 0  
}
```


## g/set_default

设置默认组

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | int       | 当前用户的 ID
| id*           | int       | 群组的 ID

返回数据:

```javascript
{
    "ret": 0  
}
```
