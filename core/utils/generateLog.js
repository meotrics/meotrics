var MongoClient = require('mongodb').MongoClient;
var mongodb = require('mongodb');

var users = null;
var numberUsers = 0;
var db = null;


exports.generate = function (converter, url, npageviews, npurchases, collection, codenamepageview, codenamepurchase, callback) {
	if (callback === undefined) callback = function () {
	};

	//list all action type

	//for each action type, generate n

	console.log('--GENERATE PAGEVIEW--');
	getUsers('pageview', converter, url, npageviews, collection, codenamepageview, function () {
		console.log('--GENERATE PURCHASE--');
		getUsers('purchase', converter, url, npurchases, collection, codenamepurchase, callback);
	});
};

function getUsers(actiontype, converter, url, n, collection, codename, callback) {
	MongoClient.connect(url)
			.then(function (database) {
				console.log('[MongoDB] connected');
				db = database;
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

				return converter.toID('_isUser');
			}).then(function (r) {
		var query = {};
		query[r] = true;
		return db.collection(collection).find(query, {_id: 1}).toArray();
	}).then(function (results) {
		users = results;
		numberUsers = users.length;
		generateDB(actiontype, converter, url, n, collection, codename, callback);
	}).catch(function (err) {
		console.error("[MongoDB]", err.message);
		setTimeout(function () {
			getUsers(actiontype, converter, url, n, collection, codename, callback)
		}, 2000);
	});
}

function generatePageView(ids, codename) {
	var user = users[generateNumber(0, numberUsers - 1)];
	var page = {};
	page[ids._typeid] = codename;
	page[ids.url] = 'http://' + generateNumber(1, 1000) + '.com';
	page[ids._segments] = [];
	page[ids._ctime] = Math.floor(new Date().getTime() / 1000);
	page[ids._mtid] = user._id;
	return page;
}

function generatePurchase(ids, codename) {
	var user = users[generateNumber(0, numberUsers - 1)];
	var pid = generateNumber(1, 1000);
	var purchase = {};
	purchase[ids._typeid] = codename;
	purchase[ids._ctime] = Math.floor(new Date().getTime() / 1000);
	purchase[ids._mtid] = user._id;
	purchase[ids._segments] = [];
	purchase[ids.cid] = pid % 10;
	purchase[ids.pid] = pid;
	purchase[ids.quantity] = generateNumber(1, 10);
	purchase[ids.amount] = generateNumber(1, 20);
	//purchase[ids.pname] = generateName();
	//purchase[ids.cname] = generateName();
	purchase[ids.price] = generateNumber(10, 200);
	purchase[ids.paymentype] = generateNumber(1, 3);
	return purchase;
}

function generateName() {
	var ho = ['An', 'Ánh', 'Ân', 'Âu', 'Ấu', 'Bá', 'Bạc', 'Bạch', 'Bàng', 'Bành', 'Bảo', 'Bế', 'Bì', 'Biện', 'Bình', 'Bồ', 'Ca', 'Cái', 'Cam', 'Cao', 'Cát', 'Cầm', 'Cấn', 'Chế', 'Chiêm', 'Chu', 'Chung', 'Chương', 'Chử', 'Cổ', 'Cù', 'Cung', 'Cự', 'Dã', 'Danh', 'Diêm', 'Doãn', 'Diệp', 'Đàm', 'Đan', 'Đào', 'Đậu', 'Điền', 'Đinh', 'Đoàn', 'Đôn', 'Đồng', 'Đổng', 'Đới', 'Đường', 'Giả', 'Giao', 'Giang', 'Giáp', 'Hà', 'Hạ', 'Hàn', 'Hán', 'Hy', 'Hình', 'Hoa', 'Hồng', 'Hùng', 'Hứa', 'Kha', 'Khương', 'Khâu', 'Khoa', 'Khổng', 'Khu', 'Khuất', 'Khúc', 'Kiều', 'Kim', 'La', 'Lạc', 'Lại', 'Lâm', 'Lều', 'Liễu', 'Lò', 'Lục', 'Lư', 'Lã', 'Lương', 'Lưu', 'Ma', 'Mã', 'Mạc', 'Mạch', 'Mai', 'Mang', 'Mâu', 'Mẫn', 'Mộc', 'Ninh', 'Nhâm', 'Ngân', 'Nghiêm', 'Nghị', 'Ngụy', 'Nhữ', 'Nông', 'Ong', 'Ông', 'Phi', 'Phí', 'Phó', 'Phùng', 'Phương', 'Quản', 'Quách', 'Sầm', 'Sơn', 'Sử', 'Tạ', 'Tào', 'Tăng', 'Thạch', 'Thái', 'Thành', 'Thào', 'Thân', 'Thập', 'Thi', 'Thiều', 'Thịnh', 'Thôi', 'Tiêu', 'Tiếp', 'Tòng', 'Tô', 'Tôn', 'Tông', 'Tống', 'Trang', 'Trà', 'Trác', 'Triệu', 'Trịnh', 'Trình', 'Trưng', 'Trương', 'Từ', 'Ty', 'Uông', 'Văn', 'Vi', 'Viên', 'Vương', 'Xa', 'Yên', 'Ngọc', 'Liêu', 'Lỗ'];

	var ten = ['Mỹ', 'Duệ', 'Tăng', 'Cường', 'Tráng', 'Liên', 'Huy', 'Phát', 'Bội', 'Hương', 'Linh', 'Nghi', 'Hàm', 'Tốn', 'Thuận', 'Vĩ', 'Vọng', 'Biểu', 'Khôn', 'Quang', 'Lương', 'Kiến', 'Ninh', 'Hòa', 'Thuật', 'Du', 'Hành', 'Suất', 'Nghĩa', 'Phương', 'Dưỡng', 'Di', 'Tương', 'Thực', 'Hảo', 'Cao', 'Túc', 'Thể', 'Vi', 'Tường', 'Tịnh', 'Hoài', 'Chiêm', 'Viễn', 'Ái', 'Cảnh', 'Ngưỡng', 'Mậu', 'Thanh', 'Kha', 'Nghiễm', 'Khác', 'Do', 'Trung', 'Ðạt', 'Liên', 'Trung', 'Tập', 'Cát', 'Ða', 'Diên', 'Hội', 'Phong', 'Hanh', 'Hiệp', 'Trùng', 'Phùng', 'Tuấn', 'Lãng', 'Nghi', 'Hậu', 'Lưu', 'Thành', 'Tú', 'Diệu', 'Diễn', 'Khánh', 'Thích', 'Phương', 'Huy', 'Từ', 'Thể', 'Dương', 'Quỳnh', 'Cẩm', 'Phu', 'Văn', 'Ái', 'Diệu', 'Dương', 'Bách', 'Chi', 'Quân', 'Phụ', 'Dực', 'Vạn', 'Diệp', 'Hiệu', 'Khuông', 'Tương'];
	return ho[Math.floor(Math.random() * 100) % ho.length] + ' ' + ten[Math.floor(Math.random() * 100) % ten.length]
}

//function generateNumber(min, max) {
//	var range = max - min;
//
//	var number = Math.floor(Math.random() * (range + 1)) + min;
//	return number;
//}

function generateDB(actiontype, converter, url, n, collection, typeid, callback) {
	var count = 0;
	if (actiontype == 'purchase') {
		converter.toIDs(['_typeid', '_ctime', 'amount', 'pname','cname', 'paymentype',  '_mtid', 'cid', 'pid', 'quantity', 'price', '_segments'], function (ids) {
			for (var i = 0; i < n; i++) {
				count++;
				var r = generatePurchase(ids, typeid);
				db.collection(collection).insertOne(r)
						.then(function (results) {
							count--;
							if (count % 1000 == 0)
								console.log((n - count) + ' records');
							if (count == 0) {
								db.close();
								console.log('Done');
								callback();
							}
						}).catch(function (err) {
					console.log('[MongoDB] insert err', err.message);
				});
			}
			;
		});
	} else {
		converter.toIDs(['_typeid', 'url', 'camid', '_ctime', '_mtid', '_segments'], function (ids) {
			for (var i = 0; i < n; i++) {
				count++;
				var r = generatePageView(ids, typeid);
				db.collection(collection).insertOne(r)
						.then(function (results) {
							count--;
							if (count % 1000 == 0)
								console.log((n - count) + ' records');
							if (count == 0) {
								db.close();
								console.log('Done');
								callback();
							}
						}).catch(function (err) {
					console.log('[MongoDB] insert err', err.message);
				});
			}
			;
		});
	}
}

function randomNumber(a,b)
{
	if(b == undefined) {
		b = a - 1;
		a = 0;
	}
	var delta = b - a + 1;
	return Math.floor(Math.random()*delta) + a
}

var h = [];
function generateNumber(a,b)
{
	var delta = b - a;
	if(h.length == 0)
	{
		//init hash
		
		for(var i = 0 ; i < delta ; i++)
		{
			if(i==0) h[i] = randomNumber(0,20);
			else h[i] = h[i-1] + randomNumber(0,20);
		}
	}
	var r = randomNumber(0, h[delta-1]);

	for(var i =0; i < delta; i++)
		if(h[i] >= r) return i + a;
	return 'fuck'
}

