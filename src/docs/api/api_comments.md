# 评论接口

编写: 成真

## 目录

[TOC]

## 概述 

提供用户评论统一 API 接口

## 公共信息

### 评论类型

- 0: 普通评论
- 1: 带图片的评论
- 2: 带短视频的评论

## t/add

发表一条新的普通评论

    onCommentAdd($request)

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 要评论的设备的 uid
| id            | int       | 要回复的评论的 ID 
| content*      | string    | 要评论的文本内容

返回数据:

```javascript
{
    "ret": 0  
}
```

## t/add_pic

发表一条新的带图片的评论

    onCommentAddPic($request)

- 请求方法必须为 POST

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 要评论的设备的 uid
| content*      | string    | 要评论的文本内容
| pic*          | file      | 附带的图片文件


返回数据:

```javascript
{
    "ret": 0    
}
```

## t/add_video

发表一条新的带短视频的评论

    onCommentAddVideo($request)

- 请求方法必须为 POST

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 要评论的设备的 uid
| content*      | string    | 要评论的文本内容
| video*        | file      | 附带的短视频文件

返回数据:

```javascript
{
    "ret": 0
}
```

## t/del

删除指定的评论

    onCommentRemove($request)

- 需要登录

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| id            | int       | 要删除的评论的 ID 

返回数据:

```javascript
{
    "ret": 0
}
```

## t/like

对指定的评论表示赞

    onCommentLike($request)

- 需要登录

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| id            | int       | 要回复的评论的 ID 

返回数据:

```javascript
{
    "ret": 0,
    "data": {
        "id": 1,
        "likes": 0
    }
}
```

| 名称          | 类型      | 说明
| ---           | ---       | ---
| id            | int       | 评论的 ID 
| likes         | int       | 评论的点赞数
| liked         | int       | 是否赞了这条评论

## t/list

查询评论

    onCommentList($request)

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid           | string    | 要查询的评论的设备的 uid
| id            | int       | 要查询的父评论的 ID 
| count         | int       | 单页返回的记录条数，默认为 50。
| start         | int       | 开始返回的索引位置
| type          | int       | 要查询的评论类型
| user_id       | int       | 要查询的评论用户的 ID

返回数据:

```javascript
{
    "total": 100,
    "start": 0,
    "data": [
        {
            "cid": 0,
            "content": "test",
            "id": 1,
            "likes": 0,
            "thumbnail": "http://....",
            "time": 100000,
            "type": 1,
            "uid": "test1234",
            "user_id": 100,
            "user": {

            }
        }
    ]
}
```

| 名称          | 类型      | 说明
| ---           | ---       | ---
| cid           | int       | 评论来源评论的 ID, 当这条评论属另一条评论的评论时返回
| comment_count | int       | 评论的回复数
| content       | string    | 评论的内容
| id            | int       | 评论的 ID 
| liked         | int       | 是否赞了这条评论
| likes         | int       | 评论的点赞数
| thumbnail     | string    | 评论的缩略图片地址, 没有时不返回
| time          | int       | 评论的时间
| type          | int       | 评论的类型
| uid           | string    | 评论的设备的 uid
| user          | object    | 评论的用户的信息
| user_id       | int       | 评论的用户的 ID

## t/unlike

取消对指定的评论的赞

    onCommentUnlike($request)

- 需要登录

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| id            | int       | 要回复的评论的 ID 

返回数据:

```javascript
{
    "ret": 0,
    "data": {
        "id": 1,
        "likes": 0
    }
}
```

| 名称          | 类型      | 说明
| ---           | ---       | ---
| id            | int       | 评论的 ID 
| likes         | int       | 评论的点赞数
| liked         | int       | 是否赞了这条评论