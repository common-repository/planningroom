var ProtoRoomPlanningAdmin = Class.create(ProtoRoomPlanning,{
	initialize: function(){
		this.endResponse();
	},
	endResponse: function(){
		//Animation for update message
		$$('div.update').each(function(ele){
			var elememt = ele;
			new Effect.Appear( ele, {duration:4, fps:25, from:1.0, to:0.0, afterFinish:function(){elememt.hide();}});
		});
	},
	onChangeView: function(){
		var sel = ( $('roomplanning_selRoom') ) ? $F('roomplanning_selRoom') : 0;
		this.loadData($F('roomplanning_input_date'),sel);
	},
	loadData: function($super,day,room_id){
		$super(day,room_id);
	},
	isInteger: function(s) {
	    return (s.toString().search(/^[0-9]+$/) == 0);
	},
	updateForm: function(idWaiting,idForm,idAjaxResponse){
		var options = $(idForm).serialize(true);
		options.security= RoomParams.ajaxnonce;
		$(idWaiting).show();
		$(idForm).disable();
		new Ajax.Request(RoomParams.ajaxurl, {
			method: 'post',
			parameters: options,
			onSuccess:function(response) {
				var rep = eval('(' + response.responseText + ')');
				$(idAjaxResponse).update(rep.html);
				$(idWaiting).hide();
				new Effect.Appear(idAjaxResponse,{duration:4, fps:25, from:1.0, to:0.0, afterFinish:function(){$(idAjaxResponse).hide();}});
				$(idForm).enable();
			}
		});
	},
	modify: function(arrIdElementHide,arrIdElementShow,aIdEdit,type){
		arrIdElementHide.each(Element.hide);
		arrIdElementShow.each(Element.show);
		var a = $('a_'+type+'_'+aIdEdit);
		if(a.text == RoomParams.edit){
			a.setAttribute('href','javascript:protoAdminList.cancelUpdate(\''+aIdEdit+'\',\''+type+'\');');
			a.text =RoomParams.cancel;
		}else{
			 a.setAttribute('href','javascript:protoAdminList.editRow(\''+aIdEdit+'\',\''+type+'\');');
			 a.text =RoomParams.edit;
		}
	}
});

var ProtoRoomPlanningAdminList = Class.create(ProtoRoomPlanningAdmin,{
	initialize: function($super){
		$super();
	},
	endResponse: function($super){
		$super();
	},
	modify: function($super,arrIdElementHide,arrIdElementShow,aIdEdit,type){
		$super(arrIdElementHide,arrIdElementShow,aIdEdit,type);
	},
	editRow: function(id,type){
		this.modify(new Array(type+'_desc_'+id),new Array('form_'+type+'_'+id),id,type);
	},
	cancelUpdate: function(id,type){
		this.modify(new Array('form_'+type+'_'+id),new Array(type+'_desc_'+id),id,type);
	},
	removeRoom: function(id){
		this.removeTr(id,'room');
	},
	removeMember: function(id){
		this.removeTr(id,'member');
	},
	groupAction: function(type){
		var input = '';
		$$('.chk_'+type).each(function(ele){if(ele.checked)input += ele.value+',';});
		if(''!=input)
			this.removeAllTr(type,input);
		if(type!='planning')
			protoAdminList.paginationTable(1,'list_planning');
	},
	submitUpdateForm: function(id,type){
		var options = $('form_'+type+'_'+id).serialize(true);
		$('waiting_'+type+'_'+id).show();
		$('form_'+type+'_'+id).disable();
		options.security = RoomParams.ajaxnonce;
		new Ajax.Request(RoomParams.ajaxurl, {
			method: 'post',
			parameters: options,
			onSuccess:function() {
				if(type=='member')
				{
					$('ref_member_mail'+id).update($F('update_member_mail'+id));
					$('ref_'+type+'_desc'+id).update($F('update_'+type+'_desc'+id));
				}
				else
				$('room_desc_'+id).update($F('update_'+type+'_desc'+id));
				protoAdminList.modify(new Array('waiting_'+type+'_'+id,'form_'+type+'_'+id),new Array(type+'_desc_'+id),id,type);
				$('form_'+type+'_'+id).enable();
				new Effect.Highlight('tr_'+type+'_'+id,{duration:1,fps:25,from:0.0,to:1.0,startcolor:'#FCDD88',endcolor:'#FFF5DB',restorecolor:'#ffffff'});
				protoAdminList.endResponse();
			}
		});

	},
	removeTr: function(id,type){
		var options = $('form_'+type+'_'+id).serialize(true);
		options.security = RoomParams.ajaxnonce;
		options.action = 'remove'+type.capitalize();
		options.id = id;
		var divWaiting = this.createWaitingDiv('list_'+type);
		new Ajax.Request(RoomParams.ajaxurl, {
			method: 'post',
			parameters: options,
			onSuccess:function() {
				protoAdminList.paginationTable(1,'list_'+type);
				if(type!='planning')
					protoAdminList.paginationTable(1,'list_planning');
			}
		});
	},
	removeAllTr: function(type,input){
		var options = $H({security:RoomParams.ajaxnonce,action:'removeGroup',type:type,input:input});
		var divWaiting = this.createWaitingDiv('list_'+type);
		new Ajax.Request(RoomParams.ajaxurl, {
			method: 'post',
			parameters: options,
			onSuccess:function() {
				protoAdminList.paginationTable(1,'list_'+type);
			}
		});
	},
	submitUpdateRoom: function(id){
		this.submitUpdateForm(id,'room');
	},
	submitUpdateMember: function(id){
		this.submitUpdateForm(id,'member');
	},
	free: function(id){
		var divWaiting = this.createWaitingDiv('list_planning');
		var options = $H({security:RoomParams.ajaxnonce,action:'freeTime',id:id});
		new Ajax.Request(RoomParams.ajaxurl, {
			method: 'post',
			parameters: options,
			onSuccess:function() {
				protoAdminList.paginationTable(1,'list_planning');
			}
		});
	},
	createWaitingDiv: function(idTr){
		$(idTr).update("<td colspan='5' class='updateMsg'><img src='"+RoomParams.wait+"' alt=''/> "+RoomParams.waiting+'</td>');
	},
	paginationTable: function(page_id,idDiv){
		var divWaiting = this.createWaitingDiv(idDiv);
		var options = $H({security:RoomParams.ajaxnonce,action:'pagination',page_id:page_id,type:idDiv});
		new Ajax.Request(RoomParams.ajaxurl, {
			method: 'post',
			parameters: options,
			onSuccess:function(response) {
				var rep = eval('(' + response.responseText + ')');
				$(idDiv).hide();
				$(idDiv).update(rep.html);
				$(idDiv+'_pagination').update(rep.pagination);
				new Effect.Appear(idDiv,{duration:1, fps:25, from:0.0, to:1.0});
				protoAdminList.endResponse();
			}
		});
	},
});

var ProtoRoomPlanningAdminEdit = Class.create(ProtoRoomPlanningAdmin,{
	initialize: function($super){
		$super();
		var option = (RoomParams.weekopen) ? DatePickerUtils.noDatesBefore(0) : DatePickerUtils.noWeekends().append(DatePickerUtils.noDatesBefore(0));
		var datePicker = new DatePicker({
			relative: 'event_date_deb',
			language: RoomParams.language,
			keepFieldEmpty:true,
			dateFormat: [ ["yyyy","mm","dd"], "-" ],
			dateFilter: option
		});
		$('member-search-input').observe('keyup', this.validateOnChange.bind(this));
		this.selMember = 'roomplanning_selMember';
		$(this.selMember).observe('change', this.onChangeView.bind(this));
	},
	onChangeView: function(){
		var sel = ( $('roomplanning_selRoom') ) ? $F('roomplanning_selRoom') : 0;
		this.loadData($F('roomplanning_input_date'),sel);
	},
	loadData: function($super,day,room_id){
		$super(day,room_id);
	},
	endResponse: function($super){
		$super();
	},
	validateOnChange: function(event){
		var value = '';
		value = $('member-search-input').getValue();
		if( value.length > 1 )
		{
			$('ajax-response').update();
			$('ajax-response').show();
			new Ajax.Updater('ajax-response',RoomParams.ajaxurl,{parameters: {action:'searchMember',method:'post',searchterm: value, security: RoomParams.ajaxnonce } });
		}
	},
	memberSelect: function(id_member){
		$('sel_member_id').value = id_member;
		$('member-search-input').value = $('sel_member_id').options[$('sel_member_id').selectedIndex].text;
		$('ajax-response').hide();
	},
	updateForm: function($super,idWaiting,idForm,idAjaxResponse){
		$super(idWaiting,idForm,idAjaxResponse);
	},
	submitForm: function(type,refresh){
		var options = $('form_'+type).serialize(true);
		options.security= RoomParams.ajaxnonce;
		$('waiting_options_'+type).show();
		$('form_'+type).disable();
		new Ajax.Request(RoomParams.ajaxurl, {
			method: 'post',
			parameters: options,
			onSuccess:function(response) {
				var rep = eval('(' + response.responseText + ')');
				$('ajax_response_'+type).update(rep.html);
				$('waiting_options_'+type).hide();
				new Effect.Appear('ajax_response_'+type,{duration:4, fps:25, from:1.0, to:0.0, afterFinish:function(){$('ajax_response_'+type).hide();}});
				$('form_'+type).enable();
				if(rep.error > 1 )
				{
					if(type=='event')
						myPlanningRoom.loadData(rep.data,rep.room_id);
					else if(type=='room')
						$('sel_room_id').insert({bottom:'<option value="'+rep.data+'">'+rep.datahtml+'</option>'});
					else
						$('sel_member_id').insert({bottom:'<option value="'+rep.data+'">'+rep.datahtml+'</option>'});
				}
			}
		});
	}

});

var ProtoRoomPlanningAdminOptions = Class.create(ProtoRoomPlanningAdmin,{
	initialize: function($super){
		$super();
		$('waiting_options').hide();
		this.booking_yesno = 'booking_yesno';
		this.updateBooking();
		$(this.booking_yesno).observe('change', this.onChangeSelected.bind(this));
	},
	endResponse: function($super){
		$super();
	},
	updateBooking: function(){
		($(this.booking_yesno).value == 0 ) ? $('trOptionDayBooking').hide() : new Effect.Appear('trOptionDayBooking',{duration:1.0, from:0.0, to:1.0});
		($(this.booking_yesno).value == 0 ) ? $('trWpRegistration').hide() : new Effect.Appear('trWpRegistration',{duration:1.0, from:0.0, to:1.0});
	},
	updateForm: function($super,idWaiting,idForm,idAjaxResponse){
		$super('waiting_options','form_option','ajax_response');
	},
	onChangeSelected: function(){
		this.updateBooking();
	}
});

var protoAdminEdit;
var protoAdminList;
var protoAdminOptions;
Event.observe(window,'load',function(){
	//Admin page to modify settings
	if($('pageOption')) protoAdminOptions = new ProtoRoomPlanningAdminOptions;
	//Admin page to add room, add members and add event
	if($('pageEdit')) protoAdminEdit = new ProtoRoomPlanningAdminEdit;
	//Admin page edit room, members and event
	if($('pageList')) protoAdminList = new ProtoRoomPlanningAdminList;
});