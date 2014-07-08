{%extends file='layout.tpl'%}
{%block name="page-css"%}
<link rel="stylesheet" type="text/css" href="../css/jqtree.css" />
<link rel="stylesheet" type="text/css" href="../css/homework.css" />
<link rel="stylesheet" type="text/css" href="../css/webuploader.css" />
<style>
.img-holder{width:398px;min-height:90px;border:gray 1px solid;}
.img-holder:hover{border:red 1px solid;}
.help-block{color:red;}
</style>
{%/block%}
{%block name="content"%}
	<!-- 请把自己的代码放在这里 -->
	<div class="select-cont">
         <div class="ex-cont">
           <h2>题目类型</h2>
           <form class="exercise-form" name="exercise_form">
              <input type="radio" name="ex-type" value="sc" checked="checked"/>单选
              <input type="radio" name="ex-type" value="mc" />多选
              <input type="radio" name="ex-type" value="jq" />判断
              <input type="radio" name="ex-type" value="bq" />填空
              <input type="radio" name="ex-type" value="sq" />问答                     
           </form>
         </div>
         <div class="level-cont">
           <h2>题目难度</h2>
           <form class="level-form" name="level_form">
              <input type="radio" name="level-type" value="1" checked="checked"/>容易
              <input type="radio" name="level-type" value="2" />一般
              <input type="radio" name="level-type" value="3" />中等
              <input type="radio" name="level-type" value="4" />困难                      
           </form>
         </div>             
       </div>
       <div class="main-cont">
          <div class="struct-tree">
            <h2>知识点结构</h2>
			<input type="hidden" name="kid"/>
			<div id="file-error-tree" class="help-block" style="display:none;">
					 请选择知识点
			</div>
			<div id="tree"></div>
          </div>
           <div class="hmwork-text" >
              <dl>
                <dt>题目描述</dt>
                <dd>
                    <div id="ex-desc" class="img-holder">
				    </div>
					<div id="uploader-desc" class="wu-example" style="display:none;">
						<!--用来存放文件信息-->
						<div id="thelist-desc" class="uploader-list"></div>
						<div class="btns">
							<div id="picker-desc">选择文件</div>
						</div>
					</div>
					<div id="file-error-desc" class="help-block" style="display:none;">
							 请选择文件
					</div>
                </dd>
                <dt>题目内容</dt>
                <dd>              
                    <div id="ex-content" class="img-holder">
				    </div>
					<div id="uploader-content" class="wu-example" style="display:none;">
						<!--用来存放文件信息-->
						<div id="thelist-content" class="uploader-list"></div>
						<div class="btns">
							<div id="picker-content">选择文件</div>
						</div>
					</div>
					<div id="file-error-content" class="help-block" style="display:none;">
							 请选择文件
					</div>
                </dd>
                <dt>答案个数</dt>
                <dd>              
                  <input class="ans-count" id="answer-num"></input>
				  <div id="file-error-num" class="help-block" style="display:none;">
						 请填写答案个数并只能填写数字
				</div>
                </dd>                
                <dt>答案文本(用“;”分开)</dt>
                <dd>              
                  <textarea id="answer-content"></textarea>
				  <div id="file-error-text" class="help-block" style="display:none;">
						 请填写答案文本
				</div>
                </dd>
                <dt>题目解答</dt>
                <dd>              
                    <div id="ex-answer" class="img-holder">
				    </div>
					<div id="uploader-answer" class="wu-example" style="display:none;">
						<!--用来存放文件信息-->
						<div id="thelist-answer" class="uploader-list"></div>
						<div class="btns">
							<div id="picker-answer">选择文件</div>
						</div>
					</div>
					<div id="file-error-answer" class="help-block" style="display:none;">
							 请选择文件
					</div>
                </dd>                                            
              </dl>  
              <p class="add-video-cont">
                <input type="button" value="" class="ans-video-btn"/>
              </p>      
           </div>
       </div>
    </div>       
    <form class="submit-form" name="form2">
         <input type="button" value="" class="submit-btn"/>
         <input type="button" value="" class="add-btn"/>
     </form>
{%/block%}
{%block name="page-js"%}
<script>
	var knowlageData = {%$tree%};
	console.log(knowlageData);
</script>
<script type="text/javascript" src="../js/webuploader.min.js"></script>
<script type="text/javascript" src="../js/jquery.validate.min.js"></script>
<script type="text/javascript" src="../js/tree.jquery.js"></script>
<script type="text/javascript" src="../js/exercise-manage.js"></script>
{%/block%}
