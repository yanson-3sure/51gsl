

// 初始化iScroll插件
var myScroll,
		pullDownEl, pullDownOffset,
		pullUpEl, pullUpOffset,
		generatedCount = 0;

function loaded() {
	pullDownEl = document.getElementById('pullDown');
	pullDownOffset = pullDownEl ? pullDownEl.offsetHeight : 0;
	pullUpEl = document.getElementById('pullUp');
	pullUpOffset = pullUpEl.offsetHeight;

	myScroll = new iScroll('wrapper',{
		click:true,
		scrollbars: true,
		topOffset: pullDownOffset,
		onRefresh: function () {
			if (pullDownEl && pullDownEl.className.match('loading')) {
				pullDownEl.className = '';
				pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
			} else if (pullUpEl.className.match('loading')) {
				pullUpEl.className = '';
				pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
			}
		},
		onScrollMove: function () {
			if (this.y > 5 && pullDownEl && !pullDownEl.className.match('flip')) {
				pullDownEl.className = 'flip';
				pullDownEl.querySelector('.pullDownLabel').innerHTML = '松手开始更新...';
				this.minScrollY = 0;
			} else if (this.y < 5 && pullDownEl && pullDownEl.className.match('flip')) {
				pullDownEl.className = '';
				pullDownEl.querySelector('.pullDownLabel').innerHTML = '下拉刷新...';
				this.minScrollY = -pullDownOffset;
			} else if (this.y < (this.maxScrollY - 5) && !pullUpEl.className.match('flip')) {
				pullUpEl.className = 'flip';
				pullUpEl.querySelector('.pullUpLabel').innerHTML = '松手开始更新...';
				this.maxScrollY = this.maxScrollY;
			} else if (this.y > (this.maxScrollY + 5) && pullUpEl.className.match('flip')) {
				pullUpEl.className = '';
				pullUpEl.querySelector('.pullUpLabel').innerHTML = '上拉加载更多...';
				this.maxScrollY = pullUpOffset;
			}
		},
		onScrollEnd: function () {
			if (pullDownEl && pullDownEl.className.match('flip')) {
				pullDownEl.className = 'loading';
				pullDownEl.querySelector('.pullDownLabel').innerHTML = '加载中...';
				pullDownAction();   // ajax call
			} else if (pullUpEl.className.match('flip')) {
				pullUpEl.className = 'loading';
				pullUpEl.querySelector('.pullUpLabel').innerHTML = '加载中...';
				pullUpAction(); // ajax call
			}
		}
	});
	/* 新增 */
	setTimeout(function () { document.getElementById('wrapper').style.left = '0'; }, 800);
}
function iscrollInit() {
	document.addEventListener('touchmove', function (e) {
		e.preventDefault();
	}, false);
// document.addEventListener('DOMContentLoaded', loaded, false);
	/* 新增 */
	document.addEventListener('DOMContentLoaded', function () {
		setTimeout(loaded, 200);
	}, false);
}

iscrollInit();
var pullDownAction_exec = function(callback){
	var pullUpEl = document.getElementById('pullDown');
	if(!pullUpEl) return;
	var _this = $("#pullDown");
	var url = _this.attr('data-url');
	var append_object = _this.attr('data-append-object') ? _this.attr('data-append-object') : '#thelist';
	var min = _this.attr('data-min');
	//var isMore = _this.attr('data-is-more');
	//if(isMore=='0') return;
	//console.log(min);
	$.get(url,{min:min}, function (data) {
		if(data.min>0){
			$(append_object).prepend(data.content);
			_this.attr('data-min', data.min);
			if (!data.isMore) {
				//_this.hide();
				//_this.attr('data-is-more','0');
			}
			if (callback) {
				callback(data);
			}
		}
		myScroll.refresh();
	});
}
var pullUpAction_exec = function(callback){
	var pullUpEl = document.getElementById('pullUp');
	if(!pullUpEl) return;
	var _this = $("#pullUp");
	var url = _this.attr('data-url');
	var append_object = _this.attr('data-append-object') ? _this.attr('data-append-object') : '#thelist';
	var max = _this.attr('data-max');
	var isMore = _this.attr('data-is-more');
	if(isMore=='0') return;
	$.get(url,{max:max}, function (data) {
		if(data.max>0) {
			$(append_object).append(data.content);
			_this.attr('data-max', data.max);
			if (!data.isMore) {
				//_this.hide();
				_this.attr('data-is-more', '0');
			}
			if (callback) {
				callback(data);
			}
		}
		if (myScroll)
			myScroll.refresh();
	});
}
function callback(){}
function pullUpAction () {
	scroll_lock = true;
	pullUpAction_exec(callback);
	scroll_lock = false;
}
function pullDownAction () {
	scroll_lock = true;
	pullDownAction_exec(callback);
	scroll_lock = false;
}
// 滚动区域高度小于屏幕高度时，隐藏底部加载区，反之则显示
function hideBottom(){
	var H = window.screen.height;
	var scroll = document.getElementById("scroller");
	var bottom = document.getElementById("pullUp");
	var h = scroll.offsetHeight;
	if(h > H){
		bottom.style.opacity = 1;
	}else{
		bottom.style.opacity = 0;
	}
}
hideBottom();
