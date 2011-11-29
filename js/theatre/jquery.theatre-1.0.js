/**
 * ELIXON THEATRE
 * Project Homepage: http://www.webdevelopers.eu/jquery/theatre
 *
 * LICENSE
 *   http://www.webdevelopers.eu/jquery/theatre/license
 *   Commons Attribution-NonCommercial 3.0
 *
 *   Get a commercial license at
 *   http://www.webdevelopers.eu/jquery/theatre/buy 
 *
 * DEMO
 *    http://www.webdevelopers.eu/jquery/theatre/demo 
 * 
 * DOCUMENTATION
 *    http://www.webdevelopers.eu/jquery/theatre/documentation
 *    
 * @project    Elixon CMS, http://www.webdevelopers.eu/
 * @package    JQuery
 * @subpackage Theatre
 * @author     Daniel Sevcik <sevcik@webdevelopers.eu>
 * @version    1.0.11
 * @copyright  2010 Daniel Sevcik
 * @since      2010-11-11T22:10:07+0100
 * @revision   $Revision: 4719 $
 * @changed    $Date: 2011-07-22 13:49:41 +0200 (Fri, 22 Jul 2011) $
 * @access     public
 */
(function($) {
	var effects={};
	var methods={};
	var urlBase;

	$.fn.theatre=function() {
		var currArguments=arguments;
		(this.length ? this : $(document)).each(function() {
				methods.initAll.apply($(this), currArguments);
			});
		return this;
	}

	// name:string - 'FILE:EFFECT' or 'EFFECT', one file may register EFFECT or multiple effect prefixed by FILE string
	methods.loadEffect=function(name) { 
		if (!urlBase) { // Find the locations
			$('script[src*="jquery.theatre-"], link[href*="theatre.css"]').first().each(function() {
					urlBase=(this.href || this.src).replace(/\/[^\/]*(#.*)?$/, '');
				});
		}
		var url=urlBase+'/effect.'+name.split(':')[0]+'.js';
		$('head').append('<script type="text/javascript" src="'+url+'"></script>');
		return effects[name];
	}

	methods.initAll=function(method) {
		// Init theatre
		if (typeof method == 'object' || !method || method == 'init') { 
			return methods.init.apply(this, arguments);
		}
		
		// Register Effect
		if (method == 'effect') {
			// Register new effect
			if (typeof arguments[2] == 'function') {
				return effects[arguments[1]]=arguments[2];
			} else {
				$.error("Elixon Theatre cannot register effect object unless it is a Function.");
			}
		}

		var theatre=this.data('theatre');
		if (!theatre) return false; // not initialized yet
		
		switch(method) {
		case 'jump':
		    var jumpTo;
			switch(jumpTo) {
			case 'first': jumpTo=0; break;
			case 'last' : jumpTo=theatre.actors.length - 1; break;
			default     :
				jumpTo=(parseInt(arguments[1]) - 1) % theatre.actors.length;
				jumpTo=(Math.abs(Math.floor(jumpTo / theatre.actors.length) * theatre.actors.length) + jumpTo) % theatre.actors.length; //normalize				
			}
			if (typeof arguments[2] != 'undefined') {
				if (typeof theatre.settings.speedOrig == 'undefined') theatre.settings.speedOrig=theatre.settings.speed;
				theatre.settings.speed=arguments[2];
			}
			while(theatre.index != jumpTo) {
			  if (theatre.effect.jump) {
				  theatre.effect.jump(jumpTo);
				  theatre.index=jumpTo;
			  } else {
				  this.theatre(theatre.index < jumpTo ? 'next' : 'prev');
			  }
			}
		    methods.onMove.apply(this);
			if (typeof theatre.settings.speedOrig != 'undefined') theatre.settings.speed=theatre.settings.speedOrig;			
		    break;
		case 'next':
		case 'prev':		
			// Call next/prev custom effects
			if (!arguments[1]) this.theatre('stop'); // stop animation if no second param == true
		    theatre.index=(theatre.index + (method == 'next' ? 1 : -1)) % theatre.actors.length;
			theatre.index=(Math.abs(Math.floor(theatre.index / theatre.actors.length) * theatre.actors.length) + theatre.index) % theatre.actors.length; //normalize
		    methods.onMove.apply(this);			
		    methods.updatePaging.apply(this);
		    theatre.effect[method].apply(theatre.effect, Array.prototype.slice.call(arguments, 1));
			break;
		case 'play':
		case 'stop':		
			methods[method].apply(this, arguments);
		    break;
		default: // Unsupported
			$.error('Elixon Theatre method "'+method+'" does not exist on jQuery.theatre!');
		}
	}

	methods.init=function(options) {
		methods.destroy.apply(this); // Reset old if any
		
		// Default settings
		var settings={
			selector: '> *:not(".theatre-control")',
			effect: 'horizontal', // 'horizontal'|'vertical'|'fade'|'show'|'slide'|'3d'|CUSTOM EFFECT NAME|OBJECT with constructor implementing the init,next,prev methods
			speed: 1000, // Transition speed
			still: 3000, // Time between transitions
			autoplay: true,
			controls: 'horizontal', // display control buttons 'horizontal' or 'vertical' or 'none'
			itemWidth: false, // width of the item or 'max' or false. 
			itemHeight: false, // height of the item or 'max' or false. 
			width: false, // set the height of the container. By default honors the already set size using CSS
			height: false, // set the height of the container. By default honors the already set size using CSS
			onMove: false // function(index) {}, execution context is the Stage
		}
		if (options) {
			$.extend(settings, options);
		}

		// Init effect object
		var actors=$(settings.selector, this);
		var theatre={paging: settings.paging && $(settings.paging), actors: actors, effect: false, settings: settings, interval: false, index: 0};
		var effect=(typeof settings.effect == 'function' ? settings.effect : effects[settings.effect] || methods.loadEffect(settings.effect));
		if (!effect) $.error('Elixon Theatre does not support effect "'+settings.effect+'"!');
		theatre.effect=new effects[settings.effect](this, actors, settings, theatre);
		
		// Stage
		this.addClass('theatre').data('theatre', theatre);
		this.addClass('theatre-'+settings.effect.replace(/[^a-z0-9]+/ig, '-'));
		if (settings.width) this.css('width', settings.width);
		if (settings.height) this.css('height', settings.height);
		
		// Actors - calculate orig width/height
		actors.each(function(){
				var $this=$(this);
				if (!$this.data('theatre')) {
					$this.data('theatre', {width: $this.width(), height: $this.height()});
				}
				$this.load(function() { // slow image load problem
						$this.data('theatre', {width: $this.width(), height: $this.height()});
					});
			});		
		if (settings.itemWidth || settings.itemHeight) {
			var thisObj=this;
			actors.each(function() {
					var $this=$(this);
					if (settings.itemWidth) {
						$this.css('width', settings.itemWidth == 'max' ? (thisObj.width() - $this.outerWidth() + $this.width())+'px' : settings.itemWidth);
					}
					if (settings.itemHeight) {
						$this.css('height', settings.itemHeight == 'max' ? (thisObj.height() - $this.outerHeight() + $this.height())+'px' : settings.itemHeight);
					}
				});
		}
		
		actors.addClass('theatre-actor').stop(true, true);
		theatre.effect.init();

		if (settings.autoplay) {
			methods.play.apply(this);
		}

		// Controls
		methods.appendControls.apply(this);
		methods.onMove.apply(this);					
		methods.generatePaging.apply(this); 		
	}

	methods.onMove=function() {
		var theatre=this.data('theatre');
		if (typeof theatre.settings.onMove != 'function') return;
		theatre.settings.onMove.apply(this, [theatre.index, theatre.actors[theatre.index], theatre]);
		this.trigger('theatreMove', [theatre.index, theatre.actors[theatre.index], theatre]);
	}

	methods.generatePaging=function() {
		var stage=this;
		var theatre=this.data('theatre');
		if (!theatre.paging) return;

		theatre.paging.each(function() {
				var $this=$(this);
				var jumpers=[];
				$('> *', $this).each(function() {jumpers.push($('<div></div>').append(this).html());});
				var template=jumpers[jumpers.length - 1];
				
				// Re-generate
				for(var i=0; i < theatre.actors.length; i++) {
					var jumpHTML=jumpers.length < i + 1 ? template : jumpers[i];
					(function (pos) {
						$this.append(jumpHTML.replace('{#}', pos)+"\n");
						$this.children().last().click(function() {stage.theatre('jump', pos);});
					})(i+1);
				}
			});
		methods.updatePaging.apply(this);
	}

	methods.updatePaging=function() {
		var theatre=this.data('theatre');
		if (!theatre.paging) return;		

		theatre.paging.each(function() {
				var $this=$(this);
				$('> *', $this).removeClass('active').eq(theatre.index).addClass('active');
			});		
	}

	methods.appendControls=function() {
		settings=this.data('theatre').settings;
		// Controls
		if (settings.controls == 'horizontal' || settings.controls == 'vertical') {
			var thisObj=this;
			this.append('<a class="theatre-control theatre-control-'+settings.controls+'-next theatre-next"><span></span></a>');
			this.append('<a class="theatre-control theatre-control-'+settings.controls+'-prev theatre-prev"><span></span></a>');
			this.append('<a class="theatre-control theatre-control-'+settings.controls+'-play theatre-play"><span></span></a>');
			this.append('<a class="theatre-control theatre-control-'+settings.controls+'-stop theatre-stop"><span></span></a>');
			$('.theatre-next', this).click(function() {thisObj.theatre('next');});
			$('.theatre-prev', this).click(function() {thisObj.theatre('prev');});
			$('.theatre-play', this).click(function() {thisObj.theatre('play');});
			$('.theatre-stop', this).click(function() {thisObj.theatre('stop');});
			this.mouseenter(function() {$('.theatre-control', thisObj).fadeIn();});
			this.mouseleave(function() {$('.theatre-control', thisObj).fadeOut();});
			$('.theatre-control', this).fadeOut(0);
		}

		this.append('<a class="theatre-control theatre-sign" rel="copyright license" style="position: absolute !important; display: none !important;" href="http://www.webdevelopers.eu/jquery/theatre" title="jQuery carousel plugin"><span style="display: none !important;">Elixon Theatre jQuery Plugin</span></a>');		
	}

	methods.destroy=function() {
		var theatre=this.data('theatre');
		if (theatre) { 
			clearInterval(theatre.interval);
			this.theatre('jump', 0);
			if (typeof theatre.effect.destroy == 'function') theatre.effect.destroy();
			this.removeClass('theatre-'+theatre.settings.effect.replace(/[^a-z0-9]+/ig, '-'));
			theatre.actors.each(function(){ // Restore original sizes
					var $this=$(this);
					var theatre=$this.data('theatre');
					$this.width(theatre.width);
					$this.height(theatre.height);
				})
		}
		$('.theatre-control', this).remove();
	}

	methods.play=function() {
		var theatre=this.data('theatre');
		var stage=this;
		//methods.stop.apply(this);
		//stage.theatre('next');
		stage.theatre('stop');
		theatre.interval=setInterval(function() {stage.theatre('next', true);}, theatre.settings.speed + theatre.settings.still);
	}

	methods.stop=function() {
		var theatre=this.data('theatre');
		clearInterval(theatre.interval);
		theatre.interval=false;
	}

	effects['fade']=
	effects['slide']=
	effects['show']=function(stage, actors, settings, theatre) {
		var x={
			fade: {show: 'fadeIn', hide: 'fadeOut', initStyle: {margin: 0, top: 0, left: 0, position: 'absolute', display: 'none'}},
			slide: {show: 'slideDown', hide: 'slideUp', initStyle: {}},
			show: {show: 'show', hide:'hide', initStyle: {}}
		}[settings.effect];
		
		this.init=function() {
			actors[x.hide](0).css(x.initStyle).first()[x.show](0); // actors.fadeOut(0) - does not hide it if stage has height=0 - assigning 'display:none'
		}
		this.next=function() {
			actors.stop(true, true).css('z-index', 0)[x.hide](settings.speed)
				.eq(theatre.index).css('z-index', 10)[x.show](settings.speed);			
		}
		this.prev=function() {
			actors.stop(true, true).css('z-index', 0)[x.hide](settings.speed)
				.eq(theatre.index).css('z-index', 10)[x.show](settings.speed);						
		}
		this.destroy=function() {
			actors.stop(true, true).css({zIndex: '', top: '', left: '', position: '', margin: ''})[x.show](0);			
		}
	}

	effects['vertical']=	
	effects['horizontal']=function(stage, actors, settings) {
		var x={
			horizontal: {'size': 'outerWidth', 'direction': 'left'},
			vertical: {'size': 'outerHeight', 'direction': 'top'}
		}[settings.effect];

		this.init=function() {
			actors.fadeOut(0);
			this.align(0, 0);
			actors.fadeIn();			
		}
		this.next=function() {
			// $(settings.selector, stage).last().stop(true, true); // to prevent visual contra-direction transition
			var curr=$(settings.selector, stage).first();
			var offset=this.align(-curr[x.size](true));
			curr.appendTo(stage);
		}
		this.prev=function() {
			var curr=$(settings.selector, stage).last().prependTo(stage);
			curr.stop(true, true).css(x.direction, -curr[x.size](true));
			this.align(0);
		}
		this.destroy=function() {
			actors.stop(true, true).css(x.direction, '').css({opacity: '', left: '', top: ''});
		}
		this.align=function(offset, speed) {
			var sacked=false;
			$(settings.selector, stage).each(function() {
					var callBack=null;
					var $this=$(this);
					if (offset < 0) {
						callBack=function() {$this.css(x.direction, offset);}
					}
					var props={};
					props[x.direction]=offset;
					$this.stop(true, true).animate(props, isNaN(speed) ? settings.speed : speed, callBack);
					offset+=$this[x.size](true);
				});
			return offset;
		}
	}

	effects['3d']=function(stage, actors, settings) {
		// Override custom/default settings
		settings.resize=false;
		
		var currIndex=0;
		var pivots=[];
		var maxWidth;
		var maxHeight;

		this.init=function() {
			var thisObj=this;
			maxWidth=stage.width() * 0.5;
			maxHeight=stage.height() * 0.8;
			actors.each(function(pos) {
					var rad=(2 * Math.PI * pos / actors.length) + Math.PI / 2;
					var x=Math.cos(rad);
					var y=Math.sin(rad);

					var margin=10;
					var sizeMin=0.2;
					var size=(y + 1) / 2 * (1 - sizeMin) + sizeMin;
					var x2=x * (stage.width() - margin) / 2 + stage.width() / 2;
					var y2=y * (stage.height() - margin) / 2 + stage.height() / 2;
					
					pivots.push({left: x2, top: y2, x: x, y: y, size: size, rad: rad});
				});
			this.animate();
			//console.log(pivots);
		}
		this.next=function() {
			if (++currIndex > actors.length - 1) currIndex=0;
			this.animate();
		}
		this.prev=function() {
			if (--currIndex < 0) currIndex=actors.length - 1;
			this.animate();			
		}
		this.destroy=function() {
			actors.stop(true, true).css({'z-index': '', opacity: '', left: '', top: ''});
		}
		this.animate=function() {
			var thisObj=this;
			actors.stop();
			actors.each(function(pos) {
					var pivot=pivots[(pos - currIndex + actors.length) % actors.length];
					var dim=thisObj.calcDim($(this), maxWidth, maxHeight, pivot.size);
					var left=Math.round(pivot.left - pivot.x * dim[0] / 2 - dim[0] / 2);
					var top=Math.round(pivot.top - pivot.y * dim[1] / 2 - dim[1] / 2);
					// use transitions where applicable for skew() or scale() http://www.alistapart.com/articles/understanding-css3-transitions/
					// https://github.com/brandonaaron/jquery-cssHooks/blob/master/boxreflect.js for JQuery 1.4.3
					$(this).css({'z-index': Math.round(pivot.size * 1000)}).animate({opacity: pivot.size, left: left, top: top, width: dim[0], height: dim[1]}, settings.speed);
				});
		}
		this.calcDim=function(obj, maxWidth, maxHeight, size) {
			var dim=obj.data('theatre');
			var w=maxWidth;
			var h=dim.height / dim.width * w;
			if (h > maxHeight) {
				w=maxWidth * (maxHeight / h);
				h=maxHeight;
			}
			return [Math.round(w * size), Math.round(h * size)];
		}
	}
	
	
})(jQuery);
