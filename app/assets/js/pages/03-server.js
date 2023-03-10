var app = require('express')();
var server = require('http').createServer(app);
var io = require('socket.io')(server,{
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});
// // var md5 = require('md5');
var mysql = require('mysql');
//
app.get('/', function(req, res){
    res.end('a user connected');
});

server.listen(1337, function(){
    console.log('listening on *:1337');
});
//
// var connexion = mysql.createConnection(
//     {
//         host: '192.168.1.17',
//         user: 'root',
//         password: 'toto44',
//         database: 'meet'
//     }
// );
//
// connexion.connect(function (err) {
//     if (err) {
//         console.log('Error connexion' + err);
//     }
// });
/**
 *
 * @type {{}}
 */
let connectedUsers = {};
let messages = [];
let me = {};
const limit = 5;
let step = 5;
let max = 0;
let offset = 0;

io.sockets.on('connection', function (socket) {
    /**
     * quand on est logger
     */
    socket.on('login', function (data) {
        me = {
            id: data.userId,
            lastname: data.lastname,
            firstname: data.firstname,
            token: socket.id,
            avatar: data.avatar
        };
        socket.username = me.token;
        connectedUsers[me.id] = socket.id;
        socket.broadcast.emit('login', me);
    });

    // socket.on('scroll', function (data) {
    //     /**
    //      * pour chaque personne du groupe on le notifie
    //      */
    //     // calcul du nombre de résultat et du nombre de page à montrer
    //     connexion.query('SELECT * FROM sm_discussion sd ' +
    //         'join sm_message sm on sd.id = sm.discussion_id ' +
    //         'join sm_user su on sm.sender_id = su.id ' +
    //         'join sm_discussion_user sdu on sd.id = sdu.discussion_id AND su.id = sdu.user_id ' +
    //         'WHERE sd.token = ?',
    //         [
    //             data.groupId
    //         ], function (err, rows, fields) {
    //             if (err) {
    //                 console.error(err);
    //                 return false;
    //             }
    //
    //             if (rows.length !== 0) {
    //                 step += limit;
    //                 max = rows.length;
    //                 offset = max - step;
    //
    //                 console.log('nb' + max);
    //                 console.log('step : ' + step);
    //                 console.log('offset : ' + offset);
    //             } else {
    //                 console.error('aucune message trouvé');
    //             }
    //         });
    //     connexion.query('SELECT sd.token, sm.message, sm.created_at, sdu.user_id FROM sm_discussion sd ' +
    //         'join sm_message sm on sd.id = sm.discussion_id ' +
    //         'join sm_user su on sm.sender_id = su.id ' +
    //         'join sm_discussion_user sdu on sd.id = sdu.discussion_id AND su.id = sdu.user_id ' +
    //         'WHERE sd.token = ?' +
    //         'LIMIT ? ' +
    //         'OFFSET ?',
    //         [
    //             data.groupId,
    //             limit,
    //             offset
    //         ], function (err, rows, fields) {
    //             if (err) {
    //                 console.error(err);
    //                 return false;
    //             }
    //
    //             if (rows.length !== 0) {
    //                 console.error('Message trouvé off' );
    //                 console.log(rows[0]);
    //                 // for (var k in data.groupUsers) {
    //                 //     io.to(connectedUsers[data.groupUsers[k]]).emit(
    //                 //         'message', data
    //                 //     );
    //                 //     console.log('Message to send' + data.groupUsers[k]);
    //                 // }
    //             } else {
    //                 console.error('aucune message trouvé');
    //             }
    //         });
    // });

    socket.on('message', function (data) {
        /**
         * pour chaque personne du groupe on le notifie du message
         */
        for (var k in data.groupUsers) {
            io.to(connectedUsers[data.groupUsers[k]]).emit(
                'message', data
            );
        }
    });

    /**
     * Déconnexion
     */
    socket.on('disconnect', function (data) {
        console.log('Disconnecte');
        if (!me.id) {
            return false;
        } else {
        }
    });
});
