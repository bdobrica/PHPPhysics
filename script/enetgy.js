var $netree = new function () {
	this.obj = [];
	this.mrp = [];
	this.sys = null;

	

	this.ready = function () {
		$netree.sys = arbor.ParticleSystem(1000, 600, 0.5); // create the system with sensible repulsion/stiffness/friction
		$netree.sys.parameters({gravity:true}); // use center-gravity to make the graph settle nicely (ymmv)
		$netree.sys.renderer = Renderer("#enetgy-view"); // our newly created renderer will have its .init() method called shortly by sys...

		// add some nodes to the graph and watch it go...
		if ($graft.nodes) $netree.sys.graft ($graft);
		};
	}

var $enetgy = new function () {
	this.sha = null;
	this.win = null;
	this.cls = null;
	this.htm = null;

	this.ready = function () {
		jQuery('body').append ('<div id="enetgy-shadow"></div><div id="enetgy-window"><div id="enetgy-close"></div><div id="enetgy-content"></div></div>');
		this.win = jQuery('#enetgy-window');
		this.sha = jQuery('#enetgy-shadow');
		this.cls = jQuery('#enetgy-close').click(function(e){$enetgy.window (0);});
		this.htm = jQuery('#enetgy-content');

		$netree.ready ();
		};

	this.prepare = function () {
		this.htm.find('form').submit(function(e){
			e.preventDefault ();
			
			jQuery.post (jQuery(this).attr('action'), jQuery(this).serialize(), function(h){
				if (h.indexOf('@') == 0) {
					h = h.substr(1);
					alert (h);
					o = jQuery.parseJSON(h);
					if (o.arbor == 'node')
						$netree.sys.addNode(o.label, {'color': 'blue', 'shape': 'dot', 'label':o.label});
					if (o.arbor == 'edge')
						$netree.sys.addEdge(o.from, o.to, {'label': o.label});
					$enetgy.window (0);
					}
				else {
					$enetgy.htm.empty().html(h);
					$enetgy.prepare();
					}
				});
			});
		};

	this.window = function (o, h){
		if (o) {
			$enetgy.sha.css({'opacity': 0, 'display': 'block', 'height': jQuery(document).height()}).animate({'opacity': .8}, function(){
				$enetgy.win.css({'opacity': 0, 'display': 'block'}).animate({'opacity': 1}, function(){
					if (h) {
						$enetgy.htm.empty().html(h);
						$enetgy.prepare();
						}
					});
				});
			}
		else {
			$enetgy.htm.empty();
			$enetgy.win.animate({'opacity': 0}, function(){
				$enetgy.win.css({'display': 'none'});
				$enetgy.sha.animate({'opacity': 0}, function (){
					$enetgy.sha.css({'display': 'none'});
					});
				});
			}
		};
	};

jQuery('document').ready(function(){	
	$enetgy.ready ();

	jQuery('#enetgy-app-elements a').click(function(e){
		e.preventDefault ();
		jQuery.post ('/ajax/element.php', {'add': jQuery(e.target).attr('rel')}, function (h) {
			$enetgy.window (1, h);
			});
		});
	});
