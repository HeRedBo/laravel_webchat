<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <title>websocker 聊天室</title>
    <meta name="keywords" content="关键字列表" />
    <meta name="description" content="网页描述" />
    <link rel="stylesheet" type="text/css" href="" />
    <style type="text/css"></style>
    <script type="text/javascript"></script>
</head>
<body>

<input id="input" style="width: 100%;">


</body>
</html>

<script>
   window.onload = function () {
       var nick = prompt("Enter your nickname");
       var input = document.getElementById("input");
       input.focus();

       // 初始化客户端套接字并建立连接
       //var socket = new WebSocket("ws://127.0.0.1:5200");
       var socket = new WebSocket("ws://lara-todo-s.test/ws");
       
       // 连接建立时触发
       socket.onopen = function (event) {
           console.log("Connection open ..."); 
       }

       // 接收到服务端推送时执行
       socket.onmessage = function (event) {
           var msg = event.data;
           var node = document.createTextNode(msg);
           var div = document.createElement("div");
           div.appendChild(node);
           document.body.insertBefore(div, input);
           input.scrollIntoView();
       };
       
       // 连接关闭时触发
       socket.onclose = function (event) {
           console.log("Connection closed ..."); 
       }

       input.onchange = function () {
           var msg = nick + ": " + input.value;
           // 将输入框变更信息通过 send 方法发送到服务器
           socket.send(msg);
           input.value = "";
       };
   }
</script>