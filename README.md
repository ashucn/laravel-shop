# 此项目是 B2C 商城 的个人练习项目


## composer 组件
- [laravel-admin](https://github.com/z-song/laravel-admin)


## 教程笔记
### 3.7.7 配置 Policy : 检察权限

### 4 admin后台使用 laravel-admin package

### 5.1 商品 SKU 的概念。

SKU = Stock Keeping Unit（库存量单位），也可以称为『单品』。对一种商品而言，当其品牌、型号、配置、等级、花色、包装容量、单位、生产日期、保质期、用途、价格、产地等属性中任一属性与其他商品存在不同时，可称为一个单品。

### 5.2 电商项目中与钱相关的有小数点的字段一律使用 __decimal__ 类型，而不是 float 和 double，  

后面两种类型在做小数运算时有可能出现精度丢失的问题，这在电商系统里是绝对不允许出现的。

### 10.3.1 备份数据表
````
$ mysqldump -t laravel-shop admin_menu admin_permissions admin_role_menu admin_role_permissions admin_role_users admin_roles admin_user_permissions admin_users > database/admin.sql
````  
#### 命令解析：

-t 选项代表不导出数据表结构，这些表的结构我们会通过 Laravel 的 migration 迁移文件来创建；
laravel-shop 代表我们要导出的数据库名称，后面则是要导出的表列表；
> database/admin.sql 把导出的内容保存到 database/admin.sql 文件中。
在 Homestead 环境中我们执行 Mysql 相关的命令都不需要账号密码，因为 Homestead 都已经帮我们配置好了。在线上执行 Mysql 命令时则需要在命令行里通过 -u 和 -p 参数指明账号密码，如： mysqldump -uroot -p123456 laravel-shop > database/admin.sql  

新数据库 执行过程  
````
$ php artisan migrate:fresh
$ mysql laravel-shop < database/admin.sql
````    


### 10.4 系统安全 
#### - 1. 在 PHP 的项目里要避免 SQL 注入需要两个条件：
````
使用 Prepared Statement + 参数绑定
绝对不手动拼接 SQL
````  
在 Laravel 里所有的 SQL 查询都是 Prepared Statement 模式，因此只要我们的代码中没有出现类似下方的代码，就不会存在 SQL 注入的风险：
  ````  
  $product = DB::select("select * from products where id = '".$_GET['id']."'");
  ````
  
#### - 2. XSS 是与 SQL 注入齐名的漏洞，也可以用一句话来描述其核心：将未经过滤的用户输入原样输出到网页中。  
永远不要相信用户输入的任何信息！！！！   

在 Laravel 中要输出文本有两种方式：{{ $str }} 和 {!! $str !!}，  
前者等同于 echo htmlspecialchars($str) 而后者则是 echo $str，   
htmlspecialchars() 函数默认会把 <>&" 这个 4 个字符分别转义成 <、&lgt;、& 和 "，因此我们只要保证我们一直在用 {{ }} 来输出就没有问题。
  
所以排查方式很简单，搜索 resources/views 目录，看看我们有没有用到 {!! !!}：  
检查项目结果：   
第一处是我们在实现商品搜索时，把用户的搜索条件以 JSON 格式输出到 JS 变量中，由于经过了一层 JSON 编码，所以是安全的；第二处是商品详情页输出商品详情，由于商品详情只能由我们的运营人员或者管理员来输入，因此不存在用户输入的风险。    

#### - 3. 避免 CSRF 攻击需要两个条件：
````
- 敏感操作不能用 Get 请求方式；
- 对于非 Get 的请求方式，需要校验请求中的 token 字段，这个字段值对每个用户每次登录都是不一样的。

对于 Laravel 项目来说已经内置了 CSRF 的防御手段，我们在写前端表单时都需要写一个 <input type="hidden" name="_token" value="{{ csrf_token() }}"> 来提交 CSRF Token，因此我们的项目不会有 CSRF 攻击的风险。
````


## How to play github online testing??
访问 https://travis-ci.org/   


1.创建travis配置文件  
 
$ touch .travis.yml  
``````````
language: php

php:
 - 7.0
 - 7.1

before_script:
  - composer install --dev --prefer-source --no-interaction
  - cp .env.travis .env
  - php artisan key:generate

script:
  - vendor/bin/phpunit
  - vendor/bin/phpcs app --standard=PSR2

``````````  

2.配置项目  
  
composer require --dev squizlabs/php_codesniffer
Check code before push code to github~!(https://travis-ci.org)
vendor/bin/phpunit
vendor/bin/phpcs app --standard=PSR2
vendor/bin/phpcbf ./ --standard=PSR2
