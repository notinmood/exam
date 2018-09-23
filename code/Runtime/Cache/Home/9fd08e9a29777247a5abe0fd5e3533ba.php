<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
    <!-- Meta,Tag,CSS,JS -->
    <!-- 原子力量updated：加入了更多meta，装载BT3及其依赖 -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

<title><?php echo C('WEB_SITE_TITLE');?></title>
<link href="/exam/Public/static/bootstrap3/css/bootstrap.min.css" rel="stylesheet">
<link href="/exam/Public/static/bootstrap3/css/onethink.css" rel="stylesheet">

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="/exam/Public/static/bootstrap3/js/html5shiv.js"></script>
<![endif]-->

<!--[if lt IE 9]>
<script type="text/javascript" src="/exam/Public/static/jquery-1.11.2.min.js"></script>
<![endif]-->
<!--[if gte IE 9]><!-->
<script type="text/javascript" src="/exam/Public/static/jquery-2.0.3.min.js"></script>
<script type="text/javascript" src="/exam/Public/static/bootstrap3/js/bootstrap.min.js"></script>
<!--<![endif]-->
<!-- 页面header钩子，一般用于加载插件CSS文件和代码 -->
<?php echo hook('pageHeader');?>

</head>
<body>
<!-- 头部header -->
<!-- 原子力量updated：更新为BT3的导航栏样式 -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header" style="border-right:1px solid white">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo U('index/index');?>">互动生活馆</a>
            <!-- <a class="navbar-brand" href="http://www.cleanbing.cn" target="_blank_" style="padding-top:10px"><img src="/exam/Public/Home/images/atompower-slogan.png"/></a> -->
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <?php $__NAV__ = M('Channel')->field(true)->where("status=1")->order("sort")->select(); if(is_array($__NAV__)): $i = 0; $__LIST__ = $__NAV__;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$nav): $mod = ($i % 2 );++$i; if(($nav["pid"]) == "0"): ?><li>
                            <a href="<?php echo (get_nav_url($nav["url"])); ?>"
                               target="<?php if(($nav["target"]) == "1"): ?>_blank<?php else: ?>_self<?php endif; ?>"><?php echo ($nav["title"]); ?></a>
                        </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>

            <form class="navbar-form navbar-right" role="search">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search">
                </div>
                <button type="submit" class="btn btn-default">Go</button>
            </form>

            <ul class="nav navbar-nav navbar-right">
                <!--<?php if(is_login()): ?>-->
                    <!--<li class="dropdown">-->
                        <!--<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo get_username();?><span-->
                                <!--class="caret"></span></a>-->
                        <!--<ul class="dropdown-menu" role="menu">-->
                            <!--<li><a href="<?php echo U('User/profile');?>">修改密码</a></li>-->
                            <!--<li><a href="<?php echo U('User/logout');?>">退出</a></li>-->
                        <!--</ul>-->
                    <!--</li>-->
                    <!--<?php else: ?>-->
                    <!--<li><a href="<?php echo U('User/login');?>">登录</a></li>-->
                    <!--<li><a href="<?php echo U('User/register');?>">注册</a></li>-->
                <!--<?php endif; ?>-->
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>


<div class="container-fluid" id="mainbody">

    <!-- 原子力量updated：更新为BT3样式 -->


    <header class="page-header bg-primary" id="overview">
		<div class="container-fluid">
			<h2><?php echo ($info["title"]); ?></h2>
			<p>
				<span  class="pull-left">
					<span class="author"><?php echo (get_username($info["uid"])); ?></span>
					<span> 发表于 <?php echo (date('Y-m-d H:i',$info["create_time"])); ?></span>
				</span>
				<span class="pull-right">
					<?php $prev = D('Document')->prev($info); if(!empty($prev)): ?><a href="<?php echo U('?id='.$prev['id']);?>">上一篇</a><?php endif; ?>
                    <?php $next = D('Document')->next($info); if(!empty($next)): ?><a href="<?php echo U('?id='.$next['id']);?>">下一篇</a><?php endif; ?>
				</span>
			</p>
		</div>
	</header>


<div id="main-container">
    <div class="row">
        
            <div class="col-md-3 bs-docs-sidebar">
                
                <ul class="nav nav-list bs-docs-sidenav">
                    <?php echo W('Category/lists', array($category['id'], ACTION_NAME == 'index'));?>
                </ul>
            </div>
        
        
    <div class="col-md-9 main-content">
        <!-- Contents
        ================================================== -->
        <section id="contents"><?php echo ($info["content"]); ?></section>
        <hr/>
        <?php echo hook('documentDetailAfter',$info);?>
    </div>

    </div> <!-- row -->
</div> 

<script type="text/javascript">
    $(function(){
        $(window).resize(function(){
            $("#main-container").css("min-height", $(window).height() - 343);
        }).resize();
    })
</script>

    <!-- 原子力量updated：增加了自己的link -->
<!-- 底部
================================================== -->
<footer id="footer" class="row text-center">
    <h5> 互动生活馆-让客户源源不断</h5>
    <h6> 本系统由 <a href="http://<?php echo HILAND_COMPANY_URL;?>" target="_blank"><?php echo HILAND_COMPANY_NAME;?></a> 提供技术支持</h6>
</footer>

<script type="text/javascript">
(function(){
	var ThinkPHP = window.Think = {
		"ROOT"   : "/exam", //当前网站地址
		"APP"    : "/exam/index.php?s=", //当前项目地址
		"PUBLIC" : "/exam/Public", //项目公共目录地址
		"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
		"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
		"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
	}
})();
</script>
 <!-- 用于加载js代码 -->
<!-- 页面footer钩子，一般用于加载插件JS文件和JS代码 -->
<?php echo hook('pageFooter', 'widget');?>
<div class="hidden"><!-- 用于加载统计代码等隐藏元素 -->
    
</div>


</div>

</body>
</html>