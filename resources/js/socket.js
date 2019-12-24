import io from 'socket.io-client';
// console.log(process);
// const socket = io.connect('http://lara-blog-s.test' + ':' + process.env.LARAVELS_LISTEN_PORT + '/ws/')
const socket = io('http://lara-blog-s.test',{ 
    path:'/ws',
    transports: ['websocket']
});

export default socket;
