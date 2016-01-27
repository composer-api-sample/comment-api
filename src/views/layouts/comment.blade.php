<script type="text/javascript">
	window.onload=function(){
		// alert("woohoo");
		loadJS("https://code.jquery.com/jquery-2.1.4.min.js");
		loadJS("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js");
		loadJS("{{url('/')}}/asp/res/js/comments.js");
		// loadCSS("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css");
		loadCSS("{{url('/')}}/asp/res/css/comments.css");
	};
	function loadJS(filename){
		var file=document.createElement("script");
		file.setAttribute("type","text/javascript");
		file.setAttribute("src",filename);
		if(typeof file !== "undefined"){
			document.getElementsByTagName("head")[0].appendChild(file);
		}
	}
	function loadCSS(filename){
		var file=document.createElement("link");
		file.setAttribute("rel","stylesheet");
		file.setAttribute("type","text/css");
		file.setAttribute("href",filename);
		if(typeof file !== "undefined"){
			document.getElementsByTagName("head")[0].appendChild(file);
		}
	}
</script>
