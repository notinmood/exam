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


<link href="/exam/Public/static/bootstrap4/css/bootstrap.min.css" type="text/css"	rel="stylesheet">
<script src="/exam/Public/static/jquery-2.0.3.min.js" type="application/javascript"></script>




<!-- 页面header钩子，一般用于加载插件CSS文件和代码 -->
<?php echo hook('pageHeader');?>

</head>
<body>

	<!-- 头部 -->
	<div class="header">

</div>
	<!-- /头部 -->

	<!-- 主体 -->
	

    <div class="container">
        <div class="row">
            <input type="hidden" id="topicNumber" name="topicNumber">
            <div id="topicTitle" name="topicTitle">题目载入中</div>
        </div>
        <div class="row">
            <div id="topicChoiceA" name="topicChoiceA">A</div>
        </div>
        <div class="row">
            <div id="topicChoiceB" name="topicChoiceB">B</div>
        </div>
        <input type="submit">
        <div class="row">
            <div class="col">
                <button type="button" value="5" class="btn btn-success">强A</button>
                <button type="button" value="4" class="btn btn-info">中A</button>
                <button type="button" value="3" class="btn btn-secondary">弱A</button>
                <button type="button" value="2" class="btn btn-light">弱B</button>
                <button type="button" value="1" class="btn btn-warning">中B</button>
                <button type="button" value="0" class="btn btn-danger">强B</button>
            </div>
        </div>
    </div>

    <script src="/exam/Public/static/jquery.cookie.js" type="application/javascript"></script>
    <script type="application/javascript">
        $(document).ready(function () {
            //1.页面第一次打开的时候，给页面载入题目
            //先将所有题目都载入cookie（如果题目cookie不存在的话），再从cookie中加载最后一道题到页面上
            var lastTopicNumber = $.cookie("lastTopicNumber");
            if (lastTopicNumber == "undefined" || lastTopicNumber == undefined) {
                lastTopicNumber = 0;
            }

            loadTopic(parseInt(lastTopicNumber) + 1);

            //2.为按钮绑定事件
            $('button').on('click', function () {
                //以下1和2步骤都是从cookie读取内容
                //0.alert(this.value);
                //1. 保存当前题目的 题号和选择的答案值
                var topicNumber = $("#topicNumber").val();
                $.cookie("lastTopicNumber", topicNumber);
                var topicAnswen = this.value;
                //alert(topicNumber + " - " + topicAnswen);
                saveAnswer(topicNumber,topicAnswen);

                //2. 载入下一个题目（包括题号和题目内容）
                loadTopic(parseInt(topicNumber) + 1);
            })
        });

        function saveAnswer(topicNumber,topicAnswer) {
            var userGuid="0c3adeff-53b3-4f23-8523-3c639d19f6b5";
            $.ajax({
                url: "<?php echo U('saveAnswer');?>",
                data: {topicNumber: topicNumber,topicAnswer:topicAnswer,userGuid:userGuid}
            });
        }

        function loadTopic(topicNumber) {
            $.ajax({
                url: "<?php echo U('getTopic');?>",
                data: {topicNumber: topicNumber},
                dataType: "json",
                success: function (data, textStatus) {
                    var topicnumber = data["topicnumber"];
                    var topictitle = data['topictitle'];
                    $("#topicNumber").val(topicnumber);
                    $("#topicTitle").html(topicnumber + ". " + topictitle);

                    var choices = data['choices'];
                    var choiceA = choices[0];
                    var choiceB = choices[1];
                    $("#topicChoiceA").html("A. " + choiceA['choicecontent']);
                    $("#topicChoiceB").html("B. " + choiceB['choicecontent']);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(textStatus);
                }
            });
        }
    </script>


	<!-- /主体 -->

	<!-- 底部 -->
	
	<!-- /底部 -->

</body>
</html>