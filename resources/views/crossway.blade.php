<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/d3js/5.15.1/d3.min.js"></script>
</head>
<body>
	<h1>Перекресток</h1>
	<div id="crossway" style="width:100%;"></div>
</body>
<script type="text/javascript">
	var USK = {
		colors : {
			road : '#cccccc',
			markup : '#ffffff',
			trafficligth: {
				default : 'yellow',
				yellow : 'yellow',
				red : 'red',
				green : 'green',
			},
		},
		svg : {
			scale : 15,
			object : null,
			area : null,
			transport : null,
			center : {
				x : 0,
				y : 0,
			},
			selector : '#crossway',
			height : 600,
			width : null,
			margin : {
				top: 0, 
				right: 0, 
				bottom: 0, 
				left: 0
			},
			getScaled : function(m){
				return m * this.scale;
			},
			get : function() {
				if(this.object === null) {
					this.object = this.prepareObject();
				}
				return this.object;
			},
			prepareObject : function(){
				var margin = this.margin;
		        var clientWidth = $(this.selector).width();
		        this.width = clientWidth;

		        var width = clientWidth - margin.left - margin.right,
		            height = this.height - margin.top - margin.bottom;


		        var svg = d3.select(this.selector).append("svg")
		            .attr("width", clientWidth)
		            .attr("height", height + margin.top + margin.bottom);
		        return svg;
			},
			getArea : function() {
				if(this.area === null){
					var svg = this.get();
					var margin = this.margin;
					var height =  this.height - margin.top - margin.bottom;
					var width = this.width - margin.left - margin.right;
					this.center.x = (width / 2);
					this.center.y = (height / 2);
					var area = svg.append("g")
	        			.attr("transform", "translate(" + margin.left + "," + margin.top + ")")
	        			.attr('class', 'data-area')
	        			.attr('height', height)
	        			.attr('width', width)

	        		area.append('circle')
	        			.attr('cx', (width / 2))
	        			.attr('cy', (height / 2))
	        			.attr('r', 5)
	        			.attr('fill', 'darkred');

	        		this.area = area;
				}
				return this.area;
			},
			getTransport : function() {
				if(this.transport === null){
					var svg = this.get();
					var margin = this.margin;
					var height =  this.height - margin.top - margin.bottom;
					var width = this.width - margin.left - margin.right;
					this.center.x = (width / 2);
					this.center.y = (height / 2);
					var transport = svg.append("g")
	        			.attr("transform", "translate(" + margin.left + "," + margin.top + ")")
	        			.attr('class', 'data-transport')
	        			.attr('height', height)
	        			.attr('width', width)

	        		this.transport = transport;
				}
				return this.transport;
			},
			getCenterCoords : function() {
				return this.center;
			}
		},
		trafficligth : {
			states : {},
			blinkTime : 4000,
			getDuration : function(phaseIndex, phase){
				let duration = 3000;
				if(typeof(phase.duration) !== 'undefined'){
					duration = phase.duration;
				}
				return duration;
			},
			phases : [
				{
					on : [
						{ 
							direction : [1,3,5], 
							type : 'all', 
						},
					],
					off : [
						{ 
							direction : [2,4,6], 
							type : 'all', 
						}
					],
					standby : [],
					duration : 7000,
				},
				{
					on : [
						{ 
							direction : [2,4,6], 
							type : 'all', 
						}
					],
					off : [
						{ 
							direction : [1,3,5], 
							type : 'all', 
						},
					],
					standby : [],
					duration : 6000,
				},
			],
			loop : true,
			lastPhaseIndex : -1,
			nextPhase : function(){
				if(this.loop){
					let index = this.lastPhaseIndex + 1;
					if(index >= this.phases.length){
						index = 0;
					}
					let phase = this.phases[index];
					let duration = this.getDuration(index, phase);
					let blinkTime = this.blinkTime;
					for(let p = 0; p < phase.on.length; p++){
						this.switch(phase.on[p].direction, phase.on[p].type, 1);
						if(duration > this.blinkTime){
							setTimeout(
								function(){
									USK.trafficligth.blink(phase.on[p].direction, phase.on[p].type);
								}, 
								(duration - blinkTime)
							);
						}
					}
					for(let p = 0; p < phase.off.length; p++){
						this.switch(phase.off[p].direction, phase.off[p].type, -1);
					}
					for(let p = 0; p < phase.standby.length; p++){
						this.switch(phase.standby[p].direction, phase.standby[p].type, 0);
					}
					this.lastPhaseIndex = index;
					setTimeout(function(){
						USK.trafficligth.nextPhase();
					}, duration);
				}
			},
			start : function(){
				this.loop = true;
				this.nextPhase();
			},
			stop : function(){
				this.loop = false;
				this.switch([1,2,3,4,5,6], 'all', 0);
			},
			getSelector : function(direction, type) {
				let selector = '[data-direction="'+direction+'"]';
				if(typeof(type) !== 'undefined' && type !== 'all'){
					selector += '[data-type="'+type+'"]'
				}
				return selector;
			},
			blink : function(direction,type) {
				let selector = null;
				if(typeof(direction) !== 'object'){
					direction = [direction];
				}
				let halfBlink = USK.trafficligth.blinkTime / (2 * 4);
				for(let d = 0; d < direction.length; d++){
					selector = this.getSelector(direction[d], type);
					for(let t = 0; t < 4; t++){
						d3.selectAll(selector)
							.transition()
								.delay(1000 * t)
								.duration(halfBlink)
								.style("opacity",0)

						d3.selectAll(selector)
							.transition()
								.delay(1000 * t + halfBlink)
								.duration(halfBlink)
								.style("opacity",1)
					}
				}
			},
			switch : function(direction, type, on){
				let color = USK.colors.trafficligth.default;
				switch(on) {
					case 1:
						color =  USK.colors.trafficligth.green;
						break;
					case -1:
						color =  USK.colors.trafficligth.red;
						break;
					case 0:
						color =  USK.colors.trafficligth.yellow;
						break;
				}
				let selector = null;
				if(typeof(direction) !== 'object'){
					direction = [direction];
				} 
				for(let d = 0; d < direction.length; d++){
					USK.trafficligth.states[direction[d]] = on;
					selector = this.getSelector(direction[d], type);
					d3.selectAll(selector)
						.attr('stroke', color)
						.style('opacity', 1)
				}
			},
			on : function(direction, type){
				this.switch(direction, type, 1);
			},
			off : function(direction, type){
				this.switch(direction, type, -1);
			},
			standby : function(direction, type){
				this.switch(direction, type, 0);
			}
		},
		crossway : {
			defaultRoadLen : 20,
			render : function(){
				var defaultRoadLen = this.defaultRoadLen;
				var defaultRoadWidth = 3.5;

				var area = USK.svg.getArea();
				var center = USK.svg.getCenterCoords();
				var offset =  USK.svg.getScaled(defaultRoadWidth)/2;
				var crosswalkOffset = offset*2;

				for(let r = 0; r < 360; r += 90){				
					USK.road(defaultRoadLen, defaultRoadWidth, center.x + offset, center.y + offset, r, 1)

					USK.road(defaultRoadLen, defaultRoadWidth, center.x - offset, center.y + offset, r, -1)
				}
				this.renderCenter(center.x, center.y, defaultRoadWidth* 2);
				for(let r = 0; r < 360; r += 90){	
					USK.crosswalk(defaultRoadWidth * 2, 3, center.x - crosswalkOffset, center.y + crosswalkOffset, r)
				}
				var crosslightOffset = defaultRoadWidth * 2;
				for(let r = 0; r < 360; r += 90){
					USK.crosslight(defaultRoadWidth, center.x + offset, center.y + crosswalkOffset, r, 'auto')
					USK.crosslight(defaultRoadWidth, center.x + offset+crosswalkOffset, center.y - crosswalkOffset, r+90, 'citezen')
				}
			},
			renderCenter : function(x, y, width){
				var rx = 15;
				width =  USK.svg.getScaled(width) + rx;
				var center = USK.svg.getArea().append('g')
				center.append('rect')
					.attr('width', width)
					.attr('height', width)
					.attr('fill', USK.colors.road)
					.attr('x', x - width/2)
					.attr('y', y - width/2)
					.attr('rx', rx)

				return center;
			},
		},
		crosslight : function(len, x, y, rotate, type) {
			width = 4;
			len = USK.svg.getScaled(len);
			var crosslight = USK.svg.getArea().append('g')
			crosslight.append('line')
				.attr('x1', x - len /2)
				.attr('x2', x + len /2)
				.attr('y1', y)
				.attr('y2', y)
				.attr('stroke', USK.colors.trafficligth.default)
				.attr('stroke-width', width)
				.attr('data-direction', parseInt(rotate / 90) + 1)
				.attr('data-type', type)

			crosslight.style('transform-origin','center center')
				.style('transform', 'rotate('+rotate+'deg)');


			return crosslight;
		},
		crosswalk : function(len, width, x, y, rotate) {
			var defaultCrosswalkWidth = USK.svg.getScaled(width);
			len = USK.svg.getScaled(len);
			var crosswalk = USK.svg.getArea().append('g')
			crosswalk.append('line')
				.attr('x1', x)
				.attr('x2', x + len)
				.attr('y1', y + defaultCrosswalkWidth)
				.attr('y2', y + defaultCrosswalkWidth)
				.attr('stroke', USK.colors.markup)
				.attr('stroke-width', defaultCrosswalkWidth)
				.attr('stroke-dasharray', 8)

			crosswalk.style('transform-origin','center center')
				.style('transform', 'rotate('+rotate+'deg)');

			return crosswalk;
		},
		road : function(len, width, x, y, rotate, vector){
			if(typeof(rotate) === 'undefined'){
				rotate = 0;
			}
			if(typeof(vector) === 'undefined'){
				vector = 1;
			}
			len = USK.svg.getScaled(len);
			width = USK.svg.getScaled(width);
			var xR = -(width / 2) * Math.cos(rotate* Math.PI / 180);
			var yR = -(width / 2) * Math.sin(rotate* Math.PI / 180);
			var road = USK.svg.getArea().append('g')
				.attr("transform", "translate("+ xR +","+yR+")")
					.append('g')

			road.append('rect')
				.attr('width', width)
				.attr('height', len)
				.attr('fill', USK.colors.road)
				.attr('x', x)
				.attr('y', y)
				.attr('data-road-direction', vector * (parseInt(rotate / 90) + 1))

			road.append('line')
				.attr('x1', x )
				.attr('x2', x )
				.attr('y1', y)
				.attr('y2', y + len)
				.attr('stroke', USK.colors.markup)
				.attr('stroke-width', 2)
				.attr('stroke-dasharray', 4)			

			road.append('line')
				.attr('x1', x + width)
				.attr('x2', x + width)
				.attr('y1', y)
				.attr('y2', y + len)
				.attr('stroke', USK.colors.markup)
				.attr('stroke-width', 2)
				.attr('stroke-dasharray', 4)

			road.style('transform-origin','center center')
				.style('transform', 'rotate('+rotate+'deg)');

			return road;
		},
		transport : {
			stack : [],
			lines : [],

			try : function() {
				let t = null;
				let stack = this.stack;
				let lines = this.lines;
				let stackAfter = [];
				for(let i = 0; i < stack.length; i++){
					t = stack[i];
					direction = parseInt(t.attr('data-transport-direction-form'));
					if(USK.trafficligth.states[direction] >= 0){
						USK.transport.run(t);
						lines[direction]--;
					} else {
						stackAfter.push(t);
						lines[direction]++;
					}
				}
				this.stack = stackAfter;
			},
			nextTry : function(){
				this.try();
				setTimeout(function(){
					USK.transport.nextTry();
				}, 200);
			},
			start : function(){
				this.nextTry();
			},
			car : function(direction, params){
				var center = USK.svg.getCenterCoords();
				let width = USK.svg.getScaled(1.729); // Huyindai Solaris
				let len = USK.svg.getScaled(4.405); // Huyindai Solaris
				var roadOffset = USK.svg.getScaled(USK.crossway.defaultRoadLen);
				var rotate_0 = 90 * (direction - 1);
				var rotate_1 = 0;
				if(typeof(params) !== 'undefined'){
					if(typeof(params.rotate) !== 'undefined'){
						rotate_1 = params.rotate;
					}
				}
				var y = center.y + roadOffset;
				var transport = USK.svg.getTransport().append('g')
					.attr('data-transport', 'car')
					.append('image')
						.style('transform-origin', 'center center')
						.style('transform', 'rotate('+rotate_0+'deg)')
						.attr('data-transport-direction-form', direction)
						.attr('data-rotate-0', rotate_0)
						.attr('data-rotate-1', rotate_1)
						.attr('data-y-0', y)
						.attr('data-y-1', center.y)
						.attr('xlink:href', '/upload/hackaton/car-light.png')
						.attr('width', width)
						.attr('height', len)
						.attr('x', center.x + width / 2)
						.attr('y', y)

				return transport;
			},
			run : function(transport){
				let y1 = transport.attr('data-y-1');
				let duration = 300;
				let height = transport.attr('height');
				transport
					.transition()
						.duration(duration)
						.attr('y', y1)

				let rotate = transport.attr('data-rotate-1');
				let x = transport.attr('x');
				let y = transport.attr('y');

				transport
					.transition()
						.delay(duration)
						.duration(duration)
						.style('transform', 'rotate('+rotate+'deg) translateY(-'+height+'px)');

				transport
					.transition()
						.delay(duration*2)
						.duration(duration)
							.attr('y', -height)

				setTimeout(function(){
					transport.remove();
				}, duration * 3)
			}
		}
	};

	USK.crossway.render();
	USK.transport.start()
	USK.trafficligth.start()
</script>
</html>