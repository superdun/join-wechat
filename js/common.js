$(function () {
    /*导航滑块滚动*/
    //$("nav .wrap").lavaLamp({speed: 200});

        /*导航下拉菜单*/
    //$('.subNav').width($(window).width());
    //$('.nav li').hover(function(){
    //    var _this = $(this);
    //    _this.siblings().find('.subNav').slideUp(0);
    //    //_this.find('a').addClass('active');
    //    _this.find('.subNav').slideDown(0);
    //},function(){
    //    $(this).find('.subNav').slideUp(0);
    //    //$(this).find('a').removeClass('active');
    //});

        /*滚动到目标位置简洁版*/
        /*$('#subNav a').click(function(){
         var _this = $(this), _top = $('article section').eq(_this.index()).offset().top;
         $('html,body').animate({scrollTop: _top - 75}, 500);
         return false;
         });*/


        $(".online li:not(:last)").hover(function(){
            var _width = "124px", _this = $(this);

            if(_this.attr('data-type') == "tel") {
                _width = "150px";
            }

            if(_this.attr('data-type') == "wechat"){
                _this.find('.wechat').addClass('on');
            } else {
                _this.find('a').stop().animate({"width":_width},200).css({"opacity":"1","background":"#00a7ff"});
            }
        },function(){
            if($(this).attr('data-type') == "wechat"){
                $(this).find('.wechat').removeClass('on');
            } else {
                $(this).find('a').stop().animate({"width":"54px"},200).css({"opacity":"0.8","background":"#000"});
            }
        });

        /*向上滚动*/
        $(window).scroll(function() {
            if ($(window).scrollTop() >= 200) {
                $(".scrollTop").fadeIn(200);
            } else {
                $(".scrollTop").fadeOut(100);
            }
        });
        $(".scrollTop").click(function() {
            $('body,html').animate({scrollTop: 0}, 500);
            return false
        });

    ///*图片预加载*/
    //$("img.lazy").lazyload({
    //    placeholder : "images/grey.gif",
    //    data_attribute : "src",
    //    effect : "fadeIn",
    //    failure_limit: 20
    //    //threshold :-50
    //});
});
