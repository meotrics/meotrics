0 start: bắt đầu trạng thái
	starttime = thời gian hiện tại
	lastactive = thời gian hiện tại
	totaltime = 0
	
1 interact: phát sinh khi người dùng đang ở trên site, thực hiện tương tác (di chuyển chuột, gõ phím, ...)
	totalidle = 0

2 leave: occurs when user switch to other tab or have lost focus with current tab
	pause background process by setting isfocus = false
	leavetime = current time

3 return: occurs when user focus back to tab
	if the user has left for too long (more than 20mins)
		stop the session with length is totaltime
		do event 0
	else, 
		reset totalidle counter		
		start the background process by setting isfocus to true

4 did event: event is occured when user not focus in the page
	do nothing

5,6 close:
	stop the session with length is totaltime + delta
	delta is number of second occursed from the last cycle of background process

7 start and idle
	do nothing

background process run only in [On page], if user is still on the page, (isfocus equals true)
	each 2 sec, increase totalidle and totaltime by 2
	// có thể đổi 2s thành 5, 10 , 20s tuy nhiên số càng lớn, sai số càng lớn
	if totalidle is greater then 20mins then
		stop the session with length is max of totaltime and 20mins
	else
		just do nothing
	
