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

- 在`_Documents`目录下的`novophp_com.conf`文件是网站的`Nginx`配置，更改相应的目录配置之后，通过`nginx.conf` include进去即可。

- 在SVN或者Git的配置里将以下文件忽略：`*.swp` `CommonConfig.php` `WebConfig.php` `MysqlConfig.php` `*.tpl.php` `*.tpl.cache.php`，以免这些文件入库。

- 保证PHP的Memcache/MySQL/Redis/GMP/GD等第三方模块已经编译或者内置。

- 出于安全考虑，所有文件及文件夹权限建议采用www:www，需要写权限的文件夹不置777权限。

### 二、目录结构：
<pre>
.
├── Application
│   ├── Configs
│   │   ├── AppsConfig.php
│   │   ├── AppsInitialize.php
│   │   ├── MemcacheConfig.php
│   │   ├── MysqlConfig.php
│   │   └── RedisConfig.php
│   ├── Controllers
│   │   ├── ErrorController.php
│   │   ├── HelperController.php
│   │   └── HomeController.php
│   ├── Daemons
│   │   ├── Scripts
│   │   └── ServerJobs
│   ├── Helpers
│   │   └── UsersHelper.php
│   ├── Libs
│   │   ├── AppsBaseController.class.php
│   │   ├── AppsCommon.func.php
│   │   └── NovoURI.class.php
│   ├── Models
│   │   └── HomeModels.php
│   ├── UploadRoot
│   │   ├── crossdomain.xml
│   │   ├── statics
│   │   └── upload.do
│   ├── Views
│   │   ├── Error
│   │   ├── Home
│   │   └── Share
│   └── WebRoot
│       ├── auto_signin.do
│       ├── index.do
│       └── statics
├── NovoPHP
│   ├── Configs
│   │   ├── CommonConfig.php
│   │   └── NovoInitialize.php
│   ├── Libs
│   │   ├── BaseController.class.php
│   │   ├── BaseCurls.Class.php
│   │   ├── BaseEmailServerJobs.Class.php
│   │   ├── BaseInitialize.class.php
│   │   ├── BaseInterface.class.php
│   │   ├── BaseMemcached.class.php
│   │   ├── BaseMySQLiData.class.php
│   │   ├── BasePage.class.php
│   │   ├── BaseRedis.class.php
│   │   ├── BaseStringEncrypt.class.php
│   │   ├── BaseUploader.class.php
│   │   ├── CaptchaV2.lib.php
│   │   ├── Common.func.php
│   │   ├── Helper.func.php
│   │   ├── SmartyTemplate.class.php
│   │   └── XXTeaEncryptModel.func.php
│   └── Vendors
│       ├── Asido
│       ├── EmailAddressValidator.php
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
└── _Documents
    ├── README.md
    └── nginx.conf
</pre>

### 三、Nginx Config 配置：

<pre>
    server
    {
        listen       80;
        server_name  www.novophp.com;

      	#将请求定向到index.do，而不是index.php
        index index.html index.do;
        root  /opt/webserver/Application/WebRoot;

        #禁止访问.php|.tpl的文件，返回404
        location ~ .*\.(php|tpl)?$ {
            return 404;
        }

        #对根目录的访问都做URLRewrite跳转。
        if (!-f $request_filename) {
            rewrite ^/auto_signin(.*)$ /auto_signin.do?controller=users&action=auto_sign_in&referer_uri=$1 last;
            rewrite ^/([\-_a-zA-Z]+)/?$ /index.do?controller=$1&action=index last;
            rewrite ^/([\-_a-zA-Z]+)/([\-_0-9a-zA-Z]+)/(.*)\.(html|txt|json|shtml)?$ /index.do?controller=$1&action=$2&id=$3&request_data_type=$4 last;
            rewrite ^/([\-_a-zA-Z]+)/([\-_0-9a-zA-Z]+)\.(html|txt|json|shtml)?$ /index.do?controller=$1&action=$2&request_data_type=$3 last;
            rewrite ^/([\-_a-zA-Z]+)/([\-_0-9a-zA-Z]+)/?(.*)$ /index.do?controller=$1&action=$2&$3 last;
            break;
        }

        location ~ .*\.(php|do)?$
        {
            fastcgi_pass  127.0.0.1:9000;
            fastcgi_index index.do;
            include fcgi.conf;
        }

        error_page  404              /error/404.html;
        error_page   500 502 503 504  /index.do;

        access_log /opt/idata/www_novophp_com_access.log access_log_format;
    }
</pre>

###四、php-fpm.conf配置：

<pre>
; 将.do的文件，解析成PHP
security.limit_extensions = .do .php .php3 .php4 .php5
</pre>

### 五、进阶教程：

- 创建Controller：
<pre>
FileName:~/Application/Controllers/HomeController.php
</pre>

保持Controller的Class Name与文件名高度一致
<pre>
class HomeController extends AppsBaseController {

    //页面需要身份验证才能进行操作。
    //public $isAuthRequire = true;

    //先映射一个ActionMap。
    protected $ActionsMap = array(
        "index"         =>"doIndex",
    );

    //构造函数
    public function __construct()
    {
        parent::__construct();
    }

    //执行函数体
    public function doIndex(){
        if(checkUserSignIn()){
            header("location:/dashboard/");
        }
        $homeModels = $this->getModelByName("home");
        $homeData = $homeModels->getHomeData();
        $this->smarty->assign("home_data", $homeData);
        $this->smarty->assign("timestamp", time());
        $this->smarty->display("Home/indexView.tpl");
    }

}
</pre>


- 创建Model：
<pre>
FileName：~/Application/Models/HomeModels.php
</pre>

保持Class Name与文件名高度一致
<pre>
class HomeModels extends BaseMySQLiData{

    public function __construct()
    {
        //初始化MySQL数据库配置，
        $this->MySQLDBConfig = BaseInitialize::loadAppsConfig('mysql');
        //确定当前Model连接的数据库
        $this->MySQLDBSetting = "master";

        parent::__construct();

        //创建Memcache连接
        $memcacheConfig = BaseInitialize::loadAppsConfig('memcache');
        if(count($memcacheConfig) == 0
            || !isset($memcacheConfig["memcache_namespace"])
            || !isset($memcacheConfig["memcache_server"])
        ) {
            die("Memcache Config files Error...Please Check...");
        }
        $memcacheServer = $memcacheConfig["memcache_server"];
        $this->memcacheObj = new BaseMemcached($memcacheServer, $memcacheConfig["memcache_namespace"]);
        if ($this->memcacheObj->checkStatus())
        {
            $this->memcacheObj->setDataVersion("home_index");
        }
    }

    //取数据方法
    public function getHomeData($category=0, $num=10)
    {
        //数据库表名
        $dbTableName = $this->DBTablePre."home";
        $num = intval($num);
        $category = intval($category);

        //Memcache缓存Key
        $cacheKey = "mall_{$category}_{$num}";
        if ($this->memcacheObj->checkStatus())
        {
            $cacheStatus= true;
            if ($cacheResult = $this->memcacheObj->getCache($cacheKey))
            {
                return $cacheResult;
            }
        }

        $condition = "`status`=1";

        if($category != 0){
            $condition .= " AND `category_id`={$category}";
        }

        $sql = "SELECT * FROM `".$dbTableName."` WHERE {$condition} LIMIT 0,".$num;
        $returnResult = $this->getAll($sql);

        //设置Memcache缓存
        if ($cacheStatus)
        {
            $this->memcacheObj->setCache($cacheKey, $returnResult);
        }

        return $returnResult;
    }
}
</pre>
