# 此项目是 B2C 商城 的个人练习项目


### composer 组件
- [laravel-admin](https://github.com/z-song/laravel-admin)


### 教程笔记
#### 3.7.7 配置 Policy : 检察权限

#### 4 admin后台使用 laravel-admin package

#### 5.1 商品 SKU 的概念。

SKU = Stock Keeping Unit（库存量单位），也可以称为『单品』。对一种商品而言，当其品牌、型号、配置、等级、花色、包装容量、单位、生产日期、保质期、用途、价格、产地等属性中任一属性与其他商品存在不同时，可称为一个单品。

#### 5.2 电商项目中与钱相关的有小数点的字段一律使用 __decimal__ 类型，而不是 float 和 double，后面两种类型在做小数运算时有可能出现精度丢失的问题，这在电商系统里是绝对不允许出现的。

#### 10.3.1 备份数据表
````
$ mysqldump -t laravel-shop admin_menu admin_permissions admin_role_menu admin_role_permissions admin_role_users admin_roles admin_user_permissions admin_users > database/admin.sql
````  
##### 命令解析：

-t 选项代表不导出数据表结构，这些表的结构我们会通过 Laravel 的 migration 迁移文件来创建；
laravel-shop 代表我们要导出的数据库名称，后面则是要导出的表列表；
> database/admin.sql 把导出的内容保存到 database/admin.sql 文件中。
在 Homestead 环境中我们执行 Mysql 相关的命令都不需要账号密码，因为 Homestead 都已经帮我们配置好了。在线上执行 Mysql 命令时则需要在命令行里通过 -u 和 -p 参数指明账号密码，如： mysqldump -uroot -p123456 laravel-shop > database/admin.sql  

新数据库 执行过程  
````
$ php artisan migrate:fresh
$ mysql laravel-shop < database/admin.sql
````  