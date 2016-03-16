var converter = require('./idmanager.js'),
	converter = new converter.IdManager(),
	MongoClient = require('mongodb').MongoClient,
	url = 'mongodb://localhost:1234/local',
	n = 20000,
	collection = 'test';

function generateDB(){
	MongoClient.connect(url)
	  	.then(function(db){
		    console.log('[MongoDB] connected');
		    var count = 0;
		    converter.toIDs(['_isUser', 'name', 'age', 'iq', 'gender', 'height'], function(ids){
		    	console.log(ids);
		    	for(var i=0;i<n;i++){
		    		console.log('hha');
			    	count++;
			    	var user = generateUsers(ids);
			    	console.log(user);
					db.collection(collection).insertOne(user)
			 			.then(function(results){
			    			count--;
			    			console.log((n-count) + ' records');
			    			if(count == 0){
			    				db.close();
			    				console.log('Done');
			    			}
			    		}).catch(function(err){
			    			console.log('[MongoDB] insert err', err.message);
			    		});	
		    	}
		    });
		    
		    // Listen for some events
		    db.on('reconnect', function(data){
		      console.log(data);
		      console.log('[MongoDB] reconnect success');
		    });
		    db.on('error', function(err){
		      console.log('[MongoDB] error', err.message);
		    });
		    db.on('close', function(err){
		      console.log('[MongoDB] disconnected');
		    });
	  	}).catch(function(err){
	    	console.error("[MongoDB]", err.message);
	    	setTimeout(generateDB, 2000);
	  	});	
}

function generateUsers(ids){
	var users = {};
	users[ids._isUser] = true;
	users[ids.name] = generateName();
	users[ids.height] = generateNumber(150, 180);
	users[ids.iq] = generateNumber(30, 40);
	users[ids.age] = generateNumber(20, 60);
	users[ids.gender] = generateNumber(1, 2) == 1? 'male' : 'female';
	// console.log(users);
	return users;
}

function generateNumber(min, max){
	var range = max - min;

	var number = Math.floor(Math.random()*(range+1))+min;
	return number;
}

function generateName(){
	var ho =['An','Ánh','Ân','Âu','Ấu','Bá','Bạc','Bạch','Bàng','Bành','Bảo','Bế','Bì','Biện','Bình','Bồ','Ca','Cái','Cam','Cao','Cát','Cầm','Cấn','Chế','Chiêm','Chu','Chung','Chương','Chử','Cổ','Cù','Cung','Cự','Dã','Danh','Diêm','Doãn','Diệp','Đàm','Đan','Đào','Đậu','Điền','Đinh','Đoàn','Đôn','Đồng','Đổng','Đới','Đường','Giả','Giao','Giang','Giáp','Hà','Hạ','Hàn','Hán','Hy','Hình','Hoa','Hồng','Hùng','Hứa','Kha','Khương','Khâu','Khoa','Khổng','Khu','Khuất','Khúc','Kiều','Kim','La','Lạc','Lại','Lâm','Lều','Liễu','Lò','Lục','Lư','Lã','Lương','Lưu','Ma','Mã','Mạc','Mạch','Mai','Mang','Mâu','Mẫn','Mộc','Ninh','Nhâm','Ngân','Nghiêm','Nghị','Ngụy','Nhữ','Nông','Ong','Ông','Phi','Phí','Phó','Phùng','Phương','Quản','Quách','Sầm','Sơn','Sử','Tạ','Tào','Tăng','Thạch','Thái','Thành','Thào','Thân','Thập','Thi','Thiều','Thịnh','Thôi','Tiêu','Tiếp','Tòng','Tô','Tôn','Tông','Tống','Trang','Trà','Trác','Triệu','Trịnh','Trình','Trưng','Trương','Từ','Ty','Uông','Văn','Vi','Viên','Vương','Xa','Yên','Ngọc','Liêu','Lỗ'];

	var ten = ['Mỹ','Duệ','Tăng','Cường','Tráng','Liên','Huy','Phát','Bội','Hương','Linh','Nghi','Hàm','Tốn','Thuận','Vĩ','Vọng','Biểu','Khôn','Quang','Lương','Kiến','Ninh','Hòa','Thuật','Du','Hành','Suất','Nghĩa','Phương','Dưỡng','Di','Tương','Thực','Hảo','Cao','Túc','Thể','Vi','Tường','Tịnh','Hoài','Chiêm','Viễn','Ái','Cảnh','Ngưỡng','Mậu','Thanh','Kha','Nghiễm','Khác','Do','Trung','Ðạt','Liên','Trung','Tập','Cát','Ða','Diên','Hội','Phong','Hanh','Hiệp','Trùng','Phùng','Tuấn','Lãng','Nghi','Hậu','Lưu','Thành','Tú','Diệu','Diễn','Khánh','Thích','Phương','Huy','Từ','Thể','Dương','Quỳnh','Cẩm','Phu','Văn','Ái','Diệu','Dương','Bách','Chi','Quân','Phụ','Dực','Vạn','Diệp','Hiệu','Khuông','Tương'];
	return ho[Math.floor(Math.random()*100) % ho.length] + ' ' + ten[Math.floor(Math.random()*100) % ten.length]
}

generateDB();