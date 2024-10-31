var ProtoRoomPlanningBooking = Class.create({
	initialize: function(){},
	submitBooking: function(idForm,idRep,day){
		$(idRep).update("<img src='"+RoomParams.wait+"' alt=''/>&nbsp;&nbsp;&nbsp;"+RoomParams.waiting);
		new Ajax.Request(RoomParams.ajaxurl, {
			method: 'post',
			parameters: $(idForm).serialize(true),
			onSuccess: function(response) {
				var rep = eval('(' + response.responseText + ')');
				if( rep.error == 1 )
					$(idRep).update(rep.html);
				if( rep.error == 2 )
				{
					$('booking_frame').update(rep.html);
					new Effect.Appear('booking_frame', {duration:2, fps:25, from:1.0, to:0.0, afterFinish: function(){
						myPlanningRoom.loadData(day,rep.room_id);
						//Close popup
						tb_remove();}
					});
				}
			}
		});
		return false;
	}
});
var planningRoomBooking;
Event.observe(window,'load',function(){
	planningRoomBooking = new ProtoRoomPlanningBooking();
});