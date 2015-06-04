NovoPHP Framework (www.NovoPHP.Com)
===================================

### 说明：

- 框架采用开源软件：`Nginx+MySQL+Redis+Memcache+PHP+Smarty`，MySQL作为逻辑存储，Redis作为队例存储，Memcache作为缓存。

- PHP要求5.3以上版本，保证`Nginx`的`Rewrite`模块已经编译，Linux系统保证开启epoll，FreeBSD保证开启了kqueue支持。

- 框架采用MVC架构，整合了PHP优秀的模板系统Smarty，数据缓存放到Memcache，文件缓存使用Smarty本身的缓存机制（File或者Memcache）。  

- 如果Smarty采取的是File缓存机制，需要保证`Application/Cache`目录下的两个文件夹有写权限。

- 动态加密算法涉及到精密运算，系统要有`GMP`（The GNU Multiple Precision Arithmetic Library）支持。PHP的GMP扩展需要自行编译（etc/gmp），GMP Download：[http://gmplib.org/#DOWNLOAD](http://gmplib.org/#DOWNLOAD)

- 用户访问的根目录为`WebRoot`，默认入口是index.do文件，Nginx里要限制不允许访问*.php文件，静态资源暂时也先放到这下面，如果不想配置复杂的缓存策略，可以指定独立域名以备做CDN。

- 为保证不进垃圾邮箱，且不用自己做白名单，邮件发送采用第三方接口发送，本系统暂时支持Amazon SES（SendGrid可选）。

- 后台任务启用独立进程监听，邮件队列采用PHPResque来支持，使用Superviso来调度（可用Python/Go取代）。

- 在`_Documents`目录下的`novophp_com.conf`文件是网站的`Nginx`配置，更改相应的目录配置之后，通过`nginx.conf` include进去即可。     

- 在SVN或者Git的配置里将以下文件忽略：`*.swp` `CommonConfig.php` `WebConfig.php` `MysqlConfig.php` `*.tpl.php` `*.tpl.cache.php`，以免这些文件入库。

- 保证PHP的Memcache/MySQL/Redis/GMP/GD等第三方模块已经编译或者内置。

- 出于安全考虑，所有文件及文件夹权限建议采用www:www，需要写权限的文件夹不置777权限。

--EOF--
