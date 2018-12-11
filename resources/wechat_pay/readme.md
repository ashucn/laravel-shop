密钥可以通过 http://www.unit-conversion.info/texttools/random-string-generator/ 这个网站来生成：

--------  

注意：由于微信支付配置的是正式的参数，如果泄露将导致资金损失，所以千万不能把 config/pay.php 和 resources/wechat_pay 目录下的文件提交到公共代码库中。可以用如下命令让 Git 忽略这些文件：

$ git update-index --assume-unchanged config/pay.php  

-------- 

以及修改 .gitignored 文件：

.gitignore

.
.
.
/resources/wechat_pay