/**
*资料上传
*by baixu
*/

console.log('upload begin');

$(document).ready(
	function(){
		
		$('#class').on('change',function(){
			console.log('begin');
			var gradeNo = $('#grade option:selected').val();
			var classNo = $('#class option:selected').val();
			console.log(classNo);
			
			$.ajax({
				cache: false,
				type: "POST",
				//url:"/ExerciseRecordJudge/ajax_exercises/"+gradeNo+"/"+classNo,
				url:'/ExerciseRecordJudge/ajax_exercises/2014',
				data:{},	 
				async: false,
				error: function(request) {
					console.log(request)
					//alert("发送请求失败！");
				},
				success: function(data) {
					
					var dataContent = JSON.parse(data);
					console.log(dataContent);
					if(dataContent.status == 0){
						var html=baidu.template('task-content',{data:dataContent.excis});
						$('#task-container').html(html);
					}else{;
						$('#task-container').html('<label>没有待批阅任务</label>');
					}
				}
			});
			
			
		});
		
	}
);
