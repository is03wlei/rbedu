//页面配置
var config = {
    //年级id
    gradeId: 2014,
    //班级id
    classId: 1,
    // 分组id
    groupId: 1,
    //知识点
    knowledgeId: 1,
    // 题目类型
    exType: 'sc',
    // 题目难度级别
    exLevel: 1,
    // 页码
    pagenum: 0,
    // 单页返回结果数
    listnum: 3,
    //选择的题目编号
    eids: {},
    //知识点名称
    knowledgeName: '',
    //小组名称
    groupName: '',
    //发布题型与数量，题型id为键，此题型的题目数量为值
    pubData: {
        'sc': {
            name: '单选',
            num: 0,
            eids: []
        },
        'mc': {
            name: '多选',
            num: 0,
            eids: []
        },
        'jq': {
            name: '判断',
            num: 0,
            eids: []
        },
        'bq': {
            name: '填空',
            num: 0,
            eids: []
        },
        'sq': {
            name: '问答',
            num: 0,
            eids: []
        }
    }
};
//设置左分隔符为 <!
baidu.template.LEFT_DELIMITER = '<#';

//设置右分隔符为 <!  
baidu.template.RIGHT_DELIMITER = '#>';

//初始化入口
function init() {
    initFirstPage();
    initTree();
    bindEvents();
    getGroups(config.gradeId, config.classId);
}


function initFirstPage() {
    var html = baidu.template('students_page', {
        data: {}
    });
    $('#main_wrapper').html(html);
}
/**
 * 初始化知识点结构目录树
 * @return {[type]} [description]
 */
function initTree() {
    var teacherId = null;
    var tree = $('#tree').tree({
        data: knowlageData,
        openedIcon: "/images/f-icon.png",
        closedIcon: "/images/f-icon.png",
        callback: {
            onchange: function(node, tree_obj) {
                console.log('tree onchange:', node, tree_obj)
            },
            onopen: function(node, tree_obj) {
                console.log('tree onchange:', node, tree_obj)
            }
        }
    });
}

//绑定事件
function bindEvents() {
    $('#grade_select').on('change', function(e) {
        var gradeId = $(this).val();
        config.gradeId = gradeId;
        getGroups(config.gradeId, config.classId);
    });
    $('#class_select').on('change', function(e) {
        var classId = $(this).val();
        config.classId = classId;
        getGroups(config.gradeId, config.classId);
    });
    $('#group_select').on('change', function(e) {
        var gid = $(this).val();
        config.groupId = gid;
        getStudents(gid);
    });
    $('#form1 .next').on('click', function(e) {
        getExamList();
    });
    //获取知识点id

}


/**
 * 根据年级和班级获取分组数据
 * @param  {[type]} gradeId [description]
 * @param  {[type]} classId [description]
 * @return {[type]}         [description]
 */
function getGroups(gradeId, classId) {
    console.log('getGroups');
    //
    $.ajax({
        url: '/exercise_assign/getGroups/' + gradeId + '/' + classId,
        async: true,
        dataType: 'json',
        context: this,
        success: function(json) {
            console.log('获取分组数据成功：', json);
            if (json && json.length > 0) {
                var htmls = [];
                for (var i = 0; i < json.length; i++) {
                    htmls.push('<option value="' + json[i].GID + '">' + json[i].GroupName + '</option>');
                    if (i == 0) {
                        config.groupName = json[i].GroupName;
                    }
                }
                $('#group_select').html(htmls.join(''));
                //创建分组的学生名单表格
                $('#group_select').trigger('change');
            }
        },
        error: function(err) {
            console.log('获取分组数据失败：', err);
        }
    });
}

/**
 * 根据分组数据获取学生名单
 * @param  {[type]} gid 分组id
 * @return {[type]}     [description]
 */
function getStudents(gid) {
    console.log('getStudents');
    $.ajax({
        url: '/exercise_assign/getStudents/' + gid,
        async: true,
        dataType: 'json',
        context: this,
        success: function(json) {
            console.log('获取学生名单数据成功：', json);
            if (json && json.length > 1) {
                createStuList(json);
            }
        },
        error: function(err) {
            console.log('获取学生名单数据失败：', err);
        }
    });
}

/**
 * 创建学生名单表格
 */
function createStuList(data) {
    var html = baidu.template('students_table', {
        list: data
    });
    $('#stu_wrapper').html(html);
}


/**
 * 根据题目类型和题目难度请求题目列表
 * @param  {[type]} exType  [description]
 * @param  {[type]} exLevel [description]
 * @return {[type]}         [description]
 */
function getExamList(exType, exLevel, pn) {
    console.log('getExamList');
    var kid = config.knowledgeId,
        exType = exType || config.exType,
        exLevel = exLevel || config.exLevel,
        pagenum = pn || config.pagenum,
        listnum = config.listnum;
    $.ajax({
        url: '/exercise_assign/getProblems/' + kid + '/' + exType + '/' + exLevel + '/' + pagenum + '/' + listnum,
        async: true,
        dataType: 'json',
        context: this,
        success: function(json) {
            console.log('请求题目列表数据成功：', json);
            if (json && json.length > 0) {
                switchHomework(json);
            }
        },
        error: function(err) {
            console.log('请求题目列表数据失败：', err);
        }
    });

}

// 跳转到选择题目
function switchHomework(data) {
    data = data || [];
    var html = baidu.template('homework_list', {
        worklist: data
    });
    $('#main_wrapper').html(html);
    //初始分页组件
    initPage();
    //初始化状态
    initStatus();
    //绑定页面事件
    bindSelectWork();
}
//初始分页组件
function initPage() {
    var container = $("#homework-page");
    //分页组件页码数从1开始
    var opts = {
        pageCount: 5,
        page: config.pagenum + 1,
        update: true
    };
    var page = new Page(container, function(pn) {
        console.log('分页组件：', pn);
        config.pagenum = pn - 1;
        getExamList(config.exType, console.exLevel, config.pagenum);
    }, opts);
}

//初始化单选框的选择状态
function initStatus() {
    $('#exercise_form input').each(function(index) {
        var input = $(this);
        if (input.val() == config.exType) {
            input.attr('checked', true);
        } else {
            input.attr('checked', false);
        }
    });
    $('#level_form input').each(function(index) {
        var input = $(this);
        if (input.val() == config.exLevel) {
            input.attr('checked', true);
        } else {
            input.attr('checked', false);
        }
    });
}

//选择题目页面事件绑定
function bindSelectWork() {
    $('#exercise_form input').on('click', function(e) {
        $(this).attr('checked', true);
        var type = $(this).val();
        config.exType = type;
        config.pagenum = 0;

        getExamList(config.exType, config.exLevel);
        console.log('exercise_form:', type);
    });
    $('#level_form input').on('click', function(e) {
        $(this).attr('checked', true);
        var level = $(this).val();
        config.exLevel = level;
        config.pagenum = 0;

        getExamList(config.exType, config.exLevel);
    });
    $('#form2 input.next').on('click', function(e) {
        console.log('#form2 input.next');
        if (!config.eids) {
            alert("请添加作业列表");
            return;
        }
        switchPubPage();
    });
    $('#form2 input.back').on('click', function(e) {
        console.log('#form2 input.back');
        // getGroups(config.gradeId, config.classId);
        init();
    });
    // 添加作业
    $('.hmwork-table .add-work em').on('click', function(e) {
        //添加作业到队列中
        setWorkList($(this));
    });
    //查看答案
    $('.hmwork-table .ans').on('click', function(e) {
        var id = $(this).attr('id');
        $(this).parents('td').find('.ans-cont').toggle();
        $(this).text('收起答案');
    });
}


//添加作业到队列中
function setWorkList(obj) {
    var type = obj.data('type');
    var eid = obj.data('eid');
    var extype = obj.data('extype');
    var text, ntype;

    if (type == 'add') {
        addWork(eid, extype);
        ntype = 'remove';
        text = '取消作业';
    } else if (type == 'remove') {
        removeWork(eid, extype);
        ntype = 'add';
        text = '添加作业';
    }

    obj.data('type', ntype);
    obj.attr('title', text);
    obj.parent().find('i').text(text);
    obj.parentsUntil('table').find('.add-work').toggleClass('remove-style');
}

function addWork(eid, extype) {
    ++config.pubData[extype].num;
    config.pubData[extype].eids.push(eid);
}

function removeWork(eid, extype) {
    --config.pubData[extype].num;
    var eids = config.pubData[extype].eids;
    for (var i = 0; i < eids.length; i++) {
        if (eids[i] == eid) {
            config.pubData[extype].eids.splice(i, 1);
        }
    }
}

/**
 * 跳转到“发布作业任务”页面
 * @return {[type]} [description]
 */
function switchPubPage(data) {
    data = data || {
        kname: '知识点',
        gname: '分组名称'
    };
    var html = baidu.template('pub_list', {
        pubdata: data,
        hmwork_data: config.pubData
    });
    $('#main_wrapper').html(html);
    //
    initPubPage();
}

function initPubPage() {
    $('#form3 input.next').on('click', function(e) {
        console.log('#form3 input.next');
        pubWorkData();

    });
    $('#form3 input.back').on('click', function(e) {
        console.log('#form3 input.back');
        getExamList();
    });
}

/**
 * 提交发布作业
 * @return {[type]} [description]
 */
function pubWorkData() {
    var postdata = getPostData();
    $.post("/exercise_assign/publish", postdata,
        function(json) {
            console.log('发布作业成功：', json);
        }, "json");
}
/**
 * 获取发布作业数据
 * @return {[type]} [description]
 */
function getPostData() {
    var data = config.pubData;
    var selEids = [];
    for (var p in data) {
        if (typeof data[p] == 'object') {
            for (var s in data[p]) {
                if ($.isArray(data[p][s])) {
                    for (var i = 0; i < data[p][s].length; i++) {
                        selEids.push({
                            EID: data[p][s][i],
                            ExerciseType: p.toString()
                        });
                    }
                }
            }
        }
    }
    var post = {
        exerciseIdSelected: selEids,
        GID: config.groupId,
        KID: config.knowledgeId,
        KnowledgeName: config.knowledgeName,
        GroupName: config.groupName
    };

    return post;
}

$(document).ready(init);