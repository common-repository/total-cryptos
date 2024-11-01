jQuery(document).ready(function($){
	$("#totalcryptos_market_cap").children('tr').each(function(){
		var val=$("#hidden_"+this.id).val(); 
		$("#chart_"+this.id).html('');
		if(val){ 
			$("#chart_"+this.id).sparkline(val.split(","), {type: 'line',width: '100%',height: '20',lineColor: '#1ab394',fillColor: "transparent"});
		}
	});
	
	$("#totalcryptos_usd").children('tr').each(function(){
		var val=$("#hidden_"+this.id).val(); 
		$("#chart_"+this.id).html('');
		if(val){ 
			$("#chart_"+this.id).sparkline(val.split(","), {type: 'line',width: '100%',height: '20',lineColor: '#1ab394',fillColor: "transparent"});
		}
	});
	
	$(".default-ticker").ticker();
	var newsTicker = $(".news").ticker({
		speed: 50,
		pauseOnHover: !0,
		item: ".news-item"
	}).data("ticker");
	$("#news-toggle").on("click", function() {
		newsTicker.toggle()
	}), $(".speed-test").each(function() {
		$(this).ticker({
			speed: $(this).data("speed") || 60
		})
	});
});