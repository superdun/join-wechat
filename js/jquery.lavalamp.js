/** jquery.lavalamp.min */
(function ($) {
    $.fn.lavaLamp = function (o) {
        o = $.extend({
            fx: "linear", speed: 500, click: function () {
            }
        }, o || {});
        return this.each(function () {
            var b = $(this), noop = function () {
            }, $back = $('<div class="nav-line"></div>').appendTo(b), $li = $("li", this), curr = $("li.active", this)[0] || $($li[0]).addClass("active")[0];
            $li.not(".nav-line").hover(function () {
                move(this)
            }, noop);
            $(this).hover(noop, function () {
                move(curr);
            });
            $li.click(function (e) {
                setCurr(this);
                return o.click.apply(this, [e, this])
            });
            setCurr(curr);
            function setCurr(a) {
                $back.css({"left": a.offsetLeft + "px", "width": a.offsetWidth + "px"});
                curr = a
            };
            function move(a) {
                $back.each(function () {
                    $.dequeue(this, "fx")
                }).animate({width: a.offsetWidth, left: a.offsetLeft}, o.speed, o.fx)
            }
        })
    }
})(jQuery);