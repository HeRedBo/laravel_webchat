import io from 'socket.io-client';
// import uuid from 'uuid';
// import { Base64 } from 'js-base64';

const socket = io('http://lara-blog-s.test',{ 
    path:'/ws',
    transports: ['websocket'],
    reconnection: true,
    rememberUpgrade:true
});

export default socket;
