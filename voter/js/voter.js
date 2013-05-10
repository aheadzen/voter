jQuery('.engagement').live("click",function(){
		var ajaxurl = 'http://localhost/vote/wp-admin/admin-ajax.php';
		//var vars = [], hash;
		var vars = "";
		var hashes = this.href.slice(this.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
			if(i == (hashes.length - 1))
			{
				vars = vars + hashes[i].replace("=",":");
			}
			else
			{
				vars = vars + hashes[i].replace("=",":") + ", ";
			}
		}
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: ({vars}),
			data: ({userid:1, unsername: 'tapan'}),
			success: function(data)
			{
				alert(data);
			}
		});
	return false;
})