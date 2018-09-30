<?php if (!defined('THINK_PATH')) exit();?><html>
<head>
    <title>互动生活馆</title>
<meta http-equiv="Content-Type"
	content="application/xhtml+xml; charset=utf-8">
<meta charset="UTF-8">
<meta name="viewport"
	content="width=device-width,initial-scale=1,user-scalable=0">

<meta name="keywords" content="互动生活馆" />
<meta name="description" content="互动生活馆" />


<link href="/exam/Public/static/bootstrap3.3.7/css/bootstrap.min.css" rel="stylesheet">
<link href="data:text/css;charset=utf-8," data-href="/exam/Public/static/bootstrap3.3.7/css/bootstrap-theme.min.css" rel="stylesheet" id="bs-theme-stylesheet">

<script src="/exam/Public/static/jquery-2.0.3.min.js" type="application/javascript"></script>
<script src="/exam/Public/static/jquery.cookie.js" type="application/javascript"></script>




<!-- 页面header钩子，一般用于加载插件CSS文件和代码 -->
<?php echo hook('pageHeader');?>

</head>
<body>


    <!-- 头部 -->
    <div class="header">

</div>
    <!-- /头部 -->

    <!-- 主体 -->
    
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">MBTI</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">首页</a></li>
                    <li><a href="#about">说明</a></li>
                    <li><a href="#contact">样例</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav>

    <div class="container theme-showcase" role="main">

        <!-- Main jumbotron for a primary marketing message or call to action -->
        <div class="jumbotron">
            <h1>MBTI职业性格测试</h1>
            <p>
                了解自己的优点、缺点，更容易接受自己并理解和接受他人；能使你理解为什么人与人之间在思维、行为、观念、表现等方面存在差异，有助于你在工作、生活中更好地利用这种差异，接受其他观点的合理性，避免固执己见或者简单地判定某种做法的正确或错误；而不是因为存在性格的差异而苦恼。</p>
        </div>

        <p>
            <button type="button" id="exam4continue" class="btn btn-lg btn-primary">继续上次测试</button>
            <button type="button" id="exam4new" class="btn btn-lg btn-success">开始全新测试</button>
        </p>

        <!--<a href="#" onclick="beginAnswer('rrr','fff')">ss</a>-->
    </div>
    <script type="application/javascript">
        $(document).ready(function () {
            if (<?php echo ($displayContinueButton); ?> == true) {
                //
            } else {
                $('#exam4continue').attr('style', 'display:none');
            }

            $('button').on('click', function () {
                if (this.id == "exam4new") {
                    $.cookie("lastTopicNumber", 0);

                    var lastAnswerGuid = genGuid();
                    $.cookie("lastAnswerGuid", lastAnswerGuid);

                    beginAnswer(lastAnswerGuid,  $.cookie('userGuid'));
                } else {
                    navNextStep();
                }
            });
        });

        function navNextStep() {
            window.location.href = "<?php echo U('mbtiProgress');?>";
        }

        function beginAnswer(answerGuid,userGuid) {
            $.ajax({
                url: "<?php echo U('beginAnswer4Client');?>",
                data: {answerGuid: answerGuid, userGuid: userGuid},
                dataType: "json",
                success: function (data, textStatus) {
                    //alert(data);
                    navNextStep();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(textStatus);
                }
            });
        }

        function genGuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }
    </script>


    <!-- /主体 -->

    <!-- 底部 -->
    
    <!-- /底部 -->


</body>
</html>