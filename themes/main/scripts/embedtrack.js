function DOtrackview(vid) {
$.post(site_url + 'app/ajax/track.php', { 
                video_id:   vid
            },            
            function(data){
			//console.log(data);	
			}
); 			
}