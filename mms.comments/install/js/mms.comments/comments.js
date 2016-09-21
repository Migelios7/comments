function activateButtons(){
    $('body').on('click', 'button.open-modal', function(){
        var parent = $(this).attr('data-parent');
        $('#parent_comment_id').val(parent);
        $('#commentModal').modal('show');
    });

    $('#addComment').on('click', function () {
        $('#comment_form').find('input[type="submit"]').click();
        $('#commentModal').modal('hide');
    });
}

$(function(){
    activateButtons();
});

BX.addCustomEvent("onAjaxSuccess", BX.delegate(function(data,object,params){
    activateButtons();
}, this));