
function promptBtn1(e){
    layer.prompt({title: '输入值', shade: [0.1,'#fff']},function(value, index, elem){
        layer.close(index);
        if(parseInt(value)){
            //页面层
            layer.open({
                type: 2,
                shade: [0.1,'#fff'],
                title:'管理员手机验证',
                area: ['620px', '440px'], //宽高
                content: '/admin/setting/admin_verificode_page/id/'+$(e).attr('data-id')+'/value/'+value+'/type/'+$(e).attr('data-type')+'/more/'+$(e).attr('data-more')
            });
        }else{
            layer.msg('输入的值有误！');
        }
    });
}

function promptBtn2(e){
    layer.prompt({title: $(e).attr('data-msg'),formType: 2, shade: [0.1,'#fff']},function(value, index, elem){
        layer.close(index);
        if(value!=''){
            layer.open({
                type: 2,
                shade: [0.1,'#fff'],
                title:'管理员手机验证',
                area: ['620px', '440px'], //宽高
                content: '/admin/setting/admin_verificode_page/id/'+$(e).attr('data-id')+'/value/'+value+'/type/'+$(e).attr('data-type')+'/more/'+$(e).attr('data-more')
            });
        }else{
            layer.msg('输入内容有误！');
        }
    });
}

function confirmBtn(e){
    //页面层
    //alert($(e).attr('data-value'));
    if($(e).attr('data-value') != undefined){
        if($(e).attr('data-value')==''){
            layer.msg('没有需要操作的内容！');
            return false;
        }
    }
    layer.open({
        type: 2,
        shade: [0.1,'#fff'],
        title:'管理员手机验证',
        area: ['620px', '440px'], //宽高
        content: '/admin/setting/admin_verificode_page/id/'+$(e).attr('data-id')+'/type/'+$(e).attr('data-type')+'/more/'+$(e).attr('data-more')+'/value/'+$(e).attr('data-value')
    });
}

function openUserDetailBtn(e){
    //页面层
    var value = '';
    layer.open({
        type: 2,
        shade: [0.1,'#fff'],
        title:'用户数据详情',
        area: ['80%', '70%'], //宽高
        content: '/user/admin_user/detail/user_id/'+$(e).attr('data-id')+'/type/'+$(e).attr('data-type')
    });
}
