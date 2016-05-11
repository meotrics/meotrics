'use strict';

var MongoClient = require('mongodb').MongoClient;
var mongodb = require('mongodb');

var users = null;
var numberUsers = 0;
var db = null;

exports.generate = function (appid, valuemgr, converter, url, npageviews, npurchases, collection, codenamepageview, codenamepurchase, callback) {
	if (callback === undefined) callback = function () {
	};

	//list all action type

	//for each action type, generate n

	console.log('--GENERATE PAGEVIEW--');
	getUsers(appid, 'pageview', converter, url, npageviews, collection, codenamepageview, valuemgr, function () {
		console.log('--GENERATE PURCHASE--');
		getUsers(appid, 'purchase', converter, url, npurchases, collection, codenamepurchase, valuemgr, callback);
	});
};

function getUsers(appid, actiontype, converter, url, n, collection, codename, valuemgr, callback) {
	MongoClient.connect(url).then(function (database) {
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
		generateDB(appid, actiontype, converter, url, n, collection, codename, valuemgr, callback);
	}).catch(function (err) {
		console.error("[MongoDB]", err.message);
		setTimeout(function () {
			getUsers(appid, actiontype, converter, url, n, collection, codename, valuemgr, callback);
		}, 2000);
	});
}

function generateCampaign() {
	return ["spring_sale", "newyear", "black_friday", "valentine"][generateNumber(0, 3)];
}

function generateCampaignSource() {
	var campaign_sources = ["google", "facebook", "email"];
	return campaign_sources[generateNumber(0, 2)];
}

function generateCampainTerm() {
	return ["running shoe", "red tshirt", "blue sky"][generateNumber(0, 2)];
}

function generateCampaignMedium() {
	return ["cpc", ""][generateNumber(0, 1)];
}

function generateCampaignContent() {
	return ["contentlink", "logolink", "textlink"][generateNumber(0, 2)];
}
function generatePageView(ids, codename) {
	var user = users[generateNumber(0, numberUsers - 1)];
	var page = {};
	page[ids._typeid] = codename;
	page[ids._url] = 'http://' + generateNumber(1, 1000) + '.com';
	page[ids._segments] = [];
	page[ids._ctime] = Math.floor(new Date().getTime() / 1000 - generateNumber(0, 2592000));
	page[ids._mtid] = user._id;
	page[ids._utm_campaign] = generateCampaign();
	page[ids._utm_source] = generateCampaignSource();
	page[ids._utm_term] = generateCampainTerm();
	page[ids._utm_medium] = generateCampaignMedium();
	page[ids._utm_content] = generateCampaignContent();

	page[ids._browser] = browsers[generateNumber(0, 6)];
	page[ids._os] = os[generateNumber(0, 6)];
	page[ids._device] = devices[generateNumber(0, 6)];

	return page;
}

function generatePurchase(ids, codename) {
	var user = users[generateNumber(0, numberUsers - 1)];
	var pid = generateNumber(1, 1000);
	var purchase = {};
	purchase[ids._typeid] = codename;
	purchase[ids._ctime] = Math.floor(new Date().getTime() / 1000 - generateNumber(0, 2592000));
	purchase[ids._mtid] = user._id;
	purchase[ids._segments] = [];
	var cat = genCategory();
	var prod = genProductName(cat.name);
	purchase[ids.cid] = cat.i;
	purchase[ids.pid] = prod.i;
	purchase[ids.quantity] = generateNumber(1, 5);
	purchase[ids.price] = generateNumber(10, 200);
	purchase[ids.amount] = purchase[ids.quantity] * purchase[ids.price];
	purchase[ids.pname] = prod.name;
	purchase[ids.cname] = cat.name;

	purchase[ids._utm_campaign] = generateCampaign();
	purchase[ids._utm_source] = generateCampaignSource();
	purchase[ids._utm_term] = generateCampainTerm();
	purchase[ids._utm_medium] = generateCampaignMedium();
	purchase[ids._utm_content] = generateCampaignContent();
	purchase[ids._browser] = browsers[generateNumber(0, 6)];
	purchase[ids._os] = os[generateNumber(0, 6)];
	purchase[ids._device] = devices[generateNumber(0, 6)];
	purchase[ids._lang] = generateNumber(1, 2) === 1 ? 'en' : 'vn';
	purchase[ids._city] = generateNumber(1, 2) === 1 ? 'Hồ Chí Minh' : 'Hà Nội';

	purchase[ids.paymentype] = generateNumber(1, 3);
	return purchase;
}
var os = [["window"], ["ubuntu"], ["mac"], ["ubuntu", "window"], ["mac", "ubuntu"], ["mac", "window"], ["mac", "ubuntu", "window"]];
var devices = [["phone"], ["desktop"], ["tablet"], ["phone", "desktop"], ["tablet", "phone"], ["tablet", "desktop"], ["tablet", "phone", "desktop"]];
var browsers = [["chrome"], ["firefox"], ["ie"], ["chrome", "firefox"], ["ie", "chrome"], ["ie", "firefox"], ["chrome", "ie", "firefox"]];
var category = [
	{
		name: 'phone', products: [
		'iPhone5S', 'iphone3', 'iphone4', 'iphone6', 'iphone 7', 'BPhone', 'Samsung Galaxy S6', 'Nokia 110i', 'Samsung Note 3',
		'Sansung Note 5', 'Nokie Lumine 9', 'Blackbery 9', 'Liquid X2', 'Predator 8', 'Iconia One 8', 'Iconia Tab',
		'Liquid Z4', 'Liquid Z5', 'Y3II', 'Hono Holly 2 Plus', 'Enjoy 5s', 'Mate 8', 'Ascend Y520', 'P8lite', 'P8max',
		'10 Lifestyle', 'Desire 825', 'Desire 530', 'One X9', 'One X9', '230 Dual SIM']
	},
	{
		name: "computer", products: [
		"RAM 8GB",
		"RAM 2GB", "AMD Core I3", "SSD hard drive", "USB 9GB", "USB 2GB", 'SATA HDD 500GB', 'ASUS K43e', 'Macbook Air',
		'Macbook Pro', "HP Pavilion", "HP Omnibook", "HP Elitebook", "HP Envy", "HP OMEN", "HP Mini", "ThinkPad",
		"IdeaPad", "Dynabook", "Portege", "Tecra", "Satellite", "Qosmio", "Libretto", "TravelMate", "Extensa", "Aspire",
		"Gateway", "Packard Bell", "Acer Chromebook", "Asus Eee", "Zenbook", "ROG Series", "Asus N", "Asus X", "Asus Chromebook"]
	},
	{
		name: "food", products: ['Heineken beer', 'Ha Noi beer', 'Xoài sấy dẻo', 'Mít sấy', 'Thạch rau câu Long Hải',
		'Snack đậu phộng', 'Bim Bim', 'Apple', 'Mangoes', 'Jack Fruit']
	},
	{
		name: 'Clothing', products: ["Baby Grow",
		"Bag", "Ball Gown", "Belt", "Bikini", "Blazer", "Blouse", "Boots", "Bow Tie", "Boxers", "Bra", "Bra & Knicker Set",
		"Briefs", "Camisole", "Cardigan", "Cargos", "Catsuit", "Chemise", "Coat", "Corset", "Cravat", "Cufflinks",
		"Cummerbund", "Dinner Jacket", "Dress", "Dressing Gown", "Dungarees", "Fleece", "Gloves", "Hair Accessory",
		"Hat", "Hoody", "Jacket", "Jeans", "Jewellery", "Jogging Suit", "Jumper", "Kaftan", "Kilt", "Knickers", "Kurta",
		"Lingerie", "Nightgown", "Nightwear", "Overalls", "Pashmina", "Polo Shirt", "Poncho", "Pyjamas", "Robe", "Romper",
		"Sandals", "Sarong", "Scarf", "Shawl", "Shellsuit", "Shirt", "Shoes", "Shorts", "Skirt", "Slippers", "Socks",
		"Stockings", "Suit", "Sunglasses", "Sweatshirt", "Swimming Costume", "Swimming Shorts", "Swimming Trunks",
		"Swimwear", "T-Shirt", "Tailcoat", "Tankini", "Thong", "Tie", "Tights", "Top", "Tracksuit", "Trainers",
		"Trousers", "Underwear", "Vest", "Vest Underwear", "Waistcoat", "Waterproof", "Zip"]
	},
	{
		name: 'Shoe', products: ["Adidas Kampung",
		"Ballet shoe", "Pointe shoe", "Bast shoe", "Blucher shoe", "Boat shoe", "Brogan (shoes)", "Brogue shoe",
		"Brothel creeper", "Calceology", "Cantabrian albarcas", "Chopine", "Climbing shoe", "Clog", "Court shoe",
		"Cross country running shoe", "Derby shoe", "Diabetic shoe", "Dori shoes", "D'Orsay shoes", "Dress shoe",
		"Driving moccasins", "Earth shoe", "Elevator shoes", "Espadrille", "Fashion boot", "Galesh", "Giveh",
		"High-heeled footwear", "Huarache (shoe)", "Jazz shoe", "Jelly shoes", "Jumpsoles", "Jutti", "Kitten heel",
		"Kleets", "Kolhapuri Chappal", "Loafers", "Lotus shoes", "Mary Jane (shoe)", "Mojari shoe", "Moccasin",
		"Monk shoe", "Mule (shoe)", "Opanak", "Opinga", "Organ shoes", "Orthopaedic footwear", "Over-the-knee boot",
		"Oxford shoe", "Pampootie", "Peranakan beaded slippers", "Peshawari chappal", "Platform shoe", "Pointed shoe",
		"Pointinini", "Rocker bottom shoe", "Ruby slippers", "Russian boot", "Saddle shoe", "Silver Shoes", "Slip-on shoe",
		"Slipper", "Sneakers (footwear)", "Snow boot", "Spectator shoe", "Steel-toe boot", "T-bar sandal", "Tiger-head shoes",
		"Turf Shoe", "Tsarouhi", "Turnshoe", "Venetian-style shoe", "Winklepicker", "Wörishofer"]
	},
	{
		name: 'cosmetic', products: [
		'Kem chống nắng Candes', 'Kem dưỡng White Lable', 'Son môi Lip Eye', 'Kem serum', 'Kem ẩm', 'Kem nền', 'Nước hoa',
		'Sữa tắm dê', 'Kem trang điểm CC', 'Sữa tẩy trang Shinbing']
	},
	{
		name: 'book', products: ["Harry Potter and the Goblet of Fire – JK Rowling", "The Hobbit – J R R Tolkien",
		"The Bible", "The Da Vinci Code – Dan Brown", "Harry Potter and the Prisoner of Azkaban – JK Rowling",
		"The Official Highway Code – Department for Transport", "The Lion, The Witch and The Wardrobe – CS Lewis",
		"Fifty Shades of Grey – E L James", "To Kill a Mockingbird – Harper Lee",
		"Lord of the Rings: Return of the King – JRR Tolkein", "Pride and Prejudice – Jane Austen",
		"Lord of the Rings: The Two Towers – JRR Tolkein", "Jamie’s 15 minute meals – Jamie Oliver", "The BFG – Roald Dahl",
		"Great Expectations – Charles Dickens", "The Hitchhiker’s Guide to the Galaxy – Douglas Adams",
		"Animal Farm – George Orwell", "1984 – George Orwell", "The Girl with the Dragon Tattoo – Stieg Larsson",
		"Bridget Jones’s Diary – Helen Fielding", "Little Women – Louisa May Alcott", "Romeo and Juliet – William Shakespeare",
		"Dracula – Bram Stoker", "The Secret Garden -Frances Hodgson Burnett", "George’s Marvellous Medicine – Roald Dahl",
		"Time Travellers Wife – Audrey Niffenegger", "The Hunger Games – Suzanne Collins", "The Catcher in the Rye – J.D Salinger",
		"David Copperfield – Charles Dickens", "Lovely Bones – Alice Sebold", "The Picture of Dorian Gray – Oscar Wilde",
		"Emma – Jane Austen", "Lord of the Flies – William Golding", "The Story of Tracy Beaker – Jacqueline Wilson",
		"The shining – Stephen King", "Confessions of a shopaholic – Sophie Kinsella", "Game of Thrones – George R R Martin",
		"Life of Pi – Yann Martel", "Memoirs of a Geisha – Arthur Golden", "Far from the Madding Crowd – Thomas Hardy",
		"The Magic Faraway Tree – Enid Blyton", "Silence of the Lambs – Thomas Harris", "My Sisters keeper – Jodi Picoult",
		"Is It Just Me? – Miranda Hart", "Mort – Terry Pratchett", "One Day – David Nicholls", "The Kite Runner – Khaled Hosseini",
		"Moby Dick – Herman Neville", "My Booky Wook – Russell Brand", "The Godfather – Mario Puzo",
		"The Perks of Being a Wallflower – Stephen Chbosky", "Wolf Hall – Hilary Mantel",
		"Brief history of time – Stephen Hawkin", "Men are from Mars Women are from Venus – John Gray",
		"Kane and Abel – Jeffrey Archer", "America Psycho – Bret Easton-Ellis", "Artemis Fowl – Eoin Colfer",
		"Diary of Wimpy Kid – Jeff Kinney", "Gone Girl – Gillian Flynn", "The Princess Diaries – Meg Cabot",
		"Life and Laughing – Michael McIntyre", "Wonders of the Universe – Brian Cox",
		"Call The Midwife: A True Story of the East End in the 1950′s – Jennifer Worth", "One The Road – Jack Kerouac",
		"Being Jordan – Katie Price", "Bradley Wiggins: An Autobiography – Bradley Wiggins", "Cloud Atlas – David Mitchell",
		"Secret diary of a call girl – Belle de Jour", "How to be a Woman – Caitlin Moran", "The Casual Vacancy – JK Rowling",
		"Riders – Jilly Cooper", "The Pillars of the Earth – Ken Follet", "Blood of Dragons – Robin Hobb",
		"David Walliams – Mr Stink", "Me Before You – Jojo Moyes", "Frank Skinner – By Frank Skinner", "World War Z – Max Brooks",
		"A thousand Splendid Suns – Khaled Hosseini", "12th of Never – James Patterson",
		"We can remember it for you wholesale – Phillip K Dick", "The Snail and the Whale – Julia Donaldson",
		"Steve Jobs: The Exclusive Biography – Walter Isaacson", "Still Standing: The Savage Years – Paul O’Grady",
		"Bring up the Bodies – Hilary Mantel", "The Inside – Piers Morgan", "Football! Bloody Hell – Alex Ferguson",
		"The Hundred-Year-Old Man who Climbed out of the Window and Disappeared – Jonas Jonasson",
		"A Street Cat Named Bob – James Bowen", "My Animals and Other Family – Clare Balding", "The James Bond Archives – Paul Duncan",
		"Entwined With You – Sylvia Day", "Running My Life – Seb Coe", "Ratburger – David Walliams", "The Snow Child – Eowyn Ivey",
		"Over the Moon: My Autobiography – David Essex", "Honest: My Story So Far – Tulisa Contostavlos",
		"Looking for Alaska – John Green", "The Autobiography of Jack the Ripper – James Carnac", "Eloise – Judy Finnigan",
		"The Hare with Amber Eyes: A Hidden Inheritance – Edmund de Waal"]
	},
	{
		name: 'pen', products: ["D. Leonardt & Co.", "Decoder pen", "Invisible ink", "Delta", "Demonstrator pen",
		"Derwent Cumberland Pencil Company", "Digital pen", "Dip pen", "Displays2Go", "Dollar Pen[6]",
		"Dr. Sketch", "C. Howard Hunt", "Calligraphy pen", "Four Treasures of the Study", "Camlin", "Caran d'Ache",
		"Carmel Stationery", "Carter's Ink Company", "Cerruti", "Classmate Stationery", "Compact disc pen", "Conway Stewart",
		"Counterfeit banknote detection pen", "Counter pen", "Crayola", "Cretacolor", "Curtis Australia",
		"A. T. Cross Company", "Active Pen", "Alfred Dunhill", "Anoto", "Aurora", "Macniven and Cameron", "Manu Propria",
		"Marker pen – also known as a felt-tip pen", "Connector pen", "Dry erase marker", "Mean Streak", "Wet wipe marker",
		"Melody", "Monami", "Montblanc", "Meisterstück", "Monster appa", "Monteverde Pens", "Muji", "OHTO", "OMAS",
		"Online (pen company)", "Onoto pens", "Optical pen", "Paint marker", "Paper Mate", "Paper Mate PhD Multi",
		"Parker Pen Company", "Duofold", "Parker 100", "Parker 51", "Parker Jotter", "Parker Sonnet", "Parker Vacumatic",
		"Parker Vector", "Quink", "Pelikan", "Pen Room", "PenAgain", "Pentel", "Perfect Pen Pvt Ltd", "Permanent marker",
		"Perry & Co.", "Piano", "Pilot", "Pilot Parallel Pen", "Platignum Pen", "Platinum Pen Co Ltd Japan",
		"Porous point pen", "Portok", "Prodir", "Project Eden", "Promarker"]
	}, {
		name: 'music', products: ["A Light That Never Comes", "All For Nothing", "Announcement Service Public",
			"Blackout", "Bleed It Out", "Breaking The Habit", "Burn It Down", "Burning in the Skies", "By Myself",
			"Castle Of Glass", "Crawling", "Don't Stay", "Drawbar", "Easier To Run", "Faint", "Final Masquerade",
			"Foreword", "From the Inside", "Given Up", "Guilty All The Same", "Hands Held High", "I'll Be Gone",
			"In Between", "In Pieces", "In The End", "Iridescent", "Keys to the Kingdom", "Leave Out All The Rest",
			"Lies Greed Misery", "Lost In The Echo", "Lying From You", "My December", "New Divide", "No More Sorrow",
			"No Roads Left", "Nobody's Listening", "Not Alone", "Numb", "One Step Closer", "Papercut", "Points Of Authority",
			"Powerless", "Pushing Me Away", "Rebellion", "Roads Untraveled", "Shadow Of The Day", "Somewhere I Belong",
			"The Catalyst", "The Little Things Give You Away", "The Messenger", "The Radiance", "Until It Breaks",
			"Valentine's Day", "Victimized", "Waiting for the End", "Wake", "Wastelands", "What I've Done",
			"Wisdom, Justice, and Love", "With You", "Wretches and Kings"]
	},
	{
		name: 'watch', products: ["BEN SHERMAN", "BEN SHERMAN LONDON", "BENCH", "BERING", "BIRLINE", "BOCCIA", "BRAUN",
		"BRAUN CLOCKS", "BREIL", "BREO", "BULOVA", "BULOVA ACCUSWISS", "BULOVA ACCUTRON", "BURBERRY", "CAMDEN WATCH COMPANY",
		"CANDINO SWISS", "CANNIBAL", "CARAVELLE NEW YORK", "CHARACTER", "CHEAPO", "COACH", "CROSS", "DAISY DIXON",
		"DAISY KNIGHTS", "DAVOSA", "ACCESSORIZE", "ACCURIST", "ADIDAS", "ADIDAS PERFORMANCE", "ANIMAL", "ANNE KLEIN",
		"ARMANI EXCHANGE", "AVIA", "FESTINA", "FIORELLI", "FIRETRAP", "FIYTA", "MARC JACOBS", "MARK MADDOX", "MARTIAN",
		"MAY 28TH", "MICHAEL KORS", "MICHEL HERBELIN", "MORGAN", "MOSHI MONSTERS", "MOVADO BOLD", "LA MER", "LACOSTE",
		"LAMBRETTA", "LARS LARSEN", "LG", "LIFEMAX", "LIMIT", "LIPSY", "LITTLE MISTRESS", "LOTUS", "LTD WATCH", "LULU GUINNESS"]
	}, {
		name: 'glasses',
		products: ['Dakota Smith Southern', 'Value Cassini 1264', 'Lacoste La 12204', 'Nautica N8024', 'Oakley Showdown OX1098', 'Modo 947']
	}, {
		name: 'software', products: ["3D Movie Maker", "Aladdin4D", "Anim8or", "Art of Illusion", "Autodesk 3ds Max",
			"Autodesk Maya", "Autodesk MotionBuilder", "Autodesk Softimage", "Adobe Photoshop", "Adobe After Effects",
			"Blender", "Carrara", "Cinema 4D", "Clara.io", "DAZ Studio", "Electric Image Animation System", "Houdini",
			"iClone", "K-3D", "LightWave 3D", "Messiah", "MikuMikuDance", "MilkShape 3D", "Modo", "Moviestorm", "Muvizu", "Poser",
			"Shade 3D	", "SketchUp", "Source Filmmaker", "ZBrush", "Balance of Power", "Lemonade Stand", "Number Munchers",
			"Odell Lake", "Spellevator", "Windfall: The Oil Crisis Game", "Word Munchers"]
	}, {
		name: 'bike',
		products: ["Siambretta", "Zanella", "Sommer", "Windhoff", "Sachs", "Honda", "IZH", "CBR110c", "Carberry Enfield"]
	}, {
		name: 'car', products: [
			"Abarth", "Alfa Romeo", "Ferrari", "Fornasari", "Fiat", "Lamborghini", "Lancia", "Maserati", "Honda", "Mazda",
			"Mitsubishi", "Nissan", "Suzuki", "Toyota", "Subaru", "Mustang Max", "Beattie", "Carlton", "Chevron", "Cobra",
			"Crowther", "De Joux", "Dennison", "Everson", "Fraser", "Heron", "Hulme", "Leitch", "Marlborough", "McRae",
			"Mistral", "Redline", "Ascari", "Aston Martin", "Aston Martin Lagonda", "Bentley", "Bristol", "David Brown",
			"Jaguar ", "Land Rover", "Lotus", "Range Rover", "Rolls Royce ", "Buick", "Chrysler", "Chevrolet", "Cadillac",
			"Dodge", "Ford", "GMC", "Jeep", "Lincoln", "Ram", "Tesla Motors"]
	}
];

function genCategory() {
	var i = generateNumber(0, category.length - 1);
	return {i: i, name: category[i].name};
}

function genProductName(cat) {

	for (var i in category) if (category.hasOwnProperty(i)) {
		if (category[i].name === cat) {
			var p = generateNumber(0, category[i].products.length - 1);
			return {i: p + i * 1000, name: category[i].products[p]};
		}
	}
	throw "wrong category: " + cat;
}

function generateName() {
	var ho = ['An', 'Ánh', 'Ân', 'Âu', 'Ấu', 'Bá', 'Bạc', 'Bạch', 'Bàng', 'Bành', 'Bảo', 'Bế', 'Bì', 'Biện', 'Bình', 'Bồ',
		'Ca', 'Cái', 'Cam', 'Cao', 'Cát', 'Cầm', 'Cấn', 'Chế', 'Chiêm', 'Chu', 'Chung', 'Chương', 'Chử', 'Cổ', 'Cù', 'Cung',
		'Cự', 'Dã', 'Danh', 'Diêm', 'Doãn', 'Diệp', 'Đàm', 'Đan', 'Đào', 'Đậu', 'Điền', 'Đinh', 'Đoàn', 'Đôn', 'Đồng', 'Đổng',
		'Đới', 'Đường', 'Giả', 'Giao', 'Giang', 'Giáp', 'Hà', 'Hạ', 'Hàn', 'Hán', 'Hy', 'Hình', 'Hoa', 'Hồng', 'Hùng', 'Hứa',
		'Kha', 'Khương', 'Khâu', 'Khoa', 'Khổng', 'Khu', 'Khuất', 'Khúc', 'Kiều', 'Kim', 'La', 'Lạc', 'Lại', 'Lâm', 'Lều',
		'Liễu', 'Lò', 'Lục', 'Lư', 'Lã', 'Lương', 'Lưu', 'Ma', 'Mã', 'Mạc', 'Mạch', 'Mai', 'Mang', 'Mâu', 'Mẫn', 'Mộc',
		'Ninh', 'Nhâm', 'Ngân', 'Nghiêm', 'Nghị', 'Ngụy', 'Nhữ', 'Nông', 'Ong', 'Ông', 'Phi', 'Phí', 'Phó', 'Phùng',
		'Phương', 'Quản', 'Quách', 'Sầm', 'Sơn', 'Sử', 'Tạ', 'Tào', 'Tăng', 'Thạch', 'Thái', 'Thành', 'Thào', 'Thân', 'Thập',
		'Thi', 'Thiều', 'Thịnh', 'Thôi', 'Tiêu', 'Tiếp', 'Tòng', 'Tô', 'Tôn', 'Tông', 'Tống', 'Trang', 'Trà', 'Trác', 'Triệu',
		'Trịnh', 'Trình', 'Trưng', 'Trương', 'Từ', 'Ty', 'Uông', 'Văn', 'Vi', 'Viên', 'Vương', 'Xa', 'Yên', 'Ngọc', 'Liêu', 'Lỗ'];

	var ten = ['Mỹ', 'Duệ', 'Tăng', 'Cường', 'Tráng', 'Liên', 'Huy', 'Phát', 'Bội', 'Hương', 'Linh', 'Nghi', 'Hàm', 'Tốn',
		'Thuận', 'Vĩ', 'Vọng', 'Biểu', 'Khôn', 'Quang', 'Lương', 'Kiến', 'Ninh', 'Hòa', 'Thuật', 'Du', 'Hành', 'Suất', 'Nghĩa',
		'Phương', 'Dưỡng', 'Di', 'Tương', 'Thực', 'Hảo', 'Cao', 'Túc', 'Thể', 'Vi', 'Tường', 'Tịnh', 'Hoài', 'Chiêm', 'Viễn',
		'Ái', 'Cảnh', 'Ngưỡng', 'Mậu', 'Thanh', 'Kha', 'Nghiễm', 'Khác', 'Do', 'Trung', 'Ðạt', 'Liên', 'Trung', 'Tập', 'Cát',
		'Ða', 'Diên', 'Hội', 'Phong', 'Hanh', 'Hiệp', 'Trùng', 'Phùng', 'Tuấn', 'Lãng', 'Nghi', 'Hậu', 'Lưu', 'Thành', 'Tú',
		'Diệu', 'Diễn', 'Khánh', 'Thích', 'Phương', 'Huy', 'Từ', 'Thể', 'Dương', 'Quỳnh', 'Cẩm', 'Phu', 'Văn', 'Ái', 'Diệu',
		'Dương', 'Bách', 'Chi', 'Quân', 'Phụ', 'Dực', 'Vạn', 'Diệp', 'Hiệu', 'Khuông', 'Tương'];
	return ho[Math.floor(Math.random() * 100) % ho.length] + ' ' + ten[Math.floor(Math.random() * 100) % ten.length]
}

//function generateNumber(min, max) {
//	var range = max - min;
//
//	var number = Math.floor(Math.random() * (range + 1)) + min;
//	return number;
//}

function generateDB(appid, actiontype, converter, url, n, collection, typeid, valuemgr, callback) {
	var count = 0;
	if (actiontype === 'purchase') {
		converter.toIDs(['_os', '_browser', '_device', '_lang', '_utm_campaign', '_utm_source', '_utm_medium', '_utm_content', '_utm_term','_typeid', '_ctime', 'amount', 'pname', 'cname', 'paymentype', '_mtid', 'cid', 'pid', 'pname', 'cname', 'quantity', 'price', '_segments', '_city'], function (ids) {
			for (var i = 0; i < n; i++) {
				count++;
				var r = generatePurchase(ids, typeid);
				valuemgr.cineObject(appid, "purchase", r);
				db.collection(collection).insertOne(r).then(function (results) {
					count--;
					if (count % 1000 === 0)
						console.log((n - count) + ' records');
					if (count === 0) {
						db.close();
						console.log('Done');
						callback();
					}
				}).catch(function (err) {
					console.log('[MongoDB] insert err', err.message);
				});
			}
		});
	} else {
		converter.toIDs(['_os', '_browser', '_lang', '_device','_utm_campaign', '_utm_source', '_utm_term','_utm_content','_utm_medium','_typeid', '_url', 'camid', '_ctime', '_mtid', '_segments'], function (ids) {
			for (var i = 0; i < n; i++) {
				count++;
				var r = generatePageView(ids, typeid);
				valuemgr.cineObject(appid, "pageview", r);
				db.collection(collection).insertOne(r).then(function (results) {
					count--;
					if (count % 1000 === 0)
						console.log((n - count) + ' records');
					if (count === 0) {
						db.close();
						console.log('Done');
						callback();
					}
				}).catch(function (err) {
					console.log('[MongoDB] insert err', err.message);
				});
			}
		});
	}
}

function randomNumber(a, b) {
	if (b === undefined) {
		b = a;
		a = 0;
	}
	var delta = b - a + 1;
	return Math.floor(Math.random() * delta) + a;
}

var h = [];

function generateNumber(a, b) {
	var delta = b - a + 1;
	var i;
	if (h.length === 0 || h.length < delta) {
		//init hash

		for (i = 0; i < delta; i++) {
			if (i == 0) h[i] = randomNumber(0, 20);
			else h[i] = h[i - 1] + randomNumber(0, 20);
		}
	}
	var r = randomNumber(0, h[delta - 1]);

	for (i = 0; i < delta; i++)
		if (h[i] >= r) return i + a;
	return 'fuck';
}