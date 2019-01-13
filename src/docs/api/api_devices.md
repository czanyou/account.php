# 设备接口

编写: 成真

## 目录

[TOC]

## 概述

提供设备统一访问接口

## 公共信息

## p/add

添加私有设备, 私有设备只有自己可以查看

    onPrivateDeviceAdd

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 设备的 uid
| uri*          | string    | 设备的访问地址 
| name*         | string    | 设备的名称

返回参数

```javascript
{
    "ret":0,
    "data":{
        'name': "test",
        'owner_id': 100,
        'uid': "test1234",
        'uri': "pppp://1234",
        'updated': 1449808323
    }
}
```

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid           | string    | 设备的 uid
| uri           | string    | 设备的访问地址 
| name          | string    | 设备的名称
| owner_id      | int       | 用户的 id
| updated       | int       | 最后更新时间

## p/add_public

添加公共设备, 公共设备所有人都可以查看

    onPublicDeviceAdd

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 设备的 uid
| address       | string    | 设备所在的地址
| category      | int       | 设备所属的分类
| city          | string    | 设备所在的城市
| description   | string    | 设备的描述信息
| name          | string    | 设备的名称
| phone         | string    | 设备相关的电话号码
| uri           | string    | 设备的访问地址 
| webpage       | string    | 设备相关的网址

返回参数

```javascript
{
    "ret":0,
    "data":{
        'name': "test",
        'owner_id': 100,
        'uid': "test1234",
        'uri': "pppp://1234",
        'updated': 1449808323
    }
}
```

| 名称          | 类型      | 说明
| ---           | ---       | ---
| address       | string    | 设备所在的地址
| category      | int       | 设备所属的分类
| city          | string    | 设备所在的城市
| description   | string    | 设备的描述信息
| name          | string    | 设备的名称
| owner_id      | int       | 用户的 id
| phone         | string    | 设备相关的电话号码
| uid           | string    | 设备的 uid
| updated       | int       | 最后更新时间
| uri           | string    | 设备的访问地址 
| webpage       | string    | 设备相关的网址

## p/categories

查询公共设备分类信息

    onPublicDeviceListCategories

请求参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| 无            | 无        | 无

返回参数

```javascript
{
    "ret":0,
    "data":[
        {"id":8,"name":"风景","image":"categoryScenery"},
        {"id":7,"name":"生活","image":"categoryLife"},
        {"id":2,"name":"宠物天地","image":"categoryPet"},
        {"id":9,"name":"商品展示","image":"categoryShop"},
        {"id":3,"name":"房产.办公","image":"categoryRealty"},
        {"id":1,"name":"住宅小区","image":"categoryHome"},
        {"id":4,"name":"教育培训","image":"categoryEdu"},
        {"id":6,"name":"娱乐","image":"categoryRecreation"},
        {"id":0,"name":"其他","image":"categoryCam"}
    ]
}
```

返回参数:

| 名称          | 类型      | 说明
| ---           | ---       | ---
| id            | int       | 这个分类的唯一 ID
| name          | string    | 这个分类的名称
| image         | URL       | 这个分类的图片地址

## p/del

删除指定的设备

    onPrivateDeviceRemove

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 设备的 uid
| id            | int       | 要删除的设备的 id

返回数据:

```javascript
{
    "ret": 0  
}
```

## p/del_public

删除/取消指定的公共设备

    onPublicDeviceRemove

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 要删除的设备的 uid

返回数据:

```javascript
{
    "ret": 0  
}
```

## p/edit

修改指定的设备信息

    onPrivateDeviceUpdate

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 要修改的设备的 uid
| uri           | string    | 设备的 URI
| name          | string    | 设备的名称

返回参数

```javascript
{
    "ret":0,
    "data":{
        'name': "test",
        'owner_id': 100,
        'uid': "test1234",
        'uri': "pppp://1234",
        'updated': 1449808323
    }
}
```

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid           | string    | 设备的 uid
| uri           | string    | 设备的访问地址 
| name          | string    | 设备的名称
| owner_id      | int       | 用户的 id
| updated       | int       | 最后更新时间

## p/edit_public

修改指定的公共设备信息

    onPublicDeviceUpdate

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 设备的 uid
| address       | string    | 设备所在的地址
| category      | int       | 设备所属的分类
| city          | string    | 设备所在的城市
| description   | string    | 设备的描述信息
| name          | string    | 设备的名称
| phone         | string    | 设备相关的电话号码
| uri           | string    | 设备的访问地址 
| webpage       | string    | 设备相关的网址


返回参数

```javascript
{
    "ret":0,
    "data":{
        'name': "test",
        'owner_id': 100,
        'uid': "test1234",
        'uri': "pppp://1234",
        'updated': 1449808323
    }
}
```

可参考 p/add_public

## p/get_info

查询指定的设备的详细信息

    onPublicDeviceGet

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 设备的 uid

返回参数

```javascript
{
    "ret":0,
    "data":{
        'name': "test",
        'owner_id': 100,
        'uid': "test1234",
        'uri': "pppp://1234",
        'updated': 1449808323
    }
}
```

| 名称          | 类型      | 说明
| ---           | ---       | ---
| address       | string    | 设备所在的地址
| category      | int       | 设备所属的分类
| city          | string    | 设备所在的城市
| description   | string    | 设备的描述信息
| name          | string    | 设备的名称
| owner_id      | int       | 用户的 id
| phone         | string    | 设备相关的电话号码
| uid           | string    | 设备的 uid
| updated       | int       | 最后更新时间
| uri           | string    | 设备的访问地址 
| webpage       | string    | 设备相关的网址

## p/like

收藏设备

    onPublicDeviceLike

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 设备的 uid

返回数据:

```javascript
{
    "ret": 0,
    "data": {
        "id": 100,
        "uid": "test1234",
        "likes": 1
    }
}
```

## p/list_favorites

查询当前用户收藏的公共设备

    onPublicDeviceListFavorites

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 设备的 uid

## p/list

查询设备

    onPrivateDeviceList

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---

返回参数

```javascript
{
    "ret":0,
    "tatol": 1,
    "data": 
    [
        {
            'name': "test",
            'owner_id': 100,
            'uid': "test1234",
            'uri': "pppp://1234",
            'updated': 1449808323
        }
    ]
}
```

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid           | string    | 设备的 uid
| uri           | string    | 设备的访问地址 
| name          | string    | 设备的名称
| owner_id      | int       | 用户的 id
| updated       | int       | 最后更新时间

## p/list_public

查询公共设备

    onPublicDeviceList

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| category      | int       | 设备所属的分类

返回参数

```javascript
{
    "ret":0,
    "tatol": 1,
    "data": 
    [
        {
            'name': "test",
            'owner_id': 100,
            'uid': "test1234",
            'uri': "pppp://1234",
            'updated': 1449808323
        }
    ]
}
```

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid           | string    | 设备的 uid
| uri           | string    | 设备的访问地址 
| name          | string    | 设备的名称
| owner_id      | int       | 用户的 id
| updated       | int       | 最后更新时间

## p/unlike

取消收藏设备

    onPublicDeviceUnlike

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| uid*          | string    | 设备的 uid

返回数据:

```javascript
{
    "ret": 0,
    "data": {
        "id": 100,
        "uid": "test1234",
        "likes": 0
    }
}
```

## tag/add

添加标签

    onDeviceTagAdd

## tag/add_device

为指定设备添加标签

    onDeviceTagAddDevice

## tag/del

删除标签

    onDeviceTagRemove

## tag/del_device

取消指定设备的标签

    onDeviceTagRemoveDevice

## tag/edit

修改标签

    onDeviceTagUpdate

## tag/get_info

查询标签

    onDeviceTagGetInfo

## tag/list

查询标签下的设备

    onDeviceTagList



