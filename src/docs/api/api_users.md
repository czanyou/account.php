# 设备接口

编写: 成真

## 目录

[TOC]

## 概述

提供设备统一访问接口

## u/edit

修改用户基本资料

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | string    | 用户唯一 ID
| openkey*      | string    | 登录密钥
| client_id*    | string    | 客户端唯一 ID
| about_me      | string    | 用户的自我介绍

## u/edit_password

修改用户密码

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | string    | 用户唯一 ID
| openkey*      | string    | 登录密钥
| client_id*    | string    | 客户端唯一 ID
| old_password  | string    | 旧密码
| new_password* | string    | 新密码
| code          | string    | 验证码(当未提供 old_password 时有效)

## u/forgot

找回密码

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| email*        | string    | 要找回密码的账户注册时所用的邮箱地址


## u/get_info

查询用户基本资料

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | string    | 用户唯一 ID
| openkey*      | string    | 登录密钥
| client_id*    | string    | 客户端唯一 ID


## u/is_login

指出当前登录会话信息是否有效

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openid*       | string    | 用户唯一 ID
| openkey*      | string    | 登录密钥
| client_id*    | string    | 客户端唯一 ID


## u/login

登录

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| username*     | string    | 用户名
| password*     | string    | 密码


## u/logout

登出

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| openkey*      | string    | 登录密钥
| client_id*    | string    | 客户端唯一 ID

## u/register

注册新的账号

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| name*         | string    | 用户名
| password*     | string    | 密码
| email         | string    | 邮箱地址
| mobile        | string    | 手机号码
| code          | string    | 使用手机号码注册时收到的短信验证码

## u/validator

发送验证码

请求参数

| 名称          | 类型      | 说明
| ---           | ---       | ---
| mobile*       | string    | 用来接收验证码的手机号码

