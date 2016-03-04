//Thiết kế bloom filter lư trữ user segment
/*

Số lượng hàm hash là k,
Số lượng bit output của hàm hash là m

Hệ thống, số user hiếm khi vượt quá n=600000, sai số đặt ra là không được nhiều hơn 10%
Công thức tính sai số trong Bloom Filter là 
p = (1-e^(-kn/m))^k
n = 600000, p=0.1. Chọn được k tối ưu trong đoạn [3,4] -> lấy 3, xem https://www.wolframalpha.com/input/?i=plot%5B+(x*600000)%2F-(ln%5B1+-+0.05%5E(1%2Fx)%5D),+%7Bx,1,+10%7D%5D
Lấy 3 vì giảm dc môt lần tính hash mà số lượng bit m cũng không tăng mấy

	Có k = 3 -> m sấp xỉ 3 920 000 bit = 478.5 KB

Từ giá trị m này có thể vẽ được hàm tính sai số.
https://www.wolframalpha.com/input/?i=plot+%5B100*(1-e%5E(-3*x%2F3920000)%5E3),+%7Bx,0,200000%7D%5D
Sai số tăng nhanh theo hàm mũ,
Khi lên tới 200k user, sai số khoảng 0.4%, 400k user, sai số khoảng 2.7%
Khi lên tới 800k user (vượt 600k), quá ngưỡng đảm bảo sai số < 5%, sai số tăng cực nhanh: 20%,
1 M user, sai số lâ 40% ! gần một nữa. Đây có thể coi là nhược điểm cần cân nhắc trước khi dùng
bloom filter. Khi vượt quá ngưỡng cho phép, chi phí cho bloom filter rất chát.
*/