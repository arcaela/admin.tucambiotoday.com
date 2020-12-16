var version = "1_0_2_7";
version = parseInt(version.replace(/(\D+)/gi,''));

var last_version = setcookie('updated');
last_version = !last_version ? 0 : parseInt(last_version);


var updating = {
	msg:'Se han realizado actualizaciones, <a class="close" onclick="getUpdate.call($(this).parent('+"'.notification'"+'));">Click aqui</a> para instalarlas.',
};



if ((!last_version||version>last_version)) {
	if (!$('.update-server')[0]) {
		$('body').prepend(
			Mustache.render('<div class="notification is-bar is-danger has-text-centered update-server">'+updating.msg+'</div>',updating)
		);
	}
	$('body').find('.update-server').slideDown();
}




function getUpdate() {
	try {
		if (setcookie('updated',version,(3600*24*7))) {
			localStorage.clear();
			$('[ads-item]').addClass('ads-locked');
			setTimeout(function(){
				window.location.reload();
			},1000);
		}
	} catch(e) {
		console.error("Update version fail: ",e);
	}
}