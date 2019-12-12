<style lang="scss" scoped>
    #danmu {
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #2c3e50;
    }
    .stage {
        height: 300px;
        width: 100%;
        background: #025d63;
        margin: 0;
        position: relative;
        overflow: hidden;
    }

    h1, h2 {
        font-weight: normal;
    }
    ul {
        list-style-type: none;
        padding: 0;
    }
    li {
        display: inline-block;
        margin: 0 10px;
    }

    a {
        color: #42b983;
    }

    .baberrage-stage {
        z-index: 5;
    }

    .baberrage-stage .baberrage-item.normal{
        color:#FFF;
    }
    .top{
        border:1px solid #66aabb;
    }
    .danmu-control{
        position: absolute;
        margin: 0 auto;
        width: 100%;
        bottom: 300px;
        top: 70%;
        height: 69px;
        box-sizing: border-box;
        text-align: center;
        display: flex;
        justify-content: center;
        div {
            width: 300px;
            background: rgba(0, 0, 0, 0.6);
            padding: 15px;
            border-radius: 5px;
            border: 2px solid #8ad9ff;
        }
        input,button,select{
            height:35px;
            padding:0;
            float:left;
            background:#027fbb;
            border:1px solid #CCC;
            color:#FFF;
            border-radius:0;
            width:18%;
            box-sizing: border-box;
        }
        select{
            height:33px;
            margin-top:1px;
            border: 0px;
            outline: 1px solid rgb(204,204,204);
        }
        input{
            width:64%;
            height:35px;
            background:rgba(0,0,0,.7);
            border:1px solid #8ad9ff;
            padding-left:5px;
            color:#FFF;
        }
    }
</style>



<template>
    <div id="danmu">
        <div class="stage">
            <vue-baberrage
                    :isShow = "barrageIsShow"
                    :barrageList = "barrageList"
                    :loop = "barrageLoop"
                    :maxWordCount = "60"
            >
            </vue-baberrage>
        </div>
        <div class="danmu-control">
            <div>
                <select v-model="position">
                    <option value="top">从上</option>
                    <option value="abc">从右</option>
                </select>
                <input type="text" style="float:left"  v-model="msg"/>
                <button type="button" style="float:left" @click="sendMsg"  @keyup.enter="sendMsg" >发送</button>
            </div>
        </div>
    </div>
</template>
\

<script>
    import { MESSAGE_TYPE } from 'vue-baberrage'

    export default {
        name: 'danmu',
        data () {
            return {
                msg: '你好，学院君！',
                position: 'top',
                barrageIsShow: true,
                currentId: 0,
                barrageLoop: false,
                websocket : null,
                barrageList: []
            }
        },
        created () {


            // 初始化 websocket 并定义回调函数
            this.websocket = new WebSocket("ws://lara-todo-s.test/ws");
            this.websocket.onopen = function (event) {
                console.log("已建立 WebSocket 连接");
            };
            let that = this;
            this.websocket.onmessage = function (event) {
                // 接收到 WebSocket 服务器返回消息时触发
                let data = JSON.parse(event.data);
                console.log(data);
                that.addToList(data.position, data.message);
            };
            this.websocket.onerror = function (event) {
                console.log("与 WebSocket 通信出错");
            };
            this.websocket.onclose = function (event) {
                console.log("断开 WebSocket 连接");
            };
            this.keyupEnter();
        },
        destroyed () {
            this.websocket.close();
        },
        methods: {
            keyupEnter(){
                document.onkeydown = e =>{
                    let body = document.getElementsByTagName('body')[0]
                    if (e.keyCode === 13 && e.target.baseURI.match(/inputbook/) && e.target === body) {
                        console.log('enter')
                        this.sendMsg()
                    }
                }
            },
            removeList () {
                this.barrageList = []
            },
            addToList (position,message) {
                if (position === 'top') {
                    this.barrageList.push({
                        id: ++this.currentId,
                        avatar: 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1575634389396&di=8f415969df439f1bf1d4ca666c9f4172&imgtype=0&src=http%3A%2F%2Fe.hiphotos.baidu.com%2Fzhidao%2Fwh%253D450%252C600%2Fsign%3D1c7bf3b2710e0cf3a0a246ff3f76de29%2Ff636afc379310a553f308054b44543a98226107d.jpg',
                        msg: message,
                        barrageStyle: 'top',
                        time: 8,
                        type: MESSAGE_TYPE.FROM_TOP,
                        position: 'top'
                    })
                } else {
                    this.barrageList.push({
                        id: ++this.currentId,
                        avatar: 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1575634389396&di=8f415969df439f1bf1d4ca666c9f4172&imgtype=0&src=http%3A%2F%2Fe.hiphotos.baidu.com%2Fzhidao%2Fwh%253D450%252C600%2Fsign%3D1c7bf3b2710e0cf3a0a246ff3f76de29%2Ff636afc379310a553f308054b44543a98226107d.jpg',
                        msg:message,
                        time: 15,
                        type: MESSAGE_TYPE.NORMAL
                    })
                }
            },
            sendMsg () {
                // 发送消息到 WebSocket 服务器
                if(this.msg == '') return false;
                this.websocket.send('{"position":"' + this.position + '", "message":"' + this.msg + '"}');
                this.msg = ''

            },
        },
        
    }
</script>

