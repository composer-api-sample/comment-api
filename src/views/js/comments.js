function toggleReply(index){
	// alert("hi");
	// alert(index);
	$('#newReply'+index).toggle('1000');
}
function toggleEditComment(index){
	$('#editCommentDiv'+index).toggle('1000');
}
function toggleEditReply(index){
	$('#editReplyDiv'+index).toggle('1000');
}
function toggleReplyList(id){
	$('#'+id).toggle('1000');
}
$(function(){
	$('.toggleNewReply').click(function(){
		var index = $(this).data('target');
		toggleReply(index);
		$('html, body').animate({
			scrollTop: $('#newReply'+index).offset().top
		},2000);
	});
});