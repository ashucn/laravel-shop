# 此项目是 B2C 商城 的个人练习项目


### composer 组件
- [laravel-admin](https://github.com/z-song/laravel-admin)


### 教程笔记
3.7.7 配置 Policy : 检察权限

4 admin后台使用 laravel-admin package

5.1 商品 SKU 的概念。

SKU = Stock Keeping Unit（库存量单位），也可以称为『单品』。对一种商品而言，当其品牌、型号、配置、等级、花色、包装容量、单位、生产日期、保质期、用途、价格、产地等属性中任一属性与其他商品存在不同时，可称为一个单品。

5.2 电商项目中与钱相关的有小数点的字段一律使用 __decimal__ 类型，而不是 float 和 double，后面两种类型在做小数运算时有可能出现精度丢失的问题，这在电商系统里是绝对不允许出现的。
