/**
*资料上传
*by baixu
*/

console.log('exercise upload begin');

$(document).ready(
	function(){
		
		var tree = $('#tree').tree({
			data:knowlageData,
			openedIcon:"/images/f-icon.png",
			closedIcon:"/images/f-icon.png"
		});
		
		var fileNameDesc = '';
		
		var uploaderDesc = WebUploader.create({

			// swf文件路径
			swf: '/js/Uploader.swf',

			// 文件接收服务端。
			server: '/savepic/save',

			// 选择文件的按钮。可选。
			// 内部根据当前运行是创建，可能是input元素，也可能是flash.
			pick: '#picker-desc',

			// 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
			resize: false,
			 // 只允许选择图片文件。
			accept: {
				title: 'Images',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
			},
			paste:$('#ex-desc')
		});
		
		// 当有文件添加进来的时候
		uploaderDesc.on( 'fileQueued', function( file ) {
			$('#thelist-desc').html( '<div id="' + file.id + '" class="item">' +
				'<h4 class="info">' + file.name + '</h4>' +
				'<p class="state">等待上传...</p>' +
			'</div>' );
			uploaderDesc.upload();
		});
		uploaderDesc.on( 'uploadSuccess', function( file,response ) {
			$('#file-error-desc').hide();
			fileNameDesc = response._raw;
			console.log(fileNameDesc);
			$('#ex-desc').html("<img src='/upload/"+fileNameDesc+"' width='398' />");
			$( '#'+file.id ).find('p.state').text('已上传');
		});

		uploaderDesc.on( 'uploadError', function( file ) {
			fileNameDesc = '';
			$( '#'+file.id ).find('p.state').text('上传出错');
		});
		
		var fileNameContent = '';
		
		var uploaderContent = WebUploader.create({

			// swf文件路径
			swf: '/js/Uploader.swf',

			// 文件接收服务端。
			server: '/savepic/save',

			// 选择文件的按钮。可选。
			// 内部根据当前运行是创建，可能是input元素，也可能是flash.
			pick: '#picker-content',

			// 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
			resize: false,
			 // 只允许选择图片文件。
			accept: {
				title: 'Images',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
			},
			paste:$('#ex-content')
		});
		
		// 当有文件添加进来的时候
		uploaderContent.on( 'fileQueued', function( file ) {
			$('#thelist-content').html( '<div id="' + file.id + '" class="item">' +
				'<h4 class="info">' + file.name + '</h4>' +
				'<p class="state">等待上传...</p>' +
			'</div>' );
			uploaderContent.upload();
		});
		uploaderContent.on( 'uploadSuccess', function( file,response ) {
			$('#file-error-content').hide();
			fileNameContent = response._raw;
			console.log(fileNameContent);
			$('#ex-content').html("<img src='/upload/"+fileNameContent+"' width='398' />");
			$( '#'+file.id ).find('p.state').text('已上传');
		});

		uploaderContent.on( 'uploadError', function( file ) {
			fileNameDesc = '';
			$( '#'+file.id ).find('p.state').text('上传出错');
		});
		
		var fileNameAnswer = '';
		
		var uploaderAnswer = WebUploader.create({

			// swf文件路径
			swf: '/js/Uploader.swf',

			// 文件接收服务端。
			server: '/savepic/save',

			// 选择文件的按钮。可选。
			// 内部根据当前运行是创建，可能是input元素，也可能是flash.
			pick: '#picker-answer',

			// 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
			resize: false,
			 // 只允许选择图片文件。
			accept: {
				title: 'Images',
				extensions: 'gif,jpg,jpeg,bmp,png',
				mimeTypes: 'image/*'
			},
			paste:$('#ex-answer')
		});
		
		// 当有文件添加进来的时候
		uploaderAnswer.on( 'fileQueued', function( file ) {
			$('#thelist-answer').html( '<div id="' + file.id + '" class="item">' +
				'<h4 class="info">' + file.name + '</h4>' +
				'<p class="state">等待上传...</p>' +
			'</div>' );
			uploaderAnswer.upload();
		});
		uploaderAnswer.on( 'uploadSuccess', function( file,response ) {
			$('#file-error-answer').hide();
			fileNameAnswer = response._raw;
			console.log(fileNameAnswer);
			$('#ex-answer').html("<img src='/upload/"+fileNameAnswer+"' width='398' />");
			$( '#'+file.id ).find('p.state').text('已上传');
		});

		uploaderAnswer.on( 'uploadError', function( file ) {
			fileNameDesc = '';
			$( '#'+file.id ).find('p.state').text('上传出错');
		});
		$('[name=ex-type]').change(function(){
			var checkVal = $('[name=ex-type]:checked').attr('value');
			if(checkVal == 'sq'){
				$('.need-hide').hide();
			}else{
				$('.need-hide').show();
			}
		})
		
		
		var submitExercise = function(submitType){
			var knowledgeNode =  $('#tree').tree("getSelectedNode");
			console.log(knowledgeNode);
			var answerNum = $('#answer-num').val();
			var answerContent = $('#answer-content').val();
			var exType = $('[name=ex-type]:checked').attr('value');
			console.log(answerContent);
			if(exType == 'sq'){
				if(knowledgeNode&&knowledgeNode.id&&fileNameDesc&&fileNameContent&&fileNameAnswer){
					$('.help-block').hide();
					console.log('succ');
					//send Ajax
					$.ajax({
						cache: false,
						type: "POST",
						//url:"/ExerciseRecordJudge/ajax_exercises/"+gradeNo+"/"+classNo,
						url:'/ExerciseEditControl/exercise_commit',
						data:{exercisetype:exType,exerciselevel:$('[name=level-type]:checked').attr('value'),kid:knowledgeNode.id,exercisetitlepic:fileNameDesc,exercisecontentpic:fileNameContent,exerciseanswerpic:fileNameAnswer},	 
						async: false,
						error: function(request) {
							console.log(request)
							//alert("发送请求失败！");
						},
						success: function(data) {
							var dataContent = JSON.parse(data);
							if(dataContent&&dataContent.errorno ==0){
								alert('成功');
								location.reload();
							}
							console.log(dataContent);
						}
					});
				}else{
					if(!knowledgeNode){
						$('#file-error-tree').show();
					}else{
						$('#file-error-tree').hide();
					}
					
					if(!fileNameDesc){
						$('#file-error-desc').show();
					}else{
						$('#file-error-desc').hide();
					}
					if(!fileNameContent){
						$('#file-error-conten').show();
					}else{
						$('#file-error-content').hide();
					}
					if(!fileNameAnswer){
						$('#file-error-answer').show();
					}else{
						$('#file-error-answer').hide();
					}
					
				}
			}else{
				if(knowledgeNode&&knowledgeNode.id&&answerNum&&$.isNumeric(answerNum)&&answerContent&&fileNameDesc&&fileNameContent&&fileNameAnswer){
					$('.help-block').hide();
					console.log('succ');
					//send Ajax
					$.ajax({
						cache: false,
						type: "POST",
						//url:"/ExerciseRecordJudge/ajax_exercises/"+gradeNo+"/"+classNo,
						url:'/ExerciseEditControl/exercise_commit',
						data:{exercisetype:exType,exerciselevel:$('[name=level-type]:checked').attr('value'),kid:knowledgeNode.id,answernumber:answerNum,answercontentcharacter:answerContent,exercisetitlepic:fileNameDesc,exercisecontentpic:fileNameContent,exerciseanswerpic:fileNameAnswer},	 
						async: false,
						error: function(request) {
							console.log(request)
							//alert("发送请求失败！");
						},
						success: function(data) {
							var dataContent = JSON.parse(data);
							if(dataContent&&dataContent.errorno ==0){
								alert('成功');
								location.reload();
							}
							console.log(dataContent);
						}
					});
				}else{
					if(!knowledgeNode){
						$('#file-error-tree').show();
					}else{
						$('#file-error-tree').hide();
					}
					if(!answerNum){
						$('#file-error-num').show();
					}else{
						$('#file-error-num').hide();
					}
					if(!$.isNumeric(answerNum)){
						$('#file-error-num').show();
					}else{
						$('#file-error-num').hide();
					}
					if(!answerContent){
						$('#file-error-text').show();
					}else{
						$('#file-error-text').hide();
					}
					if(!fileNameDesc){
						$('#file-error-desc').show();
					}else{
						$('#file-error-desc').hide();
					}
					if(!fileNameContent){
						$('#file-error-conten').show();
					}else{
						$('#file-error-content').hide();
					}
					if(!fileNameAnswer){
						$('#file-error-answer').show();
					}else{
						$('#file-error-answer').hide();
					}
					
				}
			}
		};
		
		$('.submit-btn').click(function(){
			
			submitExercise(1);
		});
		$('.add-btn').click(function(){
			
			submitExercise(1);
		});
		
	}
);
