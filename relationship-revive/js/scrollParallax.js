function onlyUnique(e, t, s) {

   return s.indexOf(e) === t

}

jQuery(document).ready((function (e) {

   const t = (e, t) => String(e).padStart(t, "0");

   var s = !1;

   e(window).scroll((function (e) {

       s || window.requestAnimationFrame((function () {

           l(), s = !1

       })), s = !0

   }));

   var o = e(".takeover-menu.successes .content-items .content-item");

   e(".takeover-menu.successes .total-count").text(t(e(o).length, 2)), e(".takeover-menu.successes").scroll((function () {

       var s = window.innerHeight / 2;

       o.each((function () {

           var o = Math.floor(e(this)[0].getBoundingClientRect().top),

               a = Math.floor(o + e(this).outerHeight());

           s >= o && s < a ? (rowData = e(this).data("id"), e(".takeover-menu.successes .current-count").text(t(rowData, 2)), e(".takeover-menu.successes .content-item").removeClass("active"), e(this).addClass("active"), e('.takeover-menu.successes .content-right img[data-id="' + rowData + '"]').css("opacity", 1)) : (rowData = e(this).data("id"), e('.takeover-menu.successes .content-right img[data-id="' + rowData + '"]').css("opacity", 0))

       })), console.log("----")

   }));

   var a = e(".content-module.successes .content-items .content-item");

   e(".content-module.successes .total-count").text(t(e(a).length, 2));

   var n = e(".support-cta").length > 0 ? e(".support-cta").offset().top : null,

       i = e(".donate-cta").length > 0 ? e(".donate-cta").offset().top : null;

   var c = e(".end-of-page-hero").length > 0,

       r = [];



   function l() {

       var s, o, l, d;

       ! function (e) {

           for (var t, s, o, a, n = jQuery(e), i = 0; i < n.length; i++) {

               var c = jQuery(n[i]);

               t = void 0, s = void 0, o = void 0, a = void 0, s = (t = c[0].getBoundingClientRect()).top, o = t.bottom, s + (a = t.height) >= 0 && a + window.innerHeight >= o + 40 && (c.removeClass("fade-deactive"), c.addClass("fade-active"))

           }

       }(r.join(", ")), window.scrollY > 15 ? (e("header").removeClass("base"), e("header .button").addClass("blue")) : (e("header").addClass("base"), e("header .button").removeClass("blue")), null == n && null == i || (window.scrollY + window.innerHeight > e("main").offset().top + e("main").outerHeight() ? e(".support-cta, .donate-cta").addClass("show") : e(".support-cta, .donate-cta").removeClass("show")), c && (s = e("main").offset().top + e("main").height(), window.scrollY + .75 * window.innerHeight > s ? e(".end-of-page-hero").addClass("animate") : e(".end-of-page-hero").removeClass("animate")), e("body").hasClass("home") && (o = window.scrollY + window.innerHeight / 2, l = window.scrollY + window.innerHeight, d = e(".content-module.successes").height() + e(".content-module.successes").offset().top + window.innerHeight, window.scrollY > d ? (e(".support-cta, .donate-cta").addClass("show"), e(".content-module.successes").addClass("hide")) : (e(".content-module.successes").removeClass("hide"), e(".support-cta, .donate-cta").removeClass("show")), l > e(".content-module.successes").offset().top ? e(".content-module.successes .backgrounds .overlay").css("opacity", 1) : e(".content-module.successes .backgrounds .overlay").css("opacity", 0), a.each((function () {

           rowData = e(this).data("id"), o > e(".content-module.successes .content-items").height() + e(".content-module.successes .content-items").offset().top ? e(".content-module.successes .backgrounds img:last-child").css("opacity", "1") : o < e(".content-module.successes .content-items").offset().top ? e(".content-module.successes .backgrounds img:nth-of-type(1)").css("opacity", "1") : e(this).offset().top < o && e(this).offset().top + e(this).outerHeight() > o ? (e(".content-module.successes .current-count").text(t(rowData, 2)), e(".content-module.successes .content-item").removeClass("active"), e(this).addClass("active"), e('.content-module.successes .content-right img[data-id="' + rowData + '"]').css("opacity", 1), e('.content-module.successes .backgrounds img[data-id="' + rowData + '"]').css({

               opacity: 1,

               transform: "scale(1)"

           })) : (e('.content-module.successes .content-right img[data-id="' + rowData + '"]').css("opacity", 0), e('.content-module.successes .backgrounds img[data-id="' + rowData + '"]').css({

               opacity: 0,

               transform: "scale(1.1)"

           }))

       })), window.scrollY > 0 ? e(".hero-home").addClass("scrolled") : e(".hero-home").removeClass("scrolled")), e(".chapter-hero").length > 0 && (window.scrollY > 0 ? e(".chapter-hero").addClass("scrolled") : e(".chapter-hero").removeClass("scrolled"), window.scrollY > .5 * window.innerHeight ? (e(".chapter-hero .bg-images img").css("opacity", 0), e(".chapter-hero .bg-images .overlay").css("opacity", "0.3")) : (e(".chapter-hero .bg-images img").css("opacity", ""), e(".chapter-hero .bg-images .overlay").css("opacity", "0.1")), window.scrollY > e(".chapter-hero").height() ? e(".chapter-hero").css("visibility", "hidden") : e(".chapter-hero").css("visibility", "")), e(".quote").length > 0 && e(".quote").each((function () {

           window.scrollY + .75 * window.innerHeight > e(this).offset().top && e(this).addClass("reveal")

       })), e(".page-break").length > 0 && e(".page-break").each((function () {

           window.scrollY - 70 > e(this).offset().top ? e(this).addClass("reveal") : e(this).removeClass("reveal"), window.scrollY >= e(this).find(".page-break--container").offset().top ? e(this).addClass("scroll-active") : e(this).removeClass("scroll-active"), window.scrollY >= e(this).offset().top + e(this).outerHeight() ? e(this).css("visibility", "hidden") : e(this).css("visibility", "")

       })), e(".page-break-large").length > 0 && e(".page-break-large").each((function () {

           if (window.scrollY >= e(this).offset().top) {

               var t = 1 + .6 * (window.scrollY / e(this).offset().top - 1);

               t = t < 1 ? 1 : t, e(this).addClass("scroll-active"), e(this).find(".break-image").css("transform", "scale(" + t + ")")

           } else e(this).removeClass("scroll-active"), e(this).find(".break-image").css("transform", "");

           window.scrollY >= e(this).offset().top + e(this).outerHeight() ? e(this).css("visibility", "hidden") : e(this).css("visibility", "")

       })), e(".count-up").length > 0 && e(".count-up").each((function () {

           window.scrollY + window.innerHeight > e(this).offset().top && !e(this).hasClass("counted") && (e(this).addClass("counted"), e(this).find(".counter").each((function () {

               var t = e(this),

                   s = t.attr("data-count");

               e({

                   countNum: t.text()

               }).animate({

                   countNum: s

               }, {

                   duration: 2e3,

                   easing: "swing",

                   step: function () {

                       t.text(Math.floor(this.countNum))

                   },

                   complete: function () {

                       t.text(this.countNum)

                   }

               })

           })))

       })), e(".data-viz").length > 0 && e(".data-viz").each((function () {

           window.scrollY + .5 * window.innerHeight >= e(this).offset().top && e(this).addClass("scroll-active")

       })), e(".has-animation").length > 0 && e(".has-animation").each((function () {

           if (window.scrollY + window.innerHeight > e(this).offset().top) {

               var t = .05 * (window.scrollY + window.innerHeight - e(this).offset().top) - 65;

               e(this).find("figure img").css({

                   transform: "scale(1.2) translateY(" + t + "px)"

               })

           }

       }))

   }

   e(".ns-fade").each((function () {

       r.push(".ns-fade " + e(this).data("target"))

   })), e().ready(l)

}));
