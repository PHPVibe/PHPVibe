/*!
 * PHPVibe 
 *
 * Copyright MediaVibe (PHPVibe.com)
 * http://www.phpVibe.com
 */
 
jQuery(function($){	
 $('.form-material').each(function() {
        var $this = $(this);
        if ($this.data('material') === true) {
            return;
        }
        var $control = $this.find('.form-control');
        // Add hint label if required
        if ($control.attr("data-hint")) {
            $control.after("<div class=hint>" + $control.attr("data-hint") + "</div>");
        }
        if ($this.hasClass("floating")) {
            // Add floating label if required
            if ($control.hasClass("floating-label")) {
                var placeholder = $control.attr("placeholder");
                $control.attr("placeholder", null).removeClass("floating-label");
                $control.after("<div class=floating-label>" + placeholder + "</div>");
            }
            // Set as empty if is empty
            if ($control.val() === null || $control.val() == "undefined" || $control.val() === "") {
                $control.addClass("empty");
            }
        }
       
        $this.data('material', true);
    });
	 
    $('.form-material-file [type=file]').on("focus", function() {
        $(".form-material-file .form-control").addClass("focus");
    });
    $('.form-material-file [type=file]').on("blur", function() {
        $(".form-material-file .form-control").removeClass("focus");
    });
    $('.form-material-file [type=file]').on("change", function() {
        var value = "";
        $.each($(this)[0].files, function(i, file) {
            value += file.name + ", ";
        });
        value = value.substring(0, value.length - 2);
        if (value) {
            $(this).prev().removeClass("empty");
        } else {
            $(this).prev().addClass("empty");
        }
        $(this).prev().val(value);
    });
    $('.form-control').on("keyup", function() {
        var $this = $(this);
        if ($this.val() === "") {
            $this.addClass("empty");
        } else {
            $this.removeClass("empty");
        }
    });	

	
	$('#purgecache').click(function(){
		 event.preventDefault();
		href = $(this).attr('href');
		$("#purgeresult").load(href, function(data) {		});	

		
	});
	//confirm
	$('.confirm').click(function(){
		return confirm("Are you sure you want to delete this? This is permanent");
	});

      // Disable # function
      $('a[href="#"]').click(function(e){
        e.preventDefault();
      });


    //-----  Menu functions -----//

    // slide menu out from the left 
    $('.slide_menu_left').click(function(e){
        e.preventDefault();
        if($(".navbar").hasClass('open_left')){
          sidemenu_close();
        }else{
            sidemenu_open();
            $('.main_container').bind('click', function(){
                sidemenu_close();
            });
        }
    });

    // slide menu out
    function sidemenu_close(){
        $(".main_container").stop().animate({
            'left': '0'
        }, 250, 'swing');

        $(".navbar").stop().animate({
            'left': '-400px'
        }, 250, 'swing', function(){
            $(this).css('left', '').removeClass('open_left');
            $(this).children('.sidebar-nav').css('height', '');
        });

        $('.main_container').unbind('click');

        if(typeof handler != 'undefined'){
            $(window).unbind('resize', handler);
        }
    }

    // slide menu in
    function sidemenu_open(){
        $(".main_container").stop().animate({
            'left': '200px'
        }, 250, 'swing');
        $(".navbar").stop().animate({
            'left': '0'
        }, 250, 'swing').addClass('open_left');
        $('.navbar').animate('slow', function(){
            marginLeft:0
        });
    }

    $('.accordion-toggle').removeClass('toggled');
    // fade to white when clicked on mobile
    $('.accordion-toggle').click(function(){
      $('.accordion-toggle').removeClass('toggled');
      $(this).addClass('toggled');
    });
$('.tipN').tipsy({gravity: 'n',fade: true, html:true});
	$('.tipS').tipsy({gravity: 's',fade: true, html:true});
	$('.tipW').tipsy({gravity: 'w',fade: true, html:true});
	$('.tipE').tipsy({gravity: 'e',fade: true, html:true});
	
	$('.auto').autosize();
	$('.tags').tagsInput({width:'100%'});
	//===== Select2 dropdowns =====//

	$(".select").select2(  {width: '100%'});
	 //$(".select").minimalect();
				
	$('body').find('input[type=text]').each(function() {$(this).addClass('form-control');});	
	$('body').find('textarea').each(function() {$(this).addClass('form-control');});	
    $('body').find(':checkbox').each(function() {$(this).addClass('icheckbox-primary');});	
    $('body').find(':radio').each(function() {$(this).addClass('icheckbox-primary');});	
	$('input:not(.check-all,.check-all-notb)').iCheck({mode: 'default', checkboxClass: 'icheckbox_flat-blue',radioClass: 'iradio_flat-blue'});
	$('.pv_tip').tooltip();
	  $('#thumbad').click(function(){
      $('.the-thumbnails').toggleClass('hide');
     
    });
	 // Custom scrollbar plugin
	 
	  $('.scroll-items').slimScroll({height:500});
     //$(".video-player").fitVids();
  /* Dual select boxes */	
	$.configureBoxes();
	/* Ajax forms */
		$('.ajax-form').ajaxForm({
			success: function(data) { 
           //alert(data);			
        }
        });
	$("#validate").validationEngine({promptPosition : "topRight:-122,-5"});  

	$('#myTab a').click(function (e) {
  e.preventDefault();
  $(this).tab('show');
})
	
 });  
 
 $( window ).resize(function() {
if ($( window ).width() < 1160) { 
$( "#wrap" ).addClass( "SActive" );
}	
});
//Initial plugin
 !function(e){var t=function(e,t){var a,n=e.charCodeAt(t);return 55296>n||n>56319||e.length<=t+1||(a=e.charCodeAt(t+1),56320>a||a>57343)?e[t]:e.substring(t,t+2)},a=function(e,a,n){for(var i,r="",o=0,c=0,d=e.length;d>o;)i=t(e,o),c>=a&&n>c&&(r+=i),o+=i.length,c+=1;return r};e.fn.initial=function(t){var n,i=["#1abc9c","#16a085","#f1c40f","#f39c12","#2ecc71","#27ae60","#e67e22","#d35400","#3498db","#2980b9","#e74c3c","#c0392b","#9b59b6","#8e44ad","#bdc3c7","#34495e","#2c3e50","#95a5a6","#7f8c8d","#ec87bf","#d870ad","#f69785","#9ba37e","#b49255","#b49255","#a94136"];return this.each(function(){var r=e(this),o=e.extend({name:"Name",color:null,seed:0,charCount:1,textColor:"#ffffff",height:100,width:100,fontSize:60,fontWeight:400,fontFamily:"HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial,Lucida Grande, sans-serif",radius:0},t);o=e.extend(o,r.data());var c=a(o.name,0,o.charCount).toUpperCase(),d=e('<text text-anchor="middle"></text>').attr({y:"50%",x:"50%",dy:"0.35em","pointer-events":"auto",fill:o.textColor,"font-family":o.fontFamily}).html(c).css({"font-weight":o.fontWeight,"font-size":o.fontSize+"px"});if(null==o.color){var h=Math.floor((c.charCodeAt(0)+o.seed)%i.length);n=i[h]}else n=o.color;var f=e("<svg></svg>").attr({xmlns:"http://www.w3.org/2000/svg","pointer-events":"none",width:o.width,height:o.height}).css({"background-color":n,width:o.width+"px",height:o.height+"px","border-radius":o.radius+"px","-moz-border-radius":o.radius+"px"});f.append(d);var l=window.btoa(unescape(encodeURIComponent(e("<div>").append(f.clone()).html())));r.attr("src","data:image/svg+xml;base64,"+l)})}}(jQuery);
$(document).ready(function(){
  $('img').error(function() {
	  if (!$(this).attr('data-name')) {
         $(this).data('name', 'NA');
	  }
		 $(this).addClass("NoAvatar").initial({charCount:1});
    });
var rougueAvatars = $('img[src*="def-avatar.jpg"]');
	 $.each( rougueAvatars, function( key, value ) {
	 $(this).addClass("NoAvatar").initial({charCount:1});	  
     });
 
$('.NoAvatar').initial({charCount:1});	
if ($( window ).width() < 1160) {
$('#wrap').toggleClass('SActive');	
}	
var sidebarsh = screen.height - 60;	
$('.sidescroll').slimScroll(
{
        height: sidebarsh,
        position: 'right',
        size: 5,
		distance: '-5px',
        railOpacity: '0.1',
        color: '#eee',
        railColor: '#fff',
		wheelStep : 5,
		railVisible: false,
		alwaysVisible: false
    }
);	
// Initialize navgoco with default options
	var navmenu = $('.sidebar-nav > ul').first();
	$(navmenu).navgoco({
		caretHtml: '',
		accordion: true,
		openClass: 'open',
		save: true,
		cookie: {
			name: 'phpvibe-menu',
			expires: 1,
			path: '/'
		},
		slide: {
			duration: 400,
			easing: 'swing'
		},
		// Add Active class to clicked menu item
		onClickAfter: function(e, submenu) {
			e.preventDefault();
			$(navmenu).find('li').removeClass('active');
			var li =  $(this).parent();
			var lis = li.parents('li');
			li.addClass('active');
			lis.addClass('active');
		},
	});	
	//Table checks
	$('.multicheck .check-all').click(function(){
		$('.hidable').toggleClass('hide');
		var parentTable = $('.multilist');		
	
		var ch = parentTable.find('input[type=checkbox]');										 
		if($(this).is(':checked')) {
		
			//check all rows in table
			ch.each(function(){ 
				$(this).attr('checked',true);
				$(this).parent().addClass('checked');
			
			});
						
	
		} else {
			
			//uncheck all rows in table
			ch.each(function(){ 
				$(this).attr('checked',false); 
				$(this).parent().removeClass('checked');	
			});	
		}
	});
	
$('.table-checks .check-all').click(function(){
		var parentTable = $(this).parents('table');										   
		var ch = parentTable.find('tbody input[type=checkbox]');										 
		if($(this).is(':checked')) {
		
			//check all rows in table
			ch.each(function(){ 
				$(this).attr('checked',true);
				$(this).parent().addClass('checked');	//used for the custom checkbox style
				$(this).parents('tr').addClass('selected');
			});
						
			//check both table header and footer
			parentTable.find('.check-all').each(function(){ $(this).attr('checked',true); });
		
		} else {
			
			//uncheck all rows in table
			ch.each(function(){ 
				$(this).attr('checked',false); 
				$(this).parent().removeClass('checked');	//used for the custom checkbox style
				$(this).parents('tr').removeClass('selected');
			});	
			
			//uncheck both table header and footer
			parentTable.find('.check-all').each(function(){ $(this).attr('checked',false); });
		}
	});

if($.cookie('SideHide') == 1) {
$('#wrap').addClass('SActive');		
}
$('.toggle-btn').click(function(){
if ($("#wrap").hasClass("SActive")) {
$.cookie('SideHide', '0');	
} else {
$.cookie('SideHide', '1');	 
}
$('#wrap').toggleClass('SActive');	
});	
$('.searchit-btn').click(function(){
$('#header').toggleClass('hide');	
});	
$('#autocompletesearch').devbridgeAutocomplete({
   serviceUrl: admin__url + '/searchapi.php',
    onSelect: function (suggestion) {
        console.log('You selected: ' + suggestion.value + ', ' + suggestion.data);
    }
});
	
	$('.check-all-notb').click(function(){
		var parentTable = $(this).parents('form');										   
		var ch = parentTable.find('article input[type=checkbox]');										 
		if($(this).is(':checked')) {
		
			//check all rows in article
			ch.each(function(){ 
				$(this).attr('checked',true);
				$(this).parent().addClass('checked');	//used for the custom checkbox style
				$(this).parents('article').addClass('selected');
			});
						
			//check both article header and footer
			parentTable.find('.check-all-notb').each(function(){ $(this).attr('checked',true); });
		
		} else {
			
			//uncheck all rows in article
			ch.each(function(){ 
				$(this).attr('checked',false); 
				$(this).parent().removeClass('checked');	//used for the custom checkbox style
				$(this).parents('article').removeClass('selected');
			});	
			
			//uncheck both article header and footer
			parentTable.find('.check-all-notb').each(function(){ $(this).attr('checked',false); });
		}
	});
	
$("#easyhome ul").sortable({ opacity: 0.6, cursor: 'move', update: function() {
			var order = $(this).sortable("serialize") + '&action=updateRecordsListings'; 
			
			$.post("sort.php", order, function(theResponse){
				$("#respo").html(theResponse).fadeIn(400).delay(800).slideUp(300);
			}); 															 
		}								  
		});	
	
});

