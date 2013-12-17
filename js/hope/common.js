var page = 2;
$(document).ready(function(){ 
       //首页图片的动态遮盖 
       $('.container').delegate('.item', 'mouseenter', function(){
            $(this).children().first().show();
            $(this).children().last().show();
            $(this).children().first().stop(true,true).animate({height:"40px"}, 300);
            $(this).children().last().stop(true,true).animate({height:"35px"}, 300); 
       });
       $('.container').delegate('.item', 'mouseleave', function(){
            $(this).children().first().stop(true,true).animate({height:"0px"}, 300);
            $(this).children().last().stop(true,true).animate({height:"0px"}, 300);
       }); 
       $("#add-more").click(function(){
            $.ajax({
               type:'post',
               url:'/?s=item&a=lists&format=json',
               data:{page:page},
               success:function(obj){
                   var html = '';
                   var data = obj.data;
                   for(i=0; i<data.length; i++){
                       html +='<div class="item">';
                       html +='<div class="mask">';
                       html +='<div class="avatar">';
                       html +='<a  href="http://weibo.com/u/'+data[i].userinfo.uid+'" target="_blank">';
                       html +='<img style="width:34px;height:34px;" src="'+data[i].userinfo.profile_image_url+'">';
                       html +='</a>';
                       html +='</div>';
                       html +='<div class="desc">';
                       html +='<div class="detail"><a href="http://weibo.com/u/'+data[i].userinfo.uid+'" target="_blank">'+data[i].userinfo.screen_name+'</a></div>';
                       html +='<div class="detail">'+data[i].timePass+'</div>';
                       html +='</div>';
                       html +='<div class="hearts"><a  class="heart"  href="javascript:;"></a></div>';
                       html +='<div style="clear:both;"></div>'; 
                       html +='</div>';
                       html +='<a href="http://mayhope.com/item/'+data[i].id+'.html" target="_blank">';
                       html +='<img src="' + data[i].face + '"></a>';
                       html +='<ul class="entry_tabs">';
                       html +='<li class="view-hearts">';
                       html +='<a title="围观数" tabindex="-1" href="http://mayhope.com/item/'+data[i].id+'.html">';
                       html +='<span>'+data[i].click+'</span><i>次围观</i>';
                       html +='</a>';
                       html +='</li>';
                       html +='<li class="view-tags">';
                       html +='<a title="喜欢" tabindex="-1" href="http://mayhope.com/item/'+data[i].id+'.html">';
                       html +='<span>'+data[i].like+'</span><i>喜欢</i>';
                       html +='</a>';
                       html +='</li>'; 
                       html +='<li class="view-share">';
                       html +='<a title="分享" tabindex="-1" href="javascript:;">';
                       html +='<i>分享</i></a>';
                       html +='</li>';
                       html +='</ul>';
                       html +='</div>';     
                   }
                   $('#main').append(html);  
                   page++;
                }
            });
       });       
});
