function BlockLike(config){
    var that       = this;
    this.changeNum = config.changeNum;
    this.liked     = (config.liked == 1);
    this.available = true;
    this.btn       = $('#'+config.container);
    this.btn.click(function(){
        if(that.liked){
            that.UnLike();
        }else{
            that.Like();
        }
    });
}
BlockLike.prototype = {
    'Like'   : function(){
        //iframe提交表单 
    },
    'UnLike' : function(){
        
    }
}
