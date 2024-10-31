var ProtoRoomPlanning = Class.create({
	initialize: function(){

		//options
		this.selView = 'roomplanning_selView';
		this.selRoom = 'roomplanning_selRoom';
		if( $(this.selView) )
		{
			$(this.selView).observe('change', this.onChangeView.bind(this));
			$(this.selRoom).observe('change', this.onChangeView.bind(this));
		}
		//Calculate width
		//Prototype compatibility
		//getLayout() has added in 1.7 prototype librairy
		try
		{
			this.width = $$('div.roomplanning_room')[0].getLayout().get('width');
		}
		catch(err)
		{
			this.width = $$('div.roomplanning_room')[0].getWidth() - 30//paddind;
		}
		this.hourOpen = parseFloat(RoomParams.hourOpen);
		this.hourClose = parseFloat(RoomParams.hourClose);
		this.nbHour = parseFloat(this.hourClose - this.hourOpen);
		this.distHour = this.width / this.nbHour;

		if( $('roomplanning_input_date') )
		{
			var option = (RoomParams.weekopen==true) ? DatePickerUtils.noDatesBefore(0) : DatePickerUtils.noWeekends().append(DatePickerUtils.noDatesBefore(0));
			new DatePicker({
				relative: 'roomplanning_input_date',
				language: RoomParams.language,
				keepFieldEmpty:true,
				dateFormat: [ ["yyyy","mm","dd"], "-" ],
				dateFilter: option,
				cellCallback:this.loadDataDatePicker
			});

		}
		this.initHTML();
	},
	initHTML: function(){
		if( $$('div.roomplanning_track').length > 0 )
		{
			this.createRules();
			this.createMapTimes();
			$$('div.roomplanning_room').each(Element.hide);
			$$('div.roomplanning_room')[0].show();


			$$('div.header').each(function(ele){
			var id = ele.id;
			ele.observe('click', function(e){
				Event.stop(e);
				var el = $('roomplanning_room_'+id);
				(el.style.display=='none') ? el.show() : el.hide();
			});
		});
		}
	},
	createRules: function(){
		var div_time = '';
		for(var i=0; i <= this.nbHour; i += 0.5)
		{
			var left = this.calculRulesWidthY(i);
			var txt = ( this.isInteger(i+this.hourOpen) ) ? i+this.hourOpen : '&nbsp;';
			var className = ( i%2 == 0 || this.isInteger(i) ) ? 'roomplanning_time_hour' : 'roomplanning_time_min';
			div_time += '<div class="'+className+'" style="top:2px;left:'+left+'px;">'+txt+'</div>';
		}
		$$('#planningRoom div.roomplanning_time').each(function(ele){
			var div = new Element('div');
			div.insert({bottom:div_time});
			$(ele).insert(div);
		});
	},
	onChangeView: function(){
		var sel = ( $('roomplanning_selRoom') ) ? $F('roomplanning_selRoom') : 0;
		this.loadData($F('roomplanning_input_date'),sel);
	},
	loadData: function(day,room_id){
		//Waiting icon
		if($('roomplanning_calendar')) $('roomplanning_calendar').hide();
		$('planningRoom_all').update("<div id='message' class='updated_room'><p><img src='"+RoomParams.wait+"' alt=''/>"+RoomParams.waiting+"</p></div>");

		var options = $('roomplanning_form_options').serialize(true);
		options.day = day;
		options.security = RoomParams.ajaxnonce;
		new Ajax.Request(RoomParams.ajaxurl, {
			method: 'post',
			parameters: options,
			onSuccess: function(response) {
				var rep = eval('(' + response.responseText + ')');
				$('roomplanning_input_date').value = rep.day;
				$('planningRoom_all').update(rep.html);
				myPlanningRoom.initHTML();
				if($('roomplanning_calendar'))
					$('roomplanning_calendar').show();
				$$('div.roomplanning_room').each(Element.hide);
				if(rep.room_id > 0)
					$('roomplanning_room_'+rep.room_id).show();
				else
					$$('div.roomplanning_room')[0].show();
			}
		});
	},
	onChangeView: function(){
		var day = $F('roomplanning_input_date');
		var sel = ( $('roomplanning_selRoom') ) ? $F('roomplanning_selRoom') : 0;
		this.loadData(day,sel);
	},
	loadDataDatePicker: function(){
		var day = $F('roomplanning_input_date');
		var sel = ( $('roomplanning_selRoom') ) ? $F('roomplanning_selRoom') : 0;
		myPlanningRoom.loadData(day,sel);
	},
	/*****
	* Routines dom
	*/
	createMapTimes:function(){
		var hourOpen = this.hourOpen;
		var distHour = this.distHour;
		$$('#planningRoom div.roomplaning_maptime').each(function(ele){
			var arr = ele.readAttribute('attr').split(new RegExp("[ ]", "g"));
			var t_d = parseFloat(arr[0]) - hourOpen;
			var t_f = parseFloat(arr[1]) - hourOpen;
			var left = parseFloat(t_d * distHour);
			var width = parseInt(t_f * distHour - left);
			ele.setStyle({
				left : ''+left + 'px',
				width: ''+width + 'px'
			});
		});
	},
	calculRulesWidthY: function(hour){
		if( hour == 0) return 0;
		return parseFloat(hour*this.width/this.nbHour);
	},
	isInteger: function(s) {
		return (s.toString().search(/^[0-9]+$/) == 0);
	}
});

var myPlanningRoom;
var tb_pathToImage = RoomParams.blogurl + "/wp-includes/js/thickbox/loadingAnimation.gif";
var tb_closeImage = RoomParams.blogurl + "/wp-includes/js/thickbox/tb-close.png";
Event.observe(window,'load',function(){
	if($('planningRoom'))
		myPlanningRoom = new ProtoRoomPlanning();
});