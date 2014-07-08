/**
 * 分页组件
 * @param {HTMLElement} 分页容器 
 * opts = {
 *  callback:{},   //回调函数
    page:1,      //当前页
    pageCount:100,  //总页数
    argName:'pg'  //页数名称
 * }
 */
function Page(container, callback, opts) {
    if (!container) return;
    this.container = (typeof(container) == "object") ? container : $(container); //分页容器
    this.page = 1; //当前页
    this.pageCount = 100; //总页数
    this.argName = "pg"; //参数名
    this.pagecap = 5; //分页个数
    this.callback = callback; //回调函数
    this.update = true;

    var _config = {
        page: 1, //当前页
        totalCount: 100, //总数
        pageCount: 100, //总页数
        pagecap: 5, //显示分页个数
        argName: 'pg', //页数名称
        update: true // 点击后及时更新
    };
    opts = opts || _config;
    for (var p in opts) {
        if (typeof(opts[p]) != "undefined") {
            this[p] = opts[p];
        }
    }
    this.render();
}
$.extend(Page.prototype, {
    /**
     * 层渲染
     */
    render: function() {
        this.initialize();
        this.bind();
    },
    /**
     * 层数据初试化
     */

    initialize: function() {
        this.checkPages();
        var html = this.createHtml();
        this.container.html(html);
    },

    bind: function() {
        var me = this,
            num;
        $('.index-page').delegate('a', 'click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!$(this).hasClass('next-none')) {
                num = parseInt($(this).attr('data-page'));
                me.toPage(num);
            }
        });
    },

    /**
     * 检查页数值
     */

    checkPages: function() {
        if (isNaN(parseInt(this.page))) this.page = 1;
        if (isNaN(parseInt(this.pageCount))) this.pageCount = 1;
        if (this.page < 1) this.page = 1;
        if (this.pageCount < 1) this.pageCount = 1;
        if (this.page > this.pageCount) this.page = this.pageCount;
        this.page = parseInt(this.page);
        this.pageCount = parseInt(this.pageCount);
    },
    /**
     * 获取当前页
     */
    getPage: function() {
        var args = location.search;
        var reg = new RegExp('[\?&]?' + this.argName + '=([^&]*)[&$]?', 'gi');
        var chk = args.match(reg);
        this.page = RegExp.$1;
    },

    createHtml: function() {
        var strHtml = [],
            prevPage = this.page - 1,
            nextPage = this.page + 1;

        strHtml.push('<p class="index-page">');
        if (prevPage < 1) {} else {
            if (this.page >= this.pagecap) {
                strHtml.push('<span><a href="javascript:void(0)" tid="toFirstPage" data-page="1" class="first-page">首页</a></span>');
            };
            strHtml.push('<span><a href="javascript:void(0)" tid="toPrevPage" class="prev-page"  data-page="' + prevPage + '" ><上一页</a></span>');
        };
        if (this.page < this.pagecap) {
            if (this.page % this.pagecap == 0) {
                var startPage = this.page - this.pagecap - 1;
            } else {
                var startPage = this.page - this.page % this.pagecap + 1;
            };
            var endPage = startPage + this.pagecap - 1;
        } else {
            var spt = Math.floor(this.pagecap / 2);
            var mod = this.pagecap % 2 - 1;

            if (this.pageCount > this.page + spt) {
                var endPage = this.page + spt;
                var startPage = this.page - spt - mod;
            } else {
                var endPage = this.pageCount;
                var startPage = this.page - spt - mod;
            };
        };
        if (this.page > this.pageCount - this.pagecap && this.page >= this.pagecap) {
            var startPage = this.pageCount - this.pagecap + 1;
            var endPage = this.pageCount;
        }
        for (var i = startPage; i <= endPage; i++) {
            if (i > 0) {
                if (i == this.page) {
                    strHtml.push('<span class="cur-page">' + i + '</span>');
                } else {
                    if (i >= 1 && i <= this.pageCount) {
                        var tid = '';
                        if (i == 2) {
                            tid = 'tid=secPageNum'
                        }
                        strHtml.push('<span><a class="sel-page" data-page="' + i + '" href="javascript:void(0)" ' + tid + '>' + i + '</a></span>');
                    };
                };
            };
        };
        if (nextPage > this.pageCount) {
            strHtml.push('<span><a href="javascript:void(0)" tid="toNextPage" class="next next-none">下一页&gt;</a></span>');
        } else {
            strHtml.push('<span><a class="next-page" data-page="' + nextPage + '" href="javascript:void(0)" tid="toNextPage" >下一页></a></span>');
        };
        strHtml.push('</p>');

        return strHtml.join("");
    },
    toPage: function(page) {
        var turnTo = page ? page : 1;
        if (typeof(this.callback) == "function") {
            this.callback(turnTo);
            this.page = turnTo;
        };
        if (this.update) {
            this.render();
        }
    }
});

