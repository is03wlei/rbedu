/**
*作业批阅第二步
*by baixu
*/

console.log('作业批阅');

$(document).ready(
	
	function(){
		//初始题号
		var exerciseNo = 0;
		
		var sidCache = '';
		
		var exerciseData = '';
		
		var nameCache = '';
		
		
		$('.user-list').delegate('td','click',function(e){
			console.log(e.target);
			
			var sid = $(e.target).attr('data-id');
			var eaid= $('#eaid').attr('value');
			
			$.ajax({
				cache: false,
				type: "POST",
				//url:"/ExerciseRecordJudge/ajax_exercises/"+gradeNo+"/"+classNo,
				url:'/ExerciseRecordJudge/judge/'+eaid+'/'+sid,
				data:{},	 
				async: false,
				error: function(request) {
					console.log(request)
					//alert("发送请求失败！");
				},
				success: function(data) {
					
					var dataContent = JSON.parse(data);
					console.log(dataContent);
					if(dataContent&&dataContent.exercises&& dataContent.exercises.length > 0){
						exerciseData = dataContent.exercises;
						nameCache = $(e.target).html();
						var html=baidu.template('ex-item',{data:dataContent.exercises[exerciseNo],name:$(e.target).html(),num:exerciseNo+1});
						$('.task-info').html(html);
						exerciseNo ++ ;
					}else{
						$('.task-info').html('该学生没有作业需要批阅');
						exerciseNo = 0;
					}
					$('.user-list td').css('background-color','#FFFFFF');
					$(e.target).css('background-color','#f5544b');
					
				}
			});
			
		});
		$('.task-info').delegate('.next-exercise','click',function(){
			if(exerciseData && exerciseData[exerciseNo-1]){
				if(!$('#score').val()){
					$('#score-error').html('请输入评分')
				}else if(!$.isNumeric($('#score').val())){
					$('#score-error').html('输入评分需要为数字')
				}else{
					$.ajax({
						cache: false,
						type: "POST",
						//url:"/ExerciseRecordJudge/ajax_exercises/"+gradeNo+"/"+classNo,
						url:'/ExerciseRecordJudge/ajax_teacher_judge',
						data:{erid:exerciseData[exerciseNo-1].erid,Score:$('#score').val(),review:$('#comment').val(),isCorrect:$('[name=right]:checked').attr('value')},	 
						async: false,
						error: function(request) {
							console.log(request)
							//alert("发送请求失败！");
						},
						success: function(data) {
							var dataContent = JSON.parse(data);
							console.log(dataContent);
							if(dataContent.status == 0){
								if(exerciseData && exerciseData[exerciseNo]){
									var html=baidu.template('ex-item',{data:exerciseData[exerciseNo],name:nameCache,num:exerciseNo+1});
									$('.task-info').html(html);
									exerciseNo ++ ;
								}else{
									exerciseNo = 0;
									$('.task-info').html('该学生作业已经批阅完成');
								}
							}else{
								alert('该题目批阅有误，请重新输入');
							}
							
						}
					});
				}
			}
		
			
		})
		
		
	}
);
