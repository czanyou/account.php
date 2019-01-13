# 群组成员开放访问接口

编写: 成真

## 目录

[TOC]

## m/add

添加一个新的成员

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| group_id      | int       | 群组的 ID
| id            | int       | 成员的 ID, 如果这个成员已经存在则修改为正式成员.
| user_name     | string    | 要添加的成员的名称
| user_email    | string    | 要添加的成员的邮箱地址
| user_mobile   | string    | 要添加的成员的手机号码

返回数据:

```javascript
{
    "ret": 0, 
    "data": {
        "group_id": 100,
        "user_id": 100,
        "role": 0
    }
}
```

## m/del

删除指定的成员

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| group_id      | int       | 要删除的成员所属群组的 ID, 必须和 user_id 同时提供
| user_id       | int       | 要删除的成员所属用户的 ID
| id            | int       | 要删除的成员的 ID, 不能和 group_id/user_id 同时提供

返回数据:

```javascript
{
    "ret": 0 
}
```

## m/edit

修改指定的成员的信息

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| group_id      | int       | 要修改的成员所属群组的 ID
| id            | int       | 要修改的成员的 ID
| user_name     | string    | 要修改的成员的名称

返回数据:

```javascript
{
    "ret": 0,
    "data": {
        "group_id": 100,
        "user_id": 100,
        "role": 0
    }
}
```


## m/list

修改指定群组的所有成员的信息

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| group_id      | int       | 群组的 ID

返回数据:

```javascript
{
    "ret": 0,
    "total": 1,
    "data": 
    [
        {
            "group_id": 100,
            "user_id": 100,
            "user_name": "100",
            "user_email": "100",
            "user_mobile": "100",
            "role": 0
        }
    ]
}
```

