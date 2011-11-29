/**
 * ELIXON THEATRE EFFECT
 * Project Homepage: http://www.webdevelopers.eu/jquery/theatre
 *
 * @project    Elixon CMS, http://www.webdevelopers.eu/
 * @package    JQuery
 * @subpackage Theatre
 * @author     Daniel Sevcik <sevcik@webdevelopers.eu>
 * @version    1.0.2
 * @copyright  2010 Daniel Sevcik
 * @since      2010-11-11T22:10:07+0100
 * @revision   $Revision: 4606 $
 * @changed    $Date: 2010-11-21 18:23:39 +0100 (Sun, 21 Nov 2010) $
 * @access     public
 */
(function($) {
	var myEffect=function(stage, actors, settings, theatre) {
		var orbits=[];
		var steps=32;
		var dist=[0.1, 0.6];
		var index=-1;
		
		this.init=function() {
			var thisObj=this;
			actors.each(function() {
					var orbit={planet: $(this), path: [], step: Math.round(Math.random() * 100)};
					var orbitWidth=Math.random() * 0.8 + 0.2;
					var orbitHeight=Math.random() * 0.8 +  0.2;
					var rotation=2 * Math.random() * Math.PI;
					var pivots=steps * ((orbitWidth * orbitHeight) * 0.5 + 0.5);
					for (var step=1; step <= pivots; step++) {
						var coord=thisObj.getOrbitCoord(2 * Math.PI * (step + 1) / pivots, orbitWidth, orbitHeight, rotation, stage.width(), stage.height(), $(this).width(), $(this).height(), dist[0], dist[1]);
						orbit.path.push({
								left: coord.x, top: coord.y,
									opacity: coord.z + 1 - dist[1], zIndex: Math.round(100 - coord.z * 100),
									width: coord.itemWidth, height: coord.itemHeight});
					}
					orbits.push(orbit);
					thisObj.orbitting(orbit);

					orbit.mouseenter=function() {$(this).css({'z-index': 300, opacity: 1}); $(this).stop(true, false);};
					$(this).mouseenter(orbit.mouseenter);

					orbit.mouseleave=function() {$(this).css('z-index', 100); thisObj.orbitting(orbit);}
					$(this).mouseleave(orbit.mouseleave);
				});
			
		}

		this.orbitting=function(orbit) {
			var thisObj=this;
			var reschedule=function() {thisObj.orbitting(orbit);}
			for (var i=Math.round(Math.random() * 10) + 32; i >= 0; i--) {
				orbit.planet.animate(orbit.path[orbit.step % orbit.path.length], 300, 'linear', i ? null : reschedule);
				orbit.step++;
			}
		}
		
		this.slide=function() {
			if (index != -1) {
				actors.eq(index).stop(true, false).css('z-index', 0);
				this.orbitting(orbits[index]);
			}
			index=theatre.index;
			
			var actor=actors.eq(theatre.index);
			var width=settings.itemWidth || this.getMaxDim(actor).width * 0.8;
			var height=settings.itemHeight || this.getMaxDim(actor).height * 0.8;			
			actor.stop(true, false).css('z-index', 250).animate({
					    width: width, height: height,
						left: (stage.width() - width) / 2,
						top: (stage.height() - height) / 2,
						opacity: 1
						}, settings.speed);
		}
		this.next=function() {
			this.slide();
		}
		this.prev=function() {
			this.slide();			
		}
		this.destroy=function() {
			var i=0;
			actors.stop(true, false).css({'z-index': 0, width: '', height: '', opacity: 1, left: 0, top: 0}).each(function() {
					$(this).unbind('mouseenter', orbits[i].mouseenter).unbind('mouseleave', orbits[i].mouseleave);
					i++;
				});
		}
		this.getMaxDim=function(item) {
			var width=stage.width();
			var height=item.height() / item.width() * width;
			
			if (height > stage.height()) {
				var height=stage.height();
				var width=item.width() / item.height() * height;
				
			}
			return {width: width, height: height};
		}
		this.getOrbitCoord=function(angle, width, height, rotation, containerWidth, containerHeight, itemWidth, itemHeight, distMin, distMax) {
			// Normalized
			var x0=width * Math.cos(angle) * Math.cos(rotation) - height * Math.sin(angle) * Math.sin(rotation);
			var y0=width * Math.cos(angle) * Math.sin(rotation) + height * Math.sin(angle) * Math.cos(rotation);
			var z0=1 - (Math.cos(angle) * Math.sin(rotation) + Math.sin(angle) * Math.cos(rotation) + 1) / 2;

			// Z-Axis
			distMin=(isNaN(distMin) ? 0 : distMin);
			distMax=(isNaN(distMax) ? 1 : distMax);	
			var z=z0 * (distMax - distMin) + distMin;

			// Zoom item
			itemWidth=((isNaN(itemWidth) ? 0 : itemWidth) * z);
			itemHeight=((isNaN(itemHeight) ? 0 : itemHeight) * z);

			// Fit orbit into box
			var x=(isNaN(containerWidth) ? x0 : (x0 + 1) * (containerWidth - itemWidth) / 2);
			var y=(isNaN(containerHeight) ? y0 : (y0 + 1) * (containerHeight - itemHeight) / 2);				

			return {x: x, y: y, z: z, itemWidth: itemWidth, itemHeight: itemHeight, x0: x0, y0: y0, z0: z0};
		}
		
	}

	$.fn.theatre('effect', 'orbit', myEffect);
})(jQuery)	
