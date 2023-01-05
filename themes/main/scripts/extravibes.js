$(window).load(function(){
    startNextVideo = function() {
		if($('#autoplayHandler').is(":checked")){

        if ($("li#playingNow").html()) {
            var nextPlay = $("li#playingNow").next();
        } else {
            if ($("#autoplay").html()) {
                var nextPlay = $("#autoplay").parent('li');
            }
        }
		//console.log(nextPlay);
        if (typeof nextPlay != 'undefined') {
            nextPlay.addClass('imNext').prepend('<a href="#" id="btnCancel" class="btn btn-success btn-xs">' + acanceltext + '</a>');
            nextPlayUrl = nextPlay.find("a.clip-link").attr("href");
            moveToNext = function() {
                if (nextPlayUrl.length) {
                    window.location.href = nextPlayUrl;
                }
            };
            var timeoutId = setTimeout(moveToNext, 5000);
            $('a#btnCancel').unbind("click").click(function() {
                clearTimeout(timeoutId);
                nextPlay.removeClass('imNext');
                $('a#btnCancel').remove();
                return false;
            });

        }
	}
    }

    if ($("#suggest-results").length) {
        $(".header input[name=tag]").keyup(function() {
            var searched = $(this).val();
            var gghref = 'https://suggestqueries.google.com/complete/search?hl=en&ds=yt&client=youtube&hjson=t&cp=1&q=' + searched;
            var result;
            $.ajax({
                url: gghref,
                type: "POST",
                dataType: 'jsonp',
                success: function(data) {
                    for (var i = 1; i < data[1].length; i++) {
                        if (data[1][i][0].length && data[1][i]) {
                            result += '<li class="gsuggested"><a href="#">' + data[1][i][0] + '</a></li>';
                        }
                    }
                    $("#suggest-results").html("<ul>" + result.replace("undefined", "") + "</ul>");
                    $('.gsuggested > a').click(function() {
                        var valoare = $(this).text();
                        $(".header input[name=tag]").val(valoare).focus();
						$("#suggest-results").html('&nbsp;');
                        return false;
                    });
                }
            });
        });
    }


});