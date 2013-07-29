window.addEvent('domready', function() {
	var zoomTip = new Tips($$('.zoomTip'), {
		 //this is the prefix for the CSS class
		
		offset: {x:10+ TipX,	y: 20+TipY},
		showDelay: 100,
		hideDelay: 100,
		fixed: Fixed,
		onShow: function(tip, el){
			tip.setStyles({
				display: 'block',
				opacity: 0
			}).morph({
				opacity: [OpaCity, 1]
			});
		},
	
		onHide: function(){
			this.tip.setStyle('display', 'none');
		},
		className: 'custom',
		
		
	});
});
