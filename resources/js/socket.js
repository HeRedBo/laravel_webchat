import io from 'socket.io-client';
import store from "./store";
let api_token = store.state.userInfo.token;
// import uuid from 'uuid';
// import { Base64 } from 'js-base64';
const socket = io('http://lara-blog-s.test?api_token=' + api_token);
export default socket;
