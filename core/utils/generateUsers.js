(function () {
	'use strict';

	var MongoClient = require('mongodb').MongoClient;
	exports.generate = function (appid, valuemgr, converter, url, n, collection, callback) {
		console.log('--GENERATE USER--');
		generateDB(appid, converter, url, n, collection, valuemgr, callback);
	};

	function generateDB(appid, converter, url, n, collection, valuemgr,  callback) {
		MongoClient.connect(url).then(function (db) {
			var count = 0;
			converter.toIDs(['_isUser', 'name', 'age', "_os", 'iq', "_device", "_browser", "_lang", "_city", 'gender', 'height', '_segments'], function (ids) {
				for (var i = 0; i < n; i++) {
					count++;
					var user = generateUsers(ids);
					valuemgr.cineObject(appid, 'user', user);

					db.collection(collection).insertOne(user, function (err, results) {
						if (err) throw err;
						var newuser = results.ops[0];
						newuser._mtid = newuser._id;
						db.collection(collection).updateOne({_id: newuser._id}, newuser, function () {
							count--;
							if (count % 1000 === 0)
								console.log((n - count) + ' records');

							if (count === 0) {
								db.close();
								console.log('Done');
								callback();
							}
						});
					});

				}
			});

			// Listen for some events
			db.on('reconnect', function (data) {
				console.log('[MongoDB] reconnect success');
			});
			db.on('error', function (err) {
				console.log('[MongoDB] error', err.message);
			});
			db.on('close', function (err) {
				console.log('[MongoDB] disconnected');
			});
		}).catch(function (err) {
			console.error("[MongoDB]", err.message);
			setTimeout(function () {
				generateDB(url)
			}, 2000);
		});
	}

	function generateUsers(ids) {
		var users = {};
		users[ids._isUser] = true;
		//users[ids.userid] = generateNumber(1, 1000000);
		users[ids.name] = generateName();
		users[ids.height] = generateNumber(150, 180);
		users[ids.iq] = generateNumber(30, 40);
		users[ids._segments] = [];
		users[ids.age] = generateNumber(20, 60);
		users[ids._os] = os[generateNumber(0, 6)];
		users[ids._device] = devices[generateNumber(0, 6)];
		users[ids.gender] = generateNumber(1, 2) === 1 ? 'male' : 'female';
		users[ids._lang] = generateNumber(1, 2) === 1 ? 'en' : 'vn';
		users[ids._city] = generateNumber(1, 2) === 1 ? 'Hồ Chí Minh' : 'Hà Nội';
		return users;
	}

//function generateNumber(min, max) {
//	var range = max - min;
//	return Math.floor(Math.random() * (range + 1)) + min;
//}

	function generateName() {
		var ho = ['An', 'Ánh', 'Ân', 'Âu', 'Ấu', 'Bá', 'Bạc', 'Bạch', 'Bàng', 'Bành', 'Bảo', 'Bế', 'Bì', 'Biện', 'Bình', 'Bồ', 'Ca', 'Cái', 'Cam', 'Cao', 'Cát', 'Cầm', 'Cấn', 'Chế', 'Chiêm', 'Chu', 'Chung', 'Chương', 'Chử', 'Cổ', 'Cù', 'Cung', 'Cự', 'Dã', 'Danh', 'Diêm', 'Doãn', 'Diệp', 'Đàm', 'Đan', 'Đào', 'Đậu', 'Điền', 'Đinh', 'Đoàn', 'Đôn', 'Đồng', 'Đổng', 'Đới', 'Đường', 'Giả', 'Giao', 'Giang', 'Giáp', 'Hà', 'Hạ', 'Hàn', 'Hán', 'Hy', 'Hình', 'Hoa', 'Hồng', 'Hùng', 'Hứa', 'Kha', 'Khương', 'Khâu', 'Khoa', 'Khổng', 'Khu', 'Khuất', 'Khúc', 'Kiều', 'Kim', 'La', 'Lạc', 'Lại', 'Lâm', 'Lều', 'Liễu', 'Lò', 'Lục', 'Lư', 'Lã', 'Lương', 'Lưu', 'Ma', 'Mã', 'Mạc', 'Mạch', 'Mai', 'Mang', 'Mâu', 'Mẫn', 'Mộc', 'Ninh', 'Nhâm', 'Ngân', 'Nghiêm', 'Nghị', 'Ngụy', 'Nhữ', 'Nông', 'Ong', 'Ông', 'Phi', 'Phí', 'Phó', 'Phùng', 'Phương', 'Quản', 'Quách', 'Sầm', 'Sơn', 'Sử', 'Tạ', 'Tào', 'Tăng', 'Thạch', 'Thái', 'Thành', 'Thào', 'Thân', 'Thập', 'Thi', 'Thiều', 'Thịnh', 'Thôi', 'Tiêu', 'Tiếp', 'Tòng', 'Tô', 'Tôn', 'Tông', 'Tống', 'Trang', 'Trà', 'Trác', 'Triệu', 'Trịnh', 'Trình', 'Trưng', 'Trương', 'Từ', 'Ty', 'Uông', 'Văn', 'Vi', 'Viên', 'Vương', 'Xa', 'Yên', 'Ngọc', 'Liêu', 'Lỗ'];

		var ten = ['Mỹ', 'Duệ', 'Tăng', 'Cường', 'Tráng', 'Liên', 'Huy', 'Phát', 'Bội', 'Hương', 'Linh', 'Nghi', 'Hàm', 'Tốn', 'Thuận', 'Vĩ', 'Vọng', 'Biểu', 'Khôn', 'Quang', 'Lương', 'Kiến', 'Ninh', 'Hòa', 'Thuật', 'Du', 'Hành', 'Suất', 'Nghĩa', 'Phương', 'Dưỡng', 'Di', 'Tương', 'Thực', 'Hảo', 'Cao', 'Túc', 'Thể', 'Vi', 'Tường', 'Tịnh', 'Hoài', 'Chiêm', 'Viễn', 'Ái', 'Cảnh', 'Ngưỡng', 'Mậu', 'Thanh', 'Kha', 'Nghiễm', 'Khác', 'Do', 'Trung', 'Ðạt', 'Liên', 'Trung', 'Tập', 'Cát', 'Ða', 'Diên', 'Hội', 'Phong', 'Hanh', 'Hiệp', 'Trùng', 'Phùng', 'Tuấn', 'Lãng', 'Nghi', 'Hậu', 'Lưu', 'Thành', 'Tú', 'Diệu', 'Diễn', 'Khánh', 'Thích', 'Phương', 'Huy', 'Từ', 'Thể', 'Dương', 'Quỳnh', 'Cẩm', 'Phu', 'Văn', 'Ái', 'Diệu', 'Dương', 'Bách', 'Chi', 'Quân', 'Phụ', 'Dực', 'Vạn', 'Diệp', 'Hiệu', 'Khuông', 'Tương'];
		return ho[Math.floor(Math.random() * 100) % ho.length] + ' ' + ten[Math.floor(Math.random() * 100) % ten.length]
	}

	function randomNumber(a, b) {
		if (b === undefined) {
			b = a;
			a = 0;
		}
		var delta = b - a + 1;
		return Math.floor(Math.random() * delta) + a
	}

	var h = [];

	function generateNumber(a, b) {
		var delta = b - a + 1;
		if (h.length == 0 || h.length < delta) {
			//init hash

			for (var i = 0; i < delta; i++) {
				if (i == 0) h[i] = randomNumber(0, 20);
				else h[i] = h[i - 1] + randomNumber(0, 20);
			}
		}
		var r = randomNumber(0, h[delta - 1]);

		for (var i = 0; i < delta; i++)
			if (h[i] >= r) return i + a;
		return 'fuck'
	}

	var os = [["window"], ["ubuntu"], ["mac"], ["ubuntu", "window"], ["mac", "ubuntu"], ["mac", "window"], ["mac", "ubuntu", "window"]];
	var devices = [["phone"], ["desktop"], ["table"], ["phone", "desktop"], ["table", "phone"], ["table", "desktop"], ["table", "phone", "desktop"]]
//generateDB();

})();