(function($) {
    $.fn.OnlineQQ = function(options) {
        var opts = {position: "fixed",btntext: "\u5ba2\u670d\u5728\u7ebf",qqs: [{name: "\u61d2\u4eba\u5efa\u7ad9",qq: "12345678"}],tel: "",more: null,kftop: "120",z: "99999",defshow: true,Event: "",callback: function() {
            }}, $body = $("body"), $url = "";
        $.extend(opts, options);
        if (!$("#lanrenzhijiawarp").length > 0) {
            $body.append("<div id='lanrenzhijiawarp' class='lanrenzhijia lanrenzhijiashow' style=" + opts.position + "><a href='#' class='lanrenzhijia_btn lanrenzhijia_btn_hide' id='lanrenzhijia_btn' onfocus='this.blur()'>" + opts.btntext + "</a><div class='lanrenzhijia_box'><div class='lanrenzhijia_header'><a href='#' title='\u5173\u95ed' class='x' id='lanrenzhijia_x'></a></div><div class='lanrenzhijia_con' id='lanrenzhijia_con'><ul class='kflist'></ul></div><div class='lanrenzhijia_foot'></div></div></div><style>html{overflow-x:hidden;}#lanrenzhijiawarp ul{padding-left:0; margin:0;list-style-type: none;}.lanrenzhijia{font-size:13px;position:fixed;}.lanrenzhijia a{ display:block; color:#666; text-decoration:none; font-size:13px;}#lanrenzhijiawarp img{ border:none;vertical-align:middle; margin-right:4px; margin-top:-2px;display:inline;}.lanrenzhijia_con{padding:6px 8px;}.lanrenzhijia_con li.qq{padding:5px 0;}.lanrenzhijia_con li.tel{ line-height:1.35; padding-bottom:4px;}.lanrenzhijia_con li.more{ padding:2px 0;}.lanrenzhijia_con li.tel b{ display:block; color:#C00;}.lanrenzhijia_tool a{ display:block; padding:8px 10px; text-align:center;}.lanrenzhijia_con .hr{padding:0;height:0;font-size:0;line-height:0;clear:both;margin:4px 0;border-bottom:#fff solid 1px;border-top:#CFCFCF solid 1px;border-left:none;border-right:none;}.lanrenzhijia_btn{position:absolute; top:20px;width:22px;left:-22px;display:block;text-align:center;padding:10px 0;}.lanrenzhijia .lanrenzhijia_xc{ position:absolute; bottom:-14px; right:6px;font-size:10px;display:none;}</style>")
        }
        var $lanrenzhijiawarp = $("#lanrenzhijiawarp"), $lanrenzhijia_con = $("#lanrenzhijia_con"), $kflist = $lanrenzhijia_con.children("ul"), $lanrenzhijia_x = $("#lanrenzhijia_x"), $lanrenzhijia_btn = $("#lanrenzhijia_btn"), $lanrenzhijiawarp_w = $lanrenzhijiawarp.outerWidth() * 1 + 1;
        $lanrenzhijiawarp.css({top: opts.kftop + "px","z-index": opts.z});
        if (!opts.defshow) {
            $lanrenzhijiawarp.removeClass("lanrenzhijiashow").css({right: -$lanrenzhijiawarp_w})
        }
        var json = {options: opts.qqs};
        json = eval(json.options);
        $.each(json, function(i, o) {
            $kflist.append("<li class=qq><a target=_blank href=http://wpa.qq.com/msgrd?v=3&uin=" + o.qq + "&site=qq&menu=yes><img src=http://wpa.qq.com/pa?p=2:" + o.qq + ":45>" + o.name + "</a></li>")
        });
        if (opts.tel) {
            $kflist.append("<li class=hr></li>");
            var json_tel = {options: opts.tel};
            json_tel = eval(json_tel.options);
            $.each(json_tel, function(i, o) {
                $kflist.append("<li class=tel>" + o.name + ":<b>" + o.tel + "</b></li>")
            })
        }
        if (opts.more) {
            $kflist.append("<li class=hr></li><li class=more><a href='" + opts.more + "'>>>\u66f4\u591a\u65b9\u5f0f</a></li>")
        }
        var $lanrenzhijiawarptop = $lanrenzhijiawarp.offset().top;
        if ( opts.position == "absolute") {
            $(window).scroll(function() {
                var offsetTop = $lanrenzhijiawarptop + $(window).scrollTop() + "px";
                $lanrenzhijiawarp.animate({top: offsetTop}, {duration: 600,queue: false})
            })
        }
        $lanrenzhijia_x.click(function() {
            $lanrenzhijiawarp.hide();
            return false
        });
        if (opts.Event == "") {
            $lanrenzhijiawarp.mouseenter(function() {
                $(this).stop().animate({right: 0})
            }).mouseleave(function() {
                $(this).stop().animate({right: -$lanrenzhijiawarp_w})
            })
        } else {
            $lanrenzhijia_btn.on("click", function() {
                if ($lanrenzhijiawarp.hasClass("lanrenzhijiashow")) {
                    $lanrenzhijiawarp.animate({right: -$lanrenzhijiawarp_w}, function() {
                        $lanrenzhijiawarp.removeClass("lanrenzhijiashow")
                    })
                } else {
                    $lanrenzhijiawarp.addClass("lanrenzhijiashow").animate({right: 0})
                }
                return false
            })
        }
    }
})(jQuery);