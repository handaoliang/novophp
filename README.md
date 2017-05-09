##NovoPHP使用文档

### 一、使用说明：

- 框架采用开源软件：`Nginx+MySQL+Redis+Memcache+PHP+Smarty`，MySQL作为逻辑存储，Redis作为队例存储，Memcache作为缓存。

- PHP要求5.3以上版本，保证`Nginx`的`Rewrite`模块已经编译，Linux系统保证开启epoll，FreeBSD保证开启了kqueue支持。

- 框架采用MVC架构，整合了PHP优秀的模板系统Smarty，数据缓存放到Memcache，文件缓存使用Smarty本身的缓存机制（File或者Memcache）。

- 如果Smarty采取的是File缓存机制，需要保证`Application/Cache`目录下的两个文件夹有写权限。

- 动态加密算法涉及到精密运算，系统要有`GMP`（The GNU Multiple Precision Arithmetic Library）支持。PHP的GMP扩展需要自行编译（etc/gmp），GMP Download：[http://gmplib.org/#DOWNLOAD](http://gmplib.org/#DOWNLOAD)

- 用户访问的根目录为`WebRoot`，默认入口是index.do文件，Nginx里要限制不允许访问*.php文件，静态资源暂时也先放到这下面，如果不想配置复杂的缓存策略，可以指定独立域名以备做CDN。

- 为保证不进垃圾邮箱，且不用自己做白名单，邮件发送采用第三方接口发送，本系统暂时支持Amazon SES（SendGrid可选）。

- 后台任务启用独立进程监听，邮件队列采用PHPResque来支持，使用Superviso来调度（可用Python/Go取代）。

- 在`_Documents`目录下的`nginx.conf`文件是网站的`Nginx`配置，更改相应的目录配置之后，通过`nginx.conf` include进去即可。

- 在SVN或者Git的配置里将以下文件忽略：`*.swp` `*.tpl.php` `*.tpl.cache.php`，以免这些文件入库。

- 保证PHP的Memcache/MySQL/Redis/GMP/GD等第三方模块已经编译或者内置。

- 出于安全考虑，所有文件及文件夹权限建议采用www:www，需要写权限的文件夹不置777权限。

### 二、目录结构：
<pre>
.
├── NovoPHP
│   ├── core
│   │   ├── NovoController.class.php
│   │   ├── NovoInitialize.php
│   │   ├── NovoInterface.class.php
│   │   └── NovoLoader.class.php
│   ├── lib
│   │   ├── CommonFunc.class.php
│   │   ├── EmailAddressValidator.class.php
│   │   ├── HelperFunc.class.php
│   │   ├── NovoCaptcha.class.php
│   │   ├── NovoCurls.class.php
│   │   ├── NovoEmailServerJobs.class.php
│   │   ├── NovoMemcached.class.php
│   │   ├── NovoMySQLiData.class.php
│   │   ├── NovoPage.class.php
│   │   ├── NovoRedis.class.php
│   │   ├── NovoSmarty.class.php
│   │   ├── NovoStringEncrypt.class.php
│   │   └── NovoUploader.class.php
│   └── vendor
│       ├── Asido
│       ├── Fonts
│       ├── MailMime
│       ├── Memory
│       ├── PHPExcel
│       ├── PHPMailer
│       ├── PHPMailerSdk
│       ├── PHPResque
│       ├── PHPResque.Multi
│       └── Smarty
├── README.md
├── _Documents
│   └── nginx.conf
├── app
│   ├── cache
│   │   ├── smarty_cache
│   │   └── templates_c
│   ├── controller
│   │   ├── ErrorController.php
│   │   ├── HelperController.php
│   │   └── HomeController.php
│   ├── lib
│   │   ├── AppsController.class.php
│   │   ├── AppsFunc.class.php
│   │   └── app.init.php
│   ├── view
│   │   ├── Error
│   │   ├── Home
│   │   └── Share
│   └── webroot
│       ├── auto_signin.do
│       ├── index.do
│       └── statics
├── common
│   ├── api
│   │   └── HomeApi.class.php
│   ├── config
│   │   ├── development
│   │   └── production
│   └── model
│       └── HomeModel.class.php
└── tt.txt
</pre>

### 三、Nginx Config 配置：

<pre>
    server
    {
        listen       80;
        server_name  www.novophp.com;

        index index.html index.do;
        root  /webroot/app/webroot;

        #禁止访问.php|.tpl的文件，返回404
        location ~ .*\.(php|tpl)?$ {
            return 404;
        }

        location / {
            try_files $uri $uri/ /index.do?$query_string;
        }

        if (!-f $request_filename) {
            rewrite (.*) /index.do?$args last;
        }

        location ~ .*\.(php|do)?$
        {
            #fastcgi_param NOVO_RUNNING_ENV 'production';
            fastcgi_param NOVO_RUNNING_ENV 'development';

            fastcgi_pass  127.0.0.1:9000;
            fastcgi_index index.do;
            include fcgi.conf;
        }

        error_page   404 500 502 503 504  /index.do;

        access_log /data/logs/www_novophp_com_access.log access_log_format;
    }
</pre>

### 四、php-fpm.conf配置：

将.do文件映射成PHP文件，这一步也可以不做。

<pre>
; 将.do的文件，解析成PHP
security.limit_extensions = .do .php .php3 .php4 .php5
</pre>

### 五、进阶教程：

- 创建Controller：
<pre>
FileName:~/app/controller/HomeController.php
</pre>

保持Controller的Class Name与文件名高度一致

```php

class HomeController extends AppsController {

    //是否需要身份验证才能进行操作。
    public $isAuthRequire = FALSE;

    protected $defaultJSONData = array(
        "error"     =>1,
        "msg"       =>"",
        "data"      =>"",
        "code"      =>"",
    );

    public function __construct()
    {
        parent::__construct();
    }

    //可以这样访问：http://www.novophp.com/home/index/your_name/your_password.html
    public function do_index($name=NULL, $password=NULL)
    {
        if(AppsFunc::checkUserSignIn()){
            header("location:/dashboard/");
        }
        $homeArray = array(
            "frame_name"        =>"NovoPHP",
            "frame_version"     =>"1.0.5",
        );
        var_dump($name);
        echo "<br />";
        var_dump($password);
        echo "<br />";
        $homeArrayString = CommonFunc::simplePackArray($homeArray);
        echo "Pack Array is: " . $homeArrayString . "<br />";
        print_r(CommonFunc::simpleUnpackArray($homeArrayString));
        echo "<br />";
        $uriStr = CommonFunc::packURIString(12311123);
        echo $uriStr."<br />";
        echo CommonFunc::unpackURIString($uriStr)."<br />";
        print_r(HomeApi::init(0)->getHomeData())."<br />";
        //$homeModels = $this->getModelByName("home");
        //$homeData = $homeModels->getHomeData();
        //$this->smarty->assign("home_data", $homeData);
        $this->smarty->assign("test_string", "测试字符串截取啊啊啊啊啊啊");
        $this->smarty->assign("timestamp", time());
        $this->smarty->display("home/index.tpl");
    }
    public function do_login()
    {
        echo "This is Login method..";
    }

    public function do_api()
    {

        CommonFunc::echoJSONData($this->defaultJSONData);
    }

}
```


- 创建Model：
<pre>
FileName：~/common/model/HomeModel.class.php
</pre>

保持Class Name与文件名高度一致
<pre>
class HomeModel extends NovoMySQLiData
{
    protected $AppsDBVolumes = "common_db";
    protected $AppsQueryDB = "master";

    public function __construct()
    {
        $this->MySQLDBConfig = NovoLoader::loadConfig('mysql', $this->AppsDBVolumes);
        $this->DBTablePre = $this->MySQLDBConfig["db_table_pre"];
        $this->MySQLQueryDB = $this->AppsQueryDB;

        parent::__construct();

        $memcacheConfig = NovoLoader::loadConfig('memcache');
        if(count($memcacheConfig) == 0
            || !isset($memcacheConfig["memcache_namespace"])
            || !isset($memcacheConfig["memcache_server"])
        ) {
            die("Memcache Config files Error...Please Check...");
        }
        $this->memcacheObj = new NovoMemcached($memcacheConfig["memcache_server"], $memcacheConfig["memcache_namespace"]);
        if ($this->memcacheObj->checkStatus())
        {   
            $this->memcacheObj->setDataVersion("home");
        }  
    }

    public function  getHomeData()
    {
        $dbName = $this->DBTablePre."options";
        $sql = "SELECT * FROM {$dbName} limit 10";
        return $this->getAll($sql);
    }
}
</pre>
